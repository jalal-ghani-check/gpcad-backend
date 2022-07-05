<?php

namespace App\Models\Users;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Common\CommonUtil;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class User extends BaseModel {

  const USER_TYPE_ADMIN = "admin";
  const USER_TYPE_DOJ = "doj";
  const USER_TYPE_DOC = "doc";
  const USER_TYPE_TEAM_PD_HIGH_COMMAND = "pd_high_command";
  const USER_TYPE_POLICE = "police";
  const USER_TYPE_JUDGE = "judge";
  const USER_TYPE_EMS = "ems";
  const USER_TYPE_ATTORNEY = "attorney";

  const USER_GENDER_MALE = "male";
  const USER_GENDER_FEMALE = "female";


  use SoftDeletes;

  protected $fillable = [
    'user_id', 'full_name', 'username', 'password', 'citizen_id', 'role_id', 'password_salt','role_id','rank_id','department_id','created_by', 'updated_by','profile_picture', 'call_sign'
  ];

  protected $hidden = [
    'password', 'remember_token',
  ];
    protected $primaryKey = 'user_id';
    protected $table = 'users';


    public static function manageUser($data, $user_id = null) {
        if (!(isset($data['updated_by']))) {
            return null;
        }
        $data_object = User::getUser($user_id);
        $columns = User::getTableColumns(User::getTableName());
        if (!$data_object) {
            $data_object = new User();
            $data_object->created_by = $data['updated_by'];
        }
        foreach ($data as $key => $d) {
            if (in_array($key, $columns)) {
                $data_object->$key = $d;
            }
        }

        $data_object->save();
        return $data_object;
    }

  public static function getUsersByRole($role_id) {
    return self::where('role_id', $role_id)->get();
  }

  public static function getTotalUserCountByRoleId($roleId) {
    return self::where('role_id', $roleId)->count();
  }

  public static function getUser($user_id) {
    return self::where(array('user_id' => $user_id))->first();
  }


  public static function getUserByUserName($userName) {
    return self::where('username', $userName)->first();
  }


  public static function getUsersByRoleIDs($roleIds) {
    $users = null;
    if ($roleIds) {
      $users = self::whereIn('role_id', $roleIds)->orderBy('created_at', 'DESC')->get();
    }

    return $users;
  }


    public static function verifyUserNameExist($userName) {
        $checkUserExists = self::getUserByUserName($userName);
        if ($checkUserExists) {
            return true;
        }
        return false;
    }

    public static function registerUser($data) {
        $checkUserExists = self::verifyUserNameExist($data['username']);
        if (!$checkUserExists) {

            $dataToSave = array(
                'full_name' => $data['full_name'],
                'username' => $data['username'],
                'citizen_id' => $data['citizen_id'],
                'profile_picture' => $data['profile_picture'],
                'gender' => $data['gender'],
                'rank_id' => $data['rank_id'],
                'department_id' => $data['department_id'],
                'call_sign' => $data['call_sign'],
                'role_id' => $data['role_id'],
                'password_salt' => '',
                'password' => (isset($data['password'])) ? Hash::make($data['password']) : Hash::make(rand(1, 1000000)),
                'updated_by' => 0,
            );
            return self::manageUser($dataToSave);
        }
    }

    public static function getAllUsersWithRoleKey(){
        $query = DB::table('users as u')
            ->join('roles as r', 'u.role_id', '=', 'r.role_id')
            ->join('ranks as k', 'u.rank_id', '=', 'k.id')
            ->join('departments as d', 'u.department_id', '=', 'd.id')
            ->whereNull('u.deleted_at')
            ->whereNull('r.deleted_at')
            ->whereNull('k.deleted_at')
            ->whereNull('d.deleted_at')
            ->where('u.role_id', '!=', 1)
            ->orderBy('u.created_at','desc')
            ->select( 'u.*','r.role_name','r.role_key','k.rank_name','k.rank_key','d.depart_name','d.depart_key');

        return $query->get();
    }


  public static function getUserWithRoleAndDepartKey($userId){
    $query = DB::table('users as u')
      ->join('roles as r', 'u.role_id', '=', 'r.role_id')
      ->join('ranks as k', 'u.rank_id', '=', 'k.id')
      ->join('departments as d', 'u.department_id', '=', 'd.id')
      ->whereNull('u.deleted_at')
      ->whereNull('r.deleted_at')
      ->whereNull('k.deleted_at')
      ->whereNull('d.deleted_at')
      ->where('u.user_id', '=', $userId)
      ->orderBy('u.created_at','desc')
      ->select( 'u.*','r.role_name','r.role_key','k.rank_name','k.rank_key','d.depart_name','d.depart_key');

    return $query->first();
  }



}
