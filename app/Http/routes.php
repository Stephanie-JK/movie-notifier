<?php

get('/', function () {
    return '';
});

$router->group(['prefix' => 'api'], function () use ($router) {

    $router->group(['prefix' => 'users'], function () {
        post('create', 'UserController@create');
    });

    $router->group(['prefix' => 'cinemas'], function () {
        post('all', 'CinemaController@all');
    });

    $router->group(['prefix' => 'notifications'], function () {
        post('create', 'NotificationController@create');
        post('destroy', 'NotificationController@destroy');
    });
});
