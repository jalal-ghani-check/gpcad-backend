<?php

namespace App\Http\Controllers\API\v1\Chat;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use App\Features\Chat\Channels\AddNewChannelFeature;
class ChannelsController extends Controller
{
    public function add (Request $request)
    {
      return (new AddNewChannelFeature())->_handleAPI($request);
    }
}
