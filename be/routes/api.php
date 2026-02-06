<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\JobController;
use App\Http\Controllers\Api\ResidentController;
use App\Http\Controllers\Api\WorklogController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\HistoryController;
use App\Http\Controllers\Api\FeedbackController;

Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth.api')->group(function () {
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    Route::get('/jobs', [JobController::class, 'index']);
    Route::post('/jobs', [JobController::class, 'store']);
    Route::get('/jobs/{job}', [JobController::class, 'show']);
    Route::put('/jobs/{job}', [JobController::class, 'update']);
    Route::delete('/jobs/{job}', [JobController::class, 'destroy']);
    Route::patch('/jobs/{job}/status', [JobController::class, 'updateStatus']);
    Route::patch('/jobs/{job}/assign', [JobController::class, 'assign']);
    Route::post('/jobs/{job}/worklogs', [JobController::class, 'storeWorklog']);
    Route::post('/jobs/{job}/complete', [JobController::class, 'complete']);

    Route::get('/worklogs', [WorklogController::class, 'index']);
    Route::put('/worklogs/{worklog}', [WorklogController::class, 'update']);
    Route::delete('/worklogs/{worklog}', [WorklogController::class, 'destroy']);

    Route::get('/residents', [ResidentController::class, 'index']);
    Route::post('/residents', [ResidentController::class, 'store']);
    Route::get('/residents/{resident}', [ResidentController::class, 'show']);
    Route::put('/residents/{resident}', [ResidentController::class, 'update']);
    Route::delete('/residents/{resident}', [ResidentController::class, 'destroy']);

    Route::get('/invoices', [InvoiceController::class, 'index']);
    Route::post('/invoices', [InvoiceController::class, 'store']);
    Route::put('/invoices/{invoice}', [InvoiceController::class, 'update']);
    Route::delete('/invoices/{invoice}', [InvoiceController::class, 'destroy']);

    Route::get('/reports/summary', [ReportController::class, 'summary']);
    Route::get('/histories', [HistoryController::class, 'index']);
    Route::get('/feedbacks', [FeedbackController::class, 'index']);

    Route::get('/users', [UserController::class, 'index']);
});
