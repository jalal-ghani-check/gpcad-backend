<?php


namespace App\Features\Profile;
use App\Common\AjaxResponse;
use App\Common\CommonUtil;
use App\Contracts\HTTPStatusCode;
use App\Features\BaseApi;
use App\Models\ProfileRecord;
use App\Models\Users\Role;
use App\Models\Users\User;
use App\Traits\APIResponder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AddProfile extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request ){

        try {
            $this->request = $request;
            $this->_decryptToken();
            $userId = $this->userId;
            $requestData = $request->all();

            $response = $this->saveUserData($requestData, $userId);

            if($response->status == true){
                $this->responseData =  CommonUtil::makeKeyValue('success_message','Saved Successfully');
            }else{
                $this->responseData[] = $response->errors;
                $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;
            }

            return $this->_respondApi();
        } catch (\Throwable $exception) {
            return APIResponder::respondInternalError();
        }

    }

    public function saveUserData($requestData, $updatedBy) {
        $response = new AjaxResponse();
        $profileId = CommonUtil::fetch($requestData, 'profile_id');
        $profileId = CommonUtil::decrypt($profileId);
        $citizenId = CommonUtil::fetch($requestData,'citizen_id');

        if(!$profileId) {
          $checkcitizenIdProfileExists = ProfileRecord::verifyCitizenIdExist($citizenId);
          if($checkcitizenIdProfileExists) {
            $response->status = false;
            $response->errors = CommonUtil::makeKeyValue('already_exist','Citizen ID already exists');
            return $response;
          }
        }

        $userData = [
            'full_name' => CommonUtil::fetch($requestData,'full_name'),
            'designation' => CommonUtil::fetch($requestData,'designation'),
            'gender' => CommonUtil::fetch($requestData,'gender'),
            'citizen_id' => CommonUtil::fetch($requestData,'citizen_id'),
            'dob' => CommonUtil::fetch($requestData,'dob'),
            'address' => CommonUtil::fetch($requestData,'address'),
            'finger_print' => CommonUtil::fetch($requestData,'finger_print'),
            'dna_code' => CommonUtil::fetch($requestData,'dna_code'),
            'points' => 0,
            'is_fishing_license_valid' => 0,
            'is_hunting_license_valid' => 0,
            'is_driver_license_valid' => 0,
            'is_weapon_license_valid' => 0,
            'is_pilot_license_valid' => 0,
            'jury_duty' => 0,
            'updated_by' => $updatedBy,
            'created_by' => $updatedBy,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),

        ];
        ProfileRecord::manageProfileRecord($userData, $profileId);

        return $response;
    }





}
