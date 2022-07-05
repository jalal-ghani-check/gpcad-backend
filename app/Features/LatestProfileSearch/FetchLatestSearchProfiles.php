<?php


namespace App\Features\LatestProfileSearch;
use App\Common\CommonUtil;
use App\Features\BaseApi;
use App\Models\LatestProfileSearch;
use App\Models\ProfileRecord;
use App\Traits\APIResponder;
use Illuminate\Http\Request;

class FetchLatestSearchProfiles extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request ){

        try {
            $this->request = $request;
            $this->_decryptToken();

            $this->responseData = $this->fetchLatestSearches();
            return $this->_respondApi();
        } catch (\Throwable $exception) {
            return APIResponder::respondInternalError();
        }

    }

    public function fetchLatestSearches(){
      $profileRecords = ProfileRecord::getAll();
      $profileRecords = ($profileRecords) ? collect($profileRecords) : null;
      $searchRecords = LatestProfileSearch::fetchLatestProfiles(8);
      $data = [];
      if($searchRecords && count($searchRecords) && $profileRecords) {
        foreach ($searchRecords as $record) {
          $profile = $profileRecords->where('profile_id', $record->profile_id)->first();
          if($profile) {
            $data[] = [
              'profile_id' => CommonUtil::encrypt(CommonUtil::fetchFromObject($profile, 'profile_id')),
              'full_name' => CommonUtil::fetchFromObject($profile, 'full_name'),
              'citizen_id' => CommonUtil::fetchFromObject($profile, 'citizen_id'),
              'is_weapon_license_valid' => CommonUtil::fetchFromObject($profile, 'is_weapon_license_valid')
            ];
          }
        }
      }
      return $data;
    }




}
