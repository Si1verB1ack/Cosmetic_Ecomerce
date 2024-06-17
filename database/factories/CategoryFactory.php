<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Category>
 */
class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name =[];
        $name = fake()->name();
        return [
            'name' => $name,
            'status' => fake()->numberBetween(0,1),
            'slug'=> $name,
            'showHome'=> 'Yes',
        ];
    }
}
