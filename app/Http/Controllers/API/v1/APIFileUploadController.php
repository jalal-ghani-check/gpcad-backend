<?php

namespace App\Http\Controllers\API\v1;

use App\Features\FileUpload\UploadGeneralFile;
use App\Http\Controllers\Controller;
use App\Models\ErrorLoggerModel;
use App\Traits\APIResponder;
use Illuminate\Http\Request;
use Throwable;

class APIFileUploadController extends Controller
{
  public function uploadFile(Request $request)
  {
    try {
      return (new UploadGeneralFile)->_handleAPI($request);
    } catch (Throwable $e) {
      return APIResponder::respondInternalError();
    }
  }
}
