<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     * @throws Exception
     */
    public function run()
    {
        // Clients
        for ($i = 1; $i <= 20; $i++) {
            DB::table('clients')->insert([
                'email' => Str::random(10) . '@gmail.com',
                'name' => Str::random(10),
                'tlf' => '9' . random_int(11111111, 99999999),
                'address' => Str::random(30),
                'loyalty_points' => random_int(0, 50),
                'enabled' => (bool)random_int(0,1),
                'created_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }

        // Movies Types
        DB::table('movies_type')->insert([
            'name' => 'Nuevos lanzamientos',
            'loyalty_points_rewards' => 2,
            'normal_price_days_limit' => 0,
            'additional_payment_charge' => 0,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('movies_type')->insert([
            'name' => 'Películas normales',
            'loyalty_points_rewards' => 1,
            'normal_price_days_limit' => 3,
            'additional_payment_charge' => 2,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        DB::table('movies_type')->insert([
            'name' => 'Películas viejas',
            'loyalty_points_rewards' => 1,
            'normal_price_days_limit' => 5,
            'additional_payment_charge' => 3,
            'created_at' => Carbon::now()->format('Y-m-d H:i:s')
        ]);

        // Movies
        for ($i = 1; $i <= 80; $i++) {
            $stock = random_int(1, 100);
            DB::table('movies')->insert([
                'name' => Str::random(10),
                'price' => 3,
                'stock' => $stock,
                'total_rented' => random_int(1, $stock),
                'movie_type' => random_int(1, 3),
                'enabled' => (bool)random_int(0, 1),
                'created_at' => Carbon::now()->format('Y-m-d H:i:s')
            ]);
        }
    }
}
