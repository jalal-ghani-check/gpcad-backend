<?php

namespace App\Http\Requests\API\v1;

use App\Common\CommonUtil;
use App\Models\Users\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;

class AddUserRequest extends FormRequest {

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
        $roleId = CommonUtil::fetchFromObject($request,'enc_role_id');
        $roleId = CommonUtil::decrypt($roleId);
        $rules =  [
            'username' => 'required',
            'password' => 'required',
            'profile_picture' => 'required',
            'call_sign' => 'required',
            'citizen_id' => 'required',
            'full_name' => 'required',
            'enc_rank_id' => 'required',

        ];
        if($roleId == Role::ROLE_ID_ADMIN){
            $rules['enc_role_id'] = 'required';
        }
        return $rules;
  }

  public function failedValidation(Validator $validator) {
    $ret=CommonUtil::makeRequestResponseKeyValue($validator->errors()->messages());
    throw new \App\Exceptions\ApiParamsValidationException(response()->json($ret, 422));
  }

}
