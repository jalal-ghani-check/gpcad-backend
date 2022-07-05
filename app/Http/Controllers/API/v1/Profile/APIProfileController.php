<?php

namespace App\Http\Controllers\API\v1\Profile;

use App\Features\Auth\Logout;
use App\Features\Auth\RegisterUser;
use App\Features\Auth\UserLogin;
use App\Features\Profile\AddProfile;
use App\Features\Profile\DeleteProfile;
use App\Features\Profile\FetchAllProfiles;
use App\Features\Profile\FetchAllProfilesList;
use App\Features\Profile\FetchProfileData;
use App\Features\Profile\FetchWarrents;
use App\Features\Profile\SaveProfileSettings;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\AddProfileRequest;
use App\Http\Requests\API\v1\DeleteProfileRequest;
use App\Http\Requests\API\v1\ProfileSettingsRequest;
use App\Http\Requests\API\v1\RegisterRequest;
use App\Http\Requests\API\v1\UserLoginRequest;
use App\Traits\APIResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class APIProfileController extends Controller
{

    public function fetchAllProfiles(Request $request)
    {
    return (new FetchAllProfiles())->_handleApi($request);
    }

    public function fetchAllProfilesList(Request $request)
    {
        return (new FetchAllProfilesList())->_handleApi($request);
    }



    public function fetchProfileData(Request $request, $encProfileId)
    {
    return (new FetchProfileData())->_handleApi($request,$encProfileId);
    }
    public function saveProfileSettings(ProfileSettingsRequest $request)
    {
        return (new SaveProfileSettings())->_handleApi($request);
    }
    public function deleteProfile(DeleteProfileRequest $request)
    {
        return (new DeleteProfile())->_handleApi($request);
    }

    public function addProfile(AddProfileRequest $request)
    {
        return (new AddProfile())->_handleApi($request);
    }




}


