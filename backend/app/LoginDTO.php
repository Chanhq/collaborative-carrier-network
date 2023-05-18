<?php

namespace App;

class LoginDTO
{
    public readonly string $plainTextToken;

    public readonly string $username;

    public readonly bool $isAuctioneer;

    public function __construct($plainTextToken, $username, $isAuctioneer)
    {
        $this->plainTextToken = $plainTextToken;
        $this->username = $username;
        $this->isAuctioneer = $isAuctioneer;
    }
}
