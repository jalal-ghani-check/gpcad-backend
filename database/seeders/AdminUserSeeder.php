<?php

namespace Database\Seeders;

use App\Models\Users\Role;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataToSave = array(
            'user_id' => 1,
            'full_name' => 'Admin',
            'username' => 'superadmin',
            'citizen_id' => 'admin001',
            'profile_picture' => '-',
            'gender' => 'Male',
            'rank_id' =>0,
            'department_id' => 0,
            'call_sign' => '-',
            'role_id' => Role::ROLE_ID_ADMIN,
            'password_salt' => '',
            'password' => Hash::make('Test#12345'),
            'updated_by' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        );
        return User::manageUser($dataToSave,1);
    }
}
