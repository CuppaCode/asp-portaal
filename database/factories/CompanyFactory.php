<?php

namespace Database\Factories;

use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class CompanyFactory extends Factory
{
    protected $model = Company::class;

    public function definition(): array
    {
        return [
            'name' => $this->faker->company(),
            'company_type' => 'transport',
            'street' => $this->faker->streetAddress(),
            'zipcode' => $this->faker->postcode(),
            'city' => $this->faker->city(),
            'country' => 'Nederland',
            'phone' => $this->faker->phoneNumber(),
            'active' => true,
            'description' => $this->faker->optional()->paragraph(),
            'logo' => null,
        ];
    }
}
