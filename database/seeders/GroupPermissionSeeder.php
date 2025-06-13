<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GroupPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $groupPermissions = [
            ['group_id' => 1, 'permission_id' => 1],
            ['group_id' => 1, 'permission_id' => 2],
            ['group_id' => 1, 'permission_id' => 3],
            ['group_id' => 1, 'permission_id' => 4],
            ['group_id' => 1, 'permission_id' => 5],
        ];

        DB::table('group_permission')->insert($groupPermissions);
    }
}
