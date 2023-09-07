<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/capabilityassesment/list', 'CapabilityAssesmentController@list');
    Route::post('/capabilityassesment/answer', 'CapabilityAssesmentController@createAnswer');
    Route::post('/capabilityassesment/evident/upload/{id}', 'CapabilityAssesmentController@uploadEvident');
});
