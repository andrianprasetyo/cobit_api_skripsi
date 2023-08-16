<?php

Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('/login', 'AuthController@login');
    Route::post('/reset-password', 'AuthController@resetPassword');
    Route::post('/verify-reset-password', 'AuthController@verifyResetPassword');
    Route::get('/verify-reset-password', 'AuthController@detailVerifyByToken');
});
