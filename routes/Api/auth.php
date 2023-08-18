<?php

Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('/login', 'AuthController@login');
    Route::post('/reset-password', 'AuthController@resetPassword');
    Route::get('/verify-reset-password', 'AuthController@detailVerifyByToken');
    Route::get('/verify-reset-password', 'AuthController@detailVerifyByToken');
    Route::post('/verify-reset-password/otp-check', 'AuthController@_checkKodeByToken');
});
