<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermissionGroupsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            ['id' => 1, 'group_name' => 'Leads', 'active' => 1, 'order' => 1, 'created_by' => 1, 'updated_by' => 1],
            ['id' => 2, 'group_name' => 'SMS', 'active' => 1, 'order' => 2, 'created_by' => 1, 'updated_by' => 1],
            ['id' => 3, 'group_name' => 'Stats Pages', 'active' => 1, 'order' => 3, 'created_by' => 1, 'updated_by' => 1],
            ['id' => 4, 'group_name' => 'DashBoard', 'active' => 1, 'order' => 4, 'created_by' => 1, 'updated_by' => 1],
            ['id' => 5, 'group_name' => 'Users', 'active' => 1, 'order' => 5, 'created_by' => 1, 'updated_by' => 1],
            ['id' => 6, 'group_name' => 'Reports', 'active' => 1, 'order' => 6, 'created_by' => 1, 'updated_by' => 1],
            ['id' => 7, 'group_name' => 'Supervisor', 'active' => 1, 'order' => 7, 'created_by' => 1, 'updated_by' => 1],
            ['id' => 8, 'group_name' => 'Manager', 'active' => 1, 'order' => 8, 'created_by' => 1, 'updated_by' => 1],
        ];

        foreach ($data as &$row) {
            $row['created_at'] = Carbon::now();
            $row['updated_at'] = Carbon::now();
        }

        DB::table('permission_groups')->insert($data);
    }
}
