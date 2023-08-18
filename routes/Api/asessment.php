<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/assesment/list', 'AsessmentController@list');
    Route::get('/assesment/detail/{id}', 'AsessmentController@detailByID');
});
