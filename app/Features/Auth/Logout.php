<?php

namespace App\Features\Auth;

use App\Common\CommonUtil;
use App\Contracts\HTTPStatusCode;
use App\Features\BaseApi;
use App\Models\Users\User;
use App\Models\Users\UserAPIToken;
use App\Traits\APIResponder;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Users\Role;

use Illuminate\Support\Facades\Auth;

class Logout extends BaseApi {

  public function __construct() {

  }

  public function _handleAPI(Request $request){

      try {
          $this->request = $request;
          $this->_decryptToken();
          UserAPIToken::deleteUserToken($this->userId);
          $this->responseData = [];
          return $this->_respondApi();
      } catch (\Throwable $exception) {
          return APIResponder::respondInternalError();
      }

  }



}
