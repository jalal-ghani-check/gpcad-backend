<?php


namespace App\Features\MedicalReport;
use App\Common\AjaxResponse;
use App\Common\CommonUtil;
use App\Contracts\HTTPStatusCode;
use App\Features\BaseApi;
use App\Http\Requests\API\v1\SaveMedicalReportRequest;
use App\Models\MedicalReport;
use App\Models\ProfileRecord;
use App\Traits\APIResponder;
use Carbon\Carbon;
use http\Exception;
use Illuminate\Http\Request;

class SaveMedicalReport extends BaseApi
{
  public function __construct() {

  }

  public function _handleAPI(SaveMedicalReportRequest $request)
  {
    try {
      $requestData = $this->request = $request;
      $this->_decryptToken();
      if($requestData){

        $response = $this->saveReportSettings($requestData);

        if ($response->status) {
          $this->responseData = CommonUtil::fetchFromObject($response,'data');
        } else {
          $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;
          $this->responseData[] = CommonUtil::fetchFromObject($response,'errors');
        }

      }else{
        $this->responseData[] = CommonUtil::makeKeyValue('error','Something went wrong. Report not saved successfully');
        $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;
      }
      return $this->_respondApi();
    } catch (\Throwable $exception) {
      return APIResponder::respondInternalError();
    }

  }

  private function saveReportSettings ($requestData)
  {
    $response = new AjaxResponse();

    $medicalHistory = CommonUtil::fetch($requestData, 'medicalHistory');
    $medicalHistory = implode(', ', $medicalHistory);
    $profileId = CommonUtil::fetch($requestData, 'profileId');
    $profileId = CommonUtil::decrypt($profileId);
    $encReportId = CommonUtil::fetch($requestData, 'reportId');
    $reportId = CommonUtil::decrypt($encReportId);
    $settings = [
      'profile_id' => $profileId,
      'citizen_id' => CommonUtil::fetch($requestData, 'citizenId'),
      'report_title' => CommonUtil::fetch($requestData, 'reportTitle'),

      'allergy_type' => CommonUtil::fetch($requestData, 'allergy'),
      'allergies_details' => CommonUtil::fetch($requestData, 'allergyDetails'),

      'medication_name' => CommonUtil::fetch($requestData, 'medicationName'),
      'medication_done' => CommonUtil::fetch($requestData, 'medicationDone'),
      'medication_description' => CommonUtil::fetch($requestData, 'medicationDescription'),
      'medication_reason' => CommonUtil::fetch($requestData, 'medicationReason'),

      'problem_description' => CommonUtil::fetch($requestData, 'problemDescription'),
      'problem_cause' => CommonUtil::fetch($requestData, 'problemCause'),
      'problem_cause_detail' => CommonUtil::fetch($requestData, 'problemDetails'),
      'problem_started_at' => CommonUtil::fetch($requestData, 'problemStartedAt'),

      'personal_views' => CommonUtil::fetch($requestData, 'religiousCulturalViews'),

      'surgery_name' => CommonUtil::fetch($requestData, 'surgeryName'),
      'surgery_complication' => CommonUtil::fetch($requestData, 'surgeryComplications'),
      'surgery_description' => CommonUtil::fetch($requestData, 'surgeryDescription'),
      'surgery_year' => CommonUtil::fetch($requestData, 'surgeryYear'),

      'medical_history' => $medicalHistory,

      'created_by' => $this->userId,
      'updated_by' => $this->userId,
      'created_at' => Carbon::now()
    ];

    $savedReport = MedicalReport::manageMedicalReport($settings,$reportId);

    if($savedReport) {
      $response->status = true;
      $response->data = $savedReport;
    } else {
      $response->status = false;
      $response->errors = CommonUtil::makeKeyValue('error','Something went wrong. Unable to save report');
    }
    return $response;
  }
}
