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

class FetchWarrants extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request ){

        try {
            $this->request = $request;
            $this->_decryptToken();
            $this->responseData = $this->prepareAllWarrantsData();

            return $this->_respondApi();
        } catch (\Throwable $exception) {
            return APIResponder::respondInternalError();
        }

    }

    public function prepareAllWarrantsData(){
        $warrants = Warrant::getAllWarrentsWithUserAndProfileName();
        $ret = [];
        foreach ($warrants as $warrant){
            $ret[] = [
                'warrant_id' => $warrant->warrant_id,
                'enc_warrant_id' => CommonUtil::encrypt($warrant->warrant_id),
                'user_id' => $warrant->user_id,
                'user_full_name' => $warrant->user_full_name,
                'profile_full_name' => $warrant->profile_full_name,
                'enc_user_id' => CommonUtil::encrypt($warrant->user_id),
                'title' => $warrant->title,
                'description' => $warrant->description,
                'profile_id' => $warrant->profile_id,
                'enc_profile_id' => CommonUtil::encrypt($warrant->profile_id),
                'status_updated_by' => $warrant->status_updated_by,
                'status' => $warrant->status,
                'created_by' => $warrant->created_by,
                'updated_by' => $warrant->updated_by,
                'created_at' => date("d/m/y H:i", strtotime($warrant->created_at))

            ];

        }
        return $ret;


    }

}
