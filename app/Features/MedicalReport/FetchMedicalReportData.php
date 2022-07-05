<?php


namespace App\Features\MedicalReport;
use App\Common\CommonUtil;
use App\Contracts\HTTPStatusCode;
use App\Features\BaseApi;
use App\Models\MedicalReport;
use App\Models\ProfileRecord;
use App\Traits\APIResponder;
use Illuminate\Http\Request;

class FetchMedicalReportData extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request, $encReportId)
    {
        try {
            $this->request = $request;
            $this->_decryptToken();
            $reportId = CommonUtil::decrypt($encReportId);
            if($reportId){
                $response = $this->prepareReportData($reportId);

                if(CommonUtil::fetch($response,'status')){
                    $this->responseData = CommonUtil::fetch($response,'data');
                }else {
                    $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;
                    $this->responseData[] = CommonUtil::fetch($response,'error');
                }

            }else{
                $this->responseData[] = CommonUtil::makeKeyValue('error','Something went wrong');
                $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;
            }
            return $this->_respondApi();
        } catch (\Throwable $exception) {
            return APIResponder::respondInternalError();
        }

    }

    private function prepareReportData($reportId){
      $report = MedicalReport::getMedicalReportByReportId($reportId);
      $ret = [
          'status' => false,
      ];

      if($report) {

        $profileId = CommonUtil::fetchFromObject($report, 'profile_id');
        $profile = ProfileRecord::getProfileRecordByProfileId($profileId);
        $profileName = CommonUtil::fetchFromObject($profile, 'full_name', 'N/A');
        $allergyTypeSelected = CommonUtil::fetchFromObject($report, 'allergy_type');
        $medicalHistory = CommonUtil::fetchFromObject($report, 'medical_history', '');
        $medicalHistoryArray = array_unique(explode(',', $medicalHistory));


        $data = [
          'report_id' => $reportId,
          'enc_report_id' => CommonUtil::encrypt($reportId),
          'enc_profile_id' => CommonUtil::encrypt($profileId),
          'profile_name' => $profileName,
          'report_title' => CommonUtil::fetchFromObject($report, 'report_title', ''),
          'problem_description' => CommonUtil::fetchFromObject($report, 'problem_description', 'N/A'),
          'citizen_id' => CommonUtil::fetchFromObject($report, 'citizen_id'),
          'problem_started_at' => CommonUtil::fetchFromObject($report, 'problem_started_at', 'N/A'),
          'problem_cause' => CommonUtil::fetchFromObject($report, 'problem_cause', 'N/A'),
          'problem_cause_detail' => CommonUtil::fetchFromObject($report, 'problem_cause_detail', 'N/A'),
          'medical_history' => CommonUtil::fetchFromObject($report, 'medical_history', 'N/A'),
          'medical_history_array' => $medicalHistoryArray,
          'surgery_name' => CommonUtil::fetchFromObject($report, 'surgery_name', 'N/A'),
          'surgery_year' => CommonUtil::fetchFromObject($report, 'surgery_year', 'N/A'),
          'surgery_complication' => CommonUtil::fetchFromObject($report, 'surgery_complication', 'N/A'),
          'surgery_description' => CommonUtil::fetchFromObject($report, 'surgery_description', 'N/A'),
          'medication_name' => CommonUtil::fetchFromObject($report, 'medication_name', 'N/A'),
          'medication_done' => CommonUtil::fetchFromObject($report, 'medication_done', 'N/A'),
          'medication_reason' => CommonUtil::fetchFromObject($report, 'medication_reason', 'N/A'),
          'medication_description' => CommonUtil::fetchFromObject($report, 'medication_description', 'N/A'),
          'allergies_latex' => ($allergyTypeSelected === MedicalReport::ALLERGY_TYPE_LATEX) ? 'Yes' : 'No',
          'allergies_iodine' => ($allergyTypeSelected === MedicalReport::ALLERGY_TYPE_IODINE) ? 'Yes' : 'No',
          'allergies_bromine' => ($allergyTypeSelected === MedicalReport::ALLERGY_TYPE_BROMINE) ? 'Yes' : 'No',
          'allergies_other' => ($allergyTypeSelected === MedicalReport::ALLERGY_TYPE_OTHER) ? 'Yes' : 'No',
          'allergy_type_selected' => $allergyTypeSelected,
          'allergies_details' => CommonUtil::fetchFromObject($report, 'allergies_details'),
          'personal_views' => CommonUtil::fetchFromObject($report, 'personal_views')
        ];
        $ret['status'] = true;
        $ret['data'] = $data;
      }else{
          $ret['status'] = false;
          $ret['error'] = CommonUtil::makeKeyValue('not_found','Profile not found against given Id');
      }

      return $ret;
    }
}
