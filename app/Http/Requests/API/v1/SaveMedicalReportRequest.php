<?php

namespace App\Http\Requests\API\v1;

use App\Common\CommonUtil;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class SaveMedicalReportRequest extends FormRequest {

  /**
   * Determine if the user is authorized to make this request.
   *
   * @return bool
   */
  public function authorize() {
    return true;
  }

  /**
   * Get the validation rules that apply to the request.
   *
   * @return array
   */
    public function rules() {
        return [
            'profileId' => 'required',
            'reportTitle' => 'required',
            'citizenId' => 'required',
            'problemDescription' => 'required',
            'problemStartedAt' => 'required',
            'problemCause' => 'required',
            'medicalHistory' => 'required',
            'religiousCulturalViews' => 'required',
        ];
  }

  public function failedValidation(Validator $validator) {
    $ret=CommonUtil::makeRequestResponseKeyValue($validator->errors()->messages());
    throw new \App\Exceptions\ApiParamsValidationException(response()->json($ret, 422));
  }

}
