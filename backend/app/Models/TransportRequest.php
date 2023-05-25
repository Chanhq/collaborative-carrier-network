<?php

namespace App\Models;

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
        'origin_x',
        'origin_y',
        'destination_x',
        'destination_y',
    ];


    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
