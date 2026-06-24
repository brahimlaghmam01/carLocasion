<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\CarsController;
use App\Http\Controllers\Admin\ReservationsController;
use App\Http\Controllers\Admin\ClientsController;
use App\Http\Controllers\Admin\PaymentsController;
use App\Http\Controllers\Admin\ReportsController;
use App\Http\Controllers\Admin\SupportController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\CitiesController;
use App\Http\Controllers\Admin\AgenciesController;
use App\Http\Controllers\Admin\AgencyAdminsController;
use App\Http\Controllers\Admin\ActivityLogsController;

Route::middleware(['auth', 'verified', 'active', 'admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(function () {
        // Redirect '/admin' to '/admin/cars' with a named route we can reference
        Route::redirect('/', '/admin/cars')->name('home');

        // Role-aware dashboard (Super Admin vs Agency Admin)
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Activity logs (scoped to agency for Agency Admins)
        Route::get('activity-logs', [ActivityLogsController::class, 'index'])->name('activity-logs.index');

        // Super Admin only: cities, agencies and agency administrators
        Route::middleware('super_admin')->group(function () {
            Route::resource('cities', CitiesController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::resource('agencies', AgenciesController::class)->only(['index', 'store', 'update', 'destroy']);
            Route::resource('agency-admins', AgencyAdminsController::class)->only(['index', 'store', 'update', 'destroy']);
        });

        // Cars
        Route::resource('cars', CarsController::class)->except(['show']);

        // Reservations
        Route::resource('reservations', ReservationsController::class)->only(['index', 'show', 'edit', 'update']);
        Route::get('reservations/{reservation}/print', [ReservationsController::class, 'print'])->name('reservations.print');

        // Clients
        Route::resource('clients', ClientsController::class)->only(['index', 'show']);
        Route::patch('clients/{client}/suspend', [ClientsController::class, 'suspend'])->name('clients.suspend');
        Route::patch('clients/{client}/activate', [ClientsController::class, 'activate'])->name('clients.activate');

        // Payments
        Route::resource('payments', PaymentsController::class)->only(['index']);

        // Reports
        Route::resource('reports', ReportsController::class)->except(['show']);

        // Support
        Route::resource('support', SupportController::class)->only(['index']);
        Route::get('/support/tickets/{ticket}', [SupportController::class, 'show'])
        ->name('support.show');
        Route::post('/support/tickets/{ticket}/reply', [SupportController::class, 'reply'])
        ->name('support.reply');
        Route::post('/support/tickets/{ticket}/close', [SupportController::class, 'close'])
        ->name('support.close');

    });
