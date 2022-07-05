<?php


namespace App\Features\Users;
use App\Common\AjaxResponse;
use App\Common\CommonUtil;
use App\Contracts\HTTPStatusCode;
use App\Features\BaseApi;
use App\Models\ProfileRecord;
use App\Models\UserRights;
use App\Models\Users\Role;
use App\Models\Users\User;
use App\Traits\APIResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AddUserData extends BaseApi
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
        $username = CommonUtil::fetch($requestData,'username');

        $checkUserExists = User::verifyUserNameExist($username);
        if($checkUserExists) {
            $response->status = false;
            $response->errors = CommonUtil::makeKeyValue('already_exist','Username already exists');
            return $response;
        }

        $updatedByUser = User::getUser($updatedBy);
        $updatedByUserRoleId = CommonUtil::fetchFromObject($updatedByUser,'role_id');
        $updatedByUserDepartmentId = CommonUtil::fetchFromObject($updatedByUser,'department_id');




        $userData = [
            'username' => CommonUtil::fetch($requestData,'username'),
            'profile_picture' => CommonUtil::fetch($requestData,'profile_picture'),
            'call_sign' => CommonUtil::fetch($requestData,'call_sign'),
            'citizen_id' => CommonUtil::fetch($requestData,'citizen_id'),
            'full_name' => CommonUtil::fetch($requestData,'full_name'),
            'rank_id' => CommonUtil::decrypt(CommonUtil::fetch($requestData,'enc_rank_id')),
            'department_id' => CommonUtil::decrypt(CommonUtil::fetch($requestData,'enc_department_id')),
            'updated_by' => $updatedBy,
            'gender' => '',
            'password_salt' => '',
        ];



        if( $updatedByUserRoleId == Role::ROLE_ID_ADMIN){
            $userData['role_id'] = CommonUtil::decrypt(CommonUtil::fetch($requestData,'enc_role_id')) ?? 2;
            $userData['department_id'] = CommonUtil::decrypt(CommonUtil::fetch($requestData,'enc_department_id')) ?? 2;

        }else {
            $userData['role_id'] = $updatedByUserRoleId;
            $userData['department_id'] = $updatedByUserDepartmentId;
        }

        $password = CommonUtil::fetch($requestData,'password');
        if($password && $password != ''){
            $userData['password'] = Hash::make($password);
        }
        User::manageUser($userData);

        return $response;
    }

}

