<?php


namespace App\Features\Profile;
use App\Common\CommonUtil;
use App\Contracts\HTTPStatusCode;
use App\Features\BaseApi;
use App\Models\CriminalRecord;
use App\Models\House;
use App\Models\MedicalReport;
use App\Models\PoliceReport;
use App\Models\ProfileRecord;
use App\Traits\APIResponder;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DeleteProfile extends BaseApi
{
    public function __construct() {

    }

    public function _handleAPI(Request $request ){

        try {
            $this->request = $request;
            $this->_decryptToken();
            $requestData = $request->all();
            $profileId = CommonUtil::decrypt(CommonUtil::fetch($requestData,'profile_id'));

            if($profileId) {
                $data = [
                    'deleted_at' => Carbon::now(),
                    'updated_by' => $this->userId,
                ];
                $criminalRecords = CriminalRecord::getCriminalRecordsByProfileRecordId($profileId);
                foreach ($criminalRecords as $criminalRecord){
                    CriminalRecord::manageCriminalRecord($data,$criminalRecord->record_id);
                }

                $policeReports = PoliceReport::getPoliceReportsProfileId($profileId);

                foreach ($policeReports as $policeReport) {
                    PoliceReport::managePoliceReport($data,$policeReport->report_id);
                }

                $medicalReports = MedicalReport::getMedicalReportsProfileId($profileId);
                foreach ($medicalReports as $medicalReport) {
                    MedicalReport::manageMedicalReport($data,$medicalReport->report_id);
                }

//                $linkedHouses = House::getAllLinkedHousesByProfileId($profileId);
//
//                foreach ($linkedHouses as $house){
//                    $houseData = [
//                        'linked_profile_id' => null,
//                        'updated_by' => $this->userId,
//                    ];
//                    House::manageHouse($houseData,$house->house_id);
//                }

                $this->responseData =  CommonUtil::makeKeyValue('success_message','Records Expunged');

            }else{
                $this->responseData[] = CommonUtil::makeKeyValue('error','Something went wrong');
                $this->statusCode = HTTPStatusCode::UNPROCESSABLE_ENTITY;
            }

            return $this->_respondApi();
        } catch (\Throwable $exception) {
            return APIResponder::respondInternalError();
        }

    }





}
