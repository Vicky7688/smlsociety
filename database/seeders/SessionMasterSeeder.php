<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SessionMasterSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('session_masters')->truncate();
        DB::table('session_masters')->insert(
            [
                'startDate' => '2024-04-01',
                'endDate' => '2025-03-31',
                'status' => 'Active',
                'auditPerformed' => 'No',
            ]
        );
    }
}
