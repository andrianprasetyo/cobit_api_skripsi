<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/report/quisioner-result', 'ReportController@listJawabanResponden');
    Route::get('/report/quisioner-result-by-responeden', 'ReportController@listJawabanResponden');
});
Route::get('/report/download/quisioner-tes', 'ReportController@detailUserByID');
Route::get('/report/download/quisioner-result', 'ReportController@downloadExcel');
