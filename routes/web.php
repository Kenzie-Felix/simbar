<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;

// Home route
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    return view('welcome');
})->name('home');

// Authentication routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Authenticated routes
Route::middleware('auth')->group(function () {
    // Dashboard routes
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/admin/dashboard', [DashboardController::class, 'adminDashboard'])->middleware('admin')->name('admin.dashboard');
    Route::get('/staff/dashboard', [DashboardController::class, 'staffDashboard'])->middleware('staff')->name('staff.dashboard');

    // Admin routes
    Route::middleware('admin')->prefix('admin')->name('admin.')->group(function () {
        // Category routes
        Route::resource('categories', \App\Http\Controllers\Admin\CategoryController::class);
        
        // Location routes
        Route::resource('locations', \App\Http\Controllers\Admin\LocationController::class);
        
        // Item routes
        Route::resource('items', \App\Http\Controllers\Admin\ItemController::class);
        Route::post('items/preview-image', [\App\Http\Controllers\Admin\ItemController::class, 'previewImage'])->name('items.preview-image');
        
        // Stock movement routes
        Route::prefix('stock')->name('stock.')->group(function () {
            Route::get('in', [\App\Http\Controllers\Admin\StockMovementController::class, 'showStockInForm'])->name('in');
            Route::post('in', [\App\Http\Controllers\Admin\StockMovementController::class, 'recordStockIn'])->name('in.store');
            Route::get('adjustment', [\App\Http\Controllers\Admin\StockMovementController::class, 'showStockAdjustmentForm'])->name('adjustment');
            Route::post('adjustment', [\App\Http\Controllers\Admin\StockMovementController::class, 'recordStockAdjustment'])->name('adjustment.store');
            Route::get('history', [\App\Http\Controllers\Admin\StockMovementController::class, 'history'])->name('history');
        });

        // Loan approval routes
        Route::prefix('loans')->name('loans.')->group(function () {
            // Specific routes BEFORE generic ones
            Route::get('export/pending', [\App\Http\Controllers\Admin\LoanApprovalController::class, 'exportPending'])->name('exportPending');
            Route::get('api/stats', [\App\Http\Controllers\Admin\LoanApprovalController::class, 'getStats'])->name('stats');
            Route::post('bulk/approve', [\App\Http\Controllers\Admin\LoanApprovalController::class, 'bulkApprove'])->name('bulkApprove');
            
            // Generic routes AFTER specific ones
            Route::get('/', [\App\Http\Controllers\Admin\LoanApprovalController::class, 'index'])->name('index');
            Route::post('{loan}/approve', [\App\Http\Controllers\Admin\LoanApprovalController::class, 'approve'])->name('approve');
            Route::get('{loan}/reject', [\App\Http\Controllers\Admin\LoanApprovalController::class, 'rejectForm'])->name('rejectForm');
            Route::post('{loan}/reject', [\App\Http\Controllers\Admin\LoanApprovalController::class, 'reject'])->name('reject');
            Route::get('{loan}', [\App\Http\Controllers\Admin\LoanApprovalController::class, 'show'])->name('show');
        });

        // Return verification routes
        Route::prefix('returns')->name('returns.')->group(function () {
            // Specific routes BEFORE generic ones
            Route::get('export/pending', [\App\Http\Controllers\Admin\ReturnVerificationController::class, 'exportPending'])->name('exportPending');
            Route::get('api/stats', [\App\Http\Controllers\Admin\ReturnVerificationController::class, 'getStats'])->name('stats');
            
            // Generic routes AFTER specific ones
            Route::get('/', [\App\Http\Controllers\Admin\ReturnVerificationController::class, 'index'])->name('index');
            Route::post('{return}/verify', [\App\Http\Controllers\Admin\ReturnVerificationController::class, 'verify'])->name('verify');
            Route::get('{return}/reject', [\App\Http\Controllers\Admin\ReturnVerificationController::class, 'rejectForm'])->name('rejectForm');
            Route::post('{return}/reject', [\App\Http\Controllers\Admin\ReturnVerificationController::class, 'reject'])->name('reject');
            Route::get('{return}', [\App\Http\Controllers\Admin\ReturnVerificationController::class, 'show'])->name('show');
        });

        // Reporting routes
        Route::prefix('reports')->name('reports.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Admin\ReportingController::class, 'dashboard'])->name('dashboard');
            Route::get('loans', [\App\Http\Controllers\Admin\ReportingController::class, 'loans'])->name('loans');
            Route::get('inventory', [\App\Http\Controllers\Admin\ReportingController::class, 'inventory'])->name('inventory');
            Route::get('stock-movement', [\App\Http\Controllers\Admin\ReportingController::class, 'stockMovement'])->name('stock-movement');
            Route::get('overdue', [\App\Http\Controllers\Admin\ReportingController::class, 'overdue'])->name('overdue');
            Route::get('export/loans', [\App\Http\Controllers\Admin\ReportingController::class, 'exportLoans'])->name('export-loans');
            Route::get('export/inventory', [\App\Http\Controllers\Admin\ReportingController::class, 'exportInventory'])->name('export-inventory');
            Route::get('export/stock-movement', [\App\Http\Controllers\Admin\ReportingController::class, 'exportStockMovement'])->name('export-stock-movement');
            Route::get('export/overdue', [\App\Http\Controllers\Admin\ReportingController::class, 'exportOverdue'])->name('export-overdue');
            Route::get('api/staff-search', [\App\Http\Controllers\Admin\ReportingController::class, 'apiStaffSearch'])->name('api.staff-search');
        });
    });

    // Staff routes
    Route::middleware('staff')->prefix('staff')->name('staff.')->group(function () {
        // Item search routes
        Route::prefix('items')->name('items.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Staff\ItemSearchController::class, 'index'])->name('index');
            Route::get('search', [\App\Http\Controllers\Staff\ItemSearchController::class, 'search'])->name('search');
            Route::get('{item}', [\App\Http\Controllers\Staff\ItemSearchController::class, 'show'])->name('show');
        });
        
        // Loan request routes
        Route::prefix('loans')->name('loans.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Staff\LoanRequestController::class, 'index'])->name('index');
            Route::get('create', [\App\Http\Controllers\Staff\LoanRequestController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Staff\LoanRequestController::class, 'store'])->name('store');
            Route::get('{loan}', [\App\Http\Controllers\Staff\LoanRequestController::class, 'show'])->name('show');
            Route::get('{item}/details', [\App\Http\Controllers\Staff\LoanRequestController::class, 'getItemDetails'])->name('item-details');
            Route::get('check-availability', [\App\Http\Controllers\Staff\LoanRequestController::class, 'checkAvailability'])->name('check-availability');
        });
        
        // Return request routes
        Route::prefix('returns')->name('returns.')->group(function () {
            Route::get('/', [\App\Http\Controllers\Staff\ReturnRequestController::class, 'index'])->name('index');
            Route::get('create', [\App\Http\Controllers\Staff\ReturnRequestController::class, 'create'])->name('create');
            Route::post('/', [\App\Http\Controllers\Staff\ReturnRequestController::class, 'store'])->name('store');
            Route::get('{return}', [\App\Http\Controllers\Staff\ReturnRequestController::class, 'show'])->name('show');
            Route::get('loan/{loan}/details', [\App\Http\Controllers\Staff\ReturnRequestController::class, 'getLoanDetails'])->name('loan-details');
        });
    });
});

