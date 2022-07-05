<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class CriminalRecord extends BaseModel
{
    use SoftDeletes;

    protected $primaryKey = 'record_id';

    protected $fillable = [
      'police_report_id',
      'profile_record_id',
      'law_id',
      'law_title',
      'crime_type',
      'fine_amount',
      'jail_time',
      'created_by',
      'updated_by'
    ];


  public static function manageCriminalRecord($data, $recordId = null)
  {
    if (!(isset($data['updated_by']))) {
      return null;
    }
    $dataObject = self::getRecordById($recordId);
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

  public static function getRecordById($recordId)
  {
    return self::where(array('record_id' => $recordId))->first();
  }

  public static function getCriminalRecordsByProfileRecordId($profileRecordId)
  {
    return self::getAll()->where('profile_record_id', $profileRecordId)->all();
  }

  public static function getCriminalRecordsByProfileRecordIdAndReportId($profileRecordId, $reportId)
  {
    return self::getAll()->where('profile_record_id', $profileRecordId)->where('police_report_id', $reportId)->all();
  }

  public static function getCriminalRecordsByProfileRecordIdAndCrimeType($profileRecordId, $type)
  {
    return self::getAll()->where('profile_record_id', $profileRecordId)->where('crime_type', $type)->all();
  }

  public static function getCriminalRecordsByPoliceReportId($reportId)
  {
    return self::getAll()->where('police_report_id', $reportId)->all();
  }

  public static function getAll ()
  {
    return self::all();
  }
}
