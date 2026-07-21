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
            'status' => 'new',
            'date_accident' => $this->faker->dateTimeBetween('-1 month', 'now')->format('d-m-Y'),
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
