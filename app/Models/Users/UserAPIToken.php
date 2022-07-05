<?php

namespace App\Models\Users;


use App\Common\CommonUtil;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use App\Models\BaseCache;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserAPIToken extends Model {

  protected $table = 'user_api_token';
  protected $protected = ['id'];
  protected $primaryKey = 'id';

  use SoftDeletes;


    public static function createToken($userId, $deviceIdentifier = 'web', $apiToken = null) {
        self::deleteUserToken($userId);

        $expiry_sec = config('common.api_user_token_expiry_ts', 900); // Default 15 Mins
        $expiryTime = time() + $expiry_sec;
        if (!$apiToken) {
            $apiToken = CommonUtil::encrypt(['user_id' => $userId, 'time' => $expiryTime]);
        }
        $data = new UserAPIToken();
        $data->user_id = $userId;
        $data->api_token = $apiToken;
        $data->device_identifier = $deviceIdentifier;
        $data->expire_at = date('Y-m-d H:i:s', $expiryTime);
        $data->created_at = date('Y-m-d H:i:s');
        $data->updated_at = date('Y-m-d H:i:s');
        $data->save();

        return $apiToken;
    }

    public static function isTokenExist($encApiToken, $deviceIdentifier = 'web')
    {
        return self::validateTokenFromDB($encApiToken, $deviceIdentifier);
    }


    public static function isUserTokenValid($userTokenEnc, $userId) {
//        if (config('common.by_pass_user_token_validation')) {
//            if ($userTokenEnc == 'n2-6tEhkj1lbenSPjlhWfm5snpWXop6PxI6Klm-Acldbj1hRabfFyYmRaHxsmWFqa2Bbw4tVWbF_pViLxFWLf6e7llyIdnV7aGFrZY1I2w') {
//                return true;
//            }
//        }

        $userToken = CommonUtil::decrypt($userTokenEnc);
        if ($userToken) {
            $time = CommonUtil::fetch($userToken, 'ts');
            $token = CommonUtil::fetch($userToken, 'token');
            $identifier = CommonUtil::fetch($userToken, 'identifier');
            if ($time && $token && $identifier) {
                $timeDiff = time() - $time; // time diff in sec
                if ($timeDiff < config('common.api_secret_key_validation_ts')) { // if Time difference is less than 30 sec than process the req
                    $tokenUserId = self::validateToken($token, $identifier);
                    if ($tokenUserId && $tokenUserId == $userId) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    public static function validateToken($apiToken, $deviceIdentifier) {
        $data = self::validateTokenFromDB($apiToken, $deviceIdentifier);
        if ($data) {
            $expiry = strtotime($data->expire_at);
            if ($expiry >= time()) {
                self::refreshToken($data->user_id);
                return $data->user_id;
            } else {
                self::deleteUserToken($data->user_id);
            }
        }
        return null;
    }

    public static function deleteUserToken($userId) {
        return self::where(array('user_id' => $userId))->update(['updated_at' => date('Y-m-d H:i:s'), 'deleted_at' => date('Y-m-d H:i:s')]);
    }

    public static function refreshToken($userId) {
        $expiry_sec = config('common.api_user_token_expiry_ts', 900); // Default 15 Mins
        $expiryTime = time() + $expiry_sec;
        $expire_at = date('Y-m-d H:i:s', $expiryTime);

        return self::refreshTokenFromDB($userId, $expire_at);
    }

    public static function hasValidSession($userId) {
        $data = UserAPIToken::getUserTokenByUserId($userId);
        if ($data) {
            $expiry = strtotime($data->expire_at);
            if ($expiry >= time()) {
                return true;
            } else {
                self::deleteUserToken($userId);
            }
        }

        return false;
    }
    public static function getUserTokenByUserId($userId){
        return self::where(array('user_id' => $userId))->orderBy('id', 'DESC')->first();
    }

    public static function getUserAPITokenByUserId($userId){
        $token = self::getUserTokenByUserId($userId);
        if($token){
            return $token->api_token;
        }
        return null;
    }


  public static function validateTokenFromDB($apiToken, $deviceIdentifier) {
    return self::where(array('api_token' => $apiToken, 'device_identifier' => $deviceIdentifier))->orderBy('id', 'DESC')->first();
  }

  public static function refreshTokenFromDB($userId, $expire_at) {
    return self::where(array('user_id' => $userId))->update(['updated_at' => date('Y-m-d H:i:s'), 'expire_at' => $expire_at]);
  }


}
