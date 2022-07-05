<?php


namespace App\Features\MedicalReport;
use App\Common\AjaxResponse;
use App\Common\CommonUtil;
use App\Contracts\HTTPStatusCode;
use App\Features\BaseApi;
use App\Http\Requests\API\v1\SavePoliceReportRequest;
use App\Models\CriminalRecord;
use App\Models\MedicalReport;
use App\Models\PoliceReport;
use App\Models\Vehicle;
use App\Traits\APIResponder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DeleteMedicalReport extends BaseApi
{
  public function __construct() {

  }

  public function _handleAPI(Request $request)
  {
    try {
      $requestData = $this->request = $request;
      $this->_decryptToken();

      $encReportId = CommonUtil::fetch($requestData,'report_id');
      $reportId = CommonUtil::decrypt($encReportId);
      if($reportId){
          $response = $this->deleteReportById($reportId);
          if($response->status) {
              $this->responseData =  CommonUtil::makeKeyValue('success_message',['Saved Successfully']);
          } else {
              $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;
              $this->responseData[] = CommonUtil::fetchFromObject($response,'errors');
          }

      }else {
          $this->responseData[] = CommonUtil::makeKeyValue('error','Something went wrong. Report not deleted successfully');
          $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;
      }

      return $this->_respondApi();
    } catch (\Throwable $exception) {
      return APIResponder::respondInternalError();
    }

  }

    public function deleteReportById($reportId)
    {
        $response = new AjaxResponse();

        $delReport = MedicalReport::manageMedicalReport([
            'deleted_at' => Carbon::now(),
            'updated_by' => $this->userId
        ], $reportId);

        if ($delReport) {
            $response->status = true;
            $response->data = $delReport;
        } else {
            $response->status = false;
            $response->errors = CommonUtil::makeKeyValue('error', 'Internal Error: unable to delete');
        }
        return $response;
    }

}
