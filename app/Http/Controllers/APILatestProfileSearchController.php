<?php

namespace App\Http\Controllers;

use App\Features\LatestProfileSearch\FetchLatestSearchProfiles;
use App\Features\LatestProfileSearch\ManageProfileSearch;
use Illuminate\Http\Request;

class APILatestProfileSearchController extends Controller
{
    public function fetchLatestProfileSearches (Request $request)
    {
      return (new FetchLatestSearchProfiles())->_handleAPI($request);
    }

    public function manageLatestProfileSearch (Request $request, $profileId)
    {
      return (new ManageProfileSearch())->_handleAPI($request, $profileId);
    }
}
