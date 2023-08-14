<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/design-faktor/list', 'DesignFaktorController@list');
    Route::get('/design-faktor/detail/{id}', 'DesignFaktorController@detail');
});
