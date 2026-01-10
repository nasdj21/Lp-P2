<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AppointmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('payment')->insert([
            [
                'person_id' => 5,
                'service_id' => 1,
                'status_id' => 2,
                'file' => 'https://storage.aspy.com/payments/payment_001.pdf',
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'person_id' => 6,
                'service_id' => 3,
                'status_id' => 2,
                'file' => 'https://storage.aspy.com/payments/payment_002.pdf',
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'person_id' => 5,
                'service_id' => 4,
                'status_id' => 2,
                'file' => 'https://storage.aspy.com/payments/payment_003.pdf',
                'created_by' => 'system',
                'creation_date' => now()
            ],
        ]);

        // Insertar recibos
        DB::table('receipt')->insert([
            [
                'payment_id' => 1,
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'payment_id' => 2,
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'payment_id' => 3,
                'created_by' => 'system',
                'creation_date' => now()
            ],
        ]);

        // Insertar citas
        DB::table('appointment')->insert([
            [
            'payment_id' => 1,
            'scheduled_by' => 7,
            'worker_schedule_id' => 1,
            'status' => 2,
            'created_by' => 'system',
            'creation_date' => now()
            ],
            [
            'payment_id' => 2,
            'scheduled_by' => 7,
            'worker_schedule_id' => 4,
            'status' => 2,
            'created_by' => 'system',
            'creation_date' => now()
            ],
            [
            'payment_id' => 3,
            'scheduled_by' => 7,
            'worker_schedule_id' => 7,
            'status' => 2,
            'created_by' => 'system',
            'creation_date' => now()
            ],
        ]);
        // Insertar reportes de citas
        DB::table('appointment_report')->insert([
            [
                'appointment_id' => 1,
                'file' => 'proximamente',
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'appointment_id' => 2,
                'file' => 'proximamente',
                'created_by' => 'system',
                'creation_date' => now()
            ],
            [
                'appointment_id' => 3,
                'file' => 'proximamente',
                'created_by' => 'system',
                'creation_date' => now()
            ],
        ]);

    }
}
