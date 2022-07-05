<?php
/**
 * Created by PhpStorm.
 * User: mmhaq
 * Date: 9/11/19
 * Time: 3:43 PM
 */

namespace App\Models\Chat;


use App\Models\MongoAbstractDataCollection;
use App\Traits\APIResponder;

class User extends MongoAbstractDataCollection
{
    function __construct() {
        $this->_context = 'users';
        parent::__construct($this->_context);
    }

    public static function addUser($data) {
      try {
        return (new User())->create($data);
      } catch (\Throwable $exception) {
        return APIResponder::respondInternalError();
      }
    }

    public static function getUser($filters) {
      try {
        return (new User())->getByQuery($filters);
      }
      catch (\Throwable $exception) {
        return APIResponder::respondInternalError();
      }
    }

    public static function updateUser($filters, $data){
      try{
        return (new User())->updateBYQuery($filters, $data);
      } catch (\Throwable $exception) {
        return APIResponder::respondInternalError();
      }
    }

  public static function getUserNameByExternalId ($id) {

    $user = self::getUserByExternalId($id)[0];
    return $user->name;
  }

  public static function getUserByExternalId($userExternalId)
  {
    try {
      return (new User())->getByQuery($userExternalId);
    } catch (\Throwable $exception) {
      return APIResponder::respondInternalError();
    }
  }

}
