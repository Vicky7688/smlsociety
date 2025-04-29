<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleMastersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Define the data to be inserted
        $roles = [
            ['name' => 'Super Admin', 'slug' => 'super-admin', 'status' => 'Active'],
            ['name' => 'Admin', 'slug' => 'admin', 'status' => 'Active'],
            ['name' => 'Staff', 'slug' => 'staff', 'status' => 'Active'],
            ['name' => 'Agent', 'slug' => 'agent', 'status' => 'Active'],
        ];

        // Insert data into the user_role_masters table
        DB::table('user_role_masters')->insert($roles);
    }
}
