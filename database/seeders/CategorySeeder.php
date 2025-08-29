<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    public function run(): void
    {

        Category::firstOrCreate([
            'name' => 'Belum Dikategorikan',
            'is_default' => true,
        ]);

        Category::create(['name' => 'Kosmetik dan Skincare']);
        Category::create(['name' => 'Healthcare']);
        Category::create(['name' => 'Babycare']);
        Category::create(['name' => 'Makanan dan Minuman']);
        Category::create(['name' => 'Haircare']);
        Category::create(['name' => 'Homecare']);
        Category::create(['name' => 'Personal Hygiene']);
        Category::create(['name' => 'Mainan']);
    }
}
