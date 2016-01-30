<?php

namespace App\Console\Commands;

use App\Events\NotificationWasDeleted;
use App\Movie;
use App\Showtime;
use Illuminate\Console\Command;

class OldShowsRemover extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'movie:remove';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Removes old shows no longer having show times.';

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $today = date('Y-m-d');
        $showtimes = Showtime::where('date', '<', $today)->with('movie')->get();
        foreach ($showtimes as $showtime) {
            $this->removeOldNotifications($showtime);
            $showtime->delete();
        }

        $movies = Movie::where('release_date', '<', $today)->get();
        foreach ($movies as $movie) {
            $this->removeIfHasNoLongerShowTimes($movie);
        }
    }

    /**
     * Removes the old notifications.
     *
     * @param $showtime
     *
     * @internal param $today
     */
    private function removeOldNotifications(Showtime $showtime)
    {
        $oldNotifications = $showtime->movie->notifications()->with(['movie', 'user', 'movie.cinema'])->where('date',
            '<', date('Y-m-d'))->get();
        foreach ($oldNotifications as $notification) {
            event(new NotificationWasDeleted($notification));
            $notification->delete();
        }
    }

    /**
     * @param $movie
     */
    public function removeIfHasNoLongerShowTimes($movie)
    {
        if (!$movie->showtimes()->count()) {
            $movie->delete();
        }
    }
}
