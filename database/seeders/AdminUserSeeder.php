<?php
// database/seeders/AdminUserSeeder.php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $sdoBranch = Branch::where('code', 'SDO')->first();
        $btyBranch = Branch::where('code', 'BTY')->first();
        $nrvBranch = Branch::where('code', 'NRV')->first();

        $users = [
            [
                'name' => 'System Administrator',
                'email' => 'admin@ace-ebike.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'branch_id' => $sdoBranch->id,
                'role' => 'admin',
            ],
            [
                'name' => 'SDO Manager',
                'email' => 'sdo.manager@ace-ebike.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'branch_id' => $sdoBranch->id,
                'role' => 'manager',
            ],
            [
                'name' => 'BTY Manager',
                'email' => 'bty.manager@ace-ebike.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'branch_id' => $btyBranch->id,
                'role' => 'manager',
            ],
            [
                'name' => 'NRV Staff',
                'email' => 'nrv.staff@ace-ebike.com',
                'password' => Hash::make('password123'),
                'email_verified_at' => now(),
                'branch_id' => $nrvBranch->id,
                'role' => 'staff',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
    }
}
