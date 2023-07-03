<?php

namespace App\Models;

use App\Models\Enum\AuctionStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

/**
 * App\Models\Auction
 *
 * @method static Builder active()
 * @method static Builder inactive()
 * @method static Builder completed()
 * @property int $id
 * @property AuctionStatusEnum $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TransportRequest> $transportRequests
 * @property-read int|null $transport_requests_count
 * @method static Builder|Auction newModelQuery()
 * @method static Builder|Auction newQuery()
 * @method static Builder|Auction query()
 * @method static Builder|Auction whereCreatedAt($value)
 * @method static Builder|Auction whereId($value)
 * @method static Builder|Auction whereStatus($value)
 * @method static Builder|Auction whereUpdatedAt($value)
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
        return $query->where('status', AuctionStatusEnum::Active);
    }

    public function scopeInactive(Builder $query): Builder
    {
        return $query->where('status', AuctionStatusEnum::Inactive);
    }

    public function scopeCompleted(Builder $query): Builder
    {
        return $query->where('status', AuctionStatusEnum::Completed);
    }

    public function setInactive(): Auction
    {
        $this->status = AuctionStatusEnum::Inactive;
        $this->save();
        return $this;
    }
}
