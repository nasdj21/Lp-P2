<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppointmentStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('appointment_status')->insert([
            ['name' => 'Scheduled'],
            ['name' => 'Completed'],
            ['name' => 'Pending Approval'],
        ]);
    }
}
