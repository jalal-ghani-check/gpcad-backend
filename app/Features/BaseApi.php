<?php


namespace App\Features;


use App\Common\CommonUtil;
use App\Contracts\HTTPStatusCode;
use App\Models\Users\UserAPIToken;
use App\Traits\APIResponder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\UserAuthentication\Repositories\UserAPITokenRepository;

abstract class BaseApi
{
  protected $userId;
  protected $expiryTime;
  protected $responseData;
  protected $errorResponse;
  protected $request;
  protected $statusCode = 200;

  private function _logResponse() : void
  {
    #TODO dispatch job to log request and response
  }

  private function _prepareHeaders() : array
  {
    $withHeaders = [];
    $currentHeader = $this->request->header('Pb-Token', null);
    if (config('app.env')!='local' && $currentHeader) {
      $decryptToken = CommonUtil::decrypt($currentHeader);
      $userId = CommonUtil::fetch($decryptToken,'user_id', null);
      // generate new token for the next request
      $withHeaders = ['Pb-Token' => UserAPIToken::refreshToken($userId)];
    }
    return $withHeaders;
  }

  protected function _decryptToken()
  {
    $currentHeader = $this->request->header('Pb-Token', null);
    if ($currentHeader) {
      $decryptToken = CommonUtil::decrypt($currentHeader);
      $this->userId = CommonUtil::fetch($decryptToken,'user_id', null);
      $this->expiryTime = CommonUtil::fetch($decryptToken,'time', null);
    }
  }

  private function _prepareResponse() : JsonResponse
  {
    $withHeaders = $this->_prepareHeaders();
    $responseKey = 'data';
    if ($this->statusCode != HTTPStatusCode::OK) {
      $responseKey = 'error';
    }
    $this->_logResponse();
    return APIResponder::respond([$responseKey => $this->responseData], $this->statusCode, $withHeaders);
  }

  public function _respondApi() : JsonResponse
  {
    return $this->_prepareResponse();
  }
}
