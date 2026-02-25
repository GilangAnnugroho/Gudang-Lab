<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ItemMasterController;
use App\Http\Controllers\ItemVariantController;
use App\Http\Controllers\StockCurrentController;
use App\Http\Controllers\ItemBatchController;
use App\Http\Controllers\RequestController as RequestCrudController;
use App\Http\Controllers\RequestApprovalController;
use App\Http\Controllers\RequestDetailController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\StockOpnameController; 
use App\Http\Controllers\ReportController;
use App\Http\Controllers\GlobalSearchController;

Route::pattern('itemVariantId', '[0-9]+');
Route::pattern('itemVariant',   '[0-9]+');
Route::pattern('requestModel',  '[0-9]+');
Route::pattern('requestDetail', '[0-9]+');
Route::pattern('variant',       '[0-9]+');
Route::pattern('batch',         '[0-9]+');

Route::get('/', function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login.form');
});

Route::middleware('guest')->group(function () {
    Route::get('login',  [LoginController::class, 'showLoginForm'])->name('login.form');
    Route::post('login', [LoginController::class, 'login'])->name('login');
});

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('search', [GlobalSearchController::class, 'index'])->name('search.global');

    Route::resource('requests', RequestCrudController::class)
        ->parameters(['requests' => 'requestModel'])
        ->only(['index','show','create','store','edit','update','destroy']);

    Route::middleware('role:Super Admin|Kepala Lab')->group(function () {
        Route::put('requests/{requestModel}/approve', [RequestApprovalController::class, 'approve'])
            ->name('requests.approve');

        Route::put('requests/{requestModel}/reject',  [RequestApprovalController::class, 'reject'])
            ->name('requests.reject');
    });

    Route::middleware('role:Admin Gudang|Super Admin')->group(function () {
        Route::post('requests/{requestModel}/distribute', [RequestApprovalController::class, 'distribute'])
            ->name('requests.distribute');
    });

    Route::patch ('request-details/{requestDetail}', [RequestDetailController::class, 'update'])
        ->name('request-details.update');
    Route::delete('request-details/{requestDetail}', [RequestDetailController::class, 'destroy'])
        ->name('request-details.destroy');

    Route::middleware('role:Admin Gudang|Super Admin')->group(function () {
        Route::get('transactions/create', [TransactionController::class, 'create'])
            ->name('transactions.create');
        Route::post('transactions', [TransactionController::class, 'store'])
            ->name('transactions.store');
        Route::get('transactions/{transaction}/edit', [TransactionController::class, 'edit'])
            ->name('transactions.edit');
        Route::put('transactions/{transaction}', [TransactionController::class, 'update'])
            ->name('transactions.update');
        Route::delete('transactions/{transaction}', [TransactionController::class, 'destroy'])
            ->name('transactions.destroy');
    });

    Route::middleware('role:Super Admin|Admin Gudang|Kepala Lab')->group(function () {
        Route::get('transactions', [TransactionController::class, 'index'])
            ->name('transactions.index');
        Route::get('transactions/{transaction}', [TransactionController::class, 'show'])
            ->name('transactions.show');
    });

    Route::middleware('role:Kepala Lab')->group(function () {
        Route::get('/stock-opnames/create', [StockOpnameController::class, 'create'])
            ->name('stock-opnames.create');
        
        Route::post('/stock-opnames', [StockOpnameController::class, 'store'])
            ->name('stock-opnames.store');
    });

    Route::middleware('role:Admin Gudang|Kepala Lab|Super Admin')->group(function () {
        Route::get('/stock-opnames/print', [StockOpnameController::class, 'print'])
            ->name('stock-opnames.print');
        Route::get('/stock-opnames', [StockOpnameController::class, 'index'])
            ->name('stock-opnames.index');
    });
    
    Route::middleware('role:Admin Gudang|Super Admin')->group(function () {
        Route::resource('categories', CategoryController::class);
        Route::resource('suppliers', SupplierController::class);
        Route::resource('units', UnitController::class)->except(['show']);
        Route::resource('items', ItemMasterController::class);
        Route::resource('variants', ItemVariantController::class)
            ->parameters(['variants' => 'itemVariant']);
        Route::resource('batches', ItemBatchController::class)
            ->parameters(['batches' => 'batch'])
            ->except(['show']);

        Route::get ('stock',                     [StockCurrentController::class, 'index'])
            ->name('stock.index');
        Route::get ('stock/{variant}',           [StockCurrentController::class, 'show'])
            ->name('stock.show');
        Route::post('stock/{variant}/seed',      [StockCurrentController::class, 'seed'])
            ->name('stock.seed');
        Route::post('stock/{variant}/recompute', [StockCurrentController::class, 'recompute'])
            ->name('stock.recompute');
        Route::get('stock/item/{itemMaster}', [StockCurrentController::class, 'cardByItem'])
            ->name('stock.card-item');
    });

    Route::middleware('role:Super Admin')->group(function () {
        Route::resource('users', UserController::class);
        Route::get   ('/roles',                [RoleController::class, 'page'])->name('roles.index');
        Route::get   ('/roles/list',           [RoleController::class, 'index'])->name('roles.list');
        Route::post  ('/roles/store',          [RoleController::class, 'store'])->name('roles.store');
        Route::put   ('/roles/{role}/update',  [RoleController::class, 'update'])->name('roles.update');
        Route::delete('/roles/{role}/delete',  [RoleController::class, 'destroy'])->name('roles.delete');
    });
    
    Route::middleware('role:Super Admin|Admin Gudang|Kepala Lab')
        ->prefix('reports')
        ->name('reports.')
        ->group(function () {
            Route::get('/stock',        [ReportController::class, 'stock'])->name('stock');
            Route::get('/stock/print',  [ReportController::class, 'printStock'])->name('stock.print');
            Route::get('/usage-yearly',       [ReportController::class, 'usageYearly'])->name('usage_yearly');
            Route::get('/usage-yearly/print', [ReportController::class, 'printUsageYearly'])->name('usage_yearly.print');
        });

    Route::middleware('role:Super Admin|Admin Gudang')
        ->prefix('reports')
        ->name('reports.')
        ->group(function () {
            Route::get('/distribution',       [ReportController::class, 'distribution'])->name('distribution');
            Route::get('/distribution/print', [ReportController::class, 'printDistribution'])->name('distribution.print');
        });

    Route::middleware('role:Kepala Lab')
        ->prefix('reports')
        ->name('reports.')
        ->group(function () {
            Route::get('/approved',       [ReportController::class, 'approvedRequests'])->name('approved');
            Route::get('/approved/print', [ReportController::class, 'printApprovedRequests'])->name('approved.print');

            Route::get('/outgoing',       [ReportController::class, 'outgoing'])->name('outgoing');
            Route::get('/outgoing/print', [ReportController::class, 'printOutgoing'])->name('outgoing.print');
        });

    Route::middleware('role:Petugas Unit')
        ->prefix('reports')
        ->name('reports.')
        ->group(function () {
            Route::get('/unit-received',       [ReportController::class, 'unitReceived'])->name('unit_received');
            Route::get('/unit-received/print', [ReportController::class, 'printUnitReceived'])->name('unit_received.print');
        });

});

Route::fallback(function () {
    return Auth::check()
        ? redirect()->route('dashboard')
        : redirect()->route('login.form');
});