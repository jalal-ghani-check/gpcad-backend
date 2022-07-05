<?php

namespace App\Http\Middleware;


use App\Common\CommonUtil;
use App\Models\Users\UserAPIToken;
use App\Traits\APIResponder;
use Closure;
use Illuminate\Http\Request;

class ValidateWebApiToken
{
  /**
   * Handle an incoming request.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  \Closure  $next
   * @return mixed
   */
  public function handle(Request $request, Closure $next)
  {
//      $routesActions = $request->route()->getAction();
//      dd($routesActions);

      $header = $request->header('Pb-Token', null);
    if (! $header) {
      return APIResponder::respondForbidden();
    }
    if (! UserAPIToken::isTokenExist($header)){
      return APIResponder::respondWithInvalidAPIKey();
    }
    $decryptToken = CommonUtil::decrypt($header);
    $userId = CommonUtil::fetch($decryptToken,'user_id', null);
    $expiryTime = CommonUtil::fetch($decryptToken,'time', null);
    if (! ($userId && $expiryTime)) {
      return APIResponder::respondWithInvalidAPIKey();
    }
    if (time() > $expiryTime ) {
      return APIResponder::respondWithInvalidAPIKey();
    }

    return $next($request);
  }
}
