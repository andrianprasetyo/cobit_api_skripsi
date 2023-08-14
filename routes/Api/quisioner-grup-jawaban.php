<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/quisioner-grup-jawaban', 'GrupJawabanController@list');
    Route::post('/quisioner-grup-jawaban', 'GrupJawabanController@addGrupJawaban');
    Route::get('/quisioner-grup-jawaban/detail/{id}', 'GrupJawabanController@detailByID');
});
