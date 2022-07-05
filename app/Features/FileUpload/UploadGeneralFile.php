<?php

namespace App\Features\FileUpload;

use App\Common\CommonUtil;
use App\Common\FileManagementUtil;
use App\Common\SessionManager;
use App\Contracts\HTTPStatusCode;
use App\Helpers\FileUploader;
use App\Traits\APIResponder;
use Illuminate\Http\Request;

class UploadGeneralFile
{
  public function _handleAPI(Request $request)
  {
    $header	=	$request->header('Pb-token', null);
    $decryptToken = CommonUtil::decrypt($header);
    $userId = CommonUtil::fetch($decryptToken,'user_id', null);
    $fileName = null;

    if ($request->hasFile('profile_image')) {
        $fileName = 'profile_image';
        $acl	=	'public-read';
    }

    $result = $this->uploadFile($fileName, $userId);

    if ($result) {
      return APIResponder::respondWithOK($result);
    } else {
      $error[] = CommonUtil::makeKeyValue('something_went_wrong', __('api_messages.something_went_wrong'));
      return APIResponder::respondWithError($error, HTTPStatusCode::UNPROCESSABLE_ENTITY);
    }
  }


  public function uploadFile($fileName, $userId)
  {
    $FileUploader = new FileUploader($fileName, [
      'uploadDir' => storage_path().'/app/houses/',
      'title' => 'auto',
    ]);
    $data = $FileUploader->upload();

    $result = null;
    if (isset($data['files'][0]['file'])) {
      unset($data['files'][0]['date']);
      unset($data['files'][0]['file']);
      unset($data['files'][0]['uploaded']);
      unset($data['files'][0]['size2']);
      unset($data['files'][0]['replaced']);
      $result = $data['files'][0];

    }

    return $result;
  }
}
