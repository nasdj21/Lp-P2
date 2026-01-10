<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insertar usuarios
        DB::table('user_account')->insert([
            ['role_id' => 1, 'email' => 'admin@aspy.com', 'password_hash' => hash('sha256', 'admin'), 'status' => 1, 'created_by' => 'system', 'creation_date' => now()],
            ['role_id' => 2, 'email' => 'prof1@aspy.com', 'password_hash' => hash('sha256', 'prof1'), 'status' => 1, 'created_by' => 'system', 'creation_date' => now()],
            ['role_id' => 2, 'email' => 'prof2@aspy.com', 'password_hash' => hash('sha256', 'prof2'), 'status' => 1, 'created_by' => 'system', 'creation_date' => now()],
            ['role_id' => 2, 'email' => 'prof3@aspy.com', 'password_hash' => hash('sha256', 'prof3'), 'status' => 1, 'created_by' => 'system', 'creation_date' => now()],
            ['role_id' => 3, 'email' => 'client1@aspy.com', 'password_hash' => hash('sha256', 'client1'), 'status' => 1, 'created_by' => 'system', 'creation_date' => now()],
            ['role_id' => 3, 'email' => 'client2@aspy.com', 'password_hash' => hash('sha256', 'client2'), 'status' => 1, 'created_by' => 'system', 'creation_date' => now()],
            ['role_id' => 4, 'email' => 'staff1@aspy.com', 'password_hash' => hash('sha256', 'staff1'), 'status' => 1, 'created_by' => 'system', 'creation_date' => now()],
        ]);

        // Insertar personas
        DB::table('person')->insert([
            ['user_account_id' => 1, 'first_name' => 'Carlos Alberto', 'last_name' => 'García Mendoza', 'birthdate' => '1985-03-15', 'gender' => 1, 'occupation' => 1, 'marital_status' => 2, 'education' => 3, 'phone' => '0987654321', 'country_id' => 1, 'created_by' => 'system', 'creation_date' => now()],
            ['user_account_id' => 2, 'first_name' => 'María Elena', 'last_name' => 'Rodríguez Silva', 'birthdate' => '1980-07-22', 'gender' => 2, 'occupation' => 1, 'marital_status' => 1, 'education' => 3, 'phone' => '0998765432', 'country_id' => 1, 'created_by' => 'system', 'creation_date' => now()],
            ['user_account_id' => 3, 'first_name' => 'Juan Pablo', 'last_name' => 'Martínez Torres', 'birthdate' => '1978-11-10', 'gender' => 1, 'occupation' => 1, 'marital_status' => 2, 'education' => 3, 'phone' => '0976543210', 'country_id' => 1, 'created_by' => 'system', 'creation_date' => now()],
            ['user_account_id' => 4, 'first_name' => 'Ana Lucía', 'last_name' => 'Fernández Ramos', 'birthdate' => '1982-05-18', 'gender' => 2, 'occupation' => 1, 'marital_status' => 1, 'education' => 3, 'phone' => '0965432109', 'country_id' => 1, 'created_by' => 'system', 'creation_date' => now()],
            ['user_account_id' => 5, 'first_name' => 'Pedro Antonio', 'last_name' => 'López Vega', 'birthdate' => '1990-09-25', 'gender' => 1, 'occupation' => 4, 'marital_status' => 1, 'education' => 2, 'phone' => '0954321098', 'country_id' => 1, 'created_by' => 'system', 'creation_date' => now()],
            ['user_account_id' => 6, 'first_name' => 'Sofía Gabriela', 'last_name' => 'Morales Castro', 'birthdate' => '1995-12-08', 'gender' => 2, 'occupation' => 4, 'marital_status' => 1, 'education' => 2, 'phone' => '0943210987', 'country_id' => 1, 'created_by' => 'system', 'creation_date' => now()],
            ['user_account_id' => 7, 'first_name' => 'Luis Fernando', 'last_name' => 'Sánchez Ortiz', 'birthdate' => '1988-02-14', 'gender' => 1, 'occupation' => 2, 'marital_status' => 2, 'education' => 3, 'phone' => '0932109876', 'country_id' => 1, 'created_by' => 'system', 'creation_date' => now()],
        ]);

        // Insertar identificaciones (una por cada persona)
        DB::table('identification')->insert([
            ['person_id' => 1, 'number' => '1712345678', 'created_by' => 'system', 'creation_date' => now()],
            ['person_id' => 2, 'number' => '1723456789', 'created_by' => 'system', 'creation_date' => now()],
            ['person_id' => 3, 'number' => '1734567890', 'created_by' => 'system', 'creation_date' => now()],
            ['person_id' => 4, 'number' => '1745678901', 'created_by' => 'system', 'creation_date' => now()],
            ['person_id' => 5, 'number' => '1756789012', 'created_by' => 'system', 'creation_date' => now()],
            ['person_id' => 6, 'number' => '1767890123', 'created_by' => 'system', 'creation_date' => now()],
            ['person_id' => 7, 'number' => '1778901234', 'created_by' => 'system', 'creation_date' => now()],
        ]);

        // Insertar clientes
        DB::table('client')->insert([
            ['person_id' => 5, 'created_by' => 'system', 'creation_date' => now()],
            ['person_id' => 6, 'created_by' => 'system', 'creation_date' => now()],
        ]);

        // Insertar staff
        DB::table('staff')->insert([
            ['person_id' => 7, 'created_by' => 'system', 'creation_date' => now()],
        ]);

        // Insertar profesionales
        DB::table('professional')->insert([
            ['person_id' => 2, 'specialty' => 'Psicología Clínica', 'title' => 'Mg.', 'created_by' => 'system', 'creation_date' => now()],
            ['person_id' => 3, 'specialty' => 'Terapia Familiar', 'title' => 'PhD.', 'created_by' => 'system', 'creation_date' => now()],
            ['person_id' => 4, 'specialty' => 'Psicología Infantil', 'title' => 'Mg.', 'created_by' => 'system', 'creation_date' => now()],
        ]);
    }
}
