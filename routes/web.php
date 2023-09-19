<?php

use App\Http\Controllers\DownloadController;
use App\Http\Controllers\CommandController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return response()->json("Cobit Rest API V1");
});

Route::controller(DownloadController::class)->group(function(){
    Route::get('/download/sample/template-invite-respondent', 'TemplateInviteResponden');
});

Route::get('/artisan', [CommandController::class, 'run']);
