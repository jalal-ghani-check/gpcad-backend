<?php

namespace App\Features\Auth;

use App\Common\CommonUtil;
use App\Contracts\HTTPStatusCode;
use App\Features\BaseApi;
use App\Models\UserPermissions;
use App\Models\Users\User;
use App\Models\Users\UserAPIToken;
use App\Traits\APIResponder;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Users\Role;

use Illuminate\Support\Facades\Auth;

class RegisterUser extends BaseApi {

  public function __construct() {

  }

  public function _handleAPI(Request $request){

      try {
          $this->request = $request;
          $this->_decryptToken();
          $response =  $this->registerUser();

          if(isset($response['status']) && $response['status'] == true){
              $user_id = $response['user_id'];
              $user = User::getUser($user_id);

              $data_response = array(
                  'user_id' => $user->user_id,
                  'enc_user_id' => CommonUtil::encrypt($user->user_id),
                  'gender' => $user->gender,
                  'full_name' => $user->full_name,
                  'username' => $user->username,
                  'citizen_id' => $user->citizen_id,
                  'profile_picture' => $user->profile_picture,
                  'rank_id' => $user->rank_id,
                  'department_id' => $user->department_id,
                  'call_sign' => $user->call_sign,
                  'role_id' => $user->role_id,
                  'enc_rank_id' => CommonUtil::encrypt($user->rank_id),
                  'enc_department_id' => CommonUtil::encrypt($user->department_id),
                  'enc_role_id' => CommonUtil::encrypt($user->role_id),
                  'api_token' => UserAPIToken::createToken($user->user_id)

              );
              $rights = UserPermissions::loadUserRoleAndPermissionValues($user->user_id);
              $this->responseData = array_merge($data_response,$rights);
          }else{
              $this->responseData[] = CommonUtil::makeKeyValue('error',$response['message']);
              $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;
          }
          return $this->_respondApi();
      } catch (\Throwable $exception) {
          return APIResponder::respondInternalError();
      }

  }


  public function registerUser(){
    $response = array();
    $data = $this->request->all();
    $roleId = $data['role_id'];
    $userName = $data['username'];

    $checkUserExists = User::verifyUserNameExist($userName);

    $nonAdminRoles = Role::NON_ADMIN_ROLES_ARR;

    if (!$checkUserExists && in_array($roleId,$nonAdminRoles)) {

        $user = User::registerUser($data);
        if ($user) {
            $response['status'] = true;
            $response['user_id'] = $user->user_id;
            return $response;
        }
    }

    $response['errors']  = ['error' => __('validation.email_exists'),];
    $response['message'] = __('validation.email_exists');
    $response['status'] = false;
    return $response;

  }


}
