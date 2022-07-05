<?php


namespace App\Features\LatestProfileSearch;
use App\Common\AjaxResponse;
use App\Common\CommonUtil;
use App\Contracts\HTTPStatusCode;
use App\Features\BaseApi;
use App\Models\LatestProfileSearch;
use App\Traits\APIResponder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ManageProfileSearch extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request, $profileId)
    {

        try {
            $this->request = $request;
            $profileId = CommonUtil::decrypt($profileId);
            $this->_decryptToken();

            $response = $this->manageProfileSearch($profileId, $this->userId);

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

    public function manageProfileSearch ($profileId, $userId) {
        $response = new AjaxResponse();

        $data = [
            'profile_id' => $profileId,
            'updated_by' => $userId,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ];

        $profile = LatestProfileSearch::manageProfileSearch($data, $profileId);

        $response->status = (bool) $profile;
        if($profile) {
          $response->data = $profile;
        } else {
          $response->errors = CommonUtil::makeRequestResponseKeyValue(['error' => 'Error: search not saved']);
        }

        return $response;
    }





}
