<?php
// database/seeders/CategorySeeder.php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            [
                'name' => 'City E-Bikes',
                'slug' => 'city-e-bikes',
                'description' => 'Electric bikes perfect for city commuting',
                'is_active' => true,
            ],
            [
                'name' => 'Folding E-Bikes',
                'slug' => 'folding-e-bikes',
                'description' => 'Compact and portable electric bikes',
                'is_active' => true,
            ],
            [
                'name' => 'E-Bike Batteries',
                'slug' => 'e-bike-batteries',
                'description' => 'Replacement and upgrade batteries',
                'is_active' => true,
            ],
            [
                'name' => 'E-Bike Motors',
                'slug' => 'e-bike-motors',
                'description' => 'Hub motors and mid-drive motors',
                'is_active' => true,
            ],
            [
                'name' => 'E-Bike Controllers',
                'slug' => 'e-bike-controllers',
                'description' => 'Speed controllers and displays',
                'is_active' => true,
            ],
            [
                'name' => 'E-Bike Chargers',
                'slug' => 'e-bike-chargers',
                'description' => 'Battery chargers and power adapters',
                'is_active' => true,
            ],
            [
                'name' => 'E-Bike Accessories',
                'slug' => 'e-bike-accessories',
                'description' => 'Helmets, lights, locks, and other accessories',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
