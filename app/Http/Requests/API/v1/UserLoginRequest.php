<?php

namespace App\Http\Requests\API\v1;

use App\Common\CommonUtil;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Request;
use Illuminate\Contracts\Validation\Validator;

class UserLoginRequest extends FormRequest {

  protected $redirectRoute='login';

  public function authorize() {
    return true;
  }

  public function rules(Request $request)
  {
    $rules = [
      'password' => 'required|min:8|max:128',
    ];
    $rules['username'] = 'required|string';

    return $rules;
  }

  public function failedValidation(Validator $validator) {
    $ret=CommonUtil::makeRequestResponseKeyValue($validator->errors()->messages());
    throw new \App\Exceptions\ApiParamsValidationException(response()->json($ret, 422));
  }

}
