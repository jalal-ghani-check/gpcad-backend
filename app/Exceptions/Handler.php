<?php

namespace App\Exceptions;

use App\Contracts\HTTPStatusCode;
use App\Traits\APIResponder;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
    \Illuminate\Auth\AuthenticationException::class,
    \Illuminate\Validation\ValidationException::class,
  ];
    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
    'password',
    'password_confirmation',
  ];

    public function report(Throwable $exception)
    {
      if (! $this->shouldntReport($exception)) {
        parent::report($exception);
      }
      if (app()->bound('sentry') && $this->shouldReport($exception)) {
        app('sentry')->captureException($exception);
      }
    }

    public function render($request, Throwable $exception)
    {
        if ($exception instanceof \App\Exceptions\ApiParamsValidationException) {
            $exception = [
            'data' => is_string($exception->getResponse()->getContent()) ? json_decode($exception->getResponse()->getContent()) : $exception->getResponse()->getContent(),
            'status_code' => HTTPStatusCode::UNPROCESSABLE_ENTITY,
        ];

            return APIResponder::respondWithError($exception['data'], $exception['status_code']);
        } elseif ($exception instanceof TooManyRequestsHttpException && $request->ajax()) {
            return APIResponder::respondWithError('Too many requests. Please try again later', 429);
        }

        return parent::render($request, $exception);
    }

}
