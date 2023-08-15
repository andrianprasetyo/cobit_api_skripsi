<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::post('/quisioner/add', 'QuisionerController@add');
});
