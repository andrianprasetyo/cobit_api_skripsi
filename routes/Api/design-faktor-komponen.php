<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/design-faktor-komponen/list', 'DesignFaktorKomponenController@list');
    Route::get('/design-faktor-komponen/detail/{id}', 'DesignFaktorKomponenController@detail');
});
