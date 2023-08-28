<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/report/quisioner-result', 'ReportController@listJawabanResponden');
    Route::get('/report/quisioner-result-by-responeden', 'ReportController@listJawabanResponden');
    Route::post('/report/canvas/result/{id}', 'ReportController@setHasilCanvas');
    Route::get('/report/canvas/list', 'ReportController@canvas');
    Route::post('/report/canvas/set-adjustment', 'ReportController@setValueAdjustment');
    Route::post('/report/canvas/set-weight', 'ReportController@setValueWeight');
});
Route::get('/report/download/quisioner-result', 'ReportController@downloadExcel');
