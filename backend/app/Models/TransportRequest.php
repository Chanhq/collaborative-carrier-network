<?php

namespace App\Models;

use App\Models\Enum\TransportRequestStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TransportRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'origin_node',
        'destination_node',
        'status',
        'auction_id'
    ];

    protected $casts = [
        'status' => TransportRequestStatusEnum::class,
    ];

    public function id(): int
    {
        return $this->id;
    }

    public function originNode(): int
    {
        return $this->origin_node;
    }

    public function destinationNode(): int
    {
        return $this->destination_node;
    }

    public function status(): TransportRequestStatusEnum
    {
        return $this->status;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    public function bids(): HasMany
    {
        return $this->hasMany(AuctionBid::class);
    }

    public function markAsCompleted(): void
    {
        $this->status = TransportRequestStatusEnum::Completed;
        $this->save();
    }

    public function markAsUnsold(): void
    {
        $this->status = TransportRequestStatusEnum::Unsold;
        $this->save();
    }
}
