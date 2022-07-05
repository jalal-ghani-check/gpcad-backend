<?php

namespace Database\Seeders;

use App\Models\UserPermissions;
use Illuminate\Database\Seeder;

class UserPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $general_permissions = [
            'is_allowed_to_approve_warrants' => UserPermissions::IS_ALLOWED_TO_APPROVE_WARRANTS,
            'is_allowed_to_create_laws' => UserPermissions::IS_ALLOWED_TO_CREATE_LAWS,
            'is_allowed_to_create_profile' => UserPermissions::IS_ALLOWED_TO_CREATE_PROFILE,
            'is_allowed_to_create_medical_reports' => UserPermissions::IS_ALLOWED_TO_CREATE_MEDICAL_REPORTS,
            'is_allowed_to_create_police_reports' => UserPermissions::IS_ALLOWED_TO_CREATE_POLICE_REPORTS,
            'is_allowed_to_create_warrants' => UserPermissions::IS_ALLOWED_TO_CREATE_WARRANTS,
            'is_allowed_to_delete_laws' => UserPermissions::IS_ALLOWED_TO_DELETE_LAWS,
            'is_allowed_to_delete_medical_reports' => UserPermissions::IS_ALLOWED_TO_DELETE_MEDICAL_REPORTS,
            'is_allowed_to_delete_police_reports' => UserPermissions::IS_ALLOWED_TO_DELETE_POLICE_REPORTS,
            'is_allowed_to_delete_warrants' => UserPermissions::IS_ALLOWED_TO_DELETE_WARRANTS,
            'is_allowed_to_edit_laws' => UserPermissions::IS_ALLOWED_TO_EDIT_LAWS,
            'is_allowed_to_edit_profile' => UserPermissions::IS_ALLOWED_TO_EDIT_PROFILE,
            'is_allowed_to_edit_medical_reports' => UserPermissions::IS_ALLOWED_TO_EDIT_MEDICAL_REPORTS,
            'is_allowed_to_edit_police_reports' => UserPermissions::IS_ALLOWED_TO_EDIT_POLICE_REPORTS,
            'is_allowed_to_edit_warrants' => UserPermissions::IS_ALLOWED_TO_EDIT_WARRANTS,
            'is_allowed_to_expunge_records' => UserPermissions::IS_ALLOWED_TO_EXPUNGE_RECORDS,
            'is_allowed_to_high_commands' => UserPermissions::IS_ALLOWED_TO_HIGH_COMMANDS,
            'is_allowed_to_serve_warrants' => UserPermissions::IS_ALLOWED_TO_SERVE_WARRANTS,
            'is_allowed_to_view_bails' => UserPermissions::IS_ALLOWED_TO_VIEW_BAILS,
            'is_allowed_to_view_charges' => UserPermissions::IS_ALLOWED_TO_VIEW_CHARGES,
            'is_allowed_to_view_laws' => UserPermissions::IS_ALLOWED_TO_VIEW_LAWS,
            'is_allowed_to_view_profile' => UserPermissions::IS_ALLOWED_TO_VIEW_PROFILE,
            'is_allowed_to_view_medical_reports' => UserPermissions::IS_ALLOWED_TO_VIEW_MEDICAL_REPORTS,
            'is_allowed_to_view_police_reports' => UserPermissions::IS_ALLOWED_TO_VIEW_POLICE_REPORTS,
            'is_allowed_to_view_warrants' => UserPermissions::IS_ALLOWED_TO_VIEW_WARRANTS,


        ];
        foreach ($general_permissions as $key => $value) {
            $value["updated_by"] = 0;
            UserPermissions::manageUserPermissions($value, $key);
        }
    }
}
