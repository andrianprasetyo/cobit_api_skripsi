<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/domain/list', 'DomainController@list');
    Route::get('/domain/detail/{id}', 'DomainController@detailByID');
});
