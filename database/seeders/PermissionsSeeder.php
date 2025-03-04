<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'Create Lead', 'guard_name' => 'web', 'p_group' => 1],
            ['name' => 'View Leads', 'guard_name' => 'web', 'p_group' => 1],
            ['name' => 'View All Leads', 'guard_name' => 'web', 'p_group' => 1],
            ['name' => 'Edit Lead', 'guard_name' => 'web', 'p_group' => 1],
            ['name' => 'Delete Lead', 'guard_name' => 'web', 'p_group' => 1],
            ['name' => 'View Stats', 'guard_name' => 'web', 'p_group' => 3],
            ['name' => 'Lead Reports', 'guard_name' => 'web', 'p_group' => 6],
            ['name' => 'Create User', 'guard_name' => 'web', 'p_group' => 5],
            ['name' => 'View User', 'guard_name' => 'web', 'p_group' => 5],
            ['name' => 'Edit User', 'guard_name' => 'web', 'p_group' => 5],
            ['name' => 'Delete User', 'guard_name' => 'web', 'p_group' => 5],
            ['name' => 'View All Paid Leads', 'guard_name' => 'web', 'p_group' => 1],
            ['name' => 'View New Registrations Leads', 'guard_name' => 'web', 'p_group' => 1],
            ['name' => 'View Paid Leads', 'guard_name' => 'web', 'p_group' => 1],
            ['name' => 'View Scheduled Leads', 'guard_name' => 'web', 'p_group' => 1],
            ['name' => 'View Active Leads', 'guard_name' => 'web', 'p_group' => 1],
            ['name' => 'View Closed Leads', 'guard_name' => 'web', 'p_group' => 1],
            ['name' => 'Authenticated', 'guard_name' => 'web', 'p_group' => 5],
            ['name' => 'is_supervisor', 'guard_name' => 'web', 'p_group' => 7],
            ['name' => 'is_manager', 'guard_name' => 'web', 'p_group' => 8]

        ];

        foreach ($permissions as &$permission) {
            $permission['created_at'] = Carbon::now();
            $permission['updated_at'] = Carbon::now();
        }

        DB::table('permissions')->insert($permissions);
    }
}
