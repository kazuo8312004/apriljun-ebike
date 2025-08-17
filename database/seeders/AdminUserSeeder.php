<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Branch;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Ensure branches exist
        $sdoBranch = Branch::firstOrCreate(['code' => 'SDO'], ['name' => 'San Simon']);
        $btyBranch = Branch::firstOrCreate(['code' => 'BTY'], ['name' => 'Batasan']);
        $nrvBranch = Branch::firstOrCreate(['code' => 'NRV'], ['name' => 'Norzagaray']);

        // Seed users
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
