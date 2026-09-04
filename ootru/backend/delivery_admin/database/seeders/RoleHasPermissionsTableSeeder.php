<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleHasPermissionsTableSeeder extends Seeder
{
    public function run()
    {
        DB::table('role_has_permissions')->delete();

        $permissions = DB::table('permissions')->pluck('id');
        $data = [];
        foreach ($permissions as $permission_id) {
            $data[] = [
                'permission_id' => $permission_id,
                'role_id' => 1,
            ];
        }
        if (!empty($data)) {
            DB::table('role_has_permissions')->insert($data);
        }
    }
}