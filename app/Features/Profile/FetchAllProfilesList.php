<?php


namespace App\Features\Profile;
use App\Common\CommonUtil;
use App\Features\BaseApi;
use App\Models\ProfileRecord;
use App\Traits\APIResponder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FetchAllProfilesList extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request ){

        try {
            $this->request = $request;
            $this->_decryptToken();
            $this->responseData = $this->fetchAllProfiles();
            return $this->_respondApi();
        } catch (\Throwable $exception) {
            return APIResponder::respondInternalError();
        }

    }

    public function fetchAllProfiles(){
      $allProfiles = ProfileRecord::getAllProfilesComplete();
      $data = [];
      if($allProfiles && count($allProfiles)) {
        foreach ($allProfiles as $profile) {
            $crimesData = (new FetchProfileData())->getChargesList(CommonUtil::fetchFromObject($profile, 'profile_id'));

            $data[] = [
            'profile_id' => CommonUtil::encrypt(CommonUtil::fetchFromObject($profile, 'profile_id')),
            'full_name' => CommonUtil::fetchFromObject($profile, 'full_name'),
            'create_user_id' => CommonUtil::encrypt(CommonUtil::fetchFromObject($profile, 'user_id')),
            'creator_full_name' => CommonUtil::fetchFromObject($profile, 'user_full_name'),
            'designation' => CommonUtil::fetchFromObject($profile, 'designation'),
            'dob' => Carbon::parse(CommonUtil::fetchFromObject($profile,'dob'))->toFormattedDateString(),
            'dob_ymd' => Carbon::parse(CommonUtil::fetchFromObject($profile,'dob'))->format('Y-m-d'),
            'age' => CommonUtil::calculateAge(CommonUtil::fetchFromObject($profile,'dob')),
            'address' => CommonUtil::fetchFromObject($profile,'address'),
            'citizen_id' => CommonUtil::fetchFromObject($profile,'citizen_id'),
            'finger_print' => CommonUtil::fetchFromObject($profile,'finger_print'),
            'dna_code' => CommonUtil::fetchFromObject($profile,'dna_code'),
            'points' => $crimesData['points'],
            'gender' => ucfirst(CommonUtil::fetchFromObject($profile,'gender')),

            'is_driver_license_valid' => CommonUtil::fetchFromObject($profile,'is_driver_license_valid'),
            'is_weapon_license_valid' => CommonUtil::fetchFromObject($profile,'is_weapon_license_valid'),
            'is_pilot_license_valid' => CommonUtil::fetchFromObject($profile,'is_pilot_license_valid'),
            'is_hunting_license_valid' => CommonUtil::fetchFromObject($profile,'is_hunting_license_valid'),
            'is_fishing_license_valid' => CommonUtil::fetchFromObject($profile,'is_fishing_license_valid'),
            'jury_duty' => CommonUtil::fetchFromObject($profile,'jury_duty'),
            'created_at' => date("d/m/y H:i", strtotime(CommonUtil::fetchFromObject($profile,'created_at'))),
            'updated_at' => date("d/m/y H:i", strtotime(CommonUtil::fetchFromObject($profile,'updated_at'))),


          ];
        }
      }
      return $data;
    }


}
