<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/domain/list', 'DomainController@list');
    Route::get('/domain/detail/{id}', 'DomainController@detailByID');
    Route::post('/domain/add', 'DomainController@add');
    Route::put('/domain/edit/{id}', 'DomainController@edit');
    Route::delete('/domain/remove/{id}', 'DomainController@remove');
    Route::get('/domain/assesment/list', 'DomainController@listDomainByAssesment');
    Route::get('/domain/assesment/capability-level/list', 'DomainController@listDomainByAssesmentCapable');
    Route::get('/domain/chart/list', 'DomainController@chartDomainResult');
    Route::get('/domain/chart/list-adjustment', 'DomainController@chartDomainAdjustmentResult');
});

Route::get('/domain/assesment/download', 'DomainController@exportDomainByAssesment');
Route::get('/domain/assesment-adjustment/download', 'DomainController@exportDomainAdjustmentByAssesment');
