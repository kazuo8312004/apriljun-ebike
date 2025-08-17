<?php
// database/seeders/ProductSeeder.php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run()
    {
        $mountainCategory = Category::where('slug', 'mountain-e-bikes')->first();
        $cityCategory = Category::where('slug', 'city-e-bikes')->first();
        $foldingCategory = Category::where('slug', 'folding-e-bikes')->first();
        $batteryCategory = Category::where('slug', 'e-bike-batteries')->first();
        $motorCategory = Category::where('slug', 'e-bike-motors')->first();
        $controllerCategory = Category::where('slug', 'e-bike-controllers')->first();
        $chargerCategory = Category::where('slug', 'e-bike-chargers')->first();
        $accessoryCategory = Category::where('slug', 'e-bike-accessories')->first();

        $products = [
            // Mountain E-Bikes (Units)
            [
                'name' => 'TrailBlazer Pro 29"',
                'sku' => 'MTB-TB-PRO-29',
                'description' => 'Professional mountain e-bike with 29" wheels',
                'category_id' => $mountainCategory->id,
                'price' => 85000.00,
                'cost_price' => 65000.00,
                'brand' => 'A.C.E',
                'model' => 'TrailBlazer Pro',
                'type' => 'unit',
                'status' => 'active',
            ],
            [
                'name' => 'MountainMaster 27.5"',
                'sku' => 'MTB-MM-275',
                'description' => 'Versatile mountain e-bike for all terrains',
                'category_id' => $mountainCategory->id,
                'price' => 75000.00,
                'cost_price' => 58000.00,
                'brand' => 'A.C.E',
                'model' => 'MountainMaster',
                'type' => 'unit',
                'status' => 'active',
            ],

            // City E-Bikes (Units)
            [
                'name' => 'City Cruiser Deluxe',
                'sku' => 'CTY-CC-DLX',
                'description' => 'Comfortable city e-bike for daily commuting',
                'category_id' => $cityCategory->id,
                'price' => 55000.00,
                'cost_price' => 42000.00,
                'brand' => 'A.C.E',
                'model' => 'City Cruiser',
                'type' => 'unit',
                'status' => 'active',
            ],
            [
                'name' => 'Urban Explorer',
                'sku' => 'CTY-UE-STD',
                'description' => 'Stylish urban e-bike with modern features',
                'category_id' => $cityCategory->id,
                'price' => 48000.00,
                'cost_price' => 36000.00,
                'brand' => 'A.C.E',
                'model' => 'Urban Explorer',
                'type' => 'unit',
                'status' => 'active',
            ],

            // Folding E-Bikes (Units)
            [
                'name' => 'FoldMaster Compact',
                'sku' => 'FLD-FM-CMP',
                'description' => 'Compact folding e-bike for easy storage',
                'category_id' => $foldingCategory->id,
                'price' => 35000.00,
                'cost_price' => 26000.00,
                'brand' => 'A.C.E',
                'model' => 'FoldMaster',
                'type' => 'unit',
                'status' => 'active',
            ],

            // Batteries (Parts)
            [
                'name' => '48V 15Ah Lithium Battery',
                'sku' => 'BAT-48V-15AH',
                'description' => 'High capacity lithium battery for e-bikes',
                'category_id' => $batteryCategory->id,
                'price' => 12000.00,
                'cost_price' => 8500.00,
                'brand' => 'PowerCell',
                'model' => 'PC-48-15',
                'type' => 'part',
                'status' => 'active',
            ],
            [
                'name' => '36V 12Ah Lithium Battery',
                'sku' => 'BAT-36V-12AH',
                'description' => 'Standard lithium battery for city e-bikes',
                'category_id' => $batteryCategory->id,
                'price' => 9500.00,
                'cost_price' => 7000.00,
                'brand' => 'PowerCell',
                'model' => 'PC-36-12',
                'type' => 'part',
                'status' => 'active',
            ],

            // Motors (Parts)
            [
                'name' => '1000W Rear Hub Motor',
                'sku' => 'MTR-1000W-RH',
                'description' => 'Powerful rear hub motor for mountain bikes',
                'category_id' => $motorCategory->id,
                'price' => 8500.00,
                'cost_price' => 6200.00,
                'brand' => 'MotorTech',
                'model' => 'MT-1000-RH',
                'type' => 'part',
                'status' => 'active',
            ],
            [
                'name' => '500W Front Hub Motor',
                'sku' => 'MTR-500W-FH',
                'description' => 'Efficient front hub motor for city bikes',
                'category_id' => $motorCategory->id,
                'price' => 5500.00,
                'cost_price' => 4000.00,
                'brand' => 'MotorTech',
                'model' => 'MT-500-FH',
                'type' => 'part',
                'status' => 'active',
            ],

            // Controllers (Parts)
            [
                'name' => '48V 22A Controller with Display',
                'sku' => 'CTL-48V-22A-LCD',
                'description' => 'Advanced controller with LCD display',
                'category_id' => $controllerCategory->id,
                'price' => 3500.00,
                'cost_price' => 2500.00,
                'brand' => 'ControlMax',
                'model' => 'CM-48-22-LCD',
                'type' => 'part',
                'status' => 'active',
            ],
            [
                'name' => '36V 15A Basic Controller',
                'sku' => 'CTL-36V-15A-BSC',
                'description' => 'Basic controller for standard e-bikes',
                'category_id' => $controllerCategory->id,
                'price' => 2200.00,
                'cost_price' => 1600.00,
                'brand' => 'ControlMax',
                'model' => 'CM-36-15-BSC',
                'type' => 'part',
                'status' => 'active',
            ],

            // Chargers (Parts)
            [
                'name' => '48V 3A Fast Charger',
                'sku' => 'CHG-48V-3A',
                'description' => 'Fast charging adapter for 48V batteries',
                'category_id' => $chargerCategory->id,
                'price' => 1800.00,
                'cost_price' => 1200.00,
                'brand' => 'ChargeTech',
                'model' => 'CT-48-3A',
                'type' => 'part',
                'status' => 'active',
            ],
            [
                'name' => '36V 2A Standard Charger',
                'sku' => 'CHG-36V-2A',
                'description' => 'Standard charging adapter for 36V batteries',
                'category_id' => $chargerCategory->id,
                'price' => 1200.00,
                'cost_price' => 850.00,
                'brand' => 'ChargeTech',
                'model' => 'CT-36-2A',
                'type' => 'part',
                'status' => 'active',
            ],

            // Accessories
            [
                'name' => 'Premium E-Bike Helmet',
                'sku' => 'ACC-HELM-PREM',
                'description' => 'Safety helmet with LED lights',
                'category_id' => $accessoryCategory->id,
                'price' => 2500.00,
                'cost_price' => 1800.00,
                'brand' => 'SafeRide',
                'model' => 'SR-PREM',
                'type' => 'accessory',
                'status' => 'active',
            ],
            [
                'name' => 'LED Light Set (Front & Rear)',
                'sku' => 'ACC-LED-SET',
                'description' => 'Bright LED lights for night riding',
                'category_id' => $accessoryCategory->id,
                'price' => 1500.00,
                'cost_price' => 1000.00,
                'brand' => 'BrightBeam',
                'model' => 'BB-LED-SET',
                'type' => 'accessory',
                'status' => 'active',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}
