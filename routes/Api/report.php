<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/report', 'ReportController@tes');
});
