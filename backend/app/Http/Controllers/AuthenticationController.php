<?php

namespace App\Http\Controllers;

use App\BusinessDomain\Exception\InvalidCredentialsException;
use App\BusinessDomain\Service\AuthenticationService;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Nette\Utils\Random;
use Symfony\Component\HttpFoundation\Response;

class AuthenticationController extends Controller
{
    public function __construct(private readonly AuthenticationService $authenticationService)
    {
    }

    /**
     * Registers new User
     *
     * Takes username, password and information on whether a carrier agent or an auctioneer agent
     * shall be registered and registers a new user if eligible.
     */
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
            'isAuctioneerRegistration' => 'required|boolean'
        ]);

        if($this->authenticationService->doesUserExist($validated['username'])) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'User already exists!',
                'data' => []
            ], Response::HTTP_CONFLICT);
        }

        if($validated['isAuctioneerRegistration'] && $this->authenticationService->doesAnAuctioneerAgentExist()) {
            return new JsonResponse([
                'status' => 'error',
                'message' => 'Cannot register more than one auctioneer agent.',
                'data' => []
            ], Response::HTTP_CONFLICT);
        }

        $user = User::create([
            'username' => $validated['username'],
            'password' => Hash::make($validated['password']),
            'api_token' => Hash::make(Random::generate()),
            'is_auctioneer' => $validated['isAuctioneerRegistration']
        ]);

        return new JsonResponse([
            'status' => 'success',
            'message' => 'Successfully created user!',
            'data' => [
                'api_token' => $user->api_token
            ]
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
            $apiToken = $this->authenticationService->getApiTokenByUsername($username, $password);
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
                'api_token' => $apiToken,
            ]
        ]);
    }
}
