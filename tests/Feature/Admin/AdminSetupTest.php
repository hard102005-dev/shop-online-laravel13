<?php

declare(strict_types=1);

namespace Tests\Feature\Admin;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class AdminSetupTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_creates_a_single_admin_account(): void
    {
        $this->seed(DatabaseSeeder::class);
        $this->seed(DatabaseSeeder::class);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@shoponline.com',
            'role' => 'admin',
        ]);

        $this->assertSame(1, User::where('email', 'admin@shoponline.com')->count());
    }
}
