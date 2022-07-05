<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class House extends BaseModel
{
  use SoftDeletes;

  protected $primaryKey = 'house_id';

  protected $fillable = [
    'house_name',
    'image',
    'linked_profile_id',
    'price',
    'created_by',
    'updated_by'
  ];

  public static function manageHouse($data, $houseId = null)
  {
    if (!(isset($data['updated_by']))) {
      return null;
    }
    $dataObject = self::getHouseByHouseId($houseId);
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

  public static function getHouseByHouseId($houseId)
  {
    return self::where(array('house_id' => $houseId))->first();
  }

  public static function getHousesByEstateAgentId($agentId)
  {
    return self::getAll()->where('updated_by', $agentId)->all();
  }

  public static function getAllLinkedHousesByEstateAgentId($agentId)
  {
    return self::getAll()->whereNotNull('linked_profile_id')->where('updated_by', $agentId)->all();
  }

  public static function getAllLinkedHousesByProfileId($profileId)
  {
    return self::getAll()->where('linked_profile_id', $profileId)->all();
  }

  public static function getAll ()
  {
    return self::all();
  }
}
