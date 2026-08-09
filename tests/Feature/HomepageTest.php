<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

final class HomepageTest extends TestCase
{
    public function test_homepage_route_displays_the_welcome_view(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertViewIs('welcome');
    }
}
