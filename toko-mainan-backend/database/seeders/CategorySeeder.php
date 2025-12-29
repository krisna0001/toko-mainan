<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;
use Illuminate\Support\Str;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            ['name' => 'Action Figures', 'description' => 'Koleksi action figures dan superhero'],
            ['name' => 'Boneka', 'description' => 'Boneka dan plushies lucu'],
            ['name' => 'Puzzle & Board Games', 'description' => 'Puzzle dan permainan papan'],
            ['name' => 'Mobil-mobilan', 'description' => 'Koleksi toy cars dan vehicles'],
            ['name' => 'Mainan Edukatif', 'description' => 'Mainan untuk belajar dan berkembang'],
            ['name' => 'Lego & Building Blocks', 'description' => 'Set lego dan building blocks'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'name' => $category['name'],
                'slug' => Str::slug($category['name']),
                'description' => $category['description'],
            ]);
        }
    }
}
