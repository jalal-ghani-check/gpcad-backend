<?php

namespace App\Traits;

use App\Contracts\HTTPStatusCode;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Response;

trait APIResponder
{
  /**
   * @param LengthAwarePaginator $collection
   * @param $data
   * @return mixed
   */
  public static function respondWithPagination(LengthAwarePaginator $collection, $data)
  {
    $data = array_merge($data, [
      'paginator' => [
        'total_count' => $collection->total(),
        'total_pages' => ceil($collection->total() / $collection->perPage()),
        'current_page' => $collection->currentPage(),
        'limit' => $collection->perPage(),
      ],
    ]);

    return self::respond($data);
  }

  /**
   * @param $data
   * @param int $statusCode
   * @param array $headers
   * @return mixed
   */
  public static function respond($data, int $statusCode = HTTPStatusCode::OK, array $headers = []) {
    return Response::json($data, $statusCode)->withHeaders($headers);
  }
  /**
   * @param $data
   * @param string $message
   */
  public static function respondWithOK($data = '', $message = 'OK', $responseKey = 'data')
  {
    $output = [
      'status_code' => HTTPStatusCode::OK,
      $responseKey => $data,
    ];

    return self::respond($output, HTTPStatusCode::OK);
  }

  /**
   * @param $message
   * @param $statusCode
   * @return mixed
   */
  public static function respondWithError($message, $statusCode, $extraData = [])
  {
    $data = ['status_code' => $statusCode,
      'error' => $message,
//      'data' => $extraData
    ];

    return self::respond($data, $statusCode);
  }

  /**
   * @param string $message
   * @return mixed
   */
  public static function respondNotFound($message = 'Not Found!')
  {
    return self::respondWithError($message, HTTPStatusCode::NOT_FOUND);
  }

  /**
   * @param string $message
   * @return mixed
   */
  public static function respondNotAuthorized($message = 'Not Authorized!')
  {
    return self::respondWithError($message, HTTPStatusCode::UNAUTHORIZED);
  }

  /**
   * @param string $message
   * @return mixed
   */
  public static function respondNotAcceptable($message = 'Not Acceptable!')
  {
    return self::respondWithError($message, HTTPStatusCode::NOT_ACCEPTABLE);
  }

  /**
   * @param string $message
   * @return mixed
   */
  public static function respondForbidden($message = 'Access not allowed!')
  {
    return self::respondWithError($message, HTTPStatusCode::FORBIDDEN);
  }

  /**
   * @param string $message
   * @return mixed
   */
  public static function respondInternalError($message = 'Internal Error')
  {
    return self::respondWithError($message, HTTPStatusCode::INTERNAL_SERVER_ERROR);
  }

  /**
   * @param string $message
   */
  public static function respondWithUnprocessableEntity($message = 'Unprocessable Entity !')
  {
    return self::respondWithError($message, HTTPStatusCode::UNPROCESSABLE_ENTITY);
  }

  /**
   * @param string $message
   */
  public static function respondWithInvalidAPIKey($message = 'Invalid API Key')
  {
    return self::respondWithError($message, HTTPStatusCode::UNAUTHORIZED);
  }

  /**
   * @param string $message
   */
  public static function respondWithInvalidUserToken($message = 'Invalid User Token')
  {
    return self::respondWithError($message, HTTPStatusCode::UNAUTHORIZED);
  }
}
