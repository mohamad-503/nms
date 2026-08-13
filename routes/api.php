<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('/login', [AuthController::class, 'login']);
Route::post('/forgot-password', [AuthController::class, 'sendResetLink']);
Route::post('/reset-password', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::get('/dashboard/stats', [App\Http\Controllers\DashboardController::class, 'stats']);

    Route::apiResource('customers', App\Http\Controllers\Api\CustomerApiController::class);
    Route::post('/customers/{customer}/suspend', [App\Http\Controllers\Api\CustomerApiController::class, 'suspend']);
    Route::post('/customers/{customer}/activate', [App\Http\Controllers\Api\CustomerApiController::class, 'activate']);
    Route::post('/customers/{customer}/renew', [App\Http\Controllers\Api\CustomerApiController::class, 'renew']);

    Route::apiResource('plans', App\Http\Controllers\Api\PlanApiController::class);
    Route::apiResource('invoices', App\Http\Controllers\Api\InvoiceApiController::class);
    Route::post('/invoices/{invoice}/pay', [App\Http\Controllers\Api\InvoiceApiController::class, 'markPaid']);
    Route::apiResource('expenses', App\Http\Controllers\Api\ExpenseApiController::class);
    Route::apiResource('products', App\Http\Controllers\Api\InventoryProductApiController::class);
    Route::apiResource('employees', App\Http\Controllers\Api\EmployeeApiController::class);
    Route::apiResource('tickets', App\Http\Controllers\Api\TicketApiController::class);
    Route::post('/tickets/{ticket}/reply', [App\Http\Controllers\Api\TicketApiController::class, 'reply']);
    Route::apiResource('routers', App\Http\Controllers\Api\RouterApiController::class);
    Route::get('/routers/{router}/test', [App\Http\Controllers\Api\RouterApiController::class, 'test']);
    Route::get('/reports', [App\Http\Controllers\Api\ReportApiController::class, 'index']);
    Route::get('/logs', [App\Http\Controllers\Api\ActivityLogApiController::class, 'index']);
    Route::get('/settings', [App\Http\Controllers\Api\SettingApiController::class, 'index']);
    Route::put('/settings', [App\Http\Controllers\Api\SettingApiController::class, 'update']);
});
