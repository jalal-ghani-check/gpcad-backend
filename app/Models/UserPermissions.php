<?php

namespace App\Models;

use App\Common\CommonUtil;
use App\Models\BaseModel;
use App\Models\Users\Role;
use App\Models\Users\User;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserPermissions extends BaseModel {

    use SoftDeletes;
    protected $protected = ['id'];
    protected $primaryKey = 'id';
    protected $table = 'user_permissions';
    protected $dates = ['created_at', 'updated_at'];

    const IS_ALLOWED_TO_APPROVE_WARRANTS = ["module_name" =>  "Is allowed to approve warrants", "module_name_key" => 'is_allowed_to_approve_warrants'];
    const IS_ALLOWED_TO_CREATE_LAWS = ["module_name" =>  "Is allowed to create laws", "module_name_key" => 'is_allowed_to_create_laws'];
    const IS_ALLOWED_TO_CREATE_PROFILE = ["module_name" =>  "Is allowed to create profile", "module_name_key" => 'is_allowed_to_create_profile'];
    const IS_ALLOWED_TO_CREATE_POLICE_REPORTS = ["module_name" =>  "Is allowed to create police reports", "module_name_key" => 'is_allowed_to_create_police_reports'];
    const IS_ALLOWED_TO_CREATE_MEDICAL_REPORTS = ["module_name" =>  "Is allowed to create medical reports", "module_name_key" => 'is_allowed_to_create_medical_reports'];
    const IS_ALLOWED_TO_CREATE_WARRANTS = ["module_name" =>  "Is allowed to create warrants", "module_name_key" => 'is_allowed_to_create_warrants'];
    const IS_ALLOWED_TO_DELETE_LAWS = ["module_name" =>  "Is allowed to delete laws", "module_name_key" => 'is_allowed_to_delete_laws'];
    const IS_ALLOWED_TO_DELETE_POLICE_REPORTS = ["module_name" =>  "Is allowed to delete police reports", "module_name_key" => 'is_allowed_to_delete_police_reports'];
    const IS_ALLOWED_TO_DELETE_MEDICAL_REPORTS = ["module_name" =>  "Is allowed to delete medical reports", "module_name_key" => 'is_allowed_to_delete_medical_reports'];
    const IS_ALLOWED_TO_DELETE_WARRANTS = ["module_name" =>  "Is allowed to delete warrants", "module_name_key" => 'is_allowed_to_delete_warrants'];
    const IS_ALLOWED_TO_EDIT_LAWS = ["module_name" =>  "Is allowed to edit laws", "module_name_key" => 'is_allowed_to_edit_laws'];
    const IS_ALLOWED_TO_EDIT_PROFILE = ["module_name" =>  "Is allowed to edit profile", "module_name_key" => 'is_allowed_to_edit_profile'];
    const IS_ALLOWED_TO_EDIT_POLICE_REPORTS = ["module_name" =>  "Is allowed to edit police reports", "module_name_key" => 'is_allowed_to_edit_police_reports'];
    const IS_ALLOWED_TO_EDIT_MEDICAL_REPORTS = ["module_name" =>  "Is allowed to edit medical reports", "module_name_key" => 'is_allowed_to_edit_medical_reports'];
    const IS_ALLOWED_TO_EDIT_WARRANTS = ["module_name" =>  "Is allowed to edit warrants", "module_name_key" => 'is_allowed_to_edit_warrants'];
    const IS_ALLOWED_TO_EXPUNGE_RECORDS = ["module_name" =>  "Is allowed to expunge records", "module_name_key" => 'is_allowed_to_expunge_records'];
    const IS_ALLOWED_TO_HIGH_COMMANDS = ["module_name" =>  "Is allowed to high commands", "module_name_key" => 'is_allowed_to_high_commands'];
    const IS_ALLOWED_TO_SERVE_WARRANTS = ["module_name" =>  "Is allowed to serve warrants", "module_name_key" => 'is_allowed_to_serve_warrants'];
    const IS_ALLOWED_TO_VIEW_BAILS = ["module_name" =>  "Is allowed to view bails", "module_name_key" => 'is_allowed_to_view_bails'];
    const IS_ALLOWED_TO_VIEW_CHARGES = ["module_name" =>  "Is allowed to view charges", "module_name_key" => 'is_allowed_to_view_charges'];
    const IS_ALLOWED_TO_VIEW_LAWS = ["module_name" =>  "Is allowed to view laws", "module_name_key" => 'is_allowed_to_view_laws'];
    const IS_ALLOWED_TO_VIEW_PROFILE = ["module_name" =>  "Is allowed to view profile", "module_name_key" => 'is_allowed_to_view_profile'];
    const IS_ALLOWED_TO_VIEW_POLICE_REPORTS = ["module_name" =>  "Is allowed to view police reports", "module_name_key" => 'is_allowed_to_view_police_reports'];
    const IS_ALLOWED_TO_VIEW_MEDICAL_REPORTS = ["module_name" =>  "Is allowed to view medical reports", "module_name_key" => 'is_allowed_to_view_medical_reports'];
    const IS_ALLOWED_TO_VIEW_WARRANTS = ["module_name" =>  "Is allowed to view warrants", "module_name_key" => 'is_allowed_to_view_warrants'];


    public static function manageUserPermissions($data, $module_name_key) {
        if (!(isset($data['updated_by']))) {
            return null;
        }
        $data_object = self::getPermissionByModuleNameKey($module_name_key);
        $columns = self::getTableColumns(self::getTableName());
        if (!$data_object) {
            $data_object = new UserPermissions();
            $data_object->created_by = $data['updated_by'];
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

    public static function getAllUserPermissions(){

        $data = self::getAllUserPermissionsFromDB();
        $ret = array();
        if ($data) {
            foreach ($data as $d) {
                $ret[$d->module_name_key] = $d;
            }
        }
        return $ret;
    }

    public static function loadUserRoleAndPermissionValues($user_id){
        $user = User::getUser($user_id);
        $roleId = CommonUtil::fetchFromObject($user,'role_id');
        //Getting distinct user permissions
        $all_permissions = self::getAllUserPermissions();
        //Getting user rights
        $response_ret = [];
        $user_rights = UserRights::getUserRightsByUserId($user_id);
        self::updateUserRightsRowForUserId($all_permissions, $user_rights,  $response_ret,$roleId);

        return $response_ret;
    }


    public static function updateUserRightsRowForUserId($all_permissions, $user_rights,  &$response_ret,$roleId){
        foreach($all_permissions as $key => $permission_row_data){
            if(CommonUtil::fetchFromObject($user_rights[$key] , "module_permission") || $roleId == Role::ROLE_ID_ADMIN){
                $response_ret[$key] = 1;
            } else {
                $response_ret[$key] = 0;
            }
        }
        return $response_ret;
    }

    public static function getPermissionByModuleNameKey($module_name_key) {
        $allUserPermissions = self::getAllUserPermissions();
        return CommonUtil::fetch($allUserPermissions, $module_name_key, null);
    }


  public static function getAllUserPermissionsFromDB(){
      return self::all();
  }


}
