<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();
        \App\Models\Category::factory(5)->create();
        // \App\Models\SubCategory::factory(10)->create();
        // \App\Models\Product::factory(50)->create();
        // \App\Models\Brands::factory(50)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'srengsokheng',
        //     'email' => 'srengsokheng.dm@gmail.com',
        //     'password' => Hash::make('88888888'),
        //     'role'=>2,
        // ]);
    }
}
