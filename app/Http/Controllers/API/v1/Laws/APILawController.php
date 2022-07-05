<?php

namespace App\Http\Controllers\API\v1\Laws;

use App\Features\Laws\AddLaw;
use App\Features\Laws\DeleteLaw;
use App\Features\Laws\FetchLaws;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\AddLawRequest;
use Illuminate\Http\Request;

class APILawController extends Controller
{

    public function fetchLaws(Request $request)
    {
        return (new FetchLaws())->_handleApi($request);
    }
    public function manageLaw(AddLawRequest $request)
    {
        return (new AddLaw())->_handleApi($request);
    }
    public function deleteLaw(Request $request, $lawId)
    {
        return (new DeleteLaw())->_handleApi($request, $lawId);
    }

}


