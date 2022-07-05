<?php


namespace App\Features\Houses;
use App\Common\CommonUtil;
use App\Contracts\HTTPStatusCode;
use App\Features\BaseApi;
use App\Models\House;
use App\Traits\APIResponder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DeleteHouse extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request, $encHouseId){

        try {

            $this->request = $request;
            $this->_decryptToken();
            $houseId = CommonUtil::decrypt($encHouseId);
            if($houseId) {
              $this->responseData = $this->deleteHouseByHouseId($houseId);
            } else {
              $this->responseData[] = CommonUtil::makeKeyValue('error', 'Invalid data. Unable to perform action.');
              $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;
            }

            return $this->_respondApi();
        } catch (\Throwable $exception) {
          return APIResponder::respondInternalError();
        }

    }

    public function deleteHouseByHouseId($houseId)
    {
      return House::manageHouse([
        'deleted_at' => Carbon::now(),
        'updated_by' => $this->userId
      ], $houseId);
    }






}
