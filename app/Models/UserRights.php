<?php

namespace App\Models;

use App\Common\CommonUtil;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserRights extends BaseModel {

  use SoftDeletes;
  protected $protected = ['id'];
  protected $primaryKey = 'id';
  protected $table = 'user_rights';
  protected $dates = ['created_at', 'updated_at'];


    public static function bulkManageUserRights($update_data, $user_id){
        foreach($update_data as $update_data_row){
            $module_name_key = CommonUtil::fetch($update_data_row, "module_name_key");
            $data_update["module_permission"] = CommonUtil::fetch($update_data_row, "module_permission_value", 0);
            $data_update["updated_by"] = $user_id;
            self::manageUserRights($data_update, $user_id, $module_name_key);
        }
    }

    public static function manageUserRights($data, $user_id, $module_name_key) {
        if (!(isset($data['updated_by']))) {
            return null;
        }

        $data_object = self::getUserRightsByUserIdAndModuleNameKey($user_id, $module_name_key);

        $columns = UserRights::getTableColumns(UserRights::getTableName());
        if (!$data_object) {
            $data_object = new UserRights();
            $data_object->created_by = $data['updated_by'];
            $data_object->user_id = $user_id;
            $data_object->module_name_key = $module_name_key;
        }
        foreach ($data as $key => $d) {
            if (in_array($key, $columns)) {
                $data_object->$key = $d;
            }
        }
        $data_object->save();

        return $data_object;
    }


    public static function addUpdateUserPermissionByUserId($update_data, $user_id, $data){
        //Making all old permissions value to be "n"
        if(!empty($update_data)){
            self::bulkManageUserRights($update_data, $user_id);
        }
        $data_bulk_rights = [];
        if(isset($data)){
            foreach($data as $key => $value){
                $data_bulk_rights[$key]["module_name_key"] = $key;
                $data_bulk_rights[$key]["module_permission_value"] = $value;
            }
        }
        if(!empty($data_bulk_rights)){
            self::bulkManageUserRights($data_bulk_rights, $user_id);
        }
    }


    public static function manageUserPermissionsByUserId($user_id, $data){

        $update_data = self::getUserRightsByUserId($user_id);
        self::addUpdateUserPermissionByUserId($update_data, $user_id, $data);
    }


    public static function getUserRightsByUserId($userId){
        $data = UserRights::getUserRightsByUserIdFromDb($userId);
        $ret = array();
        if ($data) {
            foreach ($data as $d) {
                $ret[$d->module_name_key] = $d;
            }
        }
        return $ret;
    }

    public static function getUserRightsByUserIdAndModuleNameKey($user_id, $module_name_key) {
        $userRights = self::getUserRightsByUserId($user_id);
        return CommonUtil::fetch($userRights, $module_name_key, null);
    }



    public static function can($feature_key){
        //    return true;
        $roleKey = SessionManager::getLoggedInUserRoleKey();
        if ($roleKey == User::USER_TYPE_ADMIN || $feature_key==self::ALL_USERS) {
            return true;
        }
        $userRoleRights = SessionManager::getLoggedInUserPermissions();
        $user_right = CommonUtil::fetch($userRoleRights, $feature_key);
        if ($user_right) {
            return true;
        }
        return false;
    }

    public static function canMultipleCheckAll($feature_key_arr){
        //    return true;
        $roleKey = SessionManager::getLoggedInUserRoleKey();
        if ($roleKey == User::USER_TYPE_ADMIN) {
            return true;
        }
        $permission_granted = true;
        $userRoleRights = SessionManager::getLoggedInUserPermissions();
        foreach($feature_key_arr as $feature_key){
            $user_right = CommonUtil::fetch($userRoleRights, $feature_key);
            if(!$user_right){
                $permission_granted= false;
                break;
            }
        }
        return $permission_granted;
    }

    public static function canMultiple($feature_key_arr){
        //    return true;
        $roleKey = SessionManager::getLoggedInUserRoleKey();
        if ($roleKey == User::USER_TYPE_ADMIN) {
            return true;
        }
        $permission_granted = false;
        $userRoleRights = SessionManager::getLoggedInUserPermissions();
        foreach($feature_key_arr as $feature_key){
            $user_right = CommonUtil::fetch($userRoleRights, $feature_key);
            if($user_right){
                $permission_granted= true;
                break;
            }
        }
        return $permission_granted;
    }




  public static function getUserRightsByUserIdFromDb($userId){
      return self::where(array( 'user_id' => $userId))->get();
  }


}
