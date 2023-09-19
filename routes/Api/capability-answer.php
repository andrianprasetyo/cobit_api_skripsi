<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/capabilityanswer/list', 'CapabilityAnswerController@list');
    // Route::get('/capabilityanswer/detail/{id}', 'CapabilityAnswerController@detailByID');
    Route::post('/capabilityanswer/add', 'CapabilityAnswerController@add');
    // Route::put('/capabilityanswer/edit/{id}', 'CapabilityAnswerController@edit');
    Route::delete('/capabilityanswer/remove/{id}', 'CapabilityAnswerController@remove');
});
