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

// ZKTeco ADMS (Automatic Push) Endpoints
Route::get('/iclock/cdata', [AttendanceController::class, 'admsHandshake']);
Route::post('/iclock/cdata', [AttendanceController::class, 'admsReceiveData']);
Route::get('/iclock/getrequest', [AttendanceController::class, 'admsGetRequest']);
Route::post('/iclock/getrequest', [AttendanceController::class, 'admsGetRequest']);
