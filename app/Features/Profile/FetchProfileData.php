<?php


namespace App\Features\Profile;
use App\Common\CommonUtil;
use App\Contracts\HTTPStatusCode;
use App\Features\BaseApi;
use App\Features\Reports\FetchAllReports;
use App\Models\CriminalRecord;
use App\Models\House;
use App\Models\Law;
use App\Models\ProfileRecord;
use App\Traits\APIResponder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FetchProfileData extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request,$encProfileId ){

        try {
            $this->request = $request;
            $this->_decryptToken();
            $profileId = CommonUtil::decrypt($encProfileId);
            if($profileId){
                $response = $this->prepareProfileData($profileId);

                if(CommonUtil::fetch($response,'status')){
                    $this->responseData = CommonUtil::fetch($response,'data');
                }else {
                    $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;
                    $this->responseData[] = CommonUtil::fetch($response,'error');
                }

            }else{
                $this->responseData[] = CommonUtil::makeKeyValue('invalid_id','Something went wrong');
                $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;
            }
            return $this->_respondApi();
        } catch (\Throwable $exception) {
            return APIResponder::respondInternalError();
        }

    }

    public function prepareProfileData($profileId){
        $profile = ProfileRecord::getProfileRecordByProfileId($profileId);

        $ret = [
            'status' => false,
        ];

        if($profile) {

            $crimesData = $this->getChargesList($profileId);

            $data = [
                'profile_id' => $profileId,
                'enc_profile_id' => CommonUtil::encrypt($profileId),
                'full_name' => CommonUtil::fetchFromObject($profile,'full_name'),
                'designation' => CommonUtil::fetchFromObject($profile,'designation'),
                'gender' => CommonUtil::fetchFromObject($profile,'gender'),
                'dob' => Carbon::parse(CommonUtil::fetchFromObject($profile,'dob'))->toFormattedDateString(),
                'age' => CommonUtil::calculateAge(CommonUtil::fetchFromObject($profile,'dob')),
                'address' => CommonUtil::fetchFromObject($profile,'address'),
                'citizen_id' => CommonUtil::fetchFromObject($profile,'citizen_id'),
                'finger_print' => CommonUtil::fetchFromObject($profile,'finger_print'),
                'dna_code' => CommonUtil::fetchFromObject($profile,'dna_code'),
                'points' => $crimesData['points'],
                'is_driver_license_valid' => CommonUtil::fetchFromObject($profile,'is_driver_license_valid'),
                'is_weapon_license_valid' => CommonUtil::fetchFromObject($profile,'is_weapon_license_valid'),
                'is_pilot_license_valid' => CommonUtil::fetchFromObject($profile,'is_pilot_license_valid'),
                'is_hunting_license_valid' => CommonUtil::fetchFromObject($profile,'is_hunting_license_valid'),
                'is_fishing_license_valid' => CommonUtil::fetchFromObject($profile,'is_fishing_license_valid'),
                'jury_duty' => CommonUtil::fetchFromObject($profile,'jury_duty'),
                'houseList' => self::getHousesLinked($profileId),
                'laws_details' => $crimesData['laws_details'],
                'reports' => $this->getReportsList($profileId)
            ];
            $ret['status'] = true;
            $ret['data'] = $data;
        }else{
            $ret['status'] = false;
            $ret['error'] = CommonUtil::makeKeyValue('not_found','Profile not found against given Id');
        }

        return $ret;


    }

    public function getHousesLinked($ProfileId) {
        $houses = House::getAllLinkedHousesByProfileId($ProfileId);
        $ret = [];
        foreach ($houses as $house){
            $ret[] = [
                'house_name' => CommonUtil::fetchFromObject($house,'house_name'),
                'image' => route('house-picture',['house_id' => CommonUtil::encrypt(CommonUtil::fetchFromObject($house, 'house_id'))]),
                'price' => number_format(CommonUtil::fetchFromObject($house,'price')),
            ];
        }
        return $ret;
    }

    public function getChargesList($profileId) {
        $ret = [];
        $points = 0;
        $crimeRecords = CriminalRecord::getCriminalRecordsByProfileRecordId($profileId);
        $lawsCount= [];
        $lawDetails = [];
        foreach ($crimeRecords as $record) {
            if(array_key_exists($record->law_id,$lawsCount)){
                $lawsCount[$record->law_id] += 1;
            }else{
                $lawsCount[$record->law_id] = 1;
            }

        }
        $crimeClasses = Law::CRIME_TYPE_COLOR_CLASS;
        foreach ($lawsCount as $lawId => $numberOfTimes){
            $law = Law::getLaw($lawId);
            $lawDetails [] = [
                'name' => CommonUtil::fetchFromObject($law,'name'),
                'crime_type' => ucfirst(CommonUtil::fetchFromObject($law,'crime_type')),
                'crime_class' => CommonUtil::fetch($crimeClasses,CommonUtil::fetchFromObject($law,'crime_type')),
                'number_of_times' => $numberOfTimes,
            ];
            $points += CommonUtil::fetch($law,'points');
        }
        $ret['laws_details'] = $lawDetails;
        $ret['points'] = $points;
        return $ret;
    }


    public function getReportsList($profileId) {
        $reportObj = new FetchAllReports();
        $policeReports = $reportObj->preparePoliceReports($profileId);
        $medicalReports = $reportObj->prepareMedicalReports($profileId);
        $allReports = array_merge($policeReports, $medicalReports);
        $reportsSorted = collect($allReports)->sortByDesc('created_at')->values()->all();
        return $reportsSorted;
    }




}
