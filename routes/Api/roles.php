<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/roles/list', 'RolesController@list');
    Route::get('/roles/detail/:id', 'RolesController@detail');
    Route::post('/roles/add', 'RolesController@addRole');
    Route::put('/roles/edit/{id}', 'RolesController@editRole');
    Route::delete('/roles/remove/{id}', 'RolesController@deleteRole');
});
