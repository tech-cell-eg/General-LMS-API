<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        // Admin user
        User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'username' => 'admin',
            'email' => 'admin@lms.test',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Create 5 instructors
        User::factory()->count(5)->create([
            'role' => 'instructor',
            'email_verified_at' => now(),
        ]);

        // Create 20 students
        User::factory()->count(20)->create([
            'role' => 'student',
            'email_verified_at' => now(),
        ]);
    }
}
