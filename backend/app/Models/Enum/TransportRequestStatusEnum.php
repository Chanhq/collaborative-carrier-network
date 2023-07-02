<?php

namespace App\Models\Enum;

enum TransportRequestStatusEnum: string
{
    case Pristine = 'pristine';
    case Selected = 'selected';
    case Sold = 'sold';
    case Unsold = 'unsold';
    case Completed = 'completed';
}
