<?php

namespace Database\Factories;

use App\Models\Claim;
use App\Models\Company;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClaimFactory extends Factory
{
    protected $model = Claim::class;

    public function definition(): array
    {
        return [
            'claim_number' => 'CLAIM-' . $this->faker->unique()->randomNumber(6),
            'company_id' => Company::factory(),
            'subject' => $this->faker->sentence(),
            'description' => $this->faker->paragraph(),
            'status' => 'open',
            'date_accident' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'time_accident' => $this->faker->time(),
            'location_accident' => $this->faker->address(),
            'name_counterparty' => $this->faker->name(),
            'street_counterparty' => $this->faker->streetAddress(),
            'zipcode_counterparty' => $this->faker->postcode(),
            'city_counterparty' => $this->faker->city(),
            'country_counterparty' => 'Nederland',
            'email_counterparty' => $this->faker->safeEmail(),
            'phone_counterparty' => $this->faker->phoneNumber(),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }

    public function draftDenied(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft_denied',
            'denied_reason' => $this->faker->sentence(),
        ]);
    }

    public function closed(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'finished',
        ]);
    }
}
