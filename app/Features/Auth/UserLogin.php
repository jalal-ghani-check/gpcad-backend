<?php

namespace App\Features\Auth;


use App\Contracts\HTTPStatusCode;
use App\Features\BaseApi;
use App\Models\Department;
use App\Models\Rank;
use App\Models\UserPermissions;
use App\Models\Users\Role;
use App\Models\Users\User;
use App\Models\Users\UserAPIToken;
use App\Models\Warrant;
use App\Traits\APIResponder;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Common\CommonUtil;
use Illuminate\Support\Facades\Auth;


class UserLogin extends BaseApi
{
  use AuthenticatesUsers;

  public $maxAttempts;
  public $decayMinutes;
  protected $redirectTo = '/auth/landingPage';

  public function __construct()
  {
    $this->maxAttempts = env("MAX_INVALID_TRIES", 5);
    $this->decayMinutes = env("MAX_INVALID_TRIES_TIMEOUT", 45);
  }

  public function _handleAPI(Request $request)
  {
      try {
          $this->request = $request;
          $this->_decryptToken();
          $response = $this->loginUser($request);


          if (isset($response['errors']) && $response['status'] == false) {
              $error[] = CommonUtil::makeKeyValue('auth_failed', $response['errors']['error']);
              $this->responseData = $error;
              $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;

          } else {
              $user = $response['data'];

              $role = Role::getRoleById($user->role_id);
              $rank = Rank::getRankById($user->rank_id);
              $department = Department::getDepartmentById($user->department_id);

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
                  'api_token' => UserAPIToken::createToken($user->user_id),
                  'is_admin' => $user->role_id == Role::ROLE_ID_ADMIN,
                  'role_name' => CommonUtil::fetchFromObject($role,'role_name','-'),
                  'rank_name' => CommonUtil::fetchFromObject($rank,'rank_name','-'),
                  'department_name' => CommonUtil::fetchFromObject($department,'depart_name','-'),
              );
              $rights = UserPermissions::loadUserRoleAndPermissionValues($user->user_id);
              $rightKeyViewWarrant = UserPermissions::IS_ALLOWED_TO_VIEW_WARRANTS['module_name_key'];
              $isTherePendingWarrants = Warrant::isTherePendingWarrants() && CommonUtil::fetch($rights,$rightKeyViewWarrant);
              $data_response['isTherePendingWarrants'] = $isTherePendingWarrants;
              $this->responseData = array_merge($data_response,$rights);
          }
          return $this->_respondApi();
      } catch (\Throwable $exception) {
          return APIResponder::respondInternalError();
      }

  }


  public function loginUser($request)
  {
    $requestData = $request->all();
    $response = array();

    if ($this->hasTooManyLoginAttempts($request)) {
      $this->fireLockoutEvent($request);
      $seconds = $this->limiter()->availableIn($this->throttleKey($request));
      $minutes = ceil($seconds / 60);
      $response['errors'] = ['error' => __('auth.throttle', ['minutes' => $minutes])];
      $response['status'] = false;
      return $response;
    }
    $this->incrementLoginAttempts($request);

    $userIdentifier = CommonUtil::fetch($requestData, 'username');
    $requestPassword = CommonUtil::fetch($requestData, 'password');

    $userData = User::getUserByUserName($userIdentifier);

    if ($userData) {


      $userName = CommonUtil::fetchFromObject($userData, 'username');

      if (Auth::attempt(['username' => $userName, 'password' => $requestPassword])) {
        $user = $this->guard()->user();
        if ($user) {
          $this->clearLoginAttempts($request);
          $response['status'] = true;
          $response['data'] = $user;
          return $response;
        }
      }
    }

    $response['errors'] = ['error' => __('auth.failed')];
    $response['status'] = false;
    return $response;
  }



  protected function throttleKey(Request $request)
  {
//    return Str::lower($request->input($this->username())).'|'.$request->ip().'|'.$request->userAgent();
    return \Illuminate\Support\Str::lower($request->input($this->username()));
  }

  public function username(){
    return 'username';
  }




}
