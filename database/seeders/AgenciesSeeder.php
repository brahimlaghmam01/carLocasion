<?php

namespace Database\Seeders;

use App\Models\Agency;
use App\Models\City;
use Illuminate\Database\Seeder;

class AgenciesSeeder extends Seeder
{
    /**
     * Seed one or two agencies per city. Idempotent via firstOrCreate on name + city.
     */
    public function run(): void
    {
        $agencies = [
            'Casablanca' => ['Downtown Auto Rentals', 'Airport Car Hire'],
            'Rabat' => ['Capital Rentals'],
            'Marrakech' => ['Medina Motors', 'Atlas Car Rental'],
            'Tangier' => ['Strait Rentals'],
            'Agadir' => ['Coastal Cars'],
        ];

        foreach ($agencies as $cityName => $names) {
            $city = City::query()->where('name', $cityName)->first();

            if (! $city) {
                continue;
            }

            foreach ($names as $name) {
                Agency::query()->firstOrCreate(
                    ['name' => $name, 'city_id' => $city->id],
                    [
                        'address' => fake()->streetAddress(),
                        'phone' => fake()->phoneNumber(),
                        'email' => fake()->unique()->companyEmail(),
                        'manager_name' => fake()->name(),
                        'status' => 'active',
                    ]
                );
            }
        }
    }
}
