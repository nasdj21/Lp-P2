<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ScheduleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('schedule')->insert([
            [
                'date' => '2026-01-13',
                'start_time' => '08:00:00',
                'end_time' => '09:00:00',
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'date' => '2026-01-13',
                'start_time' => '09:00:00',
                'end_time' => '10:00:00',
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'date' => '2026-01-13',
                'start_time' => '10:00:00',
                'end_time' => '11:00:00',
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'date' => '2026-01-14',
                'start_time' => '14:00:00',
                'end_time' => '15:00:00',
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'date' => '2026-01-14',
                'start_time' => '15:00:00',
                'end_time' => '16:00:00',
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'date' => '2026-01-14',
                'start_time' => '16:00:00',
                'end_time' => '17:00:00',
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'date' => '2026-01-15',
                'start_time' => '08:00:00',
                'end_time' => '09:00:00',
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'date' => '2026-01-15',
                'start_time' => '09:00:00',
                'end_time' => '10:00:00',
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'date' => '2026-01-16',
                'start_time' => '14:00:00',
                'end_time' => '15:00:00',
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'date' => '2026-01-16',
                'start_time' => '15:00:00',
                'end_time' => '16:00:00',
                'created_by' => 'system',
                'creation_date' => now()
            ],
        ]);

        // Insertar horarios de trabajadores
        DB::table('worker_schedule')->insert([
            [
                'schedule_id' => 1,
                'person_id' => 2,
                'is_available' => false,
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'schedule_id' => 2,
                'person_id' => 2,
                'is_available' => true,
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'schedule_id' => 3,
                'person_id' => 2,
                'is_available' => true,
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'schedule_id' => 4,
                'person_id' => 3,
                'is_available' => false,
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'schedule_id' => 5,
                'person_id' => 3,
                'is_available' => true,
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'schedule_id' => 6,
                'person_id' => 3,
                'is_available' => true,
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'schedule_id' => 7,
                'person_id' => 4,
                'is_available' => false,
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'schedule_id' => 8,
                'person_id' => 4,
                'is_available' => true,
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'schedule_id' => 9,
                'person_id' => 4,
                'is_available' => true,
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'schedule_id' => 10,
                'person_id' => 2,
                'is_available' => true,
                'created_by' => 'system',
                'creation_date' => now()
            ],
        ]);
    }
}
