<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AuctionEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'auction_id',
        'user_id',
        'transport_request_id',
        'revenue_gain',
    ];

    public function id(): int
    {
        return $this->id;
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transportRequest()
    {
        return $this->belongsTo(TransportRequest::class);
    }
}
