<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class GenerateStaticTest extends TestCase
{
    public function test_generate_static_pages()
    {
        // Generate Login Page
        $loginResponse = $this->get('/login');
        file_put_contents(public_path('login-static.html'), $loginResponse->getContent());

        // Generate Dashboard Page
        $user = User::first();
        if (!$user) {
            $this->fail("No user found. Please run seeders first.");
        }
        $dashboardResponse = $this->actingAs($user)->get('/dashboard');
        file_put_contents(public_path('dashboard-static.html'), $dashboardResponse->getContent());

        $this->assertTrue(file_exists(public_path('login-static.html')));
        $this->assertTrue(file_exists(public_path('dashboard-static.html')));
    }
}
