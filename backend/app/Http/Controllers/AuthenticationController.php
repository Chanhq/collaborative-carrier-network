<?php

namespace App\Http\Controllers;

use App\BusinessDomain\Authentication\Exception\InvalidCredentialsException;
use App\BusinessDomain\Authentication\Service\AuthenticationService;
use App\Http\Requests\Authentication\LoginRequest;
use App\Http\Requests\Authentication\RegisterRequest;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends Controller
{
    public function __construct(private readonly AuthenticationService $authenticationService)
    {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        if ($this->authenticationService->doesUserExist($request->validated('username'))) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'User already exists!',
                'data' => [],
            ], Response::HTTP_CONFLICT);
        }

        if (
            $request->validated('isAuctioneerRegistration')
            && $this->authenticationService->doesAnAuctioneerAgentExist()
        ) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Cannot register more than one auctioneer agent.',
                'data' => [],
            ], Response::HTTP_CONFLICT);
        }

         User::create([
            'username' => $request->validated('username'),
            'password' => Hash::make($request->validated('password')),
            'is_auctioneer' => $request->validated('isAuctioneerRegistration'),
         ]);

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Successfully created user!',
            'data' => [],
        ], Response::HTTP_CREATED);
    }

    public function login(LoginRequest $request): JsonResponse
    {
        $username = $request->validated('username');
        $password = $request->validated('password');

        if (!$this->authenticationService->doesUserExist($username)) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Credentials incorrect!',
                'data' => [],
            ], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $loginData = $this->authenticationService->loginUser($username, $password);
        } catch (InvalidCredentialsException) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Credentials incorrect!',
                'data' => [],
            ], Response::HTTP_UNAUTHORIZED);
        }

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Login successful!',
            'data' => [
                'username' => $loginData->username,
                'isAuctioneer' => $loginData->isAuctioneer,
                'token' => $loginData->plainTextToken,
            ],
        ]);
    }

    public function logout(): JsonResponse
    {
        try {
            Auth::user()->tokens()->delete();
        } catch (\Throwable $e) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'An unknown error occurred.',
                'data' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }


        return new JsonResponse([
            'status' => 'success',
            'message' => 'Logout successful!',
            'data' => [],
        ]);
    }

    public function getAuthenticatedUser(): JsonResponse
    {
        $user = Auth::user();

        if ($user === null) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'An unknown error occurred.',
                'data' => [],
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return new JsonResponse([
            'status' => 'success',
            'data' => [
                'username' => $user->username,
                'isAuctioneer' => $user->is_auctioneer,
            ],
        ]);
    }
}
