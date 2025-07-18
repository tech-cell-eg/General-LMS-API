<?php

namespace Database\Seeders;

use App\Models\InstructorLink;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class InstructorLinkSeeder extends Seeder
{
    public function run()
    {
        $instructors = User::where('role', 'instructor')->get();

        foreach ($instructors as $instructor) {
            $links = [
                ['title' => 'LinkedIn', 'url' => 'https://linkedin.com/in/'.Str::slug($instructor->name), 'icon_class' => 'fab fa-linkedin'],
                ['title' => 'GitHub', 'url' => 'https://github.com/'.Str::slug($instructor->username), 'icon_class' => 'fab fa-github'],
                ['title' => 'Personal Website', 'url' => 'https://'.Str::slug($instructor->username).'.com', 'icon_class' => 'fas fa-globe'],
                ['title' => 'Twitter', 'url' => 'https://twitter.com/'.Str::slug($instructor->username), 'icon_class' => 'fab fa-twitter'],
            ];

            // Each instructor gets 2-4 links
            $selectedLinks = array_rand($links, rand(2, 4));
            $selectedLinks = is_array($selectedLinks) ? $selectedLinks : [$selectedLinks];

            foreach ($selectedLinks as $linkIndex) {
                InstructorLink::create([
                    'instructor_id' => $instructor->id,
                    'title' => $links[$linkIndex]['title'],
                    'url' => $links[$linkIndex]['url'],
                    'icon_class' => $links[$linkIndex]['icon_class'],
                ]);
            }
        }
    }
}
