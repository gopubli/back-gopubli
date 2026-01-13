<?php

namespace Database\Factories;

use App\Models\Influencer;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class InfluencerFactory extends Factory
{
    protected $model = Influencer::class;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'cpf' => fake()->numerify('###########'),
            'phone' => fake()->phoneNumber(),
            'instagram' => '@' . fake()->userName(),
            'tiktok' => '@' . fake()->userName(),
            'youtube' => '@' . fake()->userName(),
            'avatar' => null,
            'bio' => fake()->sentence(),
            'remember_token' => Str::random(10),
            'active' => true,
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'active' => false,
        ]);
    }
}
