<?php

namespace App\Http\Controllers\API\v1\Houses;

use App\Features\Houses\DeleteHouse;
use App\Features\Houses\FetchHouses;
use App\Features\Houses\ManageHouse;
use App\Features\Houses\ManageHouseOwner;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\ManageHouseOwnerRequest;
use App\Http\Requests\API\v1\ManageHouseRequest;
use Illuminate\Http\Request;

class APIHouseController extends Controller
{

    public function fetchHouses(Request $request)
    {
        return (new FetchHouses())->_handleApi($request);
    }

    public function manageHouse(ManageHouseRequest $request)
    {
        return (new ManageHouse())->_handleApi($request);
    }

    public function manageHouseOwner(ManageHouseOwnerRequest $request)
    {
        return (new ManageHouseOwner())->_handleApi($request);
    }

    public function deleteHouse(Request $request, $houseId)
    {
        return (new DeleteHouse())->_handleApi($request, $houseId);
    }

}


