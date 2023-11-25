<?php

use App\Http\Controllers\Auth\RegisteredUserNewPasswordController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware(['auth:sanctum'])->group(function() {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::patch('/user', [UserController::class,'update']);
    Route::patch('/user/update-password', [RegisteredUserNewPasswordController::class,'store']);
});


require_once __DIR__.'/auth.php';
