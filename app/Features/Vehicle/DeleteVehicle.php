<?php


namespace App\Features\Vehicle;
use App\Common\AjaxResponse;
use App\Common\CommonUtil;
use App\Contracts\HTTPStatusCode;
use App\Features\BaseApi;
use App\Models\Vehicle;
use App\Traits\APIResponder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DeleteVehicle extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request, $encVehicleId){

        try {

            $this->request = $request;
            $this->_decryptToken();
            $vehicleId = CommonUtil::decrypt($encVehicleId);
            if($vehicleId) {
              $response = $this->deleteVehicleById($vehicleId);
              if($response->status) {
                $this->responseData = CommonUtil::fetchFromObject($response,'data');
              } else {
                $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;
                $this->responseData[] = CommonUtil::fetchFromObject($response,'errors');
              }
            } else {
              $this->responseData[] = CommonUtil::makeKeyValue('error', 'Invalid data. Unable to perform action.');
              $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;
            }

            return $this->_respondApi();
        } catch (\Throwable $exception) {
          return APIResponder::respondInternalError();
        }

    }

    public function deleteVehicleById($vehicleId)
    {
      $response = new AjaxResponse();
      $savedVehicle = Vehicle::manageVehicle([
        'deleted_at' => Carbon::now(),
        'updated_by' => $this->userId
      ], $vehicleId);

      if ($savedVehicle) {
        $response->status = true;
        $response->data = $savedVehicle;
      } else {
        $response->status = false;
        $response->errors = CommonUtil::makeKeyValue('error', 'Internal Error: unable to update vehicle');
      }
      return $response;
    }






}
