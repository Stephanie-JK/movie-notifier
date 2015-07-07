<?php
get('/', function(){
    $deviceToken = "fDPTQflI7qw:APA91bEz0R5spj24HbtP9MJffKsxV5RcXr-0f_9Qzu0hD_nwhDXTXXBjh-Bi4Z6MGj5Ob_qUZwnoiISiyWo6uN34m_s8HX9d4RXOr8YXGdgL7O9F4h4-zDCi_t-0YsCXv7nZ1Rs48LTY";
    $push = \App\GCM::to($deviceToken)
        ->send('Terminator Genesis 3D tickets for Friday now available at QFX Civil :)', ['title' => 'Movie Tickets']);
    dd($push);
});
$router->group(['prefix' => 'api'], function () use ($router) {
    $router->group(['prefix' => 'users'], function () {
        get('create','UserController@create');
    });
    //resource('users', 'UserController');
});