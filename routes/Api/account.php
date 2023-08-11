<?php
Route::group(['middleware' => ['jwt.auth'],'prefix'=>'account'], function ($router) {
    Route::get('/me', 'AccountController@me');
    Route::post('/logout', 'AccountController@logout');
});
