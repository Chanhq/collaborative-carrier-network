<?php

namespace App\BusinessDomain\Service;

use App\Models\User;

class AuthenticationService
{
    public function doesUserAlreadyExist(string $username): bool
    {
        try {
            User::findOrFail(['username' => $username]);
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    public function doesAnAuctioneerAgentAlreadyExist(): bool
    {
        return \count(User::where(['is_auctioneer' => true])->get()) > 0;
    }
}
