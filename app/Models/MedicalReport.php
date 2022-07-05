<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class MedicalReport extends BaseModel
{
  use SoftDeletes;

  const REPORT_TYPE_MEDICAL = 'medical_report';
  protected $primaryKey = 'report_id';

  const ALLERGY_TYPE_LATEX = 'Latex';
  const ALLERGY_TYPE_IODINE = 'Iodine';
  const ALLERGY_TYPE_BROMINE = 'Bromine';
  const ALLERGY_TYPE_OTHER = 'Other';

  protected $fillable = [
    'profile_id',
    'citizen_id',
    'report_title',
    'problem_started_at',
    'problem_description',
    'problem_cause',
    'problem_cause_detail',
    'medical_history',
    'surgery_name',
    'surgery_year',
    'surgery_complication',
    'surgery_description',
    'medication_name',
    'medication_done',
    'medication_reason',
    'medication_description',
    'allergy_type',
    'allergies_details',
    'personal_views',
    'created_by',
    'updated_by'
  ];

  public static function manageMedicalReport($data, $reportId = null)
  {
    if (!(isset($data['updated_by']))) {
      return null;
    }
    $dataObject = self::getMedicalReportByReportId($reportId);

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

  public static function getMedicalReportByReportId($reportId)
  {
    return self::where(array('report_id' => $reportId))->first();
  }

  public static function getMedicalReportsProfileId($profileId)
  {
    return self::getAll()->where('profile_id', $profileId)->all();
  }

  public static function getAll ()
  {
    return self::all();
  }
}
