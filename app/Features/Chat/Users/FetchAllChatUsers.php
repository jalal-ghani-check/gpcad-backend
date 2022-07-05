<?php


namespace App\Features\Chat\Users;


use App\Common\CommonUtil;
use App\Features\BaseApi;
use App\Features\Chat\Channels\AddNewChannelFeature;
use App\Models\UserPermissions;
use App\Models\Users\User;
use App\Traits\APIResponder;
use Illuminate\Http\Request;

class FetchAllChatUsers extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request ){

        try {
            $this->request = $request;
            $this->_decryptToken();
            $this->responseData = $this->prepareAllUsersData();

            return $this->_respondApi();
        } catch (\Throwable $exception) {
            return APIResponder::respondInternalError();
        }

    }

    public function prepareAllUsersData(){
        $users = User::getAllUsersWithRoleKey();
        $adminUsers = User::getUsersByRole(1);
        if($adminUsers) {
          $users = collect($adminUsers)->merge($users);
        }
        $ret = [];
        foreach ($users as $user){
          if ($this->userId != $user->user_id) {
            $channel = (new AddNewChannelFeature())->fetchChannel($this->userId, $user->user_id);
            $encUserId = CommonUtil::encrypt($user->user_id);
            $userData = [
              'user_id' => $user->user_id,
              'enc_user_id' => $encUserId,
              'full_name' => $user->full_name,
              'gender' => $user->gender,
              'username' => $user->username,
              'citizen_id' => $user->citizen_id,
              'profile_picture' => $user->profile_picture,
              'call_sign' => $user->call_sign,
              'created_by' => $user->created_by,
              'role_id' => $user->role_id,
              'enc_role_id' => CommonUtil::encrypt($user->role_id) ?? '-',
              'rank_id' => $user->rank_id ?? '-',
              'enc_rank_id' => CommonUtil::encrypt($user->rank_id) ?? '-',
              'department_id' => $user->department_id ?? '-',
              'enc_department_id' => CommonUtil::encrypt($user->department_id) ?? '-',
              'role_name' => $user->role_name ?? '-',
              'role_key' => $user->role_key ?? '-',
              'rank_name' => $user->rank_name ?? '-',
              'rank_key' => $user->rank_key ?? '-',
              'depart_name' => $user->depart_name ?? '-',
              'depart_key' => $user->depart_key ?? '-',
              'user_channel_id' => CommonUtil::fetch($channel, '_id')
            ];
            $rights = UserPermissions::loadUserRoleAndPermissionValues($user->user_id);
            $ret[$encUserId] = array_merge($userData, $rights);
          }

        }
        return $ret;


    }

}
