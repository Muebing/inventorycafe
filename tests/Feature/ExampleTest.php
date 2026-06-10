<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_login_page_is_accessible(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }

    public function test_dashboard_redirects_to_login_when_unauthenticated(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }
}
