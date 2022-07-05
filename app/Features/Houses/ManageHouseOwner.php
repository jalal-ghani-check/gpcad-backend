<?php


namespace App\Features\Houses;
use App\Common\CommonUtil;
use App\Contracts\HTTPStatusCode;
use App\Features\BaseApi;
use App\Models\House;
use App\Traits\APIResponder;
use Illuminate\Http\Request;

class ManageHouseOwner extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request){

        try {

            $this->request = $request;
            $this->_decryptToken();
            $requestData = $request->all();
            $houseId = CommonUtil::decrypt(CommonUtil::fetch($requestData, 'house_id'));
            if($houseId) {
              $profileId = CommonUtil::fetch($requestData, 'profile_id');
              if($profileId) {
                $profileId = CommonUtil::decrypt($profileId);
              }
              $this->responseData = $this->manageHouseOwnerHouseById($houseId, $profileId);
            } else {
              $this->responseData[] = CommonUtil::makeKeyValue('error', 'Invalid data. Unable to perform action.');
              $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;
            }

            return $this->_respondApi();
        } catch (\Throwable $exception) {
            return APIResponder::respondInternalError();
        }

    }

    public function manageHouseOwnerHouseById($houseId, $profileId = null)
    {
      return House::manageHouse([
        'linked_profile_id' => $profileId,
        'updated_by' => $this->userId
      ], $houseId);
    }






}
