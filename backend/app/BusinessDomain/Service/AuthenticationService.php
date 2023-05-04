<?php

namespace App\BusinessDomain\Service;

use App\BusinessDomain\Exception\InvalidCredentialsException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

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
     */
    public function getApiTokenByUsername(string $username, string $password): string
    {
        $user = User::where(['username' => $username])->first();

        if(Hash::check($password, $user->password)) {
            return $user->api_token;
        }

        throw new InvalidCredentialsException();
    }
}
