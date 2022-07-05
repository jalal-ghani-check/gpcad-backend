<?php

namespace App\Http\Controllers\API\v1\Auth;

use App\Features\Auth\Logout;
use App\Features\Auth\RegisterUser;
use App\Features\Auth\UserLogin;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\RegisterRequest;
use App\Http\Requests\API\v1\UserLoginRequest;
use App\Traits\APIResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class APIAuthController extends Controller
{



  public function createUser(RegisterRequest $request)
  {
    return (new RegisterUser())->_handleApi($request);
  }

  public  function authenticateUser(UserLoginRequest $request){
      return (new UserLogin())->_handleApi($request);

  }

  public  function logout(Request $request){
    return (new Logout())->_handleApi($request);
  }


}


