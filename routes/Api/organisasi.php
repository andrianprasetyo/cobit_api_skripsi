<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/organisasi/list', 'OrganisasiController@list');
    Route::get('/organisasi/detail/{id}', 'OrganisasiController@detailByID');
    Route::post('/organisasi/add', 'OrganisasiController@add');
    Route::put('/organisasi/edit/{id}', 'OrganisasiController@edit');
    // Route::delete('/organisasi/remove/{id}', 'OrganisasiController@delete');
});
