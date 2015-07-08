<?php

namespace App\Listeners;

use App\Events\UpcomingShowsWereRetrieved;

class SendPushNotificationToUpdateTheDatabase
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
     * @param  UpcomingShowsWereRetrieved $event
     *
     * @return void
     */
    public function handle(UpcomingShowsWereRetrieved $event)
    {
        //
    }
}
