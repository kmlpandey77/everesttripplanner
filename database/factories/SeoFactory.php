<?php

namespace Database\Factories;

use App\Models\Seo;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Seo>
 */
class SeoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'meta_title' => $this->faker->sentence(4),
            'meta_description' => $this->faker->sentence(12),
            'robots_index' => true,
            'robots_follow' => true,
        ];
    }
}
