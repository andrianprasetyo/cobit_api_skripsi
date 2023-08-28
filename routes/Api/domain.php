<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/domain/list', 'DomainController@list');
    Route::get('/domain/detail/{id}', 'DomainController@detailByID');
    Route::post('/domain/add', 'DomainController@add');
    Route::put('/domain/edit/{id}', 'DomainController@edit');
    Route::delete('/domain/remove/{id}', 'DomainController@remove');
});
