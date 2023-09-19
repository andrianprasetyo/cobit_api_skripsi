<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/capabilitytargetlevel/list', 'CapabilityTargetLevelController@list');
    Route::get('/capabilitytargetlevel/list-target', 'CapabilityTargetLevelController@listDefaultTarget');
    // Route::get('/capabilitytargetlevel/detail/{id}', 'CapabilityTargetLevelController@detailByID');
    Route::post('/capabilitytargetlevel/generate-domain', 'CapabilityTargetLevelController@generateDomain');
    Route::post('/capabilitytargetlevel/save-target', 'CapabilityTargetLevelController@saveUpdateTarget');
    // Route::put('/capabilitytargetlevel/edit/{id}', 'CapabilityTargetLevelController@edit');
    // Route::delete('/capabilitytargetlevel/remove/{id}', 'CapabilityTargetLevelController@remove');
});
