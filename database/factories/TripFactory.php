<?php

namespace Database\Factories;

use App\Enums\PageStatus;
use App\Enums\TripDifficulty;
use App\Models\Trip;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Trip>
 */
class TripFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = $this->faker->unique()->words(3, true).' trek';

        return [
            'title' => ucwords($title),
            'slug' => Str::slug($title),
            'nest_route_under_category' => false,
            'duration' => $this->faker->numberBetween(3, 21).' days',
            'difficulty' => $this->faker->randomElement(TripDifficulty::cases()),
            'altitude' => number_format($this->faker->numberBetween(2000, 8000)).'m',
            'price' => $this->faker->numberBetween(500, 5000),
            'overview' => '<p>'.$this->faker->paragraph().'</p>',
            'status' => PageStatus::Draft,
            'published_at' => null,
        ];
    }

    public function published(): static
    {
        return $this->state(fn (): array => [
            'status' => PageStatus::Published,
            'published_at' => now(),
        ]);
    }
}
