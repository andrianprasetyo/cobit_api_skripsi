<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/dashboard/assesment', 'DashboardController@assesment');
});
