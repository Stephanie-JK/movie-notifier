<?php namespace App\Crawler;

use GuzzleHttp\Client;
use Yangqi\Htmldom\Htmldom;

abstract class BaseProvider
{

    public function __construct()
    {
        $this->client = new Client();
        $this->dom    = new Htmldom();
    }


    public function sanitize($string)
    {
        return trim(preg_replace("/[^ \w]+/", "", $string));
    }


    abstract public function released();


    abstract public function upcoming();


    abstract public function model();

}