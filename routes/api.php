<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\EmployeeController;
use App\Http\Controllers\Api\V1\Imports\EmployeeImportController;

Route::prefix('v1')->group(function () {
    Route::post('/employee', EmployeeImportController::class);
    Route::get('/employee', [EmployeeController::class, 'index']);
    Route::get('/employee/{id}', [EmployeeController::class, 'show']);
    Route::delete('/employee/{id}', [EmployeeController::class, 'destroy']);
});
