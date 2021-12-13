<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Movie;
use App\Models\RentedMovie;
use App\Models\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class MovieController extends Controller
{
    /**
     * @var array
     */
    protected $ApiResponse = [
        'status' => true,
        'message' => 'success',
        'data' => null
    ];

    /**
     * @var Movie
     */
    protected $Movie;
    /**
     * @var RentedMovie
     */
    protected $RentedMovie;
    /**
     * @var Client
     */
    protected $Client;
    /**
     * @var
     */
    protected $RequestValidate;
    /**
     * @var bool
     */
    protected $Validate = false;
    /**
     * @var
     */
    protected $Movies;
    /**
     * @var
     */
    protected $ClientId;

    /**
     * @param Movie $Movie
     * @param RentedMovie $RentedMovie
     * @param Client $Client
     */
    public function __construct(Movie $Movie, RentedMovie $RentedMovie, Client $Client)
    {
        $this->Movie = $Movie;
        $this->RentedMovie = $RentedMovie;
        $this->Client = $Client;
    }

    /**
     * @param $request
     * @return void
     */
    private function validateRentMoviesRequest($request): void
    {
        $this->RequestValidate = Validator::make($request->all(), [
            "client_email" => 'required|email',
            "movies_id" => 'required|array',
            "date_ini" => 'required|date|date_format:Y-m-d',
            "date_end" => 'required|date|date_format:Y-m-d|after:date_ini'
        ]);

        if ($this->RequestValidate->fails()) {
            $this->ApiResponse['status'] = 'false';
            $this->ApiResponse['message'] = 'error';
            $this->ApiResponse['data'] = $this->RequestValidate->getMessageBag();
        }
    }

    /**
     * @return void
     */
    private function validateMoviesWithStock(): void
    {
        if (!($this->Movies->count() > 0)) {
            $this->ApiResponse['status'] = 'false';
            $this->ApiResponse['message'] = 'Movies not available';
            $this->Validate = true;
        }
    }

    /**
     * @return void
     */
    private function validateClient(): void
    {
        if (!$this->ClientId) {
            $this->ApiResponse['status'] = 'false';
            $this->ApiResponse['message'] = 'Email invalid or client disabled';
            $this->Validate = true;
        }
    }

    /**
     * @param $request
     * @return void
     */
    private function validateRentMovies($request)
    {
        $this->validateRentMoviesRequest($request);
        $this->Movies = $this->Movie->getMoviesWithStock($request['movies_id']);
        $this->ClientId = $this->Client->getClientIdByEmail($request['client_email']);
        $this->validateMoviesWithStock();
        $this->validateClient();
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getMoviesList(Request $request): JsonResponse
    {
        try {
            $this->ApiResponse['data'] = $this->Movie->getMovies($request);
            return response()->json($this->ApiResponse, 200);
        } catch (\Exception $e) {
            Log::error("ERROR: MovieController::getMoviesList -> {$e}");
            $this->ApiResponse['status'] = false;
            $this->ApiResponse['message'] = 'error';
            $this->ApiResponse['data'] = $e->getMessage();
            return response()->json($this->ApiResponse, 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function getBudget(Request $request): JsonResponse
    {
        try {
            $this->validateRentMovies($request);
            if (!$this->Validate && !$this->RequestValidate->fails()) {
                $dateIni = new Carbon($request['date_ini']);
                $dateEnd = new Carbon($request['date_end']);
                $totalDays = $dateIni->diffInDays($dateEnd);
                $budget = [];

                foreach ($this->Movies as $movie) {
                    $budget[] = [
                        'movie' => $movie->name,
                        'price' => $this->RentedMovie->getMovieRentTotalPrice($movie, $totalDays),
                        'dateIni' => $dateIni,
                        'dateEnd' => $dateEnd
                    ];
                }

                $this->ApiResponse['data'] = $budget;
            }
            return response()->json($this->ApiResponse, 200);
        } catch (\Exception $e) {
            Log::error("ERROR: MovieController::getBudget -> {$e}");
            $this->ApiResponse['status'] = false;
            $this->ApiResponse['message'] = 'error';
            $this->ApiResponse['data'] = $e->getMessage();
            return response()->json($this->ApiResponse, 500);
        }
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function rentMovies(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();
            $this->validateRentMovies($request);
            if (!$this->Validate && !$this->RequestValidate->fails()) {
                $dateIni = new Carbon($request['date_ini']);
                $dateEnd = new Carbon($request['date_end']);
                $totalLoyalPoints = 0;
                $movieRentedTickets = [];

                foreach ($this->Movies as $movie) {
                    $movieRentedTickets[] = $this->RentedMovie->generateMovieRent($movie, $dateIni, $dateEnd, $this->ClientId);
                    $this->Movie->updateStockMovieRented($movie);
                    $totalLoyalPoints += $movie->movieType->loyalty_points_rewards;
                }

                $this->Client->addLoyaltyPoints($this->ClientId, $totalLoyalPoints);
                $movieRentedTickets['totalLoyaltyPointsObtained'] = $totalLoyalPoints;
                $this->ApiResponse['data']['ticketsMovieRented'] = $movieRentedTickets;
            }
            DB::commit();
            return response()->json($this->ApiResponse, 200);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error("ERROR: MovieController::rentMovies -> {$e}");
            $this->ApiResponse['status'] = false;
            $this->ApiResponse['message'] = 'error';
            $this->ApiResponse['data'] = $e->getMessage();
            return response()->json($this->ApiResponse, 500);
        }
    }
}
