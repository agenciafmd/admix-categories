<?php

namespace Agenciafmd\Categories\Database\Factories;

use Agenciafmd\Categories\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    protected $model = Category::class;

    public function definition(): array
    {
        return [
            'is_active' => fake()->optional(0.3, 1)
                ->randomElement([0]),
            'name' => str(fake()->word())->ucfirst(),
            'color' => fake()->safeHexColor(),
            'description' => fake()->text(),
            'type' => 'categories',
        ];
    }
}
