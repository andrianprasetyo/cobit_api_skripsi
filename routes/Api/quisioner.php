<?php
Route::group([], function ($router) {
    Route::get('/quisioner/responden/detail', 'QuisionerController@detailRespondenByEmail');
    Route::post('/quisioner/responden/save', 'QuisionerController@start');
    Route::get('/quisioner/responden/list', 'QuisionerController@listquestion');
    Route::post('/quisioner/responden/save-jawaban', 'QuisionerController@saveJawaban');
});
