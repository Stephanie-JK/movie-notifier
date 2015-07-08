<?php

use App\CinemaHall;
use Illuminate\Database\Seeder;

class CinemaHalls extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        CinemaHall::create([
           'name' => 'QFX',
            'logo' => 'http://nikhil.com.np/images/qfx.png',
            'url' => 'http://qfxcinemas.com',
        ]);
    }
}
