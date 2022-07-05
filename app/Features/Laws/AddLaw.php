<?php


namespace App\Features\Laws;
use App\Common\AjaxResponse;
use App\Common\CommonUtil;
use App\Contracts\HTTPStatusCode;
use App\Features\BaseApi;
use App\Models\Law;
use App\Models\ProfileRecord;
use App\Models\Users\Role;
use App\Models\Users\User;
use App\Traits\APIResponder;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AddLaw extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request ){

        try {
            $this->request = $request;
            $this->_decryptToken();
            $userId = $this->userId;
            $requestData = $request->all();

            $response = $this->saveLaw($requestData, $userId);

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

    public function saveLaw($requestData, $updatedBy) {
        $response = new AjaxResponse();
        $lawId = CommonUtil::fetch($requestData, 'law_id');
        $lawId = CommonUtil::decrypt($lawId);
        $lawName = CommonUtil::fetch($requestData,'name');
        $lawCode = CommonUtil::fetch($requestData,'law_code');

        if(!$lawId) {
          $isNameDuplicate = Law::checkIfColumnValueExists('name', $lawName);
          if (!$isNameDuplicate) {
            $isLawCodeDuplicate = Law::checkIfColumnValueExists('law_code', $lawCode);
          }


          if ($isNameDuplicate || $isLawCodeDuplicate) {
            $subStr = ($isNameDuplicate) ? 'name' : 'code';
            $response->status = false;
            $response->errors = CommonUtil::makeKeyValue('already_exist', 'Law ' . $subStr . ' already exists');
            return $response;
          }
        }

        $userData = [
            'name' => CommonUtil::fetch($requestData,'name'),
            'law_code' => CommonUtil::fetch($requestData,'law_code'),
            'crime_type' => CommonUtil::fetch($requestData,'crime_type'),
            'points' => CommonUtil::fetch($requestData,'points'),
            'fine_amount' => CommonUtil::fetch($requestData,'fine_amount'),
            'jail_time' => CommonUtil::fetch($requestData,'jail_time'),
            'description' => CommonUtil::fetch($requestData,'description'),

            'updated_by' => $updatedBy,
            'created_by' => $updatedBy,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];



        Law::manageLaw($userData, $lawId);

        return $response;
    }





}
