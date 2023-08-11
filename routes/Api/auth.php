<?php

Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('/login', 'AuthController@login');
    Route::post('/token/refresh', 'AuthController@login');
});
