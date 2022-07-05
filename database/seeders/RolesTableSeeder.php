<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Users\Role;

class RolesTableSeeder extends Seeder {


  public function run() {
    $roles = [
          [
            'role_id' => 1,
            'role_name' => 'Admin',
            'role_key' => Role::ROLE_KEY_ADMIN,
          ],
          [
            'role_id' => 2,
            'role_name' => 'DOJ',
            'role_key' => Role::ROLE_KEY_DOJ,
          ],
          [
            'role_id' => 3,
            'role_name' => 'DOC',
            'role_key' => Role::ROLE_KEY_DOC,
          ],
          [
            'role_id' => 4,
            'role_name' => 'PD HC',
            "role_key" => Role::ROLE_KEY_PD_HIGH_COMMAND,
          ],
        [
            'role_id' => 5,
            'role_name' => 'Police',
            "role_key" => Role::ROLE_KEY_POLICE,
        ],
        [
            'role_id' => 6,
            'role_name' => 'Judge',
            "role_key" => Role::ROLE_KEY_JUDGE,
        ],
        [
            'role_id' => 7,
            'role_name' => 'EMS',
            "role_key" => Role::ROLE_KEY_EMS,
        ],
        [
            'role_id' => 8,
            'role_name' => 'Attorney',
            "role_key" => Role::ROLE_KEY_ATTORNEY,
        ],
    ];

    foreach ($roles as $role) {
      \App\Models\Users\Role::manageRoles($role, $role['role_key']);
    }
  }

}
