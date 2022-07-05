<?php


namespace App\Features\Houses;
use App\Common\CommonUtil;
use App\Features\BaseApi;
use App\Models\House;
use App\Models\ProfileRecord;
use App\Traits\APIResponder;
use Illuminate\Http\Request;

class FetchHouses extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request ){

        try {
            $this->request = $request;
            $this->_decryptToken();
            $this->responseData = $this->prepareAllHousesData();

            return $this->_respondApi();
        } catch (\Throwable $exception) {
            return APIResponder::respondInternalError();
        }

    }

    public function prepareAllHousesData()
    {
      $houses = House::getAll();

      $data = [];

      if($houses) {
        $profileIds = collect($houses)->unique('linked_profile_id')->pluck('linked_profile_id', 'house_id')->values();
        $allProfiles = ProfileRecord::getAll();
        $allProfiles = collect($allProfiles)->whereIn('profile_id', $profileIds)
          ->mapWithKeys(function ($profile) {
            return [$profile->profile_id => ['owner_full_name' => $profile->full_name, 'owner_designation' => $profile->designation]];
          })->all();
        foreach ($houses as $house) {
          $linkedProfileId = CommonUtil::fetchFromObject($house, 'linked_profile_id', null);
          $houseArr = [
            'enc_house_id' => CommonUtil::encrypt(CommonUtil::fetchFromObject($house, 'house_id')),
            'house_name' => CommonUtil::fetchFromObject($house, 'house_name', 'N/A'),
            'image' => route('house-picture',['house_id' => CommonUtil::encrypt(CommonUtil::fetchFromObject($house, 'house_id'))]),
            'image_base64' => route('house-picture-base64',['house_id' => CommonUtil::encrypt(CommonUtil::fetchFromObject($house, 'house_id'))]),
            'is_house_linked' => (bool) $linkedProfileId,
            'linked_profile_id' => ($linkedProfileId) ? CommonUtil::encrypt($linkedProfileId) : null,
            'price' => number_format(CommonUtil::fetchFromObject($house, 'price'), '2', '.', ','),
            'non_formatted_price' => CommonUtil::fetchFromObject($house, 'price'),
            'enc_user_id' => CommonUtil::encrypt(CommonUtil::fetchFromObject($house, 'user_id'))
          ];
          $data[] = $houseArr + ($allProfiles[$linkedProfileId] ?? []);
        }
      }

      return $data;
    }






}
