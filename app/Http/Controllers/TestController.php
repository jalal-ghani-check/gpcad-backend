<?php

namespace App\Http\Controllers;

use App\Common\CommonUtil;
use App\Features\Users\FetchAllUsers;
use App\Models\UserPermissions;
use App\Models\UserRights;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class TestController extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function test (Request $request)
    {
      $rights = UserRights::getUserRightsByUserId(1);
      $userPermissions = UserPermissions::loadUserRoleAndPermissionValues(2);
//      dd($userPermissions);
      $canViewPoliceReports = CommonUtil::fetch($userPermissions, UserPermissions::IS_ALLOWED_TO_VIEW_POLICE_REPORTS['module_name_key']);
      dd($canViewPoliceReports);
      return (new FetchAllUsers())->_handleApi($request);
    }


}
