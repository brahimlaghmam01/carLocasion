<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;

class CitiesSeeder extends Seeder
{
    /**
     * Seed a base set of cities. Idempotent via firstOrCreate on the name.
     */
    public function run(): void
    {
        $cities = ['Casablanca', 'Rabat', 'Marrakech', 'Tangier', 'Agadir'];

        foreach ($cities as $name) {
            City::query()->firstOrCreate(['name' => $name], ['status' => 'active']);
        }
    }
}
