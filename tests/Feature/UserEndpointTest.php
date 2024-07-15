<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserEndpointTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test the /user endpoint.
     *
     * @return void
     */
    public function testUserEndpoint()
    {
        // Create a user and authenticate
        $user = User::factory()->create([
            'password' => Hash::make('password'),
        ]);

        $response = $this->actingAs($user, 'api')
                         ->withHeaders(['Accept' => 'application/json'])
                         ->get('/api/user');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'id',
            'name',
            'email',
            'created_at',
            'updated_at',
        ]);
    }
}