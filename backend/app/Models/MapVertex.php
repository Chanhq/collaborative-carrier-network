<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\MapVertex
 *
 * @method static MapVertex|null find(string $id)
 * @method static int max(string $column)
 * @property int $id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Database\Factories\MapVertexFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder|MapVertex newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MapVertex newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|MapVertex query()
 * @method static \Illuminate\Database\Eloquent\Builder|MapVertex whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MapVertex whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|MapVertex whereUpdatedAt($value)
 */
class MapVertex extends Model
{
    use HasFactory;
}
