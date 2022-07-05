<?php


namespace App\Features\Users;


use App\Common\CommonUtil;
use App\Contracts\HTTPStatusCode;
use App\Features\BaseApi;
use App\Models\Department;
use App\Models\Law;
use App\Models\Rank;
use App\Models\Users\Role;
use App\Models\Users\User;
use App\Traits\APIResponder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FetchMetaData extends BaseApi
{

    public function _handleAPI(Request $request ){

        try {
            $this->request = $request;

            $genders = [
                [
                    'key' => 'Male',
                    'value' => 'Male',
                ],
                [
                    'key' => 'Female',
                    'value' => 'Female',
                ],
            ];


            $ret = [
                'roles' => Role::getNonAdminRolesKeyValues(),
                'ranks' => Rank::getAllRanksKeyValues(),
                'departments' => Department::getAllDepartmentsKeyValues(),
                'genders' => $genders,
                'crimetypes' => Law::getCrimeTypes(),
            ];
            $this->responseData = $ret;
            return $this->_respondApi();
        } catch (\Throwable $exception) {
            return APIResponder::respondInternalError();
        }

    }

}
