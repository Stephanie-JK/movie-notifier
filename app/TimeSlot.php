<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    protected $fillable = ['showtime_id', 'time', 'showId', 'location'];

    public function showtime()
    {
        return $this->belongsTo(Showtime::class);
    }

    public function reservations()
    {
        $this->belongsToMany(self::class, 'notification_timeslot');
    }
}
