<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/quisioner-grup-jawaban/list', 'GrupJawabanController@list');
    Route::post('/quisioner-grup-jawaban', 'GrupJawabanController@addGrupJawaban');
    Route::get('/quisioner-grup-jawaban/detail/{id}', 'GrupJawabanController@detailByID');
    Route::delete('/quisioner-grup-jawaban/remove/{id}', 'GrupJawabanController@deleteGrupByID');
    Route::put('/quisioner-grup-jawaban/edit/{id}', 'GrupJawabanController@editGrup');
});
