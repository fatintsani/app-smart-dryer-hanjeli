<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Default Administrator Account
        User::updateOrCreate(
            ['email' => 'admin@hanjeli.id'],
            [
                'name' => 'Admin Smart Dryer',
                'password' => Hash::make('admin123'),
                'role' => 'ADMIN',
                'phone' => '081234567890',
            ]
        );

        // 2. Default Operator Account
        User::updateOrCreate(
            ['email' => 'operator@hanjeli.id'],
            [
                'name' => 'Operator Greenhouse',
                'password' => Hash::make('operator123'),
                'role' => 'OPERATOR',
                'phone' => '089876543210',
            ]
        );

        // 3. Petani Demo Account
        User::updateOrCreate(
            ['email' => 'petani@hanjeli.id'],
            [
                'name' => 'Petani Hanjeli Waluran',
                'password' => Hash::make('petani123'),
                'role' => 'OPERATOR',
                'phone' => '081345678901',
            ]
        );
    }
}
