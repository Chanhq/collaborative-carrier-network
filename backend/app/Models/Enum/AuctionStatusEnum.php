<?php

namespace App\Models\Enum;

enum AuctionStatusEnum: string
{
    case Active = 'active';
    case Inactive = 'inactive';
}
