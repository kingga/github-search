<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class GithubTest extends TestCase
{
    /**
     * Test the GitHub search route.
     *
     * @return void
     */
    public function test_github_search()
    {
        // Attempt a successful request.
        $response = $this->postJson('/github/search', ['search' => 'kingga']);
        $response->assertStatus(200);

        // Check that some of the details are correct.
        $json = $response->json();

        $this->assertEquals('Isaac Skelton', $json['name']);
        $this->assertEquals('20/06/2016 03:42:39 AM', $json['created_at']);
        $this->assertEquals('kingga', $json['login']);
    }
}
