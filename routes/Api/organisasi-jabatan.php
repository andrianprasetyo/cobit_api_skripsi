<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/organisasi-jabatan/list', 'OrganisasiJabatanController@list');
    Route::get('/organisasi-jabatan/detail/{id}', 'OrganisasiJabatanController@detailByID');
    Route::post('/organisasi-jabatan/add', 'OrganisasiJabatanController@add');
    Route::put('/organisasi-jabatan/edit/{id}', 'OrganisasiJabatanController@edit');
    Route::delete('/organisasi-jabatan/remove/{id}', 'OrganisasiJabatanController@deleteByID');
});
