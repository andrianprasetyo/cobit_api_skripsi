<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::post('/quisioner-jawaban/add', 'GrupJawabanController@addJawaban');
    Route::delete('/quisioner-jawaban/remove/{id}', 'GrupJawabanController@deleteJawabanID');
    Route::put('/quisioner-jawaban/edit/{id}', 'GrupJawabanController@editJawaban');
    Route::delete('/quisioner-jawaban/remove-all/{id}', 'GrupJawabanController@deleteAllJawabanID');
});
