<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\EstablishmentController;
use App\Http\Controllers\Admin\AgentController;
use App\Http\Controllers\Admin\MapController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\MobileConnectionController;
use App\Http\Controllers\LocaleController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Locale Switcher
Route::get('/locale/{locale}', [LocaleController::class, 'switch'])->name('locale.switch');

// Redirect root to admin
Route::redirect('/', '/admin');

// Admin Authentication
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Admin Protected Routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard/stats', [DashboardController::class, 'stats'])->name('dashboard.stats');

    // Establishments
    Route::get('/establishments', [EstablishmentController::class, 'index'])->name('establishments.index');
    Route::get('/establishments/data', [EstablishmentController::class, 'data'])->name('establishments.data');
    Route::get('/establishments/{establishment}', [EstablishmentController::class, 'show'])->name('establishments.show');
    Route::get('/establishments/export/csv', [EstablishmentController::class, 'exportCsv'])->name('establishments.export');

    // Map
    Route::get('/map', [MapController::class, 'index'])->name('map.index');
    Route::get('/map/data', [MapController::class, 'data'])->name('map.data');

    // Agents
    Route::resource('agents', AgentController::class);
    Route::post('/agents/{agent}/toggle-status', [AgentController::class, 'toggleStatus'])->name('agents.toggle-status');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/daily', [ReportController::class, 'daily'])->name('reports.daily');
    Route::get('/reports/agent/{agent}', [ReportController::class, 'byAgent'])->name('reports.agent');

    // Mobile Connection
    Route::get('/mobile-connection', [MobileConnectionController::class, 'index'])->name('mobile-connection.index');
});
