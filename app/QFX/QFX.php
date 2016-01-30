<?php

namespace App\Qfx;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ServerException;
use Yangqi\Htmldom\Htmldom;

class Qfx
{

    protected $seatPreferences = [
        'QFX Kumari 1'     => [
            'row'             => 'G',
            'alternative_row' => 'F',
            'middle'          => 14,
        ],
        'QFX Kumari 2'     => [
            'row'             => 'F',
            'alternative_row' => 'G',
            'middle'          => 14,
        ],
        'QFX Jai Nepal'    => [
            'row'             => 'G',
            'alternative_row' => 'F',
            'middle'          => 9,
        ],
        'QFX Civil Mall 1' => [
            'row'             => 'G',
            'alternative_row' => 'F',
            'middle'          => 9,
        ],
        'QFX Civil Mall 2' => [
            'row'             => 'G',
            'alternative_row' => 'F',
            'middle'          => 9,
        ],
        'QFX Civil Mall 3' => [
            'row'             => 'G',
            'alternative_row' => 'F',
            'middle'          => 9,
        ]
    ];

    protected $seats = [ ], $showId, $transactionId, $seatsToBook = [ ], $cinema;


    public function __construct()
    {
        $this->client = new Client([ 'cookies' => true, 'base_uri' => 'http://qfxcinemas.com', ]);
        $this->dom    = new Htmldom();
    }


    public static function book($showId, $seats)
    {
        $qfx              = new self;
        $qfx->showId      = $showId;
        $qfx->seatsToBook = $seats;

        return ( $qfx->login() && $qfx->getSeats() && $qfx->addSeats() && $qfx->confirmSeats() );
    }


    public function login()
    {
        $response = $this->client->post('/Account/Login', [
            'form_params'     => [
                'Username'   => env('qfx_username'),
                'Password'   => env('qfx_password'),
                'RememberMe' => 'false'
            ],
            'allow_redirects' => false
        ]);

        return ( $response->getStatusCode() == 302 );
    }


    private function getSeats()
    {
        $response = $this->client->get('/ShoppingCart?ShowID=' . $this->showId);
        if ($response->getStatusCode() == 200) {
            $dom = $this->dom->load($response->getBody());
            if ($dom->find('input#TransactionID')) {
                $this->transactionId = $dom->find('input#TransactionID', 0)->value;
                $this->cinema        = $dom->find('td', 0)->plaintext;
                $this->parseSeats($dom->find('div#mainContInternal', 0));

                return true;
            }
        }

        return false;
    }


    private function parseSeats($container)
    {
        foreach ($container->find('div.seatRows') as $seatRow) {
            if ( ! $seatRow->find('div.seatMark')) {
                continue;
            }
            $mark = $seatRow->find('div.seatMark', 0)->plaintext;
            foreach ($seatRow->find('div.seatRow', 0)->find('div.normalSeat') as $seat) {
                $this->seats[$this->newSeatKey($mark . $seat->plaintext)] = $seat->{'data-seat-id'};
            }
        }
    }


    private function newSeatKey($string, $count = 0)
    {
        $keyName = $count ? $string . "_" . $count : $string;
        if ( ! array_key_exists($keyName, $this->seats)) {
            return $keyName;
        }

        return $this->newSeatKey($string, $count + 1);
    }


    private function addSeats()
    {
        $this->removeAnyPreviousSeats();

        $seats = $this->seats();

        if ( ! count($seats)) {
            return false;
        }

        foreach ($seats as $seat) {
            $this->client->post('/ShoppingCart/AddSeat', [
                'json' => [
                    'SeatID'         => $this->seats[$seat],
                    'ShoppingCartID' => $this->transactionId,
                    'ShowID'         => $this->showId
                ]
            ]);
        }

        return true;
    }


    private function removeAnyPreviousSeats()
    {
        $response = $this->client->get("/ShoppingCart/GetSeatStatusWithShowDetail?TransactionID={$this->transactionId}&ShowID={$this->showId}");
        $data     = json_decode($response->getBody());
        if ($data[1]->Value) {
            $this->cancelTransaction();
            $this->getSeats();
        }
        if ($data[0]->Value) {
            foreach ($data[0]->Value as $seat) {
                $search = array_search($seat->SeatID, $this->seats);
                if ($search !== false) {
                    array_forget($this->seats, $search);
                }
            }
        }
    }


    private function cancelTransaction()
    {
        $this->client->get('/Payment/CancelExistingTransaction?transactionID=' . $this->transactionId);
    }


    public function seats()
    {
        $settings = $this->seatPreferences[$this->cinema];
        $number   = $this->seatsToBook;
        $prefix   = $settings['row'];
        $middle   = $settings['middle'];

        $seats    = [ $prefix . $middle ];
        $division = $number / 2;

        for ($i = 1; $i <= $division; $i++) {
            $seats[] = $prefix . ( $middle + $i );
            if ($number % 2 != 0 || $i !== $division) {
                $seats[] = $prefix . ( $middle - $i );
            }
        }

        $seats = $this->removeIrrelevantSeats($seats);

        return array_values(array_sort($seats, function ($value) {
            return $value;
        }));
    }


    private function removeIrrelevantSeats($seats)
    {
        $existingSeats = $this->seats;

        return array_filter($seats, function ($seat) use ($existingSeats) {
            return ( array_key_exists($seat, $this->seats) === true );
        });
    }


    private function confirmSeats()
    {
        $response = $this->client->post("/ShoppingCart/Payment", [
            'form_params' => [
                'transactionID' => $this->transactionId,
            ]
        ]);

        $dom = $this->dom->load($response->getBody());

        if ($dom->find('span#cartItemList')) {
            $cartItems = json_decode(html_entity_decode($dom->find('span#cartItemList', 0)->{'data-cart-items'}));
            try {
                $response = $this->client->post('/Payment/BookTransaction', [
                    'json'            => [
                        'TransactionID'     => $this->transactionId,
                        'TransactionTypeID' => 1,
                        'CartItems'         => $cartItems,
                    ],
                    'allow_redirects' => false,
                ]);
            } catch (ServerException $e) {
                logger('ServerException while booking tickets');
            }

            return true;
        }

        return false;
    }
}