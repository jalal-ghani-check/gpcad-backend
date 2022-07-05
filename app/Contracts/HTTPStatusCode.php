<?php

namespace App\Contracts;

interface HTTPStatusCode
{
    /**
     * Success!
     */
    const OK = 200;

    /**
     * There was no new data to return.
     */
    const NOT_MODIFIED = 302;

    /**
     * The request was invalid or cannot be otherwise served. An accompanying error message will explain further.
     */
    const BAD_REQUEST = 400;

    /**
     * Authentication credentials were missing or incorrect.
     */
    const UNAUTHORIZED = 401;

    /**
     * The request is understood, but it has been refused or access is not allowed.
     * An accompanying error message will explain why.
     * This code is used when requests are being denied due to update limits.
     */
    const FORBIDDEN = 403;

    /**
     * The URI requested is invalid or the resource requested, such as a user, does not exists.
     * Also returned when the requested format is not supported by the requested method.
     */
    const NOT_FOUND = 404;

    /**
     * Returned by the API when an invalid format is specified in the request.
     */
    const NOT_ACCEPTABLE = 406;

    /**
     * This resource is gone. Used to indicate that an API endpoint has been turned off.
     */
    const GONE = 410;

    /**
     * Returned when you are being rate limited.
     */
    const ENHANCE_YOUR_CALM = 420;

    /**
     * Returned when an image uploaded to POST /path is unable to be processed.
     */
    const UNPROCESSABLE_ENTITY = 422;

    /**
     * Returned when a request cannot be served due to the application’s rate limit having been exhausted for the resource.
     */
    const TOO_MANY_REQUESTS = 429;

    /**
     * Something is broken. Please inform the backend team so they can investigate.
     */
    const INTERNAL_SERVER_ERROR = 500;

    /**
     * MoBerries is down or being upgraded.
     */
    const BAD_GATEWAY = 502;

    /**
     * The MoBerries servers are up, but overloaded with requests. Try again later.
     */
    const SERVICE_UNAVAILABLE = 503;

    /**
     * The MoBerries servers are up, but the request couldn't be serviced due to some failure within our stack. Try again later.
     */
    const GATEWAY_TIMEOUT = 504;
}
