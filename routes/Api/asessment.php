<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/assesment/list', 'AsessmentController@list');
    Route::get('/assesment/detail/{id}', 'AsessmentController@detailByID');
    Route::post('/assesment/add', 'AsessmentController@add');
    Route::put('/assesment/edit/{id}', 'AsessmentController@edit');
    Route::put('/assesment/set-status/{id}', 'AsessmentController@setStatus');
    Route::post('/assesment/responden/invite', 'AsessmentController@inviteRespondent');

    Route::post('/assesment/pic/invite', 'AsessmentController@addPIC');
    Route::put('/assesment/pic/edit/{id}', 'AsessmentController@editPIC');
});
Route::post('/assesment/responden/invite-by-excel', 'AsessmentController@inviteRespondentByExcel');
