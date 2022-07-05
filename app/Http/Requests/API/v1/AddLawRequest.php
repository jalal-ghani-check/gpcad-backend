<?php

namespace App\Http\Requests\API\v1;

use App\Common\CommonUtil;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class AddLawRequest extends FormRequest {

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
            'name' => 'required',
            'law_code' => 'required',
            'crime_type' => 'required',
            'points' => 'required|numeric',
            'fine_amount' => 'required|numeric',
            'description' => 'required',
            'jail_time' => 'required|numeric',

        ];
  }



  public function failedValidation(Validator $validator) {
    $ret=CommonUtil::makeRequestResponseKeyValue($validator->errors()->messages());
    throw new \App\Exceptions\ApiParamsValidationException(response()->json($ret, 422));
  }

}
