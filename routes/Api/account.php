<?php
Route::group(['middleware' => ['jwt.auth'],'prefix'=>'account'], function ($router) {
    Route::get('/me', 'AccountController@me');
    Route::get('/token-refresh', 'AccountController@refresh');
    Route::post('/logout', 'AccountController@logout');
    Route::post('/ubah-password', 'AccountController@ubahPassword');
    Route::post('/edit', 'AccountController@edit');
});
