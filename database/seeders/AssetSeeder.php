<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AssetSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        DB::table('assets')->insert([
            [
                'assets_name' => 'Users',
                'created_at' => now(),
                'updated_at' => now(),
                'route_name' => 'users.index',
            ],
            [
                'assets_name' => 'Roles',
                'created_at' => now(),
                'updated_at' => now(),
                'route_name' => 'roles.index',
            ],
            [
                'assets_name' => 'Role Permissions',
                'created_at' => now(),
                'updated_at' => now(),
                'route_name' => 'role_permissions.index',
            ]
        ]);
    }
}
