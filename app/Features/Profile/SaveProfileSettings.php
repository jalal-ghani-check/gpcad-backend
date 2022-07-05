<?php


namespace App\Features\Profile;
use App\Common\CommonUtil;
use App\Contracts\HTTPStatusCode;
use App\Features\BaseApi;
use App\Models\ProfileRecord;
use App\Traits\APIResponder;
use Illuminate\Http\Request;

class SaveProfileSettings extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request ){

        try {
            $this->request = $request;
            $this->_decryptToken();
            $requestData = $request->all();
            $profileId = CommonUtil::decrypt(CommonUtil::fetch($requestData,'profile_id'));

            if($profileId) {
                $data = [
                    CommonUtil::fetch($requestData,'key') => CommonUtil::fetch($requestData,'value'),
                    'updated_by' => $this->userId,
                ];
                ProfileRecord::manageProfileRecord($data,$profileId);
            }else{
                $this->responseData[] = CommonUtil::makeKeyValue('error','Something went wrong');
                $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;
            }

            $this->responseData =  CommonUtil::makeKeyValue('success_message','Saved Successfully');
            return $this->_respondApi();
        } catch (\Throwable $exception) {
            return APIResponder::respondInternalError();
        }

    }





}
