<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/capabilitylevel/list', 'CapabilityLevelController@list');
    Route::get('/capabilitylevel/detail/{id}', 'CapabilityLevelController@detailByID');
    Route::post('/capabilitylevel/add', 'CapabilityLevelController@add');
    Route::put('/capabilitylevel/edit/{id}', 'CapabilityLevelController@edit');
    Route::delete('/capabilitylevel/remove/{id}', 'CapabilityLevelController@delete');
});
