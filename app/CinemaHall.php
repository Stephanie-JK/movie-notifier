<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CinemaHall extends Model
{

    protected $fillable = [ 'name', 'logo', 'url' ];


    public function movies()
    {
        return $this->hasMany(Movie::class);
    }


    public function showtimes()
    {
        return $this->hasManyThrough(Showtime::class, Movie::class);
    }


    public function notifications()
    {
        $this->hasManyThrough(Notification::class, Movie::class);
    }
}
