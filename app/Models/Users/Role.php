<?php

namespace App\Models\Users;


use App\Common\CommonUtil;
use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends BaseModel
{

    use SoftDeletes;

    protected $protected = ['role_id'];
    protected $primaryKey = 'role_id';
    const ROLE_KEY_DOJ = User::USER_TYPE_DOJ;
    const ROLE_KEY_ADMIN = User::USER_TYPE_ADMIN;
    const ROLE_KEY_DOC = User::USER_TYPE_DOC;
    const ROLE_KEY_PD_HIGH_COMMAND = User::USER_TYPE_TEAM_PD_HIGH_COMMAND;
    const ROLE_KEY_POLICE = User::USER_TYPE_POLICE;
    const ROLE_KEY_JUDGE = User::USER_TYPE_JUDGE;
    const ROLE_KEY_EMS = User::USER_TYPE_EMS;
    const ROLE_KEY_ATTORNEY = User::USER_TYPE_ATTORNEY;


    const ROLE_ID_DOJ = 2;
    const ROLE_ID_ADMIN = 1;
    const ROLE_ID_DOC = 3;
    const ROLE_ID_PD_HIGH_COMMAND = 4;
    const ROLE_ID_POLICE = 5;
    const ROLE_ID_JUDGE = 6;
    const ROLE_ID_EMS = 7;
    const ROLE_ID_ATTORNEY = 8;

    const NON_ADMIN_ROLES_ARR = [
        self::ROLE_ID_DOJ,
        self::ROLE_ID_DOC,
        self::ROLE_ID_PD_HIGH_COMMAND,
        self::ROLE_ID_POLICE,
        self::ROLE_ID_JUDGE,
        self::ROLE_ID_EMS,
        self::ROLE_ID_ATTORNEY,
    ];


    public static function manageRoles($data, $role_key) {
        $data_object = self::getRoleByRoleKey($role_key);
        $columns = Role::getTableColumns(Role::getTableName());
        if (!$data_object) {
            $data_object = new Role();
        }
        foreach ($data as $key => $d) {
            if (in_array($key, $columns)) {
                $data_object->$key = $d;
            }
        }
        $data_object->save();
        return $data_object;
    }

    public static function getRoleByRoleKey($roleKey, $idOnly=false) {
        $rawRoles = self::getAll();
        if ($rawRoles) {
            foreach ($rawRoles as $role) {
                if ($role->role_key == $roleKey) {
                    if ($idOnly){
                        return $role->role_id;
                    }else{
                        return $role;
                    }
                }
            }
        }
        return null;
    }

    public static function getAll()
    {
        return self::all();
    }

    public static function getRoleById($roleId)
    {
        return self::where('role_id', $roleId)->first();
    }

    public static function getNonAdminRolesKeyValues() {
        $roles = self::getAll();
        $ret = [];
        foreach ($roles as $role) {
            if($role->role_id != self::ROLE_ID_ADMIN){
                $ret [] = [
                    'key' => CommonUtil::encrypt($role->role_id),
                    'value' => $role->role_name,
                ];
            }
        }
        return $ret;
    }

}
