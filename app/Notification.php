<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{

    protected $fillable = [ 'user_id', 'movie_id', 'date', 'sent' ];


    public function scopeSent($query)
    {
        return $query->where('sent', '=', true);
    }


    public function scopeUnsent($query)
    {
        return $query->where('sent', '=', false);
    }


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }


    public function hasShowTime()
    {
        return $this->movie->showtimes()->where('date', '=', $this->date)->count() ? true : false;
    }
}
