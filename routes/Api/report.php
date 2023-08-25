<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/report/quisioner-result', 'ReportController@listJawabanResponden');
    Route::get('/report/quisioner-result-by-responeden', 'ReportController@listJawabanResponden');
    Route::post('/report/canvas/result', 'ReportController@setHasilCanvas');
});
Route::get('/report/canvas/list', 'ReportController@canvas');
Route::get('/report/download/quisioner-result', 'ReportController@downloadExcel');
