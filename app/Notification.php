<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Notification extends Model
{
    use SoftDeletes;

    protected $guarded = [];

    protected $dates = ['date'];

    public function scopeSent($query)
    {
        return $query->where('sent', '=', true);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('id', 'DESC');
    }

    public function scopeUnsent($query)
    {
        return $query->where('sent', '=', false);
    }

    public function scopeToReserve($query)
    {
        return $query->where('no_of_seats', '>', 0);
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
        return $this->movie->showtimes()->where('date', '=', $this->date->format('Y-m-d'))->count() ? true : false;
    }

    public function requiresReservation()
    {
        return $this->reservations()->count() != $this->showtime()->timeslots()->count();
    }

    public function reservations()
    {
        return $this->belongsToMany(TimeSlot::class, 'notification_timeslot', 'notification_id', 'timeslot_id');
    }

    public function showtime()
    {
        return $this->movie->showtimes()->where('date', '=', $this->date->format('Y-m-d'))->first();
    }

    public function pendingTimeSlots()
    {
        $ids = DB::table('notification_timeslot')->where('notification_id', $this->id)->lists('timeslot_id');

        return $this->showtime()->timeslots()->whereNotIn('id', $ids)->get();
    }
}
