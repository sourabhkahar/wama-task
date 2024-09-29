<?php

use App\Http\Controllers\UserController;
use App\Models\Hobby;
use Illuminate\Support\Facades\Route;

Route::get('/',function () {
    return view('welcome');
});

Route::get('/user', [UserController::class, 'index']);
Route::post('/add-user', [UserController::class, 'store']);
Route::post('/edit-user/{id}', [UserController::class, 'update']);
Route::post('/delete-user', [UserController::class, 'bulkDestroy']);
Route::get('/get-category', [UserController::class, 'getCategory']);
Route::get('/get-hobby', [UserController::class, 'getHobby']);
