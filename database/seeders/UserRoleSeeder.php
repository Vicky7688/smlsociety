<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('user_role_masters')->insert([
            ['name'=>'Super Admin', 'slug'=>'super-admin', 'status'=>'Active'],
            ['name'=>'Admin', 'slug'=>'admin', 'status'=>'Active'],
            ['name'=>'Staff', 'slug'=>'staff', 'status'=>'Active'],
            ['name'=>'Agent', 'slug'=>'agent', 'status'=>'Active']
        ]);
    }
}
