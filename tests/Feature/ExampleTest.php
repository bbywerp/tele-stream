<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    public function test_the_homepage_does_not_expose_content(): void
    {
        $response = $this->get('/');

        $response->assertNotFound()->assertContent('')->assertHeaderMissing('Set-Cookie');
    }
}
