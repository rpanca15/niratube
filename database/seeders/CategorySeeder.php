<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Data kategori
        $categories = [
            ['name' => 'Education'],
            ['name' => 'Entertainment'],
            ['name' => 'Technology'],
            ['name' => 'Other'],
        ];

        // Insert data ke tabel categories
        DB::table('categories')->insert($categories);
    }
}
