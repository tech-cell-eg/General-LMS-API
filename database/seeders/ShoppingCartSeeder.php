<?php

namespace Database\Seeders;

use App\Models\ShoppingCart;
use App\Models\User;
use Illuminate\Database\Seeder;

class ShoppingCartSeeder extends Seeder
{
    public function run()
    {
        $students = User::where('role', 'student')->get();

        foreach ($students as $student) {
            ShoppingCart::create([
                'user_id' => $student->id,
            ]);
        }
    }
}
