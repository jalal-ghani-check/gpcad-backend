<?php

namespace Database\Seeders;

use App\Models\Users\Role;
use App\Models\Users\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class ClientAdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $dataToSave = array(
            'full_name' => 'Tez',
            'username' => 'Tez',
            'citizen_id' => 'admin002',
            'profile_picture' => '-',
            'gender' => 'Male',
            'rank_id' =>0,
            'department_id' => 0,
            'call_sign' => '-',
            'role_id' => Role::ROLE_ID_ADMIN,
            'password_salt' => '',
            'password' => Hash::make('Chelsea10!'),
            'updated_by' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        );
        User::manageUser($dataToSave);

        $dataToSave = array(
            'full_name' => 'George',
            'username' => 'George',
            'citizen_id' => 'admin003',
            'profile_picture' => '-',
            'gender' => 'Male',
            'rank_id' =>0,
            'department_id' => 0,
            'call_sign' => '-',
            'role_id' => Role::ROLE_ID_ADMIN,
            'password_salt' => '',
            'password' => Hash::make('Godsplan123!'),
            'updated_by' => 0,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        );
        User::manageUser($dataToSave);
    }
}
