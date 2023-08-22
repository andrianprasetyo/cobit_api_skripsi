<?php
Route::group([], function ($router) {
    Route::get('/quisioner/responden/detail', 'QuisionerController@detailRespondenByCode');
    Route::post('/quisioner/responden/save', 'QuisionerController@start');
    Route::get('/quisioner/responden/list/{id}', 'QuisionerController@listquestion');
    Route::post('/quisioner/responden/save-jawaban', 'QuisionerController@saveJawaban');
});
