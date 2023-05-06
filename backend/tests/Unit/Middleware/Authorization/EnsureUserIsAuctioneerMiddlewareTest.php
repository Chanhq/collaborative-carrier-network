<?php

namespace Tests\Unit\Middleware\Authorization;

use App\Http\Middleware\Authorization\EnsureUserIsAuctioneerMiddleware;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;
use Tests\TestCase;

class EnsureUserIsAuctioneerMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function itShouldLetThroughAuctioneerRequests(): void
    {
        $user = User::create([
            'username' => 'tester',
            'password' => Hash::make('testerpw123'),
            'is_auctioneer' => true,
        ]);
        $token = $user->createToken(Str::random(40));
        $request = Request::create('/api/auctioneer-frontend', 'get');
        $request->headers->set('Authorization', 'Bearer ' . $token->plainTextToken);
        Auth::shouldReceive('user')->once()->andReturn($user);

        $middleware = $this->getUnitUnderTest();
        $response = $middleware->handle($request, function () {
            return new JsonResponse([]);
        });

        self::assertEquals($response->getStatusCode(), Response::HTTP_OK);
    }

    /**
     * @test
     */
    public function itShouldBlockCarrierRequests(): void
    {
        $user = User::create([
            'username' => 'tester',
            'password' => Hash::make('testerpw123'),
            'is_auctioneer' => false,
        ]);
        $token = $user->createToken(Str::random(40));
        $request = Request::create('/api/auctioneer-frontend', 'get');
        $request->headers->set('Authorization', 'Bearer ' . $token->plainTextToken);
        Auth::shouldReceive('user')->once()->andReturn($user);

        $middleware = $this->getUnitUnderTest();
        $response = $middleware->handle($request, function () {
            return new JsonResponse([]);
        });

        self::assertEquals($response->getStatusCode(), Response::HTTP_FORBIDDEN);
    }

    private function getUnitUnderTest(): EnsureUserIsAuctioneerMiddleware
    {
        return new EnsureUserIsAuctioneerMiddleware();
    }
}
