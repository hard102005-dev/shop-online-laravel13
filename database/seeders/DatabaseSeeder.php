<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        if (! User::where('email', 'admin@shoponline.com')->exists()) {
            User::factory()->create([
                'name' => 'Store Admin',
                'email' => 'admin@shoponline.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]);
        }

        if (! User::where('email', 'customer@shoponline.com')->exists()) {
            User::factory()->create([
                'name' => 'John Customer',
                'email' => 'customer@shoponline.com',
                'password' => Hash::make('password'),
                'role' => 'customer',
            ]);
        }

        $this->call([
            CategorySeeder::class,
            ProductSeeder::class,
        ]);
    }
}
