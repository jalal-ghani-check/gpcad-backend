<?php

namespace App\Models;
use Illuminate\Support\Facades\DB;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

class Warrant extends BaseModel
{
    use SoftDeletes;
    protected $primaryKey = 'warrant_id';

    const WARRANT_STATUS_PENDING = 'pending';
    const WARRANT_STATUS_APPROVED = 'approved';
    const WARRANT_STATUS_REJECTED = 'rejected';
    const WARRANT_STATUS_SERVED = 'served';

    protected $fillable = [
      'title',
      'description',
      'filed_against',
      'status_updated_by',
      'status',
      'created_by',
      'updated_by',
    ];

  public static function manageWarrant($data, $warrantId = null) {
    if (!(isset($data['updated_by']))) {
      return null;
    }
    $dataObject = self::getWarrantByWarrantId($warrantId);
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

  public static function getWarrantByWarrantId($warrantId)
  {
    return self::where(array('warrant_id' => $warrantId))->first();
  }

public static function isTherePendingWarrants()
{
    $warrants =  self::where(array('status' => self::WARRANT_STATUS_PENDING))->get();
    if(count($warrants) > 0){
        return true;
    }else {
        return false;
    }
}

  public static function getAll ()
  {
    return self::all();
  }

    public static function getAllWarrentsWithUserAndProfileName(){
        $query = DB::table('warrants as w')
            ->join('users as u', 'w.user_id', '=', 'u.user_id')
            ->join('profile_records as p', 'w.profile_id', '=', 'p.profile_id')
            ->whereNull('u.deleted_at')
            ->whereNull('w.deleted_at')
            ->whereNull('p.deleted_at')
            ->select( 'w.*','u.full_name as user_full_name','p.full_name as profile_full_name');

        return $query->get();
    }

}
