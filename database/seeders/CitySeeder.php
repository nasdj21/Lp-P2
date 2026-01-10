<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cities = [
            // Azuay (state_id: 1)
            1 => ['Cuenca', 'Gualaceo', 'Paute', 'Sígsig', 'Chordeleg'],
            // Bolívar (state_id: 2)
            2 => ['Guaranda', 'San Miguel', 'Chimbo', 'Chillanes', 'Echeandía'],
            // Cañar (state_id: 3)
            3 => ['Azogues', 'Biblián', 'Cañar', 'La Troncal', 'El Tambo'],
            // Carchi (state_id: 4)
            4 => ['Tulcán', 'San Gabriel', 'Huaca', 'Montúfar', 'Bolívar'],
            // Chimborazo (state_id: 5)
            5 => ['Riobamba', 'Alausí', 'Guano', 'Chambo', 'Chunchi'],
            // Cotopaxi (state_id: 6)
            6 => ['Latacunga', 'La Maná', 'Pujilí', 'Salcedo', 'Saquisilí'],
            // El Oro (state_id: 7)
            7 => ['Machala', 'Pasaje', 'Santa Rosa', 'Huaquillas', 'Arenillas'],
            // Esmeraldas (state_id: 8)
            8 => ['Esmeraldas', 'Atacames', 'Muisne', 'Quinindé', 'San Lorenzo'],
            // Galápagos (state_id: 9)
            9 => ['Puerto Baquerizo Moreno', 'Puerto Ayora', 'Puerto Villamil', 'Isla Santa Cruz', 'Isla Isabela'],
            // Guayas (state_id: 10)
            10 => ['Guayaquil', 'Durán', 'Milagro', 'Daule', 'Samborondón'],
            // Imbabura (state_id: 11)
            11 => ['Ibarra', 'Otavalo', 'Cotacachi', 'Atuntaqui', 'Pimampiro'],
            // Loja (state_id: 12)
            12 => ['Loja', 'Catamayo', 'Macará', 'Cariamanga', 'Gonzanamá'],
            // Los Ríos (state_id: 13)
            13 => ['Babahoyo', 'Quevedo', 'Ventanas', 'Vinces', 'Baba'],
            // Manabí (state_id: 14)
            14 => ['Portoviejo', 'Manta', 'Chone', 'Jipijapa', 'Montecristi'],
            // Morona Santiago (state_id: 15)
            15 => ['Macas', 'Gualaquiza', 'Sucúa', 'Méndez', 'Palora'],
            // Napo (state_id: 16)
            16 => ['Tena', 'Archidona', 'El Chaco', 'Quijos', 'Carlos Julio Arosemena Tola'],
            // Orellana (state_id: 17)
            17 => ['Francisco de Orellana', 'La Joya de los Sachas', 'Loreto', 'Aguarico', 'Nuevo Rocafuerte'],
            // Pastaza (state_id: 18)
            18 => ['Puyo', 'Mera', 'Santa Clara', 'Arajuno', 'Shell'],
            // Pichincha (state_id: 19)
            19 => ['Quito', 'Cayambe', 'Machachi', 'Sangolquí', 'Tabacundo'],
            // Santa Elena (state_id: 20)
            20 => ['Santa Elena', 'La Libertad', 'Salinas', 'Anconcito', 'Ballenita'],
            // Santo Domingo (state_id: 21)
            21 => ['Santo Domingo', 'La Concordia', 'Valle Hermoso', 'Puerto Limón', 'El Esfuerzo'],
            // Sucumbíos (state_id: 22)
            22 => ['Nueva Loja', 'Shushufindi', 'Gonzalo Pizarro', 'Putumayo', 'Cascales'],
            // Tungurahua (state_id: 23)
            23 => ['Ambato', 'Baños', 'Pelileo', 'Píllaro', 'Patate'],
            // Zamora Chinchipe (state_id: 24)
            24 => ['Zamora', 'Yantzaza', 'Zumbi', 'Gualaquiza', 'El Pangui'],
        ];

        foreach ($cities as $stateId => $cityList) {
            foreach ($cityList as $city) {
                DB::table('city')->insert([
                    'name' => $city,
                    'state_id' => $stateId
                ]);
            }
        }
    }
}
