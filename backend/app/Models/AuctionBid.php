<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Models\AuctionBid
 *
 * @property int $id
 * @property int $auction_id
 * @property int $user_id
 * @property int $transport_request_id
 * @property string $bid_amount
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Auction $auction
 * @property-read \App\Models\TransportRequest $transportRequest
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder|AuctionBid newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AuctionBid newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AuctionBid query()
 * @method static \Illuminate\Database\Eloquent\Builder|AuctionBid whereAuctionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuctionBid whereBidAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuctionBid whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuctionBid whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuctionBid whereTransportRequestId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuctionBid whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AuctionBid whereUserId($value)
 */
class AuctionBid extends Model
{
    use HasFactory;

    protected $fillable = [
        'auction_id',
        'user_id',
        'transport_request_id',
        'bid_amount',
    ];

    public function transportRequest(): BelongsTo
    {
        return $this->belongsTo(TransportRequest::class);
    }

    public function auction(): BelongsTo
    {
        return $this->belongsTo(Auction::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
