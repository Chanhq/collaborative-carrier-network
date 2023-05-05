<?php

namespace App\BusinessDomain\Authentication\Service;

use App\BusinessDomain\Authentication\Exception\InvalidCredentialsException;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Sanctum\NewAccessToken;

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
     * @return NewAccessToken The newly generated and valid api token
     */
    public function loginUser(string $username, string $password): NewAccessToken
    {
        $user = User::where(['username' => $username])->first();

        if(Hash::check($password, $user->password)) {
            return $user->createToken(Str::random(40));
        }

        throw new InvalidCredentialsException();
    }
}
