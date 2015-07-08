<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Showtime extends Model
{

    protected $fillable = [ 'date' ];

    protected $dates = [ 'date' ];


    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
