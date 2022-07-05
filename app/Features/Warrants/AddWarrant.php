<?php


namespace App\Features\Warrants;
use App\Common\AjaxResponse;
use App\Common\CommonUtil;
use App\Contracts\HTTPStatusCode;
use App\Features\BaseApi;
use App\Models\ProfileRecord;
use App\Models\UserRights;
use App\Models\Users\Role;
use App\Models\Users\User;
use App\Models\Warrant;
use App\Traits\APIResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AddWarrant extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request ){

        try {
            $this->request = $request;
            $this->_decryptToken();
            $userId = $this->userId;
            $requestData = $request->all();

            $response = $this->addNewWarrant($requestData, $userId);

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

    public function addNewWarrant($requestData, $updatedBy) {
        $response = new AjaxResponse();

        $userData = [
            'title' => CommonUtil::fetch($requestData,'title'),
            'user_id' => $updatedBy,
            'description' => CommonUtil::fetch($requestData,'description'),
            'profile_id' => CommonUtil::decrypt(CommonUtil::fetch($requestData,'profile_id')),
            'status' => Warrant::WARRANT_STATUS_PENDING,
            'updated_by' => $updatedBy
        ];

        Warrant::manageWarrant($userData);

        return $response;
    }

}

