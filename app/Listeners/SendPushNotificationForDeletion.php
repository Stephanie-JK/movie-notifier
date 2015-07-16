<?php

namespace App\Listeners;

use App\Events\NotificationWasDeleted;
use App\GCM;

class SendPushNotificationForDeletion
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
     * @param  NotificationWasDeleted $event
     *
     * @return void
     */
    public function handle(NotificationWasDeleted $event)
    {
        $notification = $event->notification;

        $deviceToken = $notification->user->gcm_id;

        $message    = 'Sorry but '.$notification->movie->name . ' was not run on ' . $notification->date->format("l, dS M") . ' by ' . $notification->movie->cinema->name;
        $title      = $notification->movie->cinema->name;
        $url        = $notification->movie->cinema->url;
        $icon_image = $notification->movie->image;
        $big_image  = $notification->movie->cinema->logo;

        $push = GCM::to($deviceToken)->send($message,
            [ 'title' => $title, 'url' => $url, 'icon_image' => $icon_image, 'big_image' => $big_image ]);
    }
}
