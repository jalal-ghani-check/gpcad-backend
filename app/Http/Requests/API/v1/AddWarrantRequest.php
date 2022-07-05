<?php

namespace App\Http\Requests\API\v1;

use App\Common\CommonUtil;
use App\Models\Users\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;

class AddWarrantRequest extends FormRequest {

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
    public function rules(Request $request) {
        $rules =  [
            'title' => 'required',
            'description' => 'required',
            'profile_id' => 'required',

        ];
        return $rules;
  }

  public function failedValidation(Validator $validator) {
    $ret=CommonUtil::makeRequestResponseKeyValue($validator->errors()->messages());
    throw new \App\Exceptions\ApiParamsValidationException(response()->json($ret, 422));
  }

}
