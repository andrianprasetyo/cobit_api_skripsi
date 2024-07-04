<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/organisasi-divisi/list', 'OrganisasiDivisiController@list');
    Route::get('/organisasi-divisi/detail/{id}', 'OrganisasiDivisiController@detailByID');
    Route::post('/organisasi-divisi/add', 'OrganisasiDivisiController@add');
    Route::put('/organisasi-divisi/edit/{id}', 'OrganisasiDivisiController@edit');
    Route::delete('/organisasi-divisi/remove/{id}', 'OrganisasiDivisiController@deleteByID');
    Route::post('/organisasi-divisi/map-df/{id}', 'OrganisasiDivisiController@createMapDF');
    Route::delete('/organisasi-divisi/remove-map/{id}', 'OrganisasiDivisiController@deleteMapByID');
});
