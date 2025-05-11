<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Create operator user
        User::create([
            'name' => 'Operator',
            'email' => 'operator@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);

        // Create analyst user
        User::create([
            'name' => 'Analyst',
            'email' => 'analyst@example.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
} 