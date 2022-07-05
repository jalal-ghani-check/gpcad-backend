<?php

namespace App\Models\Chat;

use App\Models\MongoAbstractDataCollection;

class Channel extends MongoAbstractDataCollection
{

  const CHANNEL_TYPE_P2P = 'P2P';
  /**
   * @var string
   */
  private $_context;

  function __construct()
  {
    $this->_context = 'chat_channels';
    parent::__construct($this->_context);
  }

  public static function getChannel ($filters, $limit = 500, $options = null)
  {
    return (new Channel())->getByQuery($filters, $limit, $options);
  }

  public static function createChannel($data)
  {
    return (new Channel())->create($data);
  }
  public static function updateChannel($filters, $data)
  {
    return (new Channel())->updateBYQuery($filters, $data);
  }
}
