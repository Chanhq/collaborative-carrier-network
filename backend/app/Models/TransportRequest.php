<?php

namespace App\Models;

use App\Models\Enum\TransportRequestStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * App\Models\TransportRequest
 *
 * @property int $id
 * @property int $user_id
 * @property int $origin_node
 * @property int $destination_node
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property TransportRequestStatusEnum $status
 * @property int|null $auction_id
 * @property-read \App\Models\Auction|null $auction
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\AuctionBid> $bids
 * @property-read int|null $bids_count
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|TransportRequest newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransportRequest newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|TransportRequest query()
 * @method static \Illuminate\Database\Eloquent\Builder|TransportRequest whereAuctionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportRequest whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportRequest whereDestinationNode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportRequest whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportRequest whereOriginNode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportRequest whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportRequest whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|TransportRequest whereUserId($value)
 */
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
        'auction_id',
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

    public function revenue(): float
    {
        return $this->revenue;
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

    public function markAsSold(): void
    {
        $this->status = TransportRequestStatusEnum::Sold;
        $this->save();
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
