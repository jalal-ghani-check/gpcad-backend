<?php

namespace App\Http\Controllers\API\v1\Reports;

use App\Features\Reports\FetchAllReports;
use App\Features\Reports\FetchProfileReports;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class APIReportController extends Controller
{

    public function fetchReports(Request $request)
    {
       return (new FetchAllReports())->_handleApi($request);
    }
    public function fetchProfileReports(Request $request,$profileId)
    {
        return (new FetchProfileReports())->_handleApi($request,$profileId);
    }
}


