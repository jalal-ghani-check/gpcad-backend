<?php


namespace App\Features\Houses;
use App\Common\CommonUtil;
use App\Contracts\HTTPStatusCode;
use App\Features\BaseApi;
use App\Models\House;
use App\Traits\APIResponder;
use Illuminate\Http\Request;

class ManageHouse extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request){

        try {

            $this->request = $request;
            $this->_decryptToken();

            $requestData = $request->all();
            if($this->userId) {
              $house = $this->manageHouse($requestData);
              sleep(1);
              $this->responseData = ['image' => route('house-picture',['house_id' => CommonUtil::encrypt(CommonUtil::fetchFromObject($house, 'house_id'))]).'?h='.mt_rand(1,100)];
            } else {
              $this->responseData[] = CommonUtil::makeKeyValue('error', 'Invalid data. Unable to perform action.');
              $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;
            }

            return $this->_respondApi();
        } catch (\Throwable $exception) {
            return $exception;
          return APIResponder::respondInternalError();
        }

    }

    public function manageHouse($data)
    {
      $data['user_id'] = $data['updated_by'] = $this->userId;
      $houseId = CommonUtil::fetch($data, 'house_id', null);
      if($houseId) {
        $houseId = CommonUtil::decrypt($houseId);
        unset($data['house_id']);

        $image = $data['image'];
        if(!$image){
            unset($data['image']);
        }

      }

      return House::manageHouse($data, $houseId);
    }
}
