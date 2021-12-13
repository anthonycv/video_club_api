<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Movie extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    public $table = 'movies';

    /**
     * @var string[]
     */
    protected $fillable = [
        'id',
        'name',
        'price',
        'stock',
        'total_rented',
        'movie_type',
        'enabled'
    ];

    /**
     * @return HasOne
     */
    public function movieType(): HasOne
    {
        return $this->hasOne(MovieType::class, 'id', 'movie_type');
    }

    /**
     * @param $request
     * @return mixed
     */
    public function getMovies($request)
    {
        $limit = $request['limit'] ?? 10;
        $movieTypeName = $request['movie_type'] ?? null;
        return $this->select(
            'movies.id',
            'movies.name',
            'movies.price',
            'movies.stock',
            'movies.total_rented',
            'movies_type.name as movie_type',
            'movies_type.loyalty_points_rewards',
            'movies_type.normal_price_days_limit',
            'movies_type.additional_payment_charge'
        )
            ->join('movies_type', 'movies.movie_type', '=', 'movies_type.id')
            ->where('movies.enabled', true)
            ->where(function ($query) use ($movieTypeName) {
                return ($movieTypeName) ? $query->where('movies_type.name', '=', $movieTypeName) : $query;
            })
            ->paginate($limit);
    }

    /**
     * @param $moviesId
     * @return Builder[]|Collection
     */
    public function getMoviesWithStock($moviesId)
    {
        return $this
            ->with('movieType')
            ->where('movies.enabled', true)
            ->whereRaw('movies.stock > movies.total_rented')
            ->whereIn('movies.id', $moviesId)
            ->get();
    }

    /**
     * @param $movie
     * @return mixed
     */
    public function updateStockMovieRented($movie)
    {
        return $movie->update(['total_rented' => $movie->total_rented + 1]);
    }
}
