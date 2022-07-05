<?php


namespace App\Features\Laws;
use App\Common\CommonUtil;
use App\Features\BaseApi;
use App\Models\Law;
use App\Traits\APIResponder;
use Illuminate\Http\Request;

class FetchLaws extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request ){

        try {
            $this->request = $request;
            $this->_decryptToken();
            $this->responseData = $this->prepareAllLawsData();

            return $this->_respondApi();
        } catch (\Throwable $exception) {
            return APIResponder::respondInternalError();
        }

    }

    public function prepareAllLawsData()
    {
      $laws = Law::getAll();

      $data = [
          Law::CRIME_TYPE_INFRACTION => [],
          Law::CRIME_TYPE_MISDEMEANOR => [],
          Law::CRIME_TYPE_FELONY => [],

      ];

      if($laws) {
        foreach ($laws as $record) {
          $data[$record->crime_type][] = [
            'law_id' => CommonUtil::encrypt(CommonUtil::fetchFromObject($record, 'law_id')),
            'law_title' => CommonUtil::fetchFromObject($record, 'name', 'N/A'),
            'law_code' => CommonUtil::fetchFromObject($record, 'law_code', 'N/A'),
            'fine_amount' => number_format(CommonUtil::fetchFromObject($record, 'fine_amount'), '2', '.', ','),
            'description' => CommonUtil::fetchFromObject($record, 'description'),
            'points' => CommonUtil::fetchFromObject($record, 'points'),
            'duration' => CommonUtil::fetchFromObject($record, 'jail_time', 'N/A'),
            'crime_type' => CommonUtil::fetchFromObject($record, 'crime_type', 'N/A'),
            'type_name' => ucfirst($record->crime_type),
            'color_class' => Law::CRIME_TYPE_COLOR_CLASS[$record->crime_type]
          ];
        }
      }

      return $data;
    }






}
