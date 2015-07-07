<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use Illuminate\Contracts\Bus\SelfHandling;

class QFXDatesRetriever extends Command implements SelfHandling
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qfx:dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Retrieve the movie dates';

    /**
     * @var Client
     */
    private $client;


    /**
     * Create a new command instance.
     *
     * @param Client $client
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
        $response = $this->client->get("http://www.qfxcinemas.com/Home/GetShowDatesForTheatre?TheatreID=0");
        if($response->getStatusCode() == 200){
            $data = json_decode($response->getBody());
            foreach($data as $day){
                if($day->Value == "07/10/2015"){
                    $deviceToken = "fDPTQflI7qw:APA91bEz0R5spj24HbtP9MJffKsxV5RcXr-0f_9Qzu0hD_nwhDXTXXBjh-Bi4Z6MGj5Ob_qUZwnoiISiyWo6uN34m_s8HX9d4RXOr8YXGdgL7O9F4h4-zDCi_t-0YsCXv7nZ1Rs48LTY";
                    $push = \App\GCM::to($deviceToken)
                        ->send('Terminator Genesis 3D tickets for Friday now available at QFX Civil :)', ['title' => 'Movie Tickets']);
                }
            }
        }
    }
}
