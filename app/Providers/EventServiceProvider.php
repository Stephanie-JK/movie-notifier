<?php

namespace App\Providers;

use Illuminate\Contracts\Events\Dispatcher as DispatcherContract;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        'App\Events\ShowTimesWereRetrieved' => [
            'App\Listeners\SendPushNotificationAboutShowtime',
            'App\Listeners\ReserveMovieSeats',
        ],
        'App\Events\UpcomingShowsWereRetrieved' => [
            'App\Listeners\SendPushNotificationToUpdateTheDatabase',
        ],
        'App\Events\NotificationWasDeleted' => [
            'App\Listeners\SendPushNotificationForDeletion',
        ],
    ];

    /**
     * Register any other events for your application.
     *
     * @param  \Illuminate\Contracts\Events\Dispatcher  $events
     * @return void
     */
    public function boot(DispatcherContract $events)
    {
        parent::boot($events);

        //
    }
}
