<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('role')->insert([
            ['name' => 'Admin', 'created_by' => 'system', 'creation_date' => now()],
            ['name' => 'Professional', 'created_by' => 'system', 'creation_date' => now()],
            ['name' => 'Client', 'created_by' => 'system', 'creation_date' => now()],
            ['name' => 'Staff', 'created_by' => 'system', 'creation_date' => now()],
        ]);
    }
}
