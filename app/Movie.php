<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{

    protected $fillable = [ 'cinema_id', 'name', 'image', 'release_date', 'url' ];

    protected $dates = [ 'release_date' ];


    public function cinema()
    {
        return $this->belongsTo(CinemaHall::class, 'cinema_hall_id', 'id');
    }


    public function showtimes()
    {
        return $this->hasMany(Showtime::class);
    }


    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }
}
