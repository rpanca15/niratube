<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Artisan;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Admin',
            'email' => 'kuakui1q@gmail.com',
            'password' => 'AkuAdalah1#',
            'role' => 'admin',
        ]);
        Artisan::call('videos:clear');
        $this->call(CategorySeeder::class);
    }
}
