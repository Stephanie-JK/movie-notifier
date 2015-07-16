<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Showtime extends Model
{

    use SoftDeletes;

    protected $fillable = [ 'date' ];

    protected $dates = [ 'date' ];


    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }
}
