<?php

namespace App\Models;

class LatestProfileSearch extends BaseModel
{

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'profile_id',
    'created_by',
    'updated_by'
  ];

  protected $primaryKey = 'profile_id';

  public static function manageProfileSearch($data, $profileId)
  {
    if (!(isset($data['updated_by']))) {
      return null;
    }
    $dataObject = self::getProfileSearch($profileId);
    $columns = self::getTableColumns(self::getTableName());
    if (!$dataObject) {
      $dataObject = new self();
      $dataObject->created_by = $data['updated_by'];
      $dataObject->created_at = $data['updated_at'];
    }
    foreach ($data as $key => $d) {
      if (in_array($key, $columns)) {
        $dataObject->$key = $d;
      }
    }
    $dataObject->save();
    return $dataObject;
  }

  public static function getProfileSearch($profileId)
  {
    return self::where(array('profile_id' => $profileId))->first();
  }

  public static function getAll ()
  {
    return self::all();
  }

  public static function fetchLatestProfiles ($count)
  {
    return self::orderBy('updated_at', 'desc')->take($count)->get();

  }

}
