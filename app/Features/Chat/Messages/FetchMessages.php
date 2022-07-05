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

class FetchMessages extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request, $channelId){

        try {

            $this->request = $request;
            $this->_decryptToken();


            if($channelId) {
              $response = $this->fetchChannelMessages($channelId);
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

    public function fetchChannelMessages($channelId)
    {
      $response = new AjaxResponse();
      $messages = Message::getMessages($channelId);
      $response->status = true;
      $response->data = ($messages) ?: (object)[];

      return $response;
    }






}
