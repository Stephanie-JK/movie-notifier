<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Showtime extends Model
{
    use SoftDeletes;

    protected $fillable = ['date'];

    protected $dates = ['date'];

    public static function boot()
    {
        parent::boot();

        static::deleted(function ($showtime) {
            $showtime->timeslots()->delete();
        });
    }

    public function movie()
    {
        return $this->belongsTo(Movie::class);
    }

    public function timeslots()
    {
        return $this->hasMany(TimeSlot::class);
    }
}
