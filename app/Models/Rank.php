<?php

namespace App\Models;

use App\Common\CommonUtil;

class Rank extends BaseModel
{

  const RANK_CADET = 'cadet';
  const RANK_OFFICER = 'officer';
  const RANK_SERGEANT = 'sergeant';
  const RANK_LIEUTENANT = 'lieutenant';
  const RANK_UNDERCHIEF = 'underchief';
  const RANK_CHIEF = 'chief';

    const RANKS_ARRAY = [
      self::RANK_CADET,
      self::RANK_OFFICER,
      self::RANK_SERGEANT,
      self::RANK_LIEUTENANT,
      self::RANK_UNDERCHIEF,
      self::RANK_CHIEF
    ];

  public static function getRankByRankKey($rankKey, $idOnly=false)
  {
    $rawRanks = self::getAll();
    if ($rawRanks) {
      foreach ($rawRanks as $rank) {
        if ($rank->rank_key == $rankKey) {
          if ($idOnly){
            return $rank->id;
          }else{
            return $rank;
          }
        }
      }
    }
    return null;
  }

  public static function getAll()
  {
    return self::all();
  }

    public static function getRankById($rankId)
    {
        return self::where('id', $rankId)->first();
    }


    public static function getAllRanksKeyValues() {
        $ranks = self::getAll();
        $ret = [];
        foreach ($ranks as $rank) {
            $ret [] = [
                'key' => CommonUtil::encrypt($rank->id),
                'value' => $rank->rank_name,
            ];
        }
        return $ret;
    }
}
