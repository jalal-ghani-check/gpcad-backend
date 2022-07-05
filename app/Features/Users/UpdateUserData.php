<?php


namespace App\Features\Users;
use App\Common\AjaxResponse;
use App\Common\CommonUtil;
use App\Contracts\HTTPStatusCode;
use App\Features\BaseApi;
use App\Models\ProfileRecord;
use App\Models\UserRights;
use App\Models\Users\User;
use App\Traits\APIResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UpdateUserData extends BaseApi
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
        $userIdOfConcernedUser = CommonUtil::decrypt(CommonUtil::fetch($requestData,'enc_user_id'));

        $user =  User::getUser($userIdOfConcernedUser);
        $alreadySavedUsername = CommonUtil::fetchFromObject($user,'username');
        if($username != $alreadySavedUsername) {
            $checkUserExists = User::verifyUserNameExist($username);
            if($checkUserExists) {
                $response->status = false;
                $response->errors = CommonUtil::makeKeyValue('already_exist','Username already exists');
                return $response;
            }
        }

        $userData = [
            'username' => CommonUtil::fetch($requestData,'username'),
            'profile_picture' => CommonUtil::fetch($requestData,'profile_picture'),
            'call_sign' => CommonUtil::fetch($requestData,'call_sign'),
            'citizen_id' => CommonUtil::fetch($requestData,'citizen_id'),
            'full_name' => CommonUtil::fetch($requestData,'full_name'),
            'rank_id' => CommonUtil::decrypt(CommonUtil::fetch($requestData,'enc_rank_id')),
            'department_id' => CommonUtil::decrypt(CommonUtil::fetch($requestData,'enc_department_id')),
            'updated_by' => $updatedBy
        ];
        $password = CommonUtil::fetch($requestData,'password');
        if($password && $password != ''){
            $userData['password'] = Hash::make($password);
        }
        User::manageUser($userData,$userIdOfConcernedUser);
        $rightsData = [
            'is_allowed_to_approve_warrants' => CommonUtil::fetch($requestData,'is_allowed_to_approve_warrants'),
            'is_allowed_to_create_laws' => CommonUtil::fetch($requestData,'is_allowed_to_create_laws'),
            'is_allowed_to_create_profile' => CommonUtil::fetch($requestData,'is_allowed_to_create_profile'),
            'is_allowed_to_create_police_reports' => CommonUtil::fetch($requestData,'is_allowed_to_create_police_reports'),
            'is_allowed_to_create_medical_reports' => CommonUtil::fetch($requestData,'is_allowed_to_create_medical_reports'),
            'is_allowed_to_create_warrants' => CommonUtil::fetch($requestData,'is_allowed_to_create_warrants'),
            'is_allowed_to_delete_laws' => CommonUtil::fetch($requestData,'is_allowed_to_delete_laws'),
            'is_allowed_to_delete_police_reports' => CommonUtil::fetch($requestData,'is_allowed_to_delete_police_reports'),
            'is_allowed_to_delete_medical_reports' => CommonUtil::fetch($requestData,'is_allowed_to_delete_medical_reports'),
            'is_allowed_to_delete_warrants' => CommonUtil::fetch($requestData,'is_allowed_to_delete_warrants'),
            'is_allowed_to_edit_laws' => CommonUtil::fetch($requestData,'is_allowed_to_edit_laws'),
            'is_allowed_to_edit_profile' => CommonUtil::fetch($requestData,'is_allowed_to_edit_profile'),
            'is_allowed_to_edit_police_reports' => CommonUtil::fetch($requestData,'is_allowed_to_edit_police_reports'),
            'is_allowed_to_edit_medical_reports' => CommonUtil::fetch($requestData,'is_allowed_to_edit_medical_reports'),
            'is_allowed_to_edit_warrants' => CommonUtil::fetch($requestData,'is_allowed_to_edit_warrants'),
            'is_allowed_to_expunge_records' => CommonUtil::fetch($requestData,'is_allowed_to_expunge_records'),
            'is_allowed_to_high_commands' => CommonUtil::fetch($requestData,'is_allowed_to_high_commands'),
            'is_allowed_to_serve_warrants' => CommonUtil::fetch($requestData,'is_allowed_to_serve_warrants'),
            'is_allowed_to_view_bails' => CommonUtil::fetch($requestData,'is_allowed_to_view_bails'),
            'is_allowed_to_view_charges' => CommonUtil::fetch($requestData,'is_allowed_to_view_charges'),
            'is_allowed_to_view_laws' => CommonUtil::fetch($requestData,'is_allowed_to_view_laws'),
            'is_allowed_to_view_profile' => CommonUtil::fetch($requestData,'is_allowed_to_view_profile'),
            'is_allowed_to_view_police_reports' => CommonUtil::fetch($requestData,'is_allowed_to_view_police_reports'),
            'is_allowed_to_view_medical_reports' => CommonUtil::fetch($requestData,'is_allowed_to_view_medical_reports'),
            'is_allowed_to_view_warrants' => CommonUtil::fetch($requestData,'is_allowed_to_view_warrants'),
        ];
        UserRights::manageUserPermissionsByUserId($userIdOfConcernedUser, $rightsData);



        return $response;
    }

}

