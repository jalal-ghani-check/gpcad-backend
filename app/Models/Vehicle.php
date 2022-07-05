<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Vehicle extends BaseModel
{

  use SoftDeletes;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'vehicle_id',
    'vehicle_name',
    'vehicle_model',
    'license_plate',
    'description',
    'owner_id',
    'created_by',
    'updated_by'
  ];

  protected $primaryKey = 'vehicle_id';


  public static function manageVehicle($data, $vehicleId = null)
  {
    if (!(isset($data['updated_by']))) {
      return null;
    }
    $dataObject = self::getVehicle($vehicleId);
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

  public static function getVehicle($vehicleId)
  {
    return self::where(array('vehicle_id' => $vehicleId))->first();
  }

  public static function getAll ()
  {
    return self::all();
  }

}
