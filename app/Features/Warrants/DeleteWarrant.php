<?php


namespace App\Features\Warrants;
use App\Common\CommonUtil;
use App\Contracts\HTTPStatusCode;
use App\Features\BaseApi;
use App\Models\ProfileRecord;
use App\Models\Warrant;
use App\Traits\APIResponder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DeleteWarrant extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request ){

        try {
            $this->request = $request;
            $this->_decryptToken();
            $requestData = $request->all();
            $warrantId = CommonUtil::decrypt(CommonUtil::fetch($requestData,'warrant_id'));

            if($warrantId) {
                $data = [
                    'deleted_at' => Carbon::now(),
                    'updated_by' => $this->userId,
                ];
                Warrant::manageWarrant($data,$warrantId);
            }else{
                $this->responseData[] = CommonUtil::makeKeyValue('error','Something went wrong');
                $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;
            }

            $this->responseData =  CommonUtil::makeKeyValue('success_message','Warrant Updated');
            return $this->_respondApi();
        } catch (\Throwable $exception) {
            return APIResponder::respondInternalError();
        }

    }





}
