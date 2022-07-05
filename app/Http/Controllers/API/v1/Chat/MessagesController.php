<?php

namespace App\Http\Controllers\API\v1\Chat;

use App\Common\CommonUtil;
use App\Features\Chat\Messages\AddNewMessage;
use App\Features\Chat\Messages\FetchMessages;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessagesController extends Controller
{
  public function addNewMessage(Request $request)
  {
    return (new AddNewMessage())->_handleAPI($request);
  }

  public function fetchMessages(Request $request, $channelId)
  {
    return (new FetchMessages())->_handleAPI($request, $channelId);
  }

    public function add($data) {
        $response = [];

        $channelId = $this->request->getParam('channelId');
        if(!isset($data['text']) || !isset($data['sender_external_id']) || !isset($data['access_token'])) {
            throw new BadRequestException(ConstMessages::BAD_REQUEST_INCOMPLETE_DATA_MSG);
        }
        $externalId = $data['sender_external_id'];
        $accessToken = $data['access_token'];
        $isValidRequest = User::authenticateClientRequest($accessToken, $externalId);

        if(!$isValidRequest) {
            throw new UnauthorizedException(ConstMessages::UNAUTHORIZED_REQUEST_MSG);
        }

        $user = User::get_user(['external_id' => (int)$externalId]);
        $senderData = [
            'name' => $user[0] ->{'name'},
            'text' => $data['text']
        ];

        $externalIds = [];
        $channelExternalIds = [];
        $time = time();
        $is_member = false;
        $fcm_warn = [];

        $channelMembers = ChannelMembers::getChannelMembers($channelId);
        if(!$channelMembers) {
            throw new NotFoundException(ConstMessages::NOT_FOUND_CHANNELS_MEMBERS . $channelId);
        }
        if(is_array($channelMembers)) {

            foreach ($channelMembers as $channelMember) {
                $channelMember = (array) $channelMember;
                if ((int)$channelMember['external_id'] === (int)$data['sender_external_id']) {
                    $is_member = true;
                    break;
                }
            }
            if($is_member) {
                foreach ($channelMembers as $channelMember) {
                    $channelMember = (array) $channelMember;

                    if ((int)$channelMember['external_id'] !== (int)$data['sender_external_id']) {
                        $user = User::get_user(['external_id' => (int)$channelMember['external_id']]);
                        $tokenArray = $user[0]->{'fcm_token'};
                        $isMute = $user[0]->{'mute_notifications'};
                        if($isMute === ConstGeneral::DISABLED && $tokenArray) {
                           $res = PushNotification::notify($tokenArray, $senderData);
                           if(isset($res['warning'])) {
                               $fcm_warn[] = $res['warning'];
                           }
                        } else {
                            $fcm_warn[] = ConstMessages::WARN_NO_FCM_TOKEN;
                        }

                        array_push($externalIds, ['external_id' => (int)$channelMember['external_id']]);
                        $channelExternalIds[] = (int)$channelMember['external_id'];
                    }
                }

                $response = Message::add_message($data, $channelId, $time);

                if($response['status'] == ConstHttpResponseCodes::SUCCESS_STATUS_CREATED){
                    Channel::updateChannelLastMessageTimeStamp($channelId, $time);
                    ChannelMembers::updateUnreadCount($channelExternalIds, $channelId);
                }
                $response['http_code'] = ConstHttpResponseCodes::SUCCESS_STATUS_CODE;
                $response['msg'] = 'Success';
                if (count($fcm_warn)) {
                    $response['warnings'] = $fcm_warn;
                }
            }
        }
        return $this->getServableResponse($response);
    }

    public function getList(){

        $channelId = $this->request->getParam('channelId');
        $queryParams = $this->request->getQueryParams();

        $userId = (isset($queryParams['id']) ? $queryParams['id'] : 0);
        if(!$userId) {
            throw new UnauthorizedException();
        }

        $this->validateRequest($userId);

        $param = $this->request->getQueryParams();
        if (!isset($param['limit']) || !isset($param['offset'])) {
            throw new BadRequestException('Missing request parameter.');
        }
        $limit = (int)$param['limit'];
        $offset = (int)$param['offset'];

        $filtered = [];
        $response = [];

        if(isset($offset) && (int)$offset >= Configure::read('MSG_LOWER_OFFSET') && (int)$offset <= Configure::read('MSG_UPPER_OFFSET')) {

            $messages = Message::get_message($channelId);

            if (isset($limit) && $limit >= Configure::read('MSG_LOWER_LIMIT') && $limit <= Configure::read('MSG_UPPER_LIMIT')) {

                for ($i = $offset; $i < $limit + $offset; $i++) {
                    if (isset($messages[$i])) {
                        array_push($filtered, $messages[$i]);
                    } else {
                        break;
                    }
                }
                $response['http_code'] = ConstHttpResponseCodes::SUCCESS_STATUS_CODE;
                $response['msg'] = 'Success';
                $response['showLoadMoreIcon'] = ($offset + $limit < count($messages)) ? true : false;
                $response['messages'] = $filtered;
            } else {
                throw new BadRequestException('Integer expected');
            }
        } else {
            throw new BadRequestException('Integer expected');
        }
        return $this->getServableResponse($response);
    }

}
