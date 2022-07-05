<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;

class Law extends BaseModel
{

  use SoftDeletes;

  const CRIME_TYPE_INFRACTION = 'infraction';
  const CRIME_TYPE_MISDEMEANOR = 'misdemeanor';
  const CRIME_TYPE_FELONY = 'felony';

  const CRIME_TYPE_COLOR_CLASS = [
    self::CRIME_TYPE_INFRACTION => 'green',
    self::CRIME_TYPE_MISDEMEANOR => 'orange',
    self::CRIME_TYPE_FELONY => 'red'
  ];


  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'law_id',
    'name',
    'description',
    'crime_type',
    'fine_amount',
    'jail_time',
    'created_by',
    'updated_by'
  ];

  protected $primaryKey = 'law_id';

  public static function getCrimeTypes() {
      return
      [
          [
              'key' => self::CRIME_TYPE_INFRACTION,
              'value' => ucfirst(self::CRIME_TYPE_INFRACTION),
          ],
          [
              'key' => self::CRIME_TYPE_MISDEMEANOR,
              'value' => ucfirst(self::CRIME_TYPE_MISDEMEANOR),
          ],
          [
              'key' => self::CRIME_TYPE_FELONY,
              'value' => ucfirst(self::CRIME_TYPE_FELONY),
          ],
      ];
  }

  public static function manageLaw($data, $lawId = null)
  {
    if (!(isset($data['updated_by']))) {
      return null;
    }
    $dataObject = self::getLaw($lawId);
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

  public static function getLaw($lawId)
  {
    return self::where(array('law_id' => $lawId))->first();
  }

    public static function getLawsByPoliceReportId($reportId)
    {
        return self::where(array('police_report_id' => $reportId))->get();
    }

  public static function getAll ()
  {
    return self::all();
  }

  public static function checkIfColumnValueExists ($columnName, $columnValue): bool
  {
    $laws = self::getAll();
    return (bool) collect($laws)->where($columnName, $columnValue)->first();
  }

}
