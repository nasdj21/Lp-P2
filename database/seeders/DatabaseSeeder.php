<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Catálogos básicos
            RoleSeeder::class,
            UserAccountStatusSeeder::class,
            AppointmentStatusSeeder::class,
            PaymentStatusSeeder::class,
            GenderSeeder::class,
            OccupationSeeder::class,
            MaritalStatusSeeder::class,
            EducationSeeder::class,
            
            // Ubicaciones
            CountrySeeder::class,
            StateSeeder::class,
            CitySeeder::class,
            
            // Usuarios y personas
            UserSeeder::class,
            
            // Servicios y horarios
            ServiceSeeder::class,
            ScheduleSeeder::class,
            
            // Citas y pagos
            AppointmentSeeder::class,
        ]);
    }
}
