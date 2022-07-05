<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class PoliceReport extends BaseModel
{
  use SoftDeletes;

  protected $primaryKey = 'report_id';

  const REPORT_TYPE_POLICE = 'police_report';

  protected $fillable = [
    'report_id',
    'profile_id',
    'user_id',
    'case_number',
    'cid',
    'ref_case_number',
    'officers_involved',
    'shorts_fired',
    'gsr_test_result',
    'casing_recovered',
    'suspects_involved',
    'use_of_violence',
    'med_treatment',
    'legal_aid',
    'items_seized',
  ];

  public static function managePoliceReport($data, $reportId = null)
  {
    if (!(isset($data['updated_by']))) {
      return null;
    }
    $dataObject = self::getPoliceReportByReportId($reportId);

    if($reportId && !$dataObject){
      return null;
    }
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

  public static function getPoliceReportByReportId($reportId)
  {
    return self::where(array('report_id' => $reportId))->first();
  }

  public static function getPoliceReportsProfileId($profileId)
  {
    return self::getAll()->where('profile_id', $profileId)->all();
  }

  public static function getAll ()
  {
    return self::all();
  }
}
