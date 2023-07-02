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
        'revenue_gain',
        'price_to_pay',
    ];

    public function id(): int
    {
        return $this->id;
    }

    public function revenueGain(): float
    {
        return $this->revenue_gain;
    }


    public function priceToPay(): float
    {
        return $this->price_to_pay;
    }



    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
