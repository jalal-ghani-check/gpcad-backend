<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class ProfileRecord extends BaseModel
{

  use SoftDeletes;

  const PROFILE_GENDER_MALE = 'male';
  const PROFILE_GENDER_FEMALE = 'female';

  protected $primaryKey = 'profile_id';
  protected $fillable = [
    'full_name',
    'designation',
    'gender',
    'dob',
    'address',
    'citizen_id',
    'finger_print',
    'dna_code',
    'points',
    'is_driver_license_valid',
    'is_weapon_license_valid',
    'is_pilot_license_valid',
    'is_hunting_license_valid',
    'is_fishing_license_valid',
    'created_by',
    'updated_by'
  ];

  public static function manageProfileRecord($data, $profileId = null)
  {
    if (!(isset($data['updated_by']))) {
      return null;
    }
    $dataObject = self::getProfileRecordByProfileId($profileId);
    $columns = self::getTableColumns(self::getTableName());
    if (!$dataObject) {
      $dataObject = new self();
      $dataObject->created_by = $data['updated_by'];
    }
    foreach ($data as $key => $d) {
      if (in_array($key, $columns)) {
        $dataObject->$key = $d;
      }
    }
    $dataObject->save();
    return $dataObject;
  }


  public static function getProfileRecordByProfileId($profileId)
  {
    return self::where(array('profile_id' => $profileId))->first();
  }

    public static function getProfileByCitizenId($citizenId)
    {
        return self::where(array('citizen_id' => $citizenId))->first();
    }

  public static function getAll()
  {
    return self::all();
  }

    public static function verifyCitizenIdExist($citizenId) {
        $checkUserExists = self::getProfileByCitizenId($citizenId);
        if ($checkUserExists) {
            return true;
        }
        return false;
    }

    public static function getAllProfilesComplete(){
        $query = DB::table('profile_records as p')
            ->join('users as u', 'p.created_by', '=', 'u.user_id')
            ->whereNull('p.deleted_at')
            ->whereNull('u.deleted_at')
            ->orderBy('p.created_at','desc')
            ->select( 'p.*','u.full_name as user_full_name', 'u.user_id');

        return $query->get();
    }


}
