<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $categories = [
            ['name' => 'Web Development', 'image' => 'web-dev.jpg'],
            ['name' => 'Mobile Development', 'image' => 'mobile-dev.jpg'],
            ['name' => 'Data Science', 'image' => 'data-science.jpg'],
            ['name' => 'Programming', 'image' => 'programming.jpg'],
            ['name' => 'DevOps', 'image' => 'devops.jpg'],
            ['name' => 'Design', 'image' => 'design.jpg'],
            ['name' => 'Business', 'image' => 'business.jpg'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'image' => $category['image'],
                'slug' => Str::slug($category['name']),
            ]);
        }
    }
}
