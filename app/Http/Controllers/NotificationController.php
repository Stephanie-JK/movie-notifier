<?php

namespace App\Http\Controllers;

use App\Movie;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class NotificationController extends Controller
{

    /**
     * Stores a new notification for the user
     *
     * @param Request $request
     *
     * @return array
     */
    public function create(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'gcm_id'   => 'required|exists:users,gcm_id',
            'movie_id' => 'required|exists:movies,id',
            'date'     => 'required|date|after:today',
            'no_of_seats' => 'required_with_all:after_time,before_time|integer',
            'after_time' => 'required_with_all:no_of_seats,before_time|date_format:g\:i A',
            'before_time' => 'required_with_all:no_of_seats,after_time|date_format:g\:i A',
        ]);

        if ($validator->fails()) {
            return [ 'errors' => $validator->errors()->all(), 'status' => 'failed' ];
        }

        $user = User::whereGcmId($request->get('gcm_id'))->firstOrFail();
        $movie = Movie::findOrFail($request->get("movie_id"));
        $date = Carbon::createFromFormat("Y-m-d", $request->get('date'))->toDateString();

        if(!$movie->showtimes()->where('date',$date)->count()){
            $user->notifications()->firstOrCreate([
                'movie_id' => $movie->id,
                'date'     => Carbon::createFromFormat("Y-m-d", $date)->toDateTimeString(),
                'sent'     => false,
                'no_of_seats' => $request->get('no_of_seats', 0),
                'after_time' => $request->get('after_time'),
                'before_time' => $request->get('before_time'),
            ]);
        }

        return [ 'status' => 'success' ];
    }

    public function destroy(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'gcm_id'   => 'required|exists:users,gcm_id',
            'notification_id' => 'required|exists:notifications,id'
        ]);

        if ($validator->fails()) {
            return [ 'errors' => $validator->errors()->all(), 'status' => 'failed' ];
        }

        User::whereGcmId($request->get('gcm_id'))->first()->notifications()->findOrFail($request->get("notification_id"))->delete();

        return [ 'status' => 'success' ];
    }
}
