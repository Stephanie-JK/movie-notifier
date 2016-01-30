<?php

namespace App\Http\Controllers;

use App\CinemaHall;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CinemaController extends Controller
{
    /**
     * @param Request $request
     *
     * @return array
     */
    public function all(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gcm_id' => 'required|exists:users,gcm_id',
        ]);

        if ($validator->fails()) {
            abort(401);
        }

        $halls = CinemaHall::with(['movies', 'movies.showtimes'])->get();
        $data = [];
        foreach ($halls as $hall) {
            $h = [
                'id'   => $hall->id,
                'name' => $hall->name,
            ];
            foreach ($hall->movies as $movie) {
                $m = [
                    'id'           => $movie->id,
                    'name'         => $movie->name,
                    'release_date' => $movie->release_date->format('Y-m-d'),
                ];

                foreach ($movie->showtimes as $showtime) {
                    $m['showtimes'][] = [
                        'id'   => $showtime->id,
                        'date' => $showtime->date->format('Y-m-d'),
                    ];
                }
                $h['movies'][] = $m;
            }
            $data[] = $h;
        }

        $rem = [];
        $reminders = User::whereGcmId($request->get('gcm_id'))->firstOrFail()->notifications()->with(['movie', 'movie.cinema'])->unsent()->recent()->get();
        foreach ($reminders as $reminder) {
            $rem[] = [
                'id' => $reminder->id,
                'name' => $reminder->movie->name,
                'cinema_name' => $reminder->movie->cinema->name,
                'date' => $reminder->date->format('Y-m-d'),
                'image' => $reminder->movie->image,
            ];
        }

        return ['status' => 'success', 'data' => $data ,'reminders' => $rem];
    }
}
