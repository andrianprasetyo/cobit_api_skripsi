<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/assesment/list', 'AsessmentController@list');
    Route::get('/assesment/detail/{id}', 'AsessmentController@detailByID');
    Route::post('/assesment/add', 'AsessmentController@add');
    Route::put('/assesment/edit/{id}', 'AsessmentController@edit');
    Route::put('/assesment/set-status/{id}', 'AsessmentController@setStatus');
    Route::post('/assesment/responden/invite', 'AsessmentController@inviteRespondent');
    Route::put('/assesment/responden/reinvite/{id}', 'AsessmentController@reinviteResponden');

    Route::post('/assesment/pic/invite', 'AsessmentController@addPIC');
    Route::put('/assesment/pic/edit/{id}', 'AsessmentController@editPIC');
    Route::put('/assesment/pic/reaktifasi/{id}', 'AsessmentController@reAktifasi');
    Route::put('/assesment/pic/expire/{id}', 'AsessmentController@editPicExpire');

    Route::post('/assesment/upload/report', 'AsessmentController@uploadReport');
    Route::get('/assesment/report/list', 'AsessmentController@reportHasil');
    Route::get('/assesment/report/chart', 'AsessmentController@chartReportHasil');
    Route::get('/assesment/report/detail-ofi', 'AsessmentController@ReportDetailOfi');
    Route::delete('/assesment/remove/{id}', 'AsessmentController@remove');

    Route::get('/assesment/report/design-faktor-risk-in/list', 'AsessmentController@dfRiskSkenarioIN');
    Route::get('/assesment/report/design-faktor-risk-out/list', 'AsessmentController@dfRiskSkenarioOUT');
});
Route::post('/assesment/responden/invite-by-excel', 'AsessmentController@inviteRespondentByExcel');
Route::get('/assesment/report/capability/download', 'AsessmentController@downloadReportCapabilityAssesment');
