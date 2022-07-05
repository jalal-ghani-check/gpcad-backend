<?php


namespace App\Features\Laws;
use App\Common\AjaxResponse;
use App\Common\CommonUtil;
use App\Contracts\HTTPStatusCode;
use App\Features\BaseApi;
use App\Models\Law;
use App\Traits\APIResponder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DeleteLaw extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request, $lawId){

        try {
            $this->request = $request;
            $this->_decryptToken();
            $userId = $this->userId;
            $lawId = CommonUtil::decrypt($lawId);

            $response = $this->deleteLaw($lawId, $userId);

            if($response->status == true){
                $this->responseData =  CommonUtil::makeKeyValue('success_message',['Saved Successfully']);
            }else{
                $this->responseData[] = $response->errors;
                $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;
            }

            return $this->_respondApi();
        } catch (\Throwable $exception) {
            return APIResponder::respondInternalError();
        }

    }

    public function deleteLaw($lawId, $updatedBy) {
        $response = new AjaxResponse();

        $userData = [
            'updated_by' => $updatedBy,
            'deleted_at' => Carbon::now()
        ];
        $res = Law::manageLaw($userData, $lawId);
        if($res) {
          $response->status = true;
          $response->data = [];
        } else {
          $response->status = false;
          $response->errors = CommonUtil::makeRequestResponseKeyValue(['error' => ['law not deleted successfully']]);
        }

        return $response;
    }





}
