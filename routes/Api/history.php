<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/history-activity/list', 'HistoryActivityController@list');
    Route::get('/history-activity/detail/{id}', 'HistoryActivityController@detail');
});
