<?php
namespace App\Features\Chat\Channels;

use App\Common\CommonUtil;
use App\Features\BaseApi;
use App\Models\Chat\ChannelMembers;
use App\Models\Chat\ConstMessages;
use App\Models\Chat\Channel;
use App\Traits\APIResponder;
use Illuminate\Http\Request;
use \App\Models\Users\User;
use MongoDB\BSON\ObjectId;

class AddNewChannelFeature extends BaseApi
{

  public function _handleAPI(Request $request)
  {
    try{
      $this->request = $request;

    $userIdA = $this->request->sender_id;
    $userIdB = $this->request->receiver_id;

    $userIdA = CommonUtil::decrypt($userIdA);
    $userIdB = CommonUtil::decrypt($userIdB);

    $channel = $this->fetchChannel($userIdA, $userIdB);

    if($channel) {
      $this->responseData = $channel;
    } else {
      $this->responseData =  CommonUtil::makeKeyValue('error_message','Channel could not be created');
    }

      return $this->_respondApi();
    } catch (\Throwable $exception) {
      return APIResponder::respondInternalError();
    }
  }


  public function fetchChannel ($userIdA, $userIdB)
  {
    try {
      $channel = Channel::getChannel($this->prepareQueryFiltersToFetchChannel($userIdA, $userIdB));
    } catch (\Exception $e) {
      $channel = [];
    }

//    $usersB = User::getUser(['external_id' => (int)$userIdB]);
    $usersB = User::getUser($userIdB);

    if (count($channel) <= 0) {
      $timee = time();
//      $usersA = User::getUser(['external_id' => (int)$userIdA]);
      $usersA = User::getUser($userIdA);

      if ($userIdA && $usersB) {
        $newChannel = [
          "created_at" => $timee,
          "channel_name" => CommonUtil::fetch($usersB, 'full_name'),
          "channel_type" => Channel::CHANNEL_TYPE_P2P,
          "row_status" => ConstMessages::ROW_STATUS_ACTIVE,
          "created_by_external_id" => $userIdA,
          "last_msg_ts" => $timee,
          "group_id" => CommonUtil::fetch($usersB, 'user_id')
        ];

        $createdChannelId = Channel::createChannel($newChannel);
        if(!$createdChannelId) {
          $data = [
            'status' => 422,
            'message' => 'error!'
          ];
          $this->responseData = $data;
        }
        $memberA = [
          "channel_id" => $createdChannelId,
          "external_id" => CommonUtil::fetch($usersA, 'user_id'),
          "row_status" => ConstMessages::ROW_STATUS_ACTIVE,
          "created_at" => $timee,
          "mute_channel" => ConstMessages::DISABLED,
          "unread_msg_count" => 0
        ];

        ChannelMembers::addChannelMember($memberA);

        $memberB = [
          "channel_id" => $createdChannelId,
          "external_id" => CommonUtil::fetch($usersB, 'user_id'),
          "row_status" => ConstMessages::ROW_STATUS_ACTIVE,
          "created_at" => $timee,
          "mute_channel" => ConstMessages::DISABLED,
          "unread_msg_count" => 0
        ];
        ChannelMembers::addChannelMember($memberB);

        $newChannel = Channel::getChannel($this->prepareQueryFiltersToFetchChannel($userIdA, $userIdB));

        $channel = head($newChannel);

        $channel['channel_name'] = CommonUtil::fetch($usersB, 'full_name');
        $channel['avatar'] = CommonUtil::fetch($usersB, 'profile_picture');
      }
    } else {
      $channel = head($channel);
      Channel::updateChannel(['_id' => new ObjectId($channel['_id'])], ['last_msg_ts' => time()]);
      $channel['channel_name'] = CommonUtil::fetch($usersB, 'full_name');
      $channel['avatar'] = CommonUtil::fetch($usersB, 'profile_picture');
    }
    return $channel;
  }


  private function prepareQueryFiltersToFetchChannel($senderId, $receiverId)
  {
    try {
      return [
        '$or' => [
          [
            'channel_type' => Channel::CHANNEL_TYPE_P2P,
            'created_by_external_id' => (int)$senderId,
            'group_id' => (int)$receiverId
          ],
          [
            'channel_type' => Channel::CHANNEL_TYPE_P2P,
            'created_by_external_id' => (int)$receiverId,
            'group_id' => (int)$senderId
          ]
        ]
      ];
    } catch (\Throwable $exception) {
      return APIResponder::respondInternalError();
    }
  }

}
