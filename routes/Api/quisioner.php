<?php
Route::group([], function ($router) {
    Route::get('/quisioner/responden/detail', 'QuisionerController@detailRespondenByEmail');
    Route::post('/quisioner/responden/save', 'QuisionerController@start');
});
