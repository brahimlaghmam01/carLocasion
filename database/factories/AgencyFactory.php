<?php

namespace Database\Factories;

use App\Models\Agency;
use App\Models\City;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Agency>
 */
class AgencyFactory extends Factory
{
    protected $model = Agency::class;

    public function definition(): array
    {
        return [
            'city_id' => City::factory(),
            'name' => fake()->company() . ' Rentals',
            'address' => fake()->streetAddress(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->unique()->companyEmail(),
            'manager_name' => fake()->name(),
            'status' => 'active',
        ];
    }
}
