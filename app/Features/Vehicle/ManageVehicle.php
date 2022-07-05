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

class ManageVehicle extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request ){

        try {
            $this->request = $request;
            $this->_decryptToken();
            $userId = $this->userId;
            $requestData = $request->all();

            $response = $this->saveVehicleData($requestData, $userId);

            if($response->status == true){
                $this->responseData =  CommonUtil::makeKeyValue('success_message','Saved Successfully');
            }else{
                $this->responseData[] = $response->errors;
                $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;
            }

            return $this->_respondApi();
        } catch (\Throwable $exception) {
            return APIResponder::respondInternalError();
        }

    }

    public function saveVehicleData($requestData, $updatedBy) {
        $response = new AjaxResponse();
        $encVehicleId = CommonUtil::fetch($requestData, 'vehicle_id');
        $vehicleId = ($encVehicleId) ? CommonUtil::decrypt($encVehicleId) : null;

        $encOwnerId = CommonUtil::fetch($requestData, 'owner_id');
        $ownerId = CommonUtil::decrypt($encOwnerId);

        $dataToSave = [
            'vehicle_name' => CommonUtil::fetch($requestData, 'name'),
            'owner_id' => $ownerId,
            'description' => CommonUtil::fetch($requestData, 'description'),
            'license_plate' => CommonUtil::fetch($requestData, 'license_plate'),
            'updated_by' => $updatedBy,
            'created_by' => $updatedBy,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        $savedVehicle = Vehicle::manageVehicle($dataToSave, $vehicleId);

      if($savedVehicle) {
        $response->status = true;
        $response->data = $savedVehicle;
      } else {
        $response->status = false;
        $response->errors = CommonUtil::makeKeyValue('error','Something went wrong. Unable to save');
      }
      return $response;
    }





}
