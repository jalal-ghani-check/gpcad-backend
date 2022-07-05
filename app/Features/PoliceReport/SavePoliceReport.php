<?php


namespace App\Features\PoliceReport;
use App\Common\CommonUtil;
use App\Contracts\HTTPStatusCode;
use App\Features\BaseApi;
use App\Http\Requests\API\v1\SavePoliceReportRequest;
use App\Models\CriminalRecord;
use App\Models\PoliceReport;
use App\Traits\APIResponder;
use Carbon\Carbon;

class SavePoliceReport extends BaseApi
{
  public function __construct() {

  }

  public function _handleAPI(SavePoliceReportRequest $request)
  {
    try {
      $requestData = $this->request = $request;
      $this->_decryptToken();
      if($requestData){

        $response = $this->saveReportSettings($requestData);

        if($response){
          $this->responseData = []; CommonUtil::fetch($response,'data', []);
        }else {
          $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;
          $this->responseData[] = CommonUtil::fetch($response,'error');
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
    $response = [];

    $userId = $this->userId;
    $encProfileId = CommonUtil::fetch($requestData, 'profileId');
    $encReportId = CommonUtil::fetch($requestData, 'reportId');
    $reportId = CommonUtil::decrypt($encReportId);
    $profileId = CommonUtil::decrypt($encProfileId);

    $dataToSave = [
      'report_title' => CommonUtil::fetch($requestData, 'reportTitle'),
      'profile_id' => $profileId,
      'user_id' => $userId,
      'cid' => CommonUtil::fetch($requestData, 'cid'),
      'case_number' => CommonUtil::generateRandomString(12),
      'description' => CommonUtil::fetch($requestData, 'reportDesc'),
      'items_seized' => CommonUtil::fetch($requestData, 'itemsSeized'),
      'ref_case_number' => CommonUtil::generateRandomString(12),
      'officers_involved' => CommonUtil::fetch($requestData, 'officersInvolved'),
      'shorts_fired' => CommonUtil::fetch($requestData, 'shotsFired'),
      'gsr_test_result' => CommonUtil::fetch($requestData, 'gsrTestResults'),
      'casing_recovered' => CommonUtil::fetch($requestData, 'casingsRecovered'),
      'suspects_involved' => CommonUtil::fetch($requestData, 'suspectsInvolved'),
      'use_of_violence' => CommonUtil::fetch($requestData, 'useOfViolence'),
      'med_treatment' => CommonUtil::fetch($requestData, 'medicalTreatment'),
      'legal_aid' => CommonUtil::fetch($requestData, 'legalAid'),
      'created_by' => $userId,
      'updated_by' => $userId,
      'created_at' => Carbon::now()
    ];

    $savedPoliceReport = PoliceReport::managePoliceReport($dataToSave,$reportId);

    if($savedPoliceReport) {
      $policeReportId = CommonUtil::fetchFromObject($savedPoliceReport, 'report_id');

      (new DeletePoliceReport())->deleteReportAttachedCriminalRecords($reportId);

      $laws = CommonUtil::fetch($requestData, 'lawsArray');
      foreach ($laws as $encLawId) {
        $lawId = CommonUtil::decrypt($encLawId);


        $recordToSave = [
          'police_report_id' => $policeReportId,
          'profile_record_id' => $profileId,
          'law_id' => $lawId,
          'created_by' => $userId,
          'updated_by' => $userId,
          'created_at' => Carbon::now()
        ];
        $savedReport = CriminalRecord::manageCriminalRecord($recordToSave);
        $response['status'] = true;
        $response['data'] = $savedReport;
      }
    } else {
      $response['status'] = false;
      $response['errors'] = CommonUtil::makeKeyValue('create_error', 'Something went wrong, unable to create report');
      return $response;
    }
    return $response;
  }
}
