<?php


namespace App\Features\Chat\Messages;
use App\Common\AjaxResponse;
use App\Common\CommonUtil;
use App\Contracts\HTTPStatusCode;
use App\Features\BaseApi;
use App\Models\Chat\Message;
use App\Models\Chat\User;
use App\Traits\APIResponder;
use Illuminate\Http\Request;
use MongoDB\BSON\ObjectId;

class AddNewMessage extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request){

        try {

            $this->request = $request;
            $this->_decryptToken();
            $requestData = $request->all();
            $senderId = CommonUtil::fetch($requestData, 'sender_external_id');
            $messageText = CommonUtil::fetch($requestData, 'text');
            if($senderId && $messageText) {
              $response = $this->addNewMessage($requestData);
              if($response->status) {
                $this->responseData = CommonUtil::fetchFromObject($response, 'data');
              } else {
                $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;
                $this->responseData[] = CommonUtil::fetchFromObject($response,'errors');
              }
            } else {
              $this->responseData[] = CommonUtil::makeKeyValue('error', 'Invalid data. Unable to perform action.');
              $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;
            }

            return $this->_respondApi();
        } catch (\Throwable $exception) {
            return APIResponder::respondInternalError();
        }

    }

    public function addNewMessage($requestData)
    {
      $response = new AjaxResponse();
      $senderId = CommonUtil::fetch($requestData, 'sender_external_id');
      $text = CommonUtil::fetch($requestData, 'text');
      $messageText = CommonUtil::fetch($requestData, 'text');
      $user = head(User::getUserByExternalId(['external_id' => (int)$senderId]));
      $senderData = [
        'text' => $text,
        'sender_external_id' => $senderId
      ];
      $message = Message::addMessage($senderData, $requestData['channel_id'], time());
      if($message) {
        $response->status = true;
        $response->data = $message;
      } else {
        $response->status = false;
        $response->errors = CommonUtil::makeRequestResponseKeyValue(['error' => 'message not saved']);
      }
      return $response;
    }






}
