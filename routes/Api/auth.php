<?php

Route::group(['prefix' => 'auth'], function ($router) {
    Route::post('/login', 'AuthController@login');
    Route::post('/reset-password', 'AuthController@resetPassword');
    Route::get('/verify-reset-password', 'AuthController@detailVerifyByToken');
    Route::get('/verify-token', 'AuthController@tokenVerify');
    Route::post('/verify-token', 'AuthController@userTokenVerify');
    Route::post('/verify-reset-password', 'AuthController@verifyResetPassword');
    Route::post('/verify-reset-password/otp-check', 'AuthController@_checkKodeByToken');
});
