<?php

namespace App\BusinessDomain\Authentication\Service;

use App\BusinessDomain\Authentication\Exception\InvalidCredentialsException;
use App\Models\DTO\LoginDTO;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthenticationService
{
    public function doesUserExist(string $username): bool
    {
        return (bool)User::where(['username' => $username])->first() ?? false;
    }

    public function doesAnAuctioneerAgentExist(): bool
    {
        return \count(User::where(['is_auctioneer' => true])->get()) > 0;
    }

    /**
     * @throws InvalidCredentialsException
     *
     * @return LoginDTO Object containing all login data
     */
    public function loginUser(string $username, string $password): LoginDTO
    {
        $user = User::where(['username' => $username])->first();

        if (Hash::check($password, $user->password)) {
            $token = $user->createToken(Str::random(40));

            return new LoginDTO(
                plainTextToken: $token->plainTextToken,
                username: $user->username,
                isAuctioneer: $user->is_auctioneer,
            );
        }

        throw new InvalidCredentialsException();
    }
}
