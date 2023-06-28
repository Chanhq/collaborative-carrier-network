<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @method static User|null find(int $id)
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

    public function transportRequests(): HasMany
    {
        return $this->hasMany(TransportRequest::class);
    }
}
