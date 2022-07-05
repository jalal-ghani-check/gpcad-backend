<?php


namespace App\Features\Reports;
use App\Common\CommonUtil;
use App\Features\BaseApi;
use App\Models\MedicalReport;
use App\Models\PoliceReport;
use App\Models\Users\User;
use App\Traits\APIResponder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FetchProfileReports extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request, $profileId)
    {
        try {
            $profileId = CommonUtil::decrypt($profileId);
            $this->request = $request;
            $this->_decryptToken();
            $reportObj = new FetchAllReports();
            $policeReports = $reportObj->preparePoliceReports($profileId);
            $medicalReports = $reportObj->prepareMedicalReports($profileId);
            $allReports = array_merge($policeReports, $medicalReports);
            $reportsSorted = collect($allReports)->sortByDesc('created_at')->values()->all();
            $this->responseData = $reportsSorted;

            return $this->_respondApi();
        } catch (\Throwable $exception) {
            return APIResponder::respondInternalError();
        }

    }


}
