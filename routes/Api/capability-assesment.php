<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/capabilityassesment/list', 'CapabilityAssesmentController@list');
});
