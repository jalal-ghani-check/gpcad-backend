<?php


namespace App\Features\Users;


use App\Common\CommonUtil;
use App\Common\FileManagementUtil;
use App\Features\BaseApi;
use App\Models\House;
use App\Models\UserPermissions;
use App\Models\Users\User;
use App\Models\Users\UserAPIToken;
use App\Models\Warrant;
use App\Traits\APIResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Modules\UserAuthentication\Repositories\UserRepository;

class FetchHousePicture extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request,$encHouseId ){

        try {
            $this->request = $request;
            $requestData = $request->all();
            $this->_decryptToken();
            $houseId = CommonUtil::decrypt($encHouseId);
            if($houseId){
                $house = House::getHouseByHouseId($houseId);
                $image = CommonUtil::fetchFromObject($house,'image');
                if($image) {
                    $imageArr = explode('.',$image);
                    $extension = $imageArr[count($imageArr)-1];

                    $housePicPath = storage_path().'/app/houses/'.$image;
                    if ($housePicPath){
                        $base64 = base64_encode(file_get_contents($housePicPath));
                        if ($base64){
                            $mimeType = self::getMimeTypeByExtension($extension);
                            $this->responseData = [
                                "content-type" => $mimeType,
                                "name" => "profile_image.$extension",
                                'content' => $base64,
                            ];
                        }
                    }
                }
            }
            return $this->_respondApi();
        } catch (\Throwable $exception) {
            return APIResponder::respondInternalError();
        }

    }

    public static function getMimeTypeByExtension($extension, $getExtensionByMimeType	=	false)
    {
        $mimet = [
            'txt' => 'text/plain',
            // images
            'png' => 'image/png',
            'jpeg' => 'image/jpeg',
            'jpg' => 'image/jpeg',
            'gif' => 'image/gif',
            'bmp' => 'image/bmp',
            'ico' => 'image/vnd.microsoft.icon',
            'tiff' => 'image/tiff',
            'tif' => 'image/tiff',
            'svg' => 'image/svg+xml',
            'svgz' => 'image/svg+xml',
            // adobe
            'pdf' => 'application/pdf',
            // ms office
            'doc' => 'application/msword',
            'rtf' => 'application/rtf',
            'xls' => 'application/vnd.ms-excel',
            'ppt' => 'application/vnd.ms-powerpoint',
            'docx' => 'application/msword',
            'xlsx' => 'application/vnd.ms-excel',
            'pptx' => 'application/vnd.ms-powerpoint',
        ];

        if(!$getExtensionByMimeType){
            if (isset($mimet[$extension])) {
                return $mimet[$extension];
            } else {
                return 'application/octet-stream';
            }
        }else{
            return array_search($extension, $mimet);
        }
    }


}
