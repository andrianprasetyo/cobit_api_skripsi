<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/users/list', 'UsersController@list');
    Route::get('/users/detail/{id}', 'UsersController@detail');
});
