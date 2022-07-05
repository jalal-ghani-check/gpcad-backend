<?php


namespace App\Features\PoliceReport;
use App\Common\CommonUtil;
use App\Contracts\HTTPStatusCode;
use App\Features\BaseApi;
use App\Models\CriminalRecord;
use App\Models\Law;
use App\Models\PoliceReport;
use App\Models\ProfileRecord;
use App\Models\Users\User;
use App\Traits\APIResponder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FetchPoliceReportData extends BaseApi
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
      $report = PoliceReport::getPoliceReportByReportId($reportId);
      $ret = [
          'status' => false,
      ];

      if($report) {

        $userId = CommonUtil::fetchFromObject($report, 'user_id');
        $user = User::getUser($userId);
        $userName = CommonUtil::fetchFromObject($user, 'full_name');
        $profileId = CommonUtil::fetchFromObject($report, 'profile_id', 'N/A');
        $profile = ProfileRecord::getProfileRecordByProfileId($profileId);
        $profileName = CommonUtil::fetchFromObject($profile, 'full_name', 'N/A');
        $criminalRecord =  $this->prepareCriminalRecordStats($profileId, $reportId);

        $data = [
          'report_id' => $reportId,
          'enc_report_id' => CommonUtil::encrypt($reportId),
          'enc_profile_id_attached' => CommonUtil::encrypt($profileId),
          'regarding' => $profileName,
          'written_by' => $userName,
          'report_title_raw' => CommonUtil::fetchFromObject($report, 'report_title'),
          'report_title' => CommonUtil::fetchFromObject($report, 'report_title') . ' ' . Carbon::parse(CommonUtil::fetchFromObject($report, 'created_at'))->format('Y/m/d'),
          'case_number' => CommonUtil::fetchFromObject($report, 'case_number'),
          'cid' => CommonUtil::fetchFromObject($report, 'cid', 'N/A'),
          'ref_case_number' => CommonUtil::fetchFromObject($report, 'ref_case_number', 'N/A'),
          'officers_involved' => (bool)CommonUtil::fetchFromObject($report, 'officers_involved', 'N/A'),
          'shorts_fired' => (bool)CommonUtil::fetchFromObject($report, 'shorts_fired'),
          'gsr_test_result' => (bool)CommonUtil::fetchFromObject($report, 'gsr_test_result'),
          'casing_recovered' => (bool)CommonUtil::fetchFromObject($report, 'casing_recovered'),
          'description' => CommonUtil::fetchFromObject($report, 'description'),
          'suspects_involved' => (bool)CommonUtil::fetchFromObject($report, 'suspects_involved'),
          'use_of_violence' => (bool)CommonUtil::fetchFromObject($report, 'use_of_violence'),
          'med_treatment' => (bool)CommonUtil::fetchFromObject($report, 'med_treatment'),
          'legal_aid' => (bool)CommonUtil::fetchFromObject($report, 'legal_aid'),
          'items_seized' => CommonUtil::fetchFromObject($report, 'items_seized', 'N/A'),
          'created_at' => Carbon::parse(CommonUtil::fetchFromObject($report, 'created_at'))->format('Y-m-d H:i'),
          'criminal_record' => $criminalRecord['crimes'] ?? [],
          'received_charges' => $criminalRecord['received_charges'] ?? [],
          'total_charges' => $criminalRecord['total_charges'] ?? [],
          'added_laws' => $criminalRecord['addedLawsInReportArr'] ?? []

        ];
        $ret['status'] = true;
        $ret['data'] = $data;
      }else{
          $ret['status'] = false;
          $ret['error'] = CommonUtil::makeKeyValue('not_found','Profile not found against given Id');
      }

      return $ret;
    }

    private function prepareCriminalRecordStats($profileId, $reportId)
    {
      $addedLawsInReportArr = [];
      $recordStats = [];
      $criminalRecord = CriminalRecord::getCriminalRecordsByProfileRecordId($profileId); // for total stats
      $criminalRecordByReport = collect($criminalRecord)->where('police_report_id', $reportId);
      if($criminalRecordByReport && count($criminalRecordByReport)) {
        // pluck all law ids filed against profile
        $criminalRecordIds = collect($criminalRecord)->pluck('law_id', 'record_id')->values(); // for total stats
        $criminalRecordIdsByReport = collect($criminalRecordByReport)->pluck('law_id', 'record_id')->values();

        $laws = Law::getAll();

        $filteredLaws = collect($laws)->whereIn('law_id', $criminalRecordIds)->all(); // for total stats
        $filteredLawsByReport = collect($laws)->whereIn('law_id', $criminalRecordIdsByReport)->all();

        if($filteredLawsByReport) {
          foreach ($filteredLawsByReport as $record) {
            $points = CommonUtil::fetchFromObject($record, 'points');
            $points = ($points) ? number_format($points, '2', '.', ',') : 'N/A';
            $jailTime = CommonUtil::fetchFromObject($record, 'jail_time');
            $jailTime = ($points) ? number_format($jailTime, '2', '.', ',') : 'N/A';
            $recordStats['crimes'][$record->crime_type][] = [
              'law_title' => CommonUtil::fetchFromObject($record, 'name', 'N/A'),
              'law_code' => CommonUtil::fetchFromObject($record, 'law_code', 'N/A'),
              'fine_amount' => CommonUtil::fetchFromObject($record, 'fine_amount'),
              'duration' => $points,
              'points' => $jailTime
            ];
            $addedLawsInReportArr[] = CommonUtil::encrypt(CommonUtil::fetchFromObject($record, 'law_id'));
          }

          $recordStats['addedLawsInReportArr'] = $addedLawsInReportArr;

          $ReceivedFineAmount = collect($filteredLawsByReport)->sum('fine_amount');
          $ReceivedJailTime = collect($filteredLawsByReport)->sum('jail_time');
          $recordStats['received_charges'] = [
            'fine' => number_format($ReceivedFineAmount, 2, '.', ','),
            'jail_time' => $ReceivedJailTime
          ];

          $TotalFineAmount = collect($filteredLaws)->sum('fine_amount');
          $totalPoints = collect($filteredLaws)->sum('points');
          $recordStats['total_charges'] = [
            'fine' => number_format($TotalFineAmount, 2, '.', ','),
            'points' => number_format($totalPoints, 2, '.', ',')
          ];
        }

      }
      return $recordStats;
    }




}
