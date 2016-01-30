<?php

namespace App\Console\Commands;

use App\Crawler\FCube\FCubeProvider;
use App\Crawler\QFX\QFXProvider;
use App\Events\ShowTimesWereRetrieved;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;

class ShowsRetriever extends Command implements SelfHandling
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'movie:released';

    protected $providers = [
        QFXProvider::class,
        FCubeProvider::class,
    ];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve released movies';

    /**
     * @var Client
     */
    private $client;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
        $this->client = new Client();
    }

    /**
     * Execute the command.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->providers as $provider) {
            $this->addMovies(new $provider);
        }
        event(new ShowTimesWereRetrieved());
    }

    /**
     * @param $provider
     */
    public function addMovies($provider)
    {
        $movies = $provider->released();
        foreach ($movies as $movie) {
            $movieModel = $this->getMovieOrCreate($provider, $movie);
            $this->addShowTimesToMovie($movieModel, $movie['showtime']);
            $movieModel->touch();
        }
    }

    /**
     * @param $provider
     * @param $movie
     *
     * @return mixed
     */
    public function getMovieOrCreate($provider, $movie)
    {
        $movieModel = $provider->model()->movies()->whereName($movie['name']);
        if ($movieModel->count()) {
            $movieModel = $movieModel->first();

            return $movieModel;
        }

        $movieModel = $provider->model()->movies()->firstOrCreate(array_except($movie, 'showtime'));

        return $movieModel;
    }

    /**
     * Adds Showtime to the provided movie.
     * @param $movieModel
     * @param $showtime
     */
    public function addShowTimesToMovie($movieModel, $showtime)
    {
        $showtimeModele = $movieModel->showtimes()->firstOrCreate(array_except($showtime, 'timeslots'));
        $timeslots = array_get($showtime, 'timeslots');
        if ($timeslots) {
            $this->addTimeSlotsToShowtime($showtime['timeslots'], $showtimeModele);
        }
        $showtimeModele->touch();
    }

    /**
     * Adds time slots to the provided showtime.
     * @param $timeslots
     * @param $showtime
     */
    private function addTimeSlotsToShowtime($timeslots, $showtime)
    {
        foreach ($timeslots as $timeslot) {
            $timeslotModel = $showtime->timeslots()->firstOrCreate($timeslot);
            $timeslotModel->touch();
        }
    }
}
