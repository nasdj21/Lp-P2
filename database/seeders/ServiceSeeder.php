<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('service')->insert([
            [
                'name' => 'Terapia Individual',
                'price' => 45.00,
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'name' => 'Terapia de Pareja',
                'price' => 60.00,
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'name' => 'Terapia Familiar',
                'price' => 75.00,
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'name' => 'Terapia Infantil',
                'price' => 50.00,
                'created_by' => 'system',
                'creation_date' => now()
            ],
        ]);

        // Insertar relación profesionales-servicios
        DB::table('professional_service')->insert([
            [
                'service_id' => 1,
                'person_id' => 2,
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'service_id' => 2,
                'person_id' => 2,
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'service_id' => 3,
                'person_id' => 3,
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'service_id' => 4,
                'person_id' => 4,
                'created_by' => 'system',
                'creation_date' => now()
            ],
        ]);
    }
}
