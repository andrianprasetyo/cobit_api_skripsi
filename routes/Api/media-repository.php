<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/repository/list', 'MediaRepositoryController@list');
    Route::get('/repository/detail/{id}', 'MediaRepositoryController@detailByID');
    Route::post('/repository/add', 'MediaRepositoryController@add');
    Route::delete('/repository/remove/{id}', 'MediaRepositoryController@remove');
});
