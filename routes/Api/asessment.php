<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/assesment/list', 'AsessmentController@list');
    Route::get('/assesment/detail/{id}', 'AsessmentController@detailByID');
    Route::post('/assesment/responden/invite', 'AsessmentController@inviteRespondent');
    Route::post('/assesment/responden/invite-by-excel', 'AsessmentController@inviteRespondentByExcel');
});
