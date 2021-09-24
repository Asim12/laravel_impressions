<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Login;
use App\Http\Controllers\Rest_calls;
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

// Route::get('/Restapi', [Restapi :: class, 'index']);
Route::post('/Login/loginApi', [Login :: class, 'loginApi']);
Route::post('/Rest_calls/sendVarificationCode', [Rest_calls :: class, 'sendVarificationCode']);
Route::post('/Rest_calls/varifyCode', [Rest_calls :: class, 'varifyCode']);
Route::post('/Rest_calls/registerUser', [Rest_calls :: class, 'registerUser']);
Route::post('/Rest_calls/forgotPassword', [Rest_calls :: class, 'forgotPassword']);
Route::post('/Rest_calls/RegisterUserUsingSocial', [Rest_calls :: class, 'RegisterUserUsingSocial']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});




