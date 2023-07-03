<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\AuctionEvaluation
 *
 * @property int $id
 * @property int $auction_id
 * @property int $user_id
 * @property float $revenue_gain
 * @property float $price_to_pay
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|AuctionEvaluation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AuctionEvaluation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AuctionEvaluation query()
 * @method static \Illuminate\Database\Eloquent\Builder|AuctionEvaluation whereAuctionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuctionEvaluation whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuctionEvaluation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuctionEvaluation wherePriceToPay($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuctionEvaluation whereRevenueGain($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuctionEvaluation whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuctionEvaluation whereUserId($value)
 */
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
