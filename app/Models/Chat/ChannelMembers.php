<?php
/**
 * Created by PhpStorm.
 * User: mmhaq
 * Date: 9/11/19
 * Time: 6:32 PM
 */

namespace App\Models\Chat;


use App\Model\Repositories\ChannelMembersRepository;
use App\Model\Repositories\ChannelRepository;
use App\Models\MongoAbstractDataCollection;
use Cake\Http\Exception\BadRequestException;
use Cake\Http\Exception\ForbiddenException;
use Cake\Http\Exception\InternalErrorException;
use Cake\Http\Exception\NotFoundException;
use MongoDB\BSON\ObjectId;

class ChannelMembers extends MongoAbstractDataCollection
{
    function __construct() {
      $this->_context = 'channel-members';
      parent::__construct($this->_context);
    }

    public static function addChannelMember($data){
      return (new ChannelMembers())->create($data);
    }
    public static function getChannelMembers ($id) {
      return (new ChannelMembers())->getByQuery(['channel_id' => new ObjectId($id)]);
    }

    public static function updateChannelMembers ($channelId, $id, $data) {
      $filters = [
          'channel_id' => new ObjectId($channelId),
          'external_id' => (int)$id
      ];
      return (new ChannelMembers())->updateBYQuery($filters, $data);
    }

    public static function getChannelMembersByExternalId($external_id) {
        return (new ChannelMembers())->getByQuery(['external_id' => (int)$external_id]);
    }

    public static function getChannelMemberByUserId($channelId, $userId) {
      return (new ChannelMembers())->getByQuery(['channel_id' => new ObjectId($channelId), 'external_id' => (int)$userId]);
    }

    public static function updateUnreadCount($externalIds, $channel_id) {
        $idsArray = [];
        foreach ($externalIds as $externalId){
            $idsArray[] = ['external_id' => (int)$externalId];
        }
        if(count($idsArray)) {
          (new ChannelMembers())->fieldIncrement(['channel_id' => new ObjectId($channel_id), '$or' => [$idsArray[0]]], 'unread_msg_count', +1);
        }
    }
}
