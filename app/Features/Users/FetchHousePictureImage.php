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
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Modules\UserAuthentication\Repositories\UserRepository;

class FetchHousePictureImage extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request ){

        try {
            $this->request = $request;
            $this->_decryptToken();

            $houseId = CommonUtil::decrypt(CommonUtil::fetch($request,'house_id'));

            if($houseId){
                $house = House::getHouseByHouseId($houseId);
                $image = CommonUtil::fetchFromObject($house,'image');
                if($image) {
                    $imageArr = explode('.',$image);
                    $extension = $imageArr[count($imageArr)-1];

                    $housePicPath = storage_path().'/app/houses/'.$image;
                    if ($housePicPath){
                        $content = file_get_contents($housePicPath);
                        $mimeType = self::getMimeTypeByExtension($extension);

                        header("Content-type: $mimeType");
                        header("Content-Disposition: inline; filename=profile_image.$extension");
                        header('Content-Transfer-Encoding: binary');
                        header('Expires: 0');
                        header('Content-Length: ' . strlen($content));
                        exit($content);
                    }
                }
            }

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
