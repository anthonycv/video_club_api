<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class MovieType extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    public $table = 'movies_type';

    /**
     * @var string[]
     */
    protected $fillable = [
        'id',
        'name',
        'loyalty_points_rewards',
        'normal_price_days_limit',
        'additional_payment_charge',
        'enabled'
    ];

    /**
     * @return HasMany
     */
    public function movies(): HasMany
    {
        return $this->hasMany(Movie::class, 'movie_type', 'id');
    }
}
