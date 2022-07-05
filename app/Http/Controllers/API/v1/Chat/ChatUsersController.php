<?php

namespace App\Http\Controllers\API\v1\Chat;

use App\Features\Chat\Users\FetchAllChatUsers;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Features\Chat\Channels\AddNewChannelFeature;
class ChatUsersController extends Controller
{
    public function add (Request $request)
    {
      return (new AddNewChannelFeature())->_handleAPI($request);
    }

  public function fetchAllUsers(Request $request)
  {
    return (new FetchAllChatUsers())->_handleApi($request);
  }
}
