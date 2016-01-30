<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CinemaHall extends Model
{
    use SoftDeletes;

    protected $fillable = ['name', 'logo', 'url'];

    public static function boot()
    {
        parent::boot();

        static::deleted(function ($cinema) {
            $cinema->movies()->delete();
        });
    }

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
