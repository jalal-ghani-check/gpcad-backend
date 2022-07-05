<?php

namespace App\Http\Controllers\API\v1\PoliceReport;

use App\Features\PoliceReport\DeletePoliceReport;
use App\Features\PoliceReport\FetchPoliceReportData;
use App\Features\PoliceReport\SavePoliceReport;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\SavePoliceReportRequest;
use Illuminate\Http\Request;

class APIPoliceReportController extends Controller
{

    public function fetchPoliceReportData(Request $request, $encReportId)
    {
       return (new FetchPoliceReportData())->_handleApi($request,$encReportId);
    }

  public function savePoliceReportSettings(SavePoliceReportRequest $request)
  {
    return (new SavePoliceReport())->_handleApi($request);
  }

    public function deletePoliceReport(Request $request)
    {
        return (new DeletePoliceReport())->_handleApi($request);
    }



}


