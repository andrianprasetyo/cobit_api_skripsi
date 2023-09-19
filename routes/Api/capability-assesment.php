<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/capabilityassesment/list', 'CapabilityAssesmentController@list');
    Route::post('/capabilityassesment/answer', 'CapabilityAssesmentController@createAnswer');
    // Route::post('/capabilityassesment/evident/upload/{id}', 'CapabilityAssesmentController@uploadEvident');
    Route::get('/capabilityassesment/kalkulasi-by-domain', 'CapabilityAssesmentController@kalkukasiDomainByLevel');
    Route::get('/capabilityassesment/summary-by-domain', 'CapabilityAssesmentController@summaryAssesment');
    Route::get('/capabilityassesment/ofi/detail', 'CapabilityAssesmentController@detailOfi');
});

Route::get('/capabilityassesment/summary-by-domain/download', 'CapabilityAssesmentController@downloadReportSumCapabilityAss');
Route::get('/capabilityassesment/ofi-by-domain/download', 'CapabilityAssesmentController@downloadReportSumCapabilityAss');
