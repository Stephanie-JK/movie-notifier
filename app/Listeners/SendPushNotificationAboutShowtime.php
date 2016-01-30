<?php

namespace App\Listeners;

use App\Events\ShowTimesWereRetrieved;
use App\GCM;
use App\Notification;

class SendPushNotificationAboutShowtime
{
    /**
     * Create the event listener.
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
        $pending_notifications = Notification::unsent()->with(['movie', 'user', 'movie.cinema'])->get();
        foreach ($pending_notifications as $notification) {
            if ($notification->hasShowTime()) {
                $deviceToken = $notification->user->gcm_id;

                $message = $notification->movie->name.' tickets for '.$notification->date->format('l, dS M').' now available at '.$notification->movie->cinema->name;
                $title = $notification->movie->cinema->name;
                $url = $notification->movie->cinema->url;
                $icon_image = $notification->movie->image;
                $big_image = $notification->movie->cinema->logo;

                $push = GCM::to($deviceToken)->send($message,
                    ['title' => $title, 'url' => $url, 'icon_image' => $icon_image, 'big_image' => $big_image]);

                $notification->sent = true;
                $notification->save();
            }
        }
    }
}
