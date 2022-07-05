<?php

namespace App\Http\Requests\API\v1;

use App\Common\CommonUtil;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class UpdateUserRequest extends FormRequest {

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
            'username' => 'required',
            'profile_picture' => 'required',
            'call_sign' => 'required',
            'citizen_id' => 'required',
            'full_name' => 'required',
            'enc_user_id' => 'required',
            'enc_rank_id' => 'required',
            'enc_department_id' => 'required',
            'is_allowed_to_approve_warrants' => 'required',
            'is_allowed_to_create_laws' => 'required',
            'is_allowed_to_create_profile' => 'required',
            'is_allowed_to_create_medical_reports' => 'required',
            'is_allowed_to_create_police_reports' => 'required',
            'is_allowed_to_create_warrants' => 'required',
            'is_allowed_to_delete_laws' => 'required',
            'is_allowed_to_delete_medical_reports' => 'required',
            'is_allowed_to_delete_police_reports' => 'required',
            'is_allowed_to_delete_warrants' => 'required',
            'is_allowed_to_edit_laws' => 'required',
            'is_allowed_to_edit_profile' => 'required',
            'is_allowed_to_edit_medical_reports' => 'required',
            'is_allowed_to_edit_police_reports' => 'required',
            'is_allowed_to_edit_warrants' => 'required',
            'is_allowed_to_expunge_records' => 'required',
            'is_allowed_to_high_commands' => 'required',
            'is_allowed_to_serve_warrants' => 'required',
            'is_allowed_to_view_bails' => 'required',
            'is_allowed_to_view_charges' => 'required',
            'is_allowed_to_view_laws' => 'required',
            'is_allowed_to_view_profile' => 'required',
            'is_allowed_to_view_medical_reports' => 'required',
            'is_allowed_to_view_police_reports' => 'required',
            'is_allowed_to_view_warrants' => 'required',


        ];
  }

  public function failedValidation(Validator $validator) {
    $ret=CommonUtil::makeRequestResponseKeyValue($validator->errors()->messages());
    throw new \App\Exceptions\ApiParamsValidationException(response()->json($ret, 422));
  }

}
