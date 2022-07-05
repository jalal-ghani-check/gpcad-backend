<?php

namespace App\Http\Controllers\API\v1\Users;

use App\Features\Profile\DeleteProfile;
use App\Features\Profile\SaveProfileSettings;
use App\Features\Users\AddUserData;
use App\Features\Users\DeleteUser;
use App\Features\Users\FetchAllUsers;
use App\Features\Users\FetchHousePicture;
use App\Features\Users\FetchHousePictureImage;
use App\Features\Users\FetchMetaData;
use App\Features\Users\FetchUserData;
use App\Features\Users\UpdateUserData;
use App\Features\Warrants\FetchWarrants;
use App\Features\Warrants\UpdateWarrantStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\AddUserRequest;
use App\Http\Requests\API\v1\DeleteProfileRequest;
use App\Http\Requests\API\v1\DeleteUserRequest;
use App\Http\Requests\API\v1\ProfileSettingsRequest;

use App\Http\Requests\API\v1\UpdateUserRequest;
use App\Http\Requests\API\v1\UpdateWarrantStatusRequest;
use App\Traits\APIResponder;
use Illuminate\Http\Request;

class APIUsersController extends Controller
{
    public function fetchAllUsers(Request $request)
    {
        return (new FetchAllUsers())->_handleApi($request);
    }

    public function UpdateUserData(UpdateUserRequest $request)
    {
        return (new UpdateUserData())->_handleApi($request);
    }

    public function addUserData(AddUserRequest $request)
    {
        return (new AddUserData())->_handleApi($request);
    }




    public function DeleteUserData(DeleteUserRequest $request)
    {
        return (new DeleteUser())->_handleApi($request);
    }

    public function fetchMetaData(Request $request)
    {
        return (new FetchMetaData())->_handleApi($request);
    }

    public function fetchUserData(Request $request)
    {
        return (new FetchUserData())->_handleApi($request);
    }

    public function fetchHousePicture(Request $request)
    {
        return (new FetchHousePictureImage())->_handleApi($request);
    }
    public function fetchHousePictureBase(Request $request, $encHouseId)
    {
        return (new FetchHousePicture())->_handleApi($request,$encHouseId);
    }










}


