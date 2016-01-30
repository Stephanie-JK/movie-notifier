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
            'id'   => 1,
            'name' => 'QFX Cinemas',
            'logo' => asset('images/qfx.png'),
            'url'  => 'http://qfxcinemas.com',
        ]);

        CinemaHall::create([
            'id'   => 2,
            'name' => 'FCube Cinemas',
            'logo' => asset('images/fcube.png'),
            'url'  => 'http://www.fcubecinemas.com',
        ]);
    }
}
