<?php

use App\Http\Controllers\AttendanceController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified',
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::get('attendance/sync', [AttendanceController::class, 'sync'])->name('attendance.sync');
    Route::resource('attendance', AttendanceController::class);
});

// ZKTeco ADMS (Cloud Server) Endpoints
Route::get('/iclock/cdata', [\App\Http\Controllers\ZKTecoADMSController::class, 'handshake']);
Route::post('/iclock/cdata', [\App\Http\Controllers\ZKTecoADMSController::class, 'receiveData']);
Route::get('/iclock/getrequest', [\App\Http\Controllers\ZKTecoADMSController::class, 'getRequest']);

// API Route for Local Agent Sync
Route::post('/api/attendance/sync', [\App\Http\Controllers\Api\AttendanceSyncController::class, 'receive']);
