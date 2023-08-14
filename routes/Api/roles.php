<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/roles/list', 'RolesController@list');
    Route::post('/roles/add', 'RolesController@addRole');
});
