<?php

namespace App\Models;

use App\Model\ConstMessages;

abstract class MongoAbstractDataCollection
{
  protected $_name;
  protected $_dbName;
  protected $_connection;
  protected $_db;
  protected $_collection;

  public static function getNameFromParts($parts)
  {
    $parts = array_map(function ($i) {
      return trim(strtolower($i));
    }, $parts);

    return implode('-', array_merge(['data'], $parts));
  }

  public function __construct($name, $dbName = null)
  {
    if (!$dbName) {
      $dbName = config('common.mongodb.db_name');
    }
    $this->_name = is_array($name) ? self::getNameFromParts($name) : $name;
    $this->_dbName = $dbName;
    $this->_init();
  }

  protected function getName()
  {
    return $this->_name;
  }

  protected function getDBName()
  {
    return $this->_dbName;
  }

  protected function getStore()
  {
    return $this->_db . '.' . $this->_collection;
  }

  public function addList($documents)
  {
    $bulk = new \MongoDB\Driver\BulkWrite();
    foreach ($documents as $document) {
      if (!isset($document['status'])) {
        $document['status'] = 'active';
      }
      $bulk->insert($document);
    }

    return $this->_connection->executeBulkWrite($this->getStore(), $bulk);
  }

  public function manageData($document)
  {
    if (isset($document['_id']) && $document['_id']) {
      $this->updateBYId($document['_id'], $document);
    } else {
      unset($document['_id']);
      $document['created_ts'] = date('Y-m-d H:i:s');
      $this->create($document);
    }
  }

  public function create(&$document)
  {
    try {
      if (isset($document['_id']) && $document['_id']) {
        $document['_id'] = new \MongoDB\BSON\ObjectID($document['_id']);
      } else {
        $document['_id'] = new \MongoDB\BSON\ObjectID();
      }
      if (!isset($document['status'])) {
        $document['status'] = 'active';
      }
      $bulk = new \MongoDB\Driver\BulkWrite();
      $bulk->insert($document);
      return $this->_connection->executeBulkWrite($this->getStore(), $bulk);
    } catch (Exception $ex) {
    }
  }

  public function updateBYId($id, $document)
  {
    unset($document['_id']);
    $filter = ['_id' => new \MongoDB\BSON\ObjectID($id)];
    $bulk = new \MongoDB\Driver\BulkWrite();
    $bulk->update($filter, ['$set' => $document]);

    return $this->_connection->executeBulkWrite($this->getStore(), $bulk);
  }

  public static function filterDataSet($filters, $filteredData)
  {
    $index = 0;
    if (!$filters) {
      return $filteredData;
    }
    $ret = [];
    foreach ($filteredData as $dataIndex => $data) {
      foreach ($filters as $filterIndex => $filter) {
        $filterIndex = trim($filterIndex);
        $filterValue = trim(strtolower($filter['value']));
        if (!is_null($filterValue) && !empty($filterValue)) {
          $v = strtolower(trim($data[$filterIndex]));
          if ($filter['type'] == 'select') {
            $has = $v == $filterValue;
          } else {
            $has = strpos($v, $filterValue);
          }
          if ($has === false || empty($v)) {
            unset($filteredData[$dataIndex]);
          }
        }
      }
    }
    if ($filteredData) {
      foreach ($filteredData as $data) {
        $ret[] = $data;
      }
    }

    return $ret;
  }

  public function updateBYQuery($filter, $document)
  {
    if (isset($filter['_id'])) {
      return $this->updateBYId($filter['_id'], $document);
    }
    unset($document['_id']);
    $bulk = new \MongoDB\Driver\BulkWrite();
    $bulk->update($filter, ['$set' => $document]);

    return $this->_connection->executeBulkWrite($this->getStore(), $bulk);
  }

  public function deleteById($id, $status = 'deleted')
  {
    $filter = ['_id' => new \MongoDB\BSON\ObjectID($id)];
    $bulk = new \MongoDB\Driver\BulkWrite();
    $bulk->update($filter, ['$set' => ['status' => $status, 'updated_ts' => date('Y-m-d H:i:s')]]);

    return $this->_connection->executeBulkWrite($this->getStore(), $bulk);
  }

  public function deleteByQuery($filter, $status = 'deleted')
  {
    if (isset($filter['_id'])) {
      return $this->deleteById($filter['_id']);
    }
    $bulk = new \MongoDB\Driver\BulkWrite();
    $bulk->update($filter, ['$set' => ['status' => $status, 'updated_ts' => date('Y-m-d H:i:s')]]);

    return $this->_connection->executeBulkWrite($this->getStore(), $bulk);
  }

  public function getById($id, $status = 'active')
  {
    $filter = ['_id' => new \MongoDB\BSON\ObjectID($id)];
    if ($status) {
      $filter['status'] = $status;
    }
    $query = new \MongoDB\Driver\Query($filter);
    $cursor = $this->_connection->executeQuery($this->getStore(), $query);
    $documentObj = current($cursor->toArray());
    $document = json_decode(json_encode($documentObj), true);
    if ($document && isset($documentObj->_id)) {
      $document['_id'] = $documentObj->_id->__toString();
    }

    return $document;
  }

  public function getByQuery($filters = [], $limit = 500, $sort=false)
  {
    if (!isset($filters['status'])) {
      $filters['status'] = 'active';
    }
    $options = [
      'allowPartialResults' => true,
      'limit' => $limit,];
    if ($sort){
      $options['sort'] = ['createdDate' => -1];
    }
    $query = new \MongoDB\Driver\Query($filters, $options);
    $cursor = $this->_connection->executeQuery($this->getStore(), $query);
    $documentsArryObj = $cursor->toArray();
    $ret = [];
    if ($documentsArryObj) {
      foreach ($documentsArryObj as $documentObj) {
        $document = json_decode(json_encode($documentObj), true);
        $document['_id'] = $documentObj->_id->__toString();
        $ret[$document['_id']] = $document;
      }
    }

    return $ret;
  }

  public function getByQuerySingle($filters = [], $options = [])
  {
    if (isset($filters['_id'])) {
      return $this->getById((string)$filters['_id']);
    }
    if (!isset($filters['status'])) {
      $filters['status'] = 'active';
    }
    $query = new \MongoDB\Driver\Query($filters, $options);
    $cursor = $this->_connection->executeQuery($this->getStore(), $query);
    $documentObj = current($cursor->toArray());
    $document = json_decode(json_encode($documentObj), true);
    if ($document && isset($documentObj->_id)) {
      $document['_id'] = $documentObj->_id->__toString();
    }

    return $document;
  }

  public function fieldIncrement($filter, $field, $count) {
    $bulk = new \MongoDB\Driver\BulkWrite();
    $inc_array = [$field => $count];
    $bulk->update($filter, ['$inc' => $inc_array]);
    try {
      $res = $this->_connection->executeBulkWrite($this->getStore(), $bulk);
      if ($res) {
        return $res;
      }
      return false;
    } catch (\Exception $e) {
      throw new \Exception(ConstMessages::ERR_INTERNAL_MONGO_DB);
    }

  }

  protected function _init()
  {
    $tries = 0;
    while ($tries < 3) {
      $tries++;
      try {
        $connectionStr = config('common.mongodb.db_connection_str');
        $this->_connection = Connection::get($connectionStr);
        $this->_db = $this->_dbName;
        $this->_collection = $this->getName();

        return;
      } catch (\MongoDB\Driver\Exception\Exception $e) {
        if ($tries >= 3) {
          throw $e;
        } else {
          sleep(1);
        }
      }
    }
  }
}
