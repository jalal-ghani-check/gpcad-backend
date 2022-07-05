<?php


namespace App\Features\Reports;
use App\Common\CommonUtil;
use App\Features\BaseApi;
use App\Models\Department;
use App\Models\MedicalReport;
use App\Models\PoliceReport;
use App\Models\UserPermissions;
use App\Models\Users\Role;
use App\Models\Users\User;
use App\Traits\APIResponder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FetchAllReports extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request)
    {
        try {
            $this->request = $request;
            $this->_decryptToken();
          $userPermissions = UserPermissions::loadUserRoleAndPermissionValues($this->userId);
          $canViewPoliceReports = CommonUtil::fetch($userPermissions, UserPermissions::IS_ALLOWED_TO_VIEW_POLICE_REPORTS['module_name_key']);
          $canViewMedicalReports = CommonUtil::fetch($userPermissions, UserPermissions::IS_ALLOWED_TO_VIEW_MEDICAL_REPORTS['module_name_key']);

          $policeReports = ($canViewPoliceReports) ? $this->preparePoliceReports() : [];
          $medicalReports = ($canViewMedicalReports) ? $this->prepareMedicalReports() : [];

            $allReports = array_merge($policeReports, $medicalReports);
            $reportsSorted = collect($allReports)->sortByDesc('created_at')->values()->all();
            $this->responseData = $reportsSorted;

            return $this->_respondApi();
        } catch (\Throwable $exception) {
            return APIResponder::respondInternalError();
        }

    }

    public function checkIfUserIsAllowedToViewMedicalReports($userId)
    {
      $userInfo = User::getUser($userId);
      if($userInfo && $userInfo->role_id == 1) {
        return true;
      }
      $userInfo = User::getUserWithRoleAndDepartKey($userId);
      return ($userInfo && (in_array($userInfo->role_key, [Role::ROLE_KEY_DOC, Role::ROLE_KEY_EMS]) && $userInfo->depart_key == Department::DEPARTMENT_KEY_MEDICINE));

    }

    public function preparePoliceReports( $profileId = null)
    {
      if(!$profileId){
          $policeReports = PoliceReport::getAll();
      }else {
          $policeReports = PoliceReport::getPoliceReportsProfileId($profileId);
      }

      $userIds = collect($policeReports)
        ->unique('user_id')
        ->pluck('user_id', 'report_id')
        ->values()
        ->all();

      $users = User::all();
      $usersInfo = collect($users)->whereIn('user_id', $userIds)->all();
      $usersInfo = collect($usersInfo)->mapWithKeys(function ($user) {
        return [$user->user_id => ['written_by' => $user->full_name]];
      })->all();

      $policeReportData = [];
      if($policeReports && count($policeReports)) {
        foreach ($policeReports as $report) {
          $one = [
            'report_id' => CommonUtil::encrypt(CommonUtil::fetchFromObject($report, 'report_id')),
            'user_id' => CommonUtil::encrypt(CommonUtil::fetchFromObject($report, 'user_id')),
            'profile_id' => CommonUtil::encrypt(CommonUtil::fetchFromObject($report, 'profile_id')),
            'report_title' => CommonUtil::fetchFromObject($report, 'report_title'),
            'created_at' => Carbon::parse(CommonUtil::fetchFromObject($report, 'created_at'))->format('Y-m-d'),
            'case_number' => CommonUtil::fetchFromObject($report, 'case_number'),
            'report_type' => PoliceReport::REPORT_TYPE_POLICE
          ];
          $policeReportData[] = $one + ($usersInfo[$report->user_id] ?? []);
        }
      }
      return $policeReportData;
    }

    public function prepareMedicalReports ( $profileId = null)
    {
        if(!$profileId){
            $medicalReports = MedicalReport::getAll();
        }else {
            $medicalReports = MedicalReport::getMedicalReportsProfileId($profileId);
        }
      $userIds = collect($medicalReports)
        ->unique('created_by')
        ->pluck('created_by', 'report_id')
        ->values()
        ->all();

      $users = User::all();
      $usersInfo = collect($users)->whereIn('created_by', $userIds)->all();
      $usersInfo = collect($usersInfo)->mapWithKeys(function ($user) {
        return [$user->created_by => ['written_by' => $user->full_name]];
      })->all();

      $medicalReportData = [];
      if($medicalReports && count($medicalReports)) {
        foreach ($medicalReports as $report) {
          $one = [
            'report_id_dec' => CommonUtil::fetchFromObject($report, 'report_id'),
            'report_id' => CommonUtil::encrypt(CommonUtil::fetchFromObject($report, 'report_id')),
            'user_id' => CommonUtil::encrypt(CommonUtil::fetchFromObject($report, 'created_by')),
            'profile_id' => CommonUtil::encrypt(CommonUtil::fetchFromObject($report, 'profile_id')),
            'report_title' => CommonUtil::fetchFromObject($report, 'report_title'),
            'created_at' => Carbon::parse(CommonUtil::fetchFromObject($report, 'created_at'))->format('Y-m-d'),
            'case_number' => CommonUtil::fetchFromObject($report, 'case_number'),
            'report_type' => MedicalReport::REPORT_TYPE_MEDICAL
          ];
          $medicalReportData[] = $one + ($usersInfo[$report->user_id] ?? []);
        }
      }
      return $medicalReportData;
    }




}
