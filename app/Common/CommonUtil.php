<?php


namespace App\Common;


use Carbon\Carbon;

class CommonUtil
{

  // used in encryption
  const APP_SECRET_KEY = 'las*(^3njo23rOI*#@$!@#)@(j12049j)*Y@$!';

  public static function fetchFromObject(&$object, $keys, $defaultValue = null, $trim = false)
  {
    if (!$object) {
      return $defaultValue;
    }
    $v = $defaultValue;

    if (is_array($keys) && count($keys) > 0) {
      $ref = $object;
      $found = false;

      foreach ($keys as $key) {
        if (isset($ref->$key)) {
          $found = true;
          $ref = $ref->$key;
        } else {
          $found = false;
          break;
        }
      }

      if ($found) {
        $v = $ref;
      }
    } else {
      $key = $keys;
      $v = isset($object->$key) ? $object->$key : $defaultValue;
    }

    return $v !== null ? $trim ? trim($v) : $v : null;
  }

  public static function fetch(&$container, $keys, $defaultValue = null, $trim = false)
  {
    $v = $defaultValue;

    if (is_array($keys) && count($keys) > 0) {
      $ref = $container;
      $found = false;

      foreach ($keys as $key) {
        if (isset($ref[$key])) {
          $found = true;
          $ref = $ref[$key];
        } else {
          $found = false;
          break;
        }
      }

      if ($found) {
        $v = $ref;
      }
    } else {
      $key = $keys;
      $v = isset($container[$key]) ? $container[$key] : $defaultValue;
    }

    return $v !== null ? $trim ? trim($v) : $v : null;
  }

  public static function updateJsonObject($jsonObject, $key, $value)
  {
    $decoded_object = json_decode($jsonObject);

    if (!$decoded_object) {
      $decoded_object[$key] = $value;
    } else {
      $decoded_object->$key = $value;
    }
    return json_encode($decoded_object);
  }

  public static function fetchFromJsonObject($jsonObject, $key, $defaultValue = null)
  {
    $decoded_object = json_decode($jsonObject);
    return self::fetchFromObject($decoded_object, $key, $defaultValue);
  }

  public static function encrypt($data, $secret_key = self::APP_SECRET_KEY)
  {
    $str = json_encode($data);
    $result = '';
    for ($i = 0; $i < strlen($str); $i++) {
      $char = substr($str, $i, 1);
      $keychar = substr($secret_key, ($i % strlen($secret_key)) - 1, 1);
      $char = chr(ord($char) + ord($keychar));
      $result .= $char;
    }

    return self::base64url_encode($result);
  }

  public static function decrypt($str, $secret_key = self::APP_SECRET_KEY)
  {
    if (!$str) {
      return $str;
    }

    $str = self::base64url_decode($str);

    $result = '';
    for ($i = 0; $i < strlen($str); $i++) {
      $char = substr($str, $i, 1);
      $keychar = substr($secret_key, ($i % strlen($secret_key)) - 1, 1);
      $char = chr(ord($char) - ord($keychar));
      $result .= $char;
    }

    return json_decode($result, true);
  }

  public static function base64url_encode($data)
  {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
  }

  public static function base64url_decode($data)
  {
    return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
  }
    public static function makeRequestResponseKeyValue($messages)
    {
        $ret = [];
        foreach ($messages as $key => $value) {
            $ret[] = self::makeKeyValue($key, $value[0]);
        }

        return $ret;
    }

    public static function makeKeyValue($key, $value)
    {
        return ['key' => $key, 'value' => $value];
    }
    //    accepts in Y-m-d
    public static function calculateAge($dob){
      $dob = Carbon::parse($dob);
      $now = Carbon::now();
      return $dob->diffInYears($now);
    }

  public static function generateRandomString(
    int $length = 64,
    string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ-_'
  ) : string
  {
    if ($length < 1) {
      throw new \RangeException("Length must be a positive integer");
    }
    $pieces = [];
    $max = mb_strlen($keyspace, '8bit') - 1;
    for ($i = 0; $i < $length; ++$i) {
      $pieces []= $keyspace[random_int(0, $max)];
    }
    return implode('', $pieces);
  }

}
