<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/capabilitytarget/list', 'CapabilityTargetController@list');
    Route::get('/capabilitytarget/detail/{id}', 'CapabilityTargetController@detailByID');
    Route::get('/capabilitytarget/detail-assesmeent/{id}', 'CapabilityTargetController@detailByAssesment');
    Route::post('/capabilitytarget/add', 'CapabilityTargetController@add');
    Route::put('/capabilitytarget/edit/{id}', 'CapabilityTargetController@edit');
    Route::delete('/capabilitytarget/remove/{id}', 'CapabilityTargetController@remove');
});
