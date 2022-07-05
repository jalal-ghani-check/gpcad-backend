<?php
/**
 * Created by PhpStorm.
 * User: mmhaq
 * Date: 9/11/19
 * Time: 11:16 AM
 */

namespace App\Models\Chat;



use App\Common\CommonUtil;
use App\Models\MongoAbstractDataCollection;
use App\Traits\APIResponder;
use MongoDB\BSON\ObjectId;

class Message extends MongoAbstractDataCollection
{
    function __construct() {
        $this->_context = 'channel_messages';
        parent::__construct($this->_context);
    }

    public static function addMessage($data, $channel_id, $time){
        $props = [
            'text' => $data['text'],
            'channel_id' => new ObjectId($channel_id),
            'sender_external_id' => $data['sender_external_id'],
            'row_status' => ConstMessages::ROW_STATUS_ACTIVE,
            'created_at' => $time,
            'updated_at' => ''
        ];

        $result = (new Message())->create($props);
        return $result;
    }

    public static function getMessages($channel_id){
        try{
            $messages = (new Message()) -> getByQuery(['channel_id' => new ObjectId($channel_id)]);
            return $messages;
        } catch (\Throwable $exception) {
          return APIResponder::respondInternalError();
        }
    }

    public static function get_last_msg_info ($channel_id) {
        $msgInfo = null;

        $last_msg = self::get_message($channel_id)[0];
        if ($last_msg) {
            $msgInfo['msgText'] = $last_msg->text;
            $msgInfo['senderName'] = User::getUserByExternalId($last_msg->sender_external_id);
        }

        return $msgInfo;
    }

}
