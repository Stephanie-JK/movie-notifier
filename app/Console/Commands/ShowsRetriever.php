<?php

namespace App\Console\Commands;

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
        QFXProvider::class
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
     *
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
        //foreach ($this->providers as $provider) {
        //    $provider = new $provider;
        //
        //    $movies = $provider->released();
        //    foreach ($movies as $movie) {
        //        $provider->model()->movies()->firstOrCreate(array_except($movie,
        //                [ 'showtime' ]))->showtimes()->firstOrCreate($movie['showtime']);
        //    }
        //}

        event(new ShowTimesWereRetrieved());
    }
}
