<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRentedMoviesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rented_movies', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('movie_id')->unsigned();
            $table->bigInteger('client_id')->unsigned();
            $table->decimal('total', 10, 2);
            $table->integer('loyalty_points_obtained')->comment('With this row we can see the history of loyalty points obtained by rented movie');
            $table->dateTime('date_ini');
            $table->dateTime('date_end');
            $table->boolean('enabled')->default(true)->comment('Is false when the movie is back in stock');
            $table->timestamps();

            $table->foreign('movie_id')->references('id')->on('movies');
            $table->foreign('client_id')->references('id')->on('clients');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rented_movies');
    }
}
