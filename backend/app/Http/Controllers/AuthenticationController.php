<?php

namespace App\Http\Controllers;

use App\BusinessDomain\Authentication\Exception\InvalidCredentialsException;
use App\BusinessDomain\Authentication\Service\AuthenticationService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends Controller
{
    public function __construct(private readonly AuthenticationService $authenticationService)
    {
    }

    public function register(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => [
                'required',
                'unique:users',
                'max:255',
                'string',
            ],
            'password' => 'required|string|min:8|max:255',
            'isAuctioneerRegistration' => 'required|boolean',
        ]);

        if($this->authenticationService->doesUserExist($validated['username'])) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'User already exists!',
                'data' => [],
            ], Response::HTTP_CONFLICT);
        }

        if($validated['isAuctioneerRegistration'] && $this->authenticationService->doesAnAuctioneerAgentExist()) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Cannot register more than one auctioneer agent.',
                'data' => [],
            ], Response::HTTP_CONFLICT);
        }

         User::create([
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'is_auctioneer' => $validated['isAuctioneerRegistration'],
        ]);

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Successfully created user!',
            'data' => [],
        ]);
    }

    public function login(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'username' => [
                'required',
                'max:255',
                'string',
            ],
            'password' => 'required|string|min:8|max:255',
        ]);
        $username = $validated['username'];
        $password = $validated['password'];

        if(!$this->authenticationService->doesUserExist($username)) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Credentials incorrect!',
                'data' => [],
            ], Response::HTTP_UNAUTHORIZED);
        }

        try {
            $apiToken = $this->authenticationService->loginUser($username, $password);
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
                'api_token' => $apiToken->plainTextToken,
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
}
