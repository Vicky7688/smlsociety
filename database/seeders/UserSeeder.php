<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Shanu',
                'mobile' => '8628914999',
                'email' => 'dhanaikshanu@gmail.com',
                'username' => 'dhyanish',
                'password' => Hash::make('12345678'),
                'status' => 'Active',
                'role' => 1
            ],
            [
                'name' => 'Admin',
                'mobile' => '1234567890',
                'email' => 'admin@gmail.com',
                'username' => 'admin',
                'password' => Hash::make('12345678'),
                'status' => 'Active',
                'role' => 1
            ]
        ]);
    }
}
