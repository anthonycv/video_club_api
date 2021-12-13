<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Movies
Route::get('/get-movies',  'MovieController@getMoviesList');
Route::get('/get_budget',  'MovieController@getBudget');
Route::post('/rent_movies',  'MovieController@rentMovies');

// Clients
Route::get('/get-client-loyalty-points',  'ClientController@getClient');
