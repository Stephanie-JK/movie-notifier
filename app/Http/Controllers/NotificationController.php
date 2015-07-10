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
            'date'     => 'required|date|after:today'
        ]);

        if ($validator->fails()) {
            return [ 'errors' => $validator->errors()->all(), 'status' => 'failed' ];
        }

        $user = User::whereGcmId($request->get('gcm_id'))->firstOrFail();
        $movie = Movie::find($request->get("movie_id"));
        $date = $request->get('date');

        if(!$movie->showtimes()->whereDate($date)->count()){
            $user->notifications()->firstOrCreate([
                'movie_id' => $movie->id,
                'date'     => Carbon::createFromFormat("Y-m-d", $date),
                'sent'     => false,
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
