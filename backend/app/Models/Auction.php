<?php

namespace App\Models;

use App\Models\Enum\AuctionStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * @method static Builder active()
 */
class Auction extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'status',
    ];
    /**
     * @var string[]
     */
    protected $casts = [
        'status' => AuctionStatusEnum::class,
    ];

    public function id(): int
    {
        return $this->id;
    }

    public function status(): AuctionStatusEnum
    {
        return $this->status;
    }

    public function transportRequests(): HasMany
    {
        return $this->hasMany(TransportRequest::class);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', AuctionStatusEnum::Active->value);
    }
}
