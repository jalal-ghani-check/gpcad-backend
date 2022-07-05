<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Users\Role;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class DepartmentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      Department::query()->truncate();
      $departments = [
        [
          'depart_id' => 1,
          'depart_name' => 'Medicine',
          'depart_key' => Department::DEPARTMENT_KEY_MEDICINE,
          'created_by' => 1,
          'updated_by' => 1,
          'created_at' => Carbon::now()
        ],
        [
          'depart_id' => 2,
          'depart_name' => 'Judiciary',
          'depart_key' => Department::DEPARTMENT_KEY_JUDICIARY,
          'created_by' => 1,
          'updated_by' => 1,
          'created_at' => Carbon::now()
        ],
        [
          'depart_id' => 3,
          'depart_name' => 'LEO',
          'depart_key' => Department::DEPARTMENT_KEY_LEO,
          'created_by' => 1,
          'updated_by' => 1,
          'created_at' => Carbon::now()
        ],
        [
          'depart_id' => 4,
          'depart_name' => 'Real Estate',
          'depart_key' => Department::DEPARTMENT_KEY_REAL_ESTATE,
          'created_by' => 1,
          'updated_by' => 1,
          'created_at' => Carbon::now()
        ],

      ];

      foreach ($departments as $department) {
        Department::manageDepartment($department);
      }
    }
}
