<?php


namespace App\Features\Users;
use App\Common\CommonUtil;
use App\Contracts\HTTPStatusCode;
use App\Features\BaseApi;
use App\Models\ProfileRecord;
use App\Models\Users\User;
use App\Traits\APIResponder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DeleteUser extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request ){

        try {
            $this->request = $request;
            $this->_decryptToken();
            $requestData = $request->all();
            $userIdToDelete = CommonUtil::decrypt(CommonUtil::fetch($requestData,'user_id_to_delete'));

            if($userIdToDelete) {
                $data = [
                    'deleted_at' => Carbon::now(),
                    'updated_by' => $this->userId,
                ];
                User::manageUser($data,$userIdToDelete);
                $this->responseData =  CommonUtil::makeKeyValue('success_message','User deleted');
            }else{
                $this->responseData[] = CommonUtil::makeKeyValue('error','Something went wrong');
                $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;
            }

            return $this->_respondApi();
        } catch (\Throwable $exception) {
            return APIResponder::respondInternalError();
        }

    }





}
