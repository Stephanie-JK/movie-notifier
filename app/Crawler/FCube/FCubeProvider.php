<?php namespace App\Crawler\FCube;

use App\CinemaHall;
use App\Crawler\BaseProvider;
use Carbon\Carbon;

class FCubeProvider extends BaseProvider
{

    protected $domain = "http://www.fcubecinemas.com";


    /**
     * Returns the Provider Model
     *
     * @return mixed
     */
    public function model()
    {
        return CinemaHall::find(2);
    }


    /**
     * Gets the released movies
     * @return array
     */
    public function released()
    {
        $movies = [ ];

        $showtimes = $this->showtimes();
        foreach ($showtimes as $showtime) {
            $movies = array_merge($movies, $this->moviesOn($showtime));
        }

        return $movies;
    }


    /**
     * Gets the show times
     * @return array
     */
    private function showtimes()
    {
        $response = $this->client->get("{$this->domain}/Home/CurrentShowDate");
        if ($response->getStatusCode() == 200) {
            $data = json_decode($response->getBody(), true);

            return array_pluck($data, 'Value');
        }

        return [ ];
    }


    /**
     * Gets the movie for given show time
     *
     * @param $showtime
     *
     * @return array
     */
    private function moviesOn($showtime)
    {
        $movies = [ ];
        $url    = "{$this->domain}/Home/ShowList?id=-1&d=" . urlencode($showtime) . "&s=-2";

        $response = $this->client->get($url);
        if ($response->getStatusCode() == 200) {
            $dom = $this->dom->load($response->getBody());
            foreach ($dom->find('li') as $element) {
                $movies[] = [
                    'name'     => $this->sanitize($element->find('a', 0)->plaintext),
                    'image'    => $this->domain . $element->find('img', 0)->src,
                    //'release_date' => Carbon::parse("- 7 days")->format("Y-m-d"),
                    'showtime' => [
                        'date' => Carbon::createFromFormat("m/d/Y", $showtime)->format("Y-m-d")
                    ],
                ];
            }
        }

        return $movies;
    }


    /**
     * Gets the upcoming show movies
     *
     * @return array
     */
    public function upcoming()
    {
        $url = "{$this->domain}/NextChange";

        $response = $this->client->get($url);
        if ($response->getStatusCode() == 200) {
            $dom = $this->dom->load($response->getBody());

            $dom = $dom->find('div.ck-content', 0);

            foreach ($dom->find('div.span6') as $element) {
                $movies[] = [
                    'name'         => $this->sanitize($element->find('h2', 0)->plaintext),
                    'image'        => $this->domain . $element->find('img', 0)->src,
                    'release_date' => Carbon::createFromFormat("d M Y",
                        $element->find('small', 1)->plaintext)->format("Y-m-d")
                ];
            }
        }

        return $movies;
    }

}