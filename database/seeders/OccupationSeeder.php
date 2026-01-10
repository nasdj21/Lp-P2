<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OccupationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('occupation')->insert([
            ['name' => 'Doctor'],
            ['name' => 'Enfermero'],
            ['name' => 'Ingeniero'],
            ['name' => 'Estudiante'],
        ]);
    }
}
