<?php
Route::group([], function ($router) {
    Route::get('/quisioner/responden/detail', 'QuisionerController@detailRespondenByCode');
    Route::post('/quisioner/responden/save', 'QuisionerController@start');
    Route::get('/quisioner/responden/list/{id}', 'QuisionerController@listquestion');
    Route::post('/quisioner/responden/save-jawaban', 'QuisionerController@saveJawaban');
    Route::post('/quisioner/responden/finish', 'QuisionerController@finish');
    Route::get('/quisioner/responden/navigation/list', 'QuisionerController@navigation');
    Route::get('/quisioner/responden/divisi/list', 'QuisionerController@listDivisi');
    Route::get('/quisioner/responden/jabatan/list', 'QuisionerController@listJabatan');
    Route::post('/quisioner/responden/set-finish', 'QuisionerController@setFinish');
    Route::post('/quisioner/responden/reset', 'QuisionerController@reset');
    // Route::put('/quisioner/responden/{id}', 'QuisionerController@updateQuesioner');
    Route::put('/quisioner/responden/update-all', 'QuisionerController@updateListAssesmentUser');
    Route::post('/quisioner/responden/tes-reset', 'QuisionerController@tesReset');
});
