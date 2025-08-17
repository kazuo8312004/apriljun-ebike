<?php
// database/seeders/BranchSeeder.php

namespace Database\Seeders;

use App\Models\Branch;
use Illuminate\Database\Seeder;

class BranchSeeder extends Seeder
{
    public function run()
    {
        $branches = [
            [
                'code' => 'SDO',
                'name' => 'San Diego Office',
                'address' => 'San Diego, Ilocos Sur',
                'contact_number' => '09123456789',
                'manager_name' => 'Manager SDO',
                'is_active' => true,
            ],
            [
                'code' => 'BTY',
                'name' => 'Bantay Branch',
                'address' => 'Bantay, Ilocos Sur',
                'contact_number' => '09123456790',
                'manager_name' => 'Manager BTY',
                'is_active' => true,
            ],
            [
                'code' => 'NRV',
                'name' => 'Narvacan Branch',
                'address' => 'Narvacan, Ilocos Sur',
                'contact_number' => '09123456791',
                'manager_name' => 'Manager NRV',
                'is_active' => true,
            ],
            [
                'code' => 'SNT',
                'name' => 'Sinait Branch',
                'address' => 'Sinait, Ilocos Sur',
                'contact_number' => '09123456792',
                'manager_name' => 'Manager SNT',
                'is_active' => true,
            ],
            [
                'code' => 'SanJ',
                'name' => 'San Juan Branch',
                'address' => 'San Juan, Ilocos Sur',
                'contact_number' => '09123456793',
                'manager_name' => 'Manager SanJ',
                'is_active' => true,
            ],
        ];

        foreach ($branches as $branch) {
            Branch::create($branch);
        }
    }
}
