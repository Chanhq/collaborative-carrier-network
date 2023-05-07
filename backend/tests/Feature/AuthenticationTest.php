<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function registerLoginLogoutFlowBehavesCorrectlyForCarriers(): void
    {
        $apiToken =
            $this->registerNewUserAndLogin('tester', 'testerpw123', false);

        $this->json('get', '/api/carrier-frontend', headers: ['Authorization' => 'Bearer ' . $apiToken])
            ->assertStatus(200);

        $this->json('get', '/api/auctioneer-frontend', headers: ['Authorization' => 'Bearer ' . $apiToken])
            ->assertStatus(403);

        $this->json('post', '/api/auth/logout', headers: ['Authorization' => 'Bearer ' . $apiToken])
            ->assertStatus(200);
    }

    /**
     * @test
     */
    public function registerLoginLogoutFlowBehavesCorrectlyForAuctioneers(): void
    {
        $apiToken =
            $this->registerNewUserAndLogin('tester', 'testerpw123', true);

        $this->json('get', '/api/carrier-frontend', headers: ['Authorization' => 'Bearer ' . $apiToken])
            ->assertStatus(403);

        $this->json('get', '/api/auctioneer-frontend', headers: ['Authorization' => 'Bearer ' . $apiToken])
            ->assertStatus(200);

        $this->json('post', '/api/auth/logout', headers: ['Authorization' => 'Bearer ' . $apiToken])->assertStatus(200);
    }

    private function registerNewUserAndLogin(string $username, string $password, bool $isAuctioneerRegistration): string
    {
        $registerPayload = [
            'username' => $username,
            'password' => $password,
            'isAuctioneerRegistration' => $isAuctioneerRegistration,
        ];

        $this->json('post', '/api/auth/register', $registerPayload)->assertStatus(201);

        $loginPayload = ['username' => $username, 'password' => $password];

        $loginResponse = $this->json('post', '/api/auth/login', $loginPayload)->assertStatus(200);

        return (string)$loginResponse->json('data')['api_token'];
    }
}
