<?php

namespace App\Console\Commands;

use App\Crawler\QFX\QFXProvider;
use App\Events\UpcomingShowsWereRetrieved;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;

class UpcomingShowsRetriever extends Command implements SelfHandling
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'movie:upcoming';

    protected $providers = [
        QFXProvider::class
    ];

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve upcoming movies';

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
        foreach ($this->providers as $provider) {
            $provider = new $provider;

            $movies = $provider->upcoming();
            foreach ($movies as $movie) {
                $movieModel = $provider->model()->movies()->whereName($movie['name']);
                if (!$movieModel->count()) {
                    $provider->model()->movies()->create($movie);
                }else{
                    $movieModel->first()->update($movie);
                }
            }
        }
        event(new UpcomingShowsWereRetrieved());
    }
}
