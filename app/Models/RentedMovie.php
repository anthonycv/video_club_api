<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RentedMovie extends Model
{
    use HasFactory;

    /**
     * @var string
     */
    public $table = 'rented_movies';

    /**
     * @var string[]
     */
    protected $fillable = [
        'id',
        'movie_id',
        'client_id',
        'total',
        'loyalty_points_obtained',
        'date_ini',
        'date_end',
        'enabled'
    ];

    /**
     * @param $movie
     * @param $totalDays
     * @return float|int
     */
    public function getMovieRentTotalPrice($movie, $totalDays)
    {
        $limitDays = $movie->movieType->normal_price_days_limit;

        $paymentChargeDays = $totalDays - $limitDays;

        if ($paymentChargeDays > 0) {
            $total =
                ((double)$limitDays * (double)$movie->price) +
                ((double)$paymentChargeDays * ((double)$movie->price + (double)$movie->movieType->additional_payment_charge));
        } else {
            $total = (double)$movie->price * (double)$totalDays;
        }

        return $total;
    }

    /**
     * @param $movie
     * @param $dateIni
     * @param $dateEnd
     * @param $clientId
     * @return array
     */
    public function generateMovieRent($movie, $dateIni, $dateEnd, $clientId): array
    {
        $totalDays = $dateIni->diffInDays($dateEnd);
        $rentedMovie = new RentedMovie();
        $rentedMovie->movie_id = $movie->id;
        $rentedMovie->client_id = $clientId;
        $rentedMovie->total = $this->getMovieRentTotalPrice($movie, $totalDays);
        $rentedMovie->loyalty_points_obtained = $movie->movieType->loyalty_points_rewards;
        $rentedMovie->date_ini = $dateIni;
        $rentedMovie->date_end = $dateEnd;
        $rentedMovie->save();
        return [
            'ticket' => $rentedMovie->id,
            'movie' => $movie->name,
            'price' => $rentedMovie->total,
            'dateIni' => $dateIni,
            'dateEnd' => $dateEnd
        ];
    }

}
