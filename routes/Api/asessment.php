<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/assesment/list', 'AsessmentController@list');
    Route::get('/assesment/detail/{id}', 'AsessmentController@detailByID');
    Route::post('/assesment/add', 'AsessmentController@add');
    Route::put('/assesment/edit/{id}', 'AsessmentController@edit');
    Route::put('/assesment/set-status/{id}', 'AsessmentController@setStatus');
    Route::post('/assesment/responden/invite', 'AsessmentController@inviteRespondent');
    Route::put('/assesment/responden/reinvite/{id}', 'AsessmentController@reinviteResponden');
    Route::post('/assesment/kalkulasi/{id}', 'AsessmentController@reKalkulasi');

    Route::post('/assesment/pic/invite', 'AsessmentController@addPIC');
    Route::post('/assesment/pic/add', 'AsessmentController@addNewPIC');
    Route::put('/assesment/pic/edit/{id}', 'AsessmentController@editPIC');
    Route::put('/assesment/pic/reaktifasi/{id}', 'AsessmentController@reAktifasi');
    Route::put('/assesment/pic/expire/{id}', 'AsessmentController@editPicExpire');
    Route::put('/assesment/org/{id}/change', 'AsessmentController@changeOrg');

    Route::post('/assesment/upload/report', 'AsessmentController@uploadReport');
    Route::get('/assesment/report/list', 'AsessmentController@reportHasil');
    Route::get('/assesment/report/chart', 'AsessmentController@chartReportHasil');
    Route::get('/assesment/report/chart/all-target', 'AsessmentController@chartReportHasilAllTarget');
    Route::get('/assesment/report/detail-ofi', 'AsessmentController@ReportDetailOfi');
    Route::delete('/assesment/remove/{id}', 'AsessmentController@remove');

    Route::get('/assesment/report/design-faktor-risk-in/list', 'AsessmentController@dfRiskSkenarioIN');
    Route::get('/assesment/report/design-faktor-risk-out/list', 'AsessmentController@dfRiskSkenarioOUT');
    Route::get('/assesment/report/design-faktor-risk-out/chart', 'AsessmentController@dfRiskSkenarioOUTChart');

    Route::get('/assesment/docs/list', 'AsessmentController@listCurrentDocs');
    Route::get('/assesment/docs/detail/{id}', 'AsessmentController@detailDocs');
    Route::put('/assesment/docs/edit/{id}', 'AsessmentController@updateDocs');
    Route::delete('/assesment/docs/remove/{id}', 'AsessmentController@removeDocs');
});
Route::post('/assesment/responden/invite-by-excel', 'AsessmentController@inviteRespondentByExcel');
Route::get('/assesment/report/capability/download', 'AsessmentController@downloadReportCapabilityAssesment');
