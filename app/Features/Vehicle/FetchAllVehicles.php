<?php


namespace App\Features\Vehicle;
use App\Common\CommonUtil;
use App\Features\BaseApi;
use App\Models\ProfileRecord;
use App\Models\Vehicle;
use App\Traits\APIResponder;
use Illuminate\Http\Request;

class FetchAllVehicles extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request ){

        try {
            $this->request = $request;
            $this->_decryptToken();

            $this->responseData = $this->fetchAllVehicles();

            /*else{
                $this->responseData[] = CommonUtil::makeKeyValue('error','Something went wrong');
                $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;
            }*/
            return $this->_respondApi();
        } catch (\Throwable $exception) {
            return APIResponder::respondInternalError();
        }

    }

    public function fetchAllVehicles(){
      $allVehicles = Vehicle::getAll();
      $data = [];
      if($allVehicles && count($allVehicles)) {
        foreach ($allVehicles as $vehicle) {
          $ownerId = CommonUtil::fetchFromObject($vehicle, 'owner_id');
          $owner = ProfileRecord::getProfileRecordByProfileId($ownerId);
          $ownerName = CommonUtil::fetchFromObject($owner, 'full_name');
          $encOwnerId = CommonUtil::encrypt($ownerId);
          $data[] = [
            'vehicle_id' => CommonUtil::encrypt(CommonUtil::fetchFromObject($vehicle, 'vehicle_id')),
            'vehicle_name' => CommonUtil::fetchFromObject($vehicle, 'vehicle_name'),
            'vehicle_model' => CommonUtil::fetchFromObject($vehicle, 'vehicle_model'),
            'license_plate' => CommonUtil::fetchFromObject($vehicle, 'license_plate'),
            'description' => CommonUtil::fetchFromObject($vehicle, 'description'),
            'owner_id' => $encOwnerId,
            'full_name' => $ownerName
          ];
        }
      }
      return $data;
    }




}
