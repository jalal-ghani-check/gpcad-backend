<?php


namespace App\Features\Users;


use App\Common\CommonUtil;
use App\Features\BaseApi;
use App\Models\Department;
use App\Models\Rank;
use App\Models\UserPermissions;
use App\Models\Users\Role;
use App\Models\Users\User;
use App\Models\Users\UserAPIToken;
use App\Models\Warrant;
use App\Traits\APIResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class FetchUserData extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request ){

        try {
            $this->request = $request;
            $this->_decryptToken();

            $user = User::getUser($this->userId);

            $role = Role::getRoleById($user->role_id);
            $rank = Rank::getRankById($user->rank_id);
            $department = Department::getDepartmentById($user->department_id);

            $data_response = array(
                'user_id' => $user->user_id,
                'enc_user_id' => CommonUtil::encrypt($user->user_id),
                'gender' => $user->gender,
                'full_name' => $user->full_name,
                'username' => $user->username,
                'citizen_id' => $user->citizen_id,
                'profile_picture' => $user->profile_picture,
                'rank_id' => $user->rank_id,
                'department_id' => $user->department_id,
                'call_sign' => $user->call_sign,
                'role_id' => $user->role_id,
                'enc_rank_id' => CommonUtil::encrypt($user->rank_id),
                'enc_department_id' => CommonUtil::encrypt($user->department_id),
                'enc_role_id' => CommonUtil::encrypt($user->role_id),
                'is_admin' => $user->role_id == Role::ROLE_ID_ADMIN,
                'role_name' => CommonUtil::fetchFromObject($role,'role_name','-'),
                'rank_name' => CommonUtil::fetchFromObject($rank,'rank_name','-'),
                'department_name' => CommonUtil::fetchFromObject($department,'depart_name','-'),


            );
            $rights = UserPermissions::loadUserRoleAndPermissionValues($user->user_id);
            $rightKeyViewWarrant = UserPermissions::IS_ALLOWED_TO_VIEW_WARRANTS['module_name_key'];
            $isTherePendingWarrants = Warrant::isTherePendingWarrants() && CommonUtil::fetch($rights,$rightKeyViewWarrant);
            $data_response['isTherePendingWarrants'] = $isTherePendingWarrants;
            $this->responseData = array_merge($data_response,$rights);

            return $this->_respondApi();
        } catch (\Throwable $exception) {
            return APIResponder::respondInternalError();
        }

    }


}
