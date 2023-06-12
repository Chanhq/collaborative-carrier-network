<?php

namespace App\Models;

use App\Models\Enum\TransportRequestAuctionStatusEnum;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransportRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'requester_name',
        'origin_node',
        'destination_node',
    ];

    protected $casts = [
      'auction_status' => TransportRequestAuctionStatusEnum::class,
    ];

    public function requesterName(): string
    {
        return $this->requester_name;
    }

    public function originNode(): int
    {
        return $this->origin_node;
    }

    public function destinationNode(): int
    {
        return $this->destination_node;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
