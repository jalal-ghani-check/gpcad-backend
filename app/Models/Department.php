<?php

namespace App\Models;

use App\Common\CommonUtil;

class Department extends BaseModel
{

  const DEPARTMENT_KEY_MEDICINE = 'medicine';
  const DEPARTMENT_KEY_JUDICIARY = 'judiciary';
  const DEPARTMENT_KEY_LEO = 'leo';
  const DEPARTMENT_KEY_REAL_ESTATE = 'real_estate';



  public static function manageDepartment($data, $id = null)
  {
    if (!(isset($data['updated_by']))) {
      return null;
    }
    $dataObject = self::getDepartmentById($id);
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


  public static function getDepartmentByRankKey($departKey, $idOnly=false)
  {
    $rawDeptartments = self::getAll();
    if ($rawDeptartments) {
      foreach ($rawDeptartments as $deptartment) {
        if ($deptartment->depart_key == $departKey) {
          if ($idOnly){
            return $deptartment->id;
          }else{
            return $deptartment;
          }
        }
      }
    }
    return null;
  }

  public static function getDepartmentById($id)
  {
    return self::where(array('id' => $id))->first();
  }

  public static function getAll()
  {
    return self::all();
  }


    public static function getAllDepartmentsKeyValues() {
        $departments = self::getAll();
        $ret = [];
        foreach ($departments as $department) {
            $ret [] = [
                'key' => CommonUtil::encrypt($department->id),
                'value' => $department->depart_name,
            ];
        }
        return $ret;
    }
}
