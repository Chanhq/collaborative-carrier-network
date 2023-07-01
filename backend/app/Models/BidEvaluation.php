<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BidEvaluation extends Model
{
    use HasFactory;

    protected $table = 'bid_evaluation';

    protected $fillable = [
        'bid_id',
        'user_id',
        'transport_request_id',
        'revenue_gain',
    ];

    public function id(): int
    {
        return $this->id;
    }

    public function bid()
    {
        return $this->belongsTo(AuctionBid::class, 'bid_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function transportRequest()
    {
        return $this->belongsTo(TransportRequest::class, 'transport_request_id');
    }
}
