<?php
Route::group(['middleware' => ['jwt.auth']], function ($router) {
    Route::get('/responden/list', 'UsersAssesmentController@listResponden');
    Route::get('/responden/detail/{id}', 'UsersAssesmentController@detailResponden');
    Route::get('/responden/users/download', 'UsersAssesmentController@exportUser');
});
