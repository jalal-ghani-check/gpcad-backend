<?php

namespace App\Http\Controllers\API\v1\Vehicle;

use App\Features\Vehicle\DeleteVehicle;
use App\Features\Vehicle\FetchAllVehicles;
use App\Features\Vehicle\ManageVehicle;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\ManageVehicleRequest;
use Illuminate\Http\Request;

class APIVehicleController extends Controller
{

    public function fetchAllVehicles(Request $request)
    {
    return (new FetchAllVehicles())->_handleApi($request);
    }

    public function manageVehicle(ManageVehicleRequest $request)
    {
        return (new ManageVehicle())->_handleApi($request);
    }
    public function deleteVehicle(Request $request, $vehicleId)
    {
        return (new DeleteVehicle())->_handleApi($request, $vehicleId);
    }
}


