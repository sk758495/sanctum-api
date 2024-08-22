<?php

use App\Http\Controllers\api\AuthController;
use App\Http\Controllers\api\PostController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('signup',[AuthController::class,'signup']);
Route::post('login',[AuthController::class,'login']);


Route::middleware('auth:sanctum')->group(function(){
    
Route::post('logout',[AuthController::class,'logout']);

Route::apiResource('posts',PostController::class);
});

