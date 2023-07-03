<?php

namespace App\Models;

use Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * App\Models\User
 *
 * @method static User|null find(int $id)
 * @property int $id
 * @property string $username
 * @property string $password
 * @property float $transport_request_set_revenue_pre_auction
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property bool $is_auctioneer
 * @property int $transport_request_minimum_revenue
 * @property int $transport_request_cost_base
 * @property int $transport_request_cost_variable
 * @property int $transport_request_price_base
 * @property int $transport_request_price_variable
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\TransportRequest> $transportRequests
 * @property-read int|null $transport_requests_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|User query()
 * @method static \Illuminate\Database\Eloquent\Builder|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereIsAuctioneer($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTransportRequestCostBase($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTransportRequestCostVariable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTransportRequestMinimumRevenue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTransportRequestPriceBase($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTransportRequestPriceVariable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereTransportRequestSetRevenuePreAuction($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|User whereUsername($value)
 */
class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'password',
        'is_auctioneer',
        'transport_request_minimum_revenue',
        'transport_request_cost_base',
        'transport_request_cost_variable',
        'transport_request_price_base',
        'transport_request_price_variable',
        'transport_request_set_revenue_pre_auction'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function id(): int
    {
        return $this->id;
    }

    public function transportRequestSetRevenuePreAuction(): float
    {
        return $this->transport_request_set_revenue_pre_auction;
    }

    public function transportRequestMinimumRevenue(): int
    {
        return $this->transport_request_minimum_revenue;
    }

    public function transportRequestCostBase(): int
    {
        return $this->transport_request_cost_base;
    }

    public function transportRequestCostVariable(): int
    {
        return $this->transport_request_cost_variable;
    }

    public function transportRequestPriceBase(): int
    {
        return $this->transport_request_price_base;
    }

    public function transportRequestPriceVariable(): int
    {
        return $this->transport_request_price_variable;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function isAuctioneer(): bool
    {
        return $this->is_auctioneer;
    }

    public function setTransportRequestSetRevenuePreAuction(float $value): User
    {
        $this->transport_request_set_revenue_pre_auction = $value;
        $this->save();
        return $this;
    }

    public function transportRequests(): HasMany
    {
        return $this->hasMany(TransportRequest::class);
    }

    public function auctionEvaluations(): HasMany
    {
        return $this->hasMany(AuctionEvaluation::class);
    }

    /**
     * @return array<User>
     */
    public static function carriers(): array
    {
        return User::all()->where('is_auctioneer', false)->all();
    }
}
