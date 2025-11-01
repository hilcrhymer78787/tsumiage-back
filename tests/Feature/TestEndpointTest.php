<?php

namespace Tests\Feature;

use Tests\FeatureTestCase;

class TestEndpointTest extends FeatureTestCase
{
    public function test_returns_test_message()
    {
        $response = $this->get('/api/test');

        $response->assertStatus(200)
            ->assertJson(['message' => 'this is test 1']);
    }
}
