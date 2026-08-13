<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', fn () => redirect()->route('login'));

Route::get('/login', function () {
    return Inertia::render('Auth/Login');
})->name('login');

Route::get('/forgot-password', function () {
    return Inertia::render('Auth/ForgotPassword');
})->name('password.request');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');

    Route::get('/customers', [App\Http\Controllers\CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [App\Http\Controllers\CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [App\Http\Controllers\CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}', [App\Http\Controllers\CustomerController::class, 'show'])->name('customers.show');
    Route::get('/customers/{customer}/edit', [App\Http\Controllers\CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [App\Http\Controllers\CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [App\Http\Controllers\CustomerController::class, 'destroy'])->name('customers.destroy');
    Route::post('/customers/{customer}/suspend', [App\Http\Controllers\CustomerController::class, 'suspend'])->name('customers.suspend');
    Route::post('/customers/{customer}/activate', [App\Http\Controllers\CustomerController::class, 'activate'])->name('customers.activate');
    Route::post('/customers/{customer}/renew', [App\Http\Controllers\CustomerController::class, 'renew'])->name('customers.renew');
    Route::get('/customers/{customer}/contract', [App\Http\Controllers\CustomerController::class, 'printContract'])->name('customers.contract');
    Route::get('/customers/{customer}/invoice', [App\Http\Controllers\CustomerController::class, 'printInvoice'])->name('customers.invoice');

    Route::resource('plans', App\Http\Controllers\PlanController::class);
    Route::resource('invoices', App\Http\Controllers\InvoiceController::class);
    Route::post('/invoices/{invoice}/pay', [App\Http\Controllers\InvoiceController::class, 'markPaid'])->name('invoices.pay');
    Route::resource('expenses', App\Http\Controllers\ExpenseController::class);
    Route::resource('inventory', App\Http\Controllers\InventoryProductController::class);
    Route::resource('employees', App\Http\Controllers\EmployeeController::class);
    Route::resource('tickets', App\Http\Controllers\TicketController::class);
    Route::post('/tickets/{ticket}/reply', [App\Http\Controllers\TicketController::class, 'reply'])->name('tickets.reply');
    Route::resource('routers', App\Http\Controllers\RouterController::class);
    Route::get('/routers/{router}/test', [App\Http\Controllers\RouterController::class, 'test'])->name('routers.test');
    Route::get('/reports', [App\Http\Controllers\ReportController::class, 'index'])->name('reports.index');
    Route::get('/logs', [App\Http\Controllers\ActivityLogController::class, 'index'])->name('logs.index');
    Route::get('/settings', [App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
    Route::put('/settings', [App\Http\Controllers\SettingController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
