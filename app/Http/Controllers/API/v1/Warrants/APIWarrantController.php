<?php

namespace App\Http\Controllers\API\v1\Warrants;

use App\Features\Profile\DeleteProfile;
use App\Features\Profile\SaveProfileSettings;
use App\Features\Users\AddUserData;
use App\Features\Warrants\AddWarrant;
use App\Features\Warrants\DeleteWarrant;
use App\Features\Warrants\FetchWarrants;
use App\Features\Warrants\UpdateWarrantStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\AddUserRequest;
use App\Http\Requests\API\v1\AddWarrantRequest;
use App\Http\Requests\API\v1\DeleteProfileRequest;
use App\Http\Requests\API\v1\DeleteWarrantRequest;
use App\Http\Requests\API\v1\ProfileSettingsRequest;

use App\Http\Requests\API\v1\UpdateWarrantStatusRequest;
use Illuminate\Http\Request;

class APIWarrantController extends Controller
{

    public function fetchWarrants(Request $request)
    {
        return (new FetchWarrants())->_handleApi($request);
    }

    public function updateWarrantStatus(UpdateWarrantStatusRequest $request)
    {
        return (new UpdateWarrantStatus())->_handleApi($request);
    }
    public function deleteWarrant(DeleteWarrantRequest $request)
    {
        return (new DeleteWarrant())->_handleApi($request);
    }

    public function addWarrant(AddWarrantRequest $request)
    {
        return (new AddWarrant())->_handleApi($request);
    }



}


