<?php


namespace App\Features\Profile;
use App\Common\CommonUtil;
use App\Features\BaseApi;
use App\Models\ProfileRecord;
use App\Traits\APIResponder;
use Illuminate\Http\Request;

class FetchAllProfiles extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request ){

        try {
            $this->request = $request;
            $this->_decryptToken();

            $this->responseData = $this->fetchAllProfiles();

            /*else{
                $this->responseData[] = CommonUtil::makeKeyValue('error','Something went wrong');
                $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;
            }*/
            return $this->_respondApi();
        } catch (\Throwable $exception) {
            return APIResponder::respondInternalError();
        }

    }

    public function fetchAllProfiles(){
      $allProfiles = ProfileRecord::getAll();
      $data = [];
      if($allProfiles && count($allProfiles)) {
        foreach ($allProfiles as $profile) {
          $data[] = [
            'profile_id' => CommonUtil::encrypt(CommonUtil::fetchFromObject($profile, 'profile_id')),
            'full_name' => CommonUtil::fetchFromObject($profile, 'full_name'),
            'designation' => CommonUtil::fetchFromObject($profile, 'designation')
          ];
        }
      }
      return $data;
    }




}
