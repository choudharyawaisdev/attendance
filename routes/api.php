<?php

use App\Http\Controllers\AttendanceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Local Agent pushes attendance data from office PC to server
Route::post('/sync-from-local', [AttendanceController::class, 'receiveFromLocal']);

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
