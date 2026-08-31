<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'aburifan62@gmail.com'],
            [
                'name' => 'Root Test',
                'role' => UserRole::ROOT,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'maniacafk1@gmail.com'],
            [
                'name' => 'Company Test',
                'role' => UserRole::COMPANY,
                'email_verified_at' => now(),
            ]
        );

        User::updateOrCreate(
            ['email' => 'maniacafk2@gmail.com'],
            [
                'name' => 'Intern Test',
                'role' => UserRole::INTERN,
                'email_verified_at' => now(),
            ]
        );
    }
}