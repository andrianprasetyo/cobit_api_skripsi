<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/dashboard/assesment', 'DashboardController@assesment');
    Route::get('/dashboard/assesment-chart', 'DashboardController@assesmentChart');
    Route::get('/dashboard/assesment/report', 'DashboardController@dashboardByAssesment');
});
