<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movie extends Model
{
    use SoftDeletes;

    protected $fillable = ['cinema_id', 'name', 'image', 'release_date', 'url'];

    protected $dates = ['release_date'];

    public static function boot()
    {
        parent::boot();

        static::deleted(function ($movie) {
            $movie->showtimes()->delete();
            $movie->notifications()->delete();
        });
    }

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
