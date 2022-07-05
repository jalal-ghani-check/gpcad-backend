<?php

namespace App\Http\Requests\API\v1;

use App\Common\CommonUtil;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class RegisterRequest extends FormRequest {

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
          'full_name' => 'required|max:255',
          'username' => 'required|string|max:255',
          'password' => 'required|min:8|max:128',
          'profile_picture' => 'required',
          'gender' => 'required',
          'rank_id' => 'required',
          'citizen_id' => 'required',
          'department_id' => 'required',
          'call_sign' => 'required',
          'role_id' => 'required',

        ];
  }



  public function failedValidation(Validator $validator) {
    $ret=CommonUtil::makeRequestResponseKeyValue($validator->errors()->messages());
    throw new \App\Exceptions\ApiParamsValidationException(response()->json($ret, 422));
  }

}
