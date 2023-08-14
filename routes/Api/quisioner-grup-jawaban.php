<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/quisioner-grup-jawaban/list', 'GrupJawabanController@list');
    Route::post('/quisioner-grup-jawaban', 'GrupJawabanController@addGrupJawaban');
    Route::get('/quisioner-grup-jawaban/detail/{id}', 'GrupJawabanController@detailByID');
    Route::post('/quisioner-grup-jawaban/detail/{grupid}/add', 'GrupJawabanController@addJawaban');
    Route::delete('/quisioner-grup-jawaban/detail/{id}/remove', 'GrupJawabanController@deleteJawabanID');
});
