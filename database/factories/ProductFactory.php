<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->name();
        $slug = Str::slug($title);

        // $subCategories = rand(1,20);

        // $brands = [2,3,4,5,6,7,8];
        // $brandsRandKey = array_rand($brands);

        return [
            'title' => $title,
            'slug' => $slug,
            'category_id' => 1,
            'sub_category_id' => rand(1,10),
            'brand_id' => rand(1,10),
            'price' => rand(10,1000),
            'sku' => rand(1000,100000),
            'track_qty' => 'Yes',
            'qty' => 10,
            'is_featured' => 'Yes',
            'status' => 1
        ];
    }
}
