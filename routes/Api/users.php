<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/users/list', 'UsersController@list');
    Route::get('/users/detail/{id}', 'UsersController@detail');
    Route::post('/users/add', 'UsersController@add');
    Route::put('/users/edit/{id}', 'UsersController@edit');
});
