<?php

namespace App\Http\Controllers\API\v1\MedicalReport;

use App\Features\MedicalReport\DeleteMedicalReport;
use App\Features\MedicalReport\FetchMedicalReportData;
use App\Features\MedicalReport\SaveMedicalReport;
use App\Features\PoliceReport\DeletePoliceReport;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\SaveMedicalReportRequest;
use Illuminate\Http\Request;

class APIMedicalReportController extends Controller
{

    public function fetchMedicalReportData(Request $request, $encReportId)
    {
       return (new FetchMedicalReportData())->_handleApi($request,$encReportId);
    }

    public function saveMedicalReportSettings(SaveMedicalReportRequest $request)
    {
       return (new SaveMedicalReport())->_handleApi($request);
    }

    public function deleteMedicalReport(Request $request)
    {
        return (new DeleteMedicalReport())->_handleApi($request);
    }

}


