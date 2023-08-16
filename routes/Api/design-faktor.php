<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/design-faktor/list', 'DesignFaktorController@list');
    Route::get('/design-faktor/detail/{id}', 'DesignFaktorController@detail');
    Route::post('/design-faktor/add', 'DesignFaktorController@add');
    Route::put('/design-faktor/edit/{id}', 'DesignFaktorController@edit');
    Route::delete('/design-faktor/remove/{id}', 'DesignFaktorController@remove');

    Route::post('/design-faktor/quisioner/add', 'DesignFaktorController@addQuisioner');
    Route::get('/design-faktor/quisioner/detail/{id}', 'DesignFaktorController@detailQuisioner');
    Route::post('/design-faktor/quisioner/edit/{id}', 'DesignFaktorController@editQuisioner');
});
