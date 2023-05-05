<?php

namespace App\Http\Middleware\Authorization;

use App\BusinessDomain\Authentication\Service\AuthenticationService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsAuctioneerMiddleware
{
    public function __construct(private readonly AuthenticationService $authenticationService)
    {
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();
        if ($user === null || !$user->is_auctioneer) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'You are not privileged to perform this operation',
                'data' => [],
            ], Response::HTTP_FORBIDDEN);
        }
        return $next($request);
    }
}
