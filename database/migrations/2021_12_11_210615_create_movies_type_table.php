<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMoviesTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movies_type', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('loyalty_points_rewards');
            $table->integer('normal_price_days_limit');
            $table->decimal('additional_payment_charge', 10, 2);
            $table->boolean('enabled')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movies_type');
    }
}
