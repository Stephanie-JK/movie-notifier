<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
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
        $validator = Validator::make($request->all(),[
            'gcm_id' => 'required|unique:users', 'email' => 'email'
        ]);

        if($validator->fails()){
            return ['errors' => $validator->errors()->all()];
        }

        $user = User::create($request->all());
        return ['status' => 'pass'];
    }

}
