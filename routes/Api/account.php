<?php
Route::group(['middleware' => ['jwt.auth'],'prefix'=>'account'], function ($router) {
    Route::get('/me', 'AccountController@me');
    Route::get('/me/assesment', 'AccountController@myAssesment');
    Route::get('/token-refresh', 'AccountController@refresh');
    Route::post('/logout', 'AccountController@logout');
    Route::post('/ubah-password', 'AccountController@ubahPassword');
    Route::post('/edit', 'AccountController@edit');
    Route::post('/change-role', 'AccountController@changeRole');
    Route::put('/assesment/change-default/{id}', 'AccountController@setDefaultAssesment');
});
