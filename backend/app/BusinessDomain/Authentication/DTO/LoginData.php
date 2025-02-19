<?php

namespace App\BusinessDomain\Authentication\DTO;

class LoginData
{
    public readonly string $plainTextToken;

    public readonly string $username;

    public readonly bool $isAuctioneer;

    public function __construct(string $plainTextToken, string $username, bool $isAuctioneer)
    {
        $this->plainTextToken = $plainTextToken;
        $this->username = $username;
        $this->isAuctioneer = $isAuctioneer;
    }
}
