<?php

namespace App\Http\Controllers;

use App\CinemaHall;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{

    public function __construct()
    {
        $this->middleware('guest', [ 'only' => 'create' ]);
    }


    /**
     *  Create a new User
     *
     * @param Request $request
     *
     * @return array
     */
    public function create(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'gcm_id' => 'required|exists:users,gcm_id'
        ]);

        if ($validator->fails()) {
            abort(401);
        }

        $halls = CinemaHall::with([ 'movies', 'movies.showtimes' ])->get();
        $data  = [ ];
        foreach ($halls as $k => $hall) {
            $h = [
                'id'   => $hall->id,
                'name' => $hall->name,
            ];
            foreach ($hall->movies as $k1 => $movie) {
                $m = [
                    'id'           => $movie->id,
                    'name'         => $movie->name,
                    'release_date' => $movie->release_date->format("Y-m-d")
                ];

                foreach ($movie->showtimes as $showtime) {
                    $m['showtimes'][] = [
                        'id'   => $showtime->id,
                        'date' => $showtime->date->format("Y-m-d")
                    ];
                }
                $h['movies'][] = $m;
            }
            $data[] = $h;
        }

        return [ 'status' => 'success', 'data' => $data ];
    }


    /**
     * Stores the user's notification
     *
     * @param Request $request
     *
     * @return array
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'gcm_id'   => 'required|exists:users,gcm_id',
            'movie_id' => 'required|exists:movies,id',
            'date'     => 'required|date|after:today'
        ]);

        if ($validator->fails()) {
            return [ 'errors' => $validator->errors()->all(), 'status' => 'failed' ];
        }

        User::whereGcmId($request->get('gcm_id'))->first()->notifications()->create([
                'movie_id' => $request->get('movie_id'),
                'date'     => $request->get('date'),
                'sent'     => false,
            ]);

        return [ 'status' => 'success' ];
    }
}
