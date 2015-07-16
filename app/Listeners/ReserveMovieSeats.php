<?php

namespace App\Listeners;

use App\Events\ShowTimesWereRetrieved;
use App\Notification;
use App\Qfx\Qfx;
use Carbon\Carbon;

class ReserveMovieSeats
{

    /**
     * Create the event listener.
     *
     */
    public function __construct()
    {
        //
    }


    /**
     * Handle the event.
     *
     * @param  ShowTimesWereRetrieved $event
     *
     * @return void
     */
    public function handle(ShowTimesWereRetrieved $event)
    {
        $notifications = Notification::toReserve()->get();
        foreach ($notifications as $notification) {
            if ($notification->hasShowTime() && $notification->requiresReservation()) {
                foreach ($notification->pendingTimeSlots() as $pending)
                {
                    $time = Carbon::createFromFormat('g:i A', $pending->time);
                    $after_time = Carbon::createFromFormat('g:i A', $notification->after_time);
                    $before_time = Carbon::createFromFormat('g:i A', $notification->before_time);
                    if($time->gte($after_time) && !$time->gt($before_time))
                    {
                        if($notification->movie->cinema == 1)
                        {
                            Qfx::book($pending->showId, $notification->no_of_seats);
                        }
                        $notification->reservations()->attach($pending->id);
                    }
                }
            }
        }
    }
}
