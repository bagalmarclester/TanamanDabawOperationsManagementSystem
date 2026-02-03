<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SetupController;
use App\Models\User;
use GuzzleHttp\Middleware;
use Illuminate\Support\Facades\Route;



Route::middleware(['not.installed'])->group(function () {
    Route::get('/setup', [SetupController::class, 'index'])->name('setup');
    Route::post('/setup', [SetupController::class, 'store'])->name('setup.store');
});

Route::middleware(['guest', 'ensure.setup'])->group(function () {
    Route::get('/', function () {
        return redirect('/login');
    });

    Route::get('/login', [AuthController::class, 'index'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);

    // Placeholder for Forgot Password (prevents 404 error)
    Route::get('/forgot-password', function () {
        return "<h1>Reset Password</h1><p>Contact Admin.</p>";
    })->name('password.request');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');


Route::middleware(['auth', 'restrict.user'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Employee Project Page (Placeholder)
    Route::get('/employee/projects', function () {
        return "<h1>Employee Projects</h1>";
    })->name('employee.projects');

    // Placeholder for Forgot Password
    Route::get('/forgot-password', function () {
        return "<h1>Reset Password</h1><p>Contact your system administrator to reset your password.</p>";
    })->name('password.request');
});

// Clients
Route::middleware(['auth', 'restrict.user'])->group(function () {
    Route::get('/clients', [ClientController::class, 'index'])->name('clients');
    Route::post('/clients', [ClientController::class, 'create'])->name('clients.create');
    Route::put('clients/{id}', [ClientController::class, 'update'])
        ->name('clients.update');
    Route::delete('clients/{id}', [ClientController::class, 'destroy'])
        ->name('clients.destroy');

    Route::get('/clients/{id}', [ClientController::class, 'show'])->name('clients.panel');
});

// Projects
Route::middleware(['auth', 'restrict.user'])->group(function () {
    Route::get('/projects', [ProjectController::class, 'index'])->name('projects');
    Route::post('/projects', [ProjectController::class, 'create'])->name('projects.create');
    Route::put('projects/{id}', [ProjectController::class, 'update'])
        ->name('projects.update');
    Route::delete('projects/{id}', [ProjectController::class, 'destroy'])
        ->name('projects.destroy');
    Route::get('/projects/{id}', [ProjectController::class, 'show'])->name('projects.panel');
    Route::post('/projects/{id}/upload', [ProjectController::class, 'uploadImage'])->name('projects.upload');
});

// Employees
Route::middleware(['auth', 'restrict.user'])->group(function () {
    Route::get('/employees', [EmployeeController::class, 'index'])->name('employees');
    Route::post('/employees', [EmployeeController::class, 'store'])->name('employees.store');
    Route::put('/employees/{employee}', [EmployeeController::class, 'update'])->name('employees.update');
    Route::delete('/employees/{employee}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
    Route::get('/employees/{employee}', [EmployeeController::class, 'show'])->name('employees.panel');
});


Route::middleware(['auth', 'restrict.user'])->group(function () {
    Route::get('/inventory', [InventoryController::class, 'index'])->name('inventory');
    Route::post('/inventory', [InventoryController::class, 'store']); // Add Item
    Route::put('/inventory/{id}', [InventoryController::class, 'update']); // Edit Item
    Route::delete('/inventory/{id}', [InventoryController::class, 'destroy']); // Delete Item
    Route::post('/inventory/{id}/stock-in', [InventoryController::class, 'stockIn']);
    Route::post('/inventory/{id}/stock-out', [InventoryController::class, 'stockOut']);
});


Route::middleware(['auth', 'restrict.user'])->group(function () {

    // Quotes
    Route::get('/quotes', function () {
        return view('quotes');
    })->name('quotes');

    // Invoices
    Route::get('/invoices', function () {
        return view('invoice');
    })->name('invoices');

    // Profile (Referenced in your Navbar dropdown)
    Route::get('/profile', function () {
        return view('profile');
    })->name('profile');

    // Employee View (For non-admin redirection)
    Route::get('/employee/projects', function () {
        return view('temp');
    })->name('employee.projects');
});
