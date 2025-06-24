<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LaboratoryController;
use App\Http\Controllers\Navigation\AdminNavigationController;
use App\Http\Controllers\Navigation\BorrowerNavigationController;
use App\Http\Controllers\Navigation\EmployeeNavigationController;
use App\Http\Controllers\Navigation\StaffNavigationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserProfileController;
use App\Http\Controllers\PenaltyController;
use App\Http\Middleware\CheckRole;
use App\Http\Middleware\RedirectIfAuthenticated;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
})->name('loginPage')->middleware(RedirectIfAuthenticated::class);

Route::get('/register', function () {
    return view('register');
})->name('registerPage')->middleware(RedirectIfAuthenticated::class);

Route::prefix('admin')->middleware(CheckRole::class)->group(function () {
    // Dashboard
    Route::get('dashboard', [AdminNavigationController::class, 'dashboard'])->name('viewAdminDashboard');
    // Laboratory
    Route::get('laboratory', [AdminNavigationController::class, 'laboratory'])->name('viewAdminLaboratory');
    // Staff
    Route::get('staff', [AdminNavigationController::class, 'staff'])->name('viewAdminStaff');
    // Employee
    Route::get('employee', [AdminNavigationController::class, 'employee'])->name('viewAdminEmployee');
    // Borrower
    Route::get('borrower', [AdminNavigationController::class, 'borrower'])->name('viewAdminBorrower');
    // Category
    Route::get('category', [AdminNavigationController::class, 'category'])->name('viewAdminCategory');
    // Item
    Route::get('item', [AdminNavigationController::class, 'item'])->name('viewAdminItem');
    // Transaction
    Route::get('transaction', [AdminNavigationController::class, 'transaction'])->name('viewAdminTransaction');
    // Inventory
    Route::get('inventory', [AdminNavigationController::class, 'inventory'])->name('viewAdminInventory');
    // Penalty
    Route::get('penalty', [AdminNavigationController::class, 'penalty'])->name('viewAdminPenalty');
    // Report
    Route::get('report', [AdminNavigationController::class, 'report'])->name('viewAdminReport');
    // User Management
    Route::get('user', [AdminNavigationController::class, 'user'])->name('viewAdminUser');
});

Route::prefix('staff')->middleware(CheckRole::class)->group(function () {
    // Dashboard
    Route::get('dashboard', [StaffNavigationController::class, 'dashboard'])->name('viewStaffDashboard');
    // Category
    Route::get('category', [StaffNavigationController::class, 'category'])->name('viewStaffCategory');
    // Item
    Route::get('item', [StaffNavigationController::class, 'item'])->name('viewStaffItem');
    // Transaction
    Route::get('transaction', [StaffNavigationController::class, 'transaction'])->name('viewStaffTransaction');
    // Inventory
    Route::get('inventory', [StaffNavigationController::class, 'inventory'])->name('viewStaffInventory');
    // Report
    Route::get('penalties', [StaffNavigationController::class, 'penalties'])->name('viewStaffPenalty');
    // Report
    Route::get('report', [StaffNavigationController::class, 'report'])->name('viewStaffReport');
});

Route::prefix('employee')->middleware(CheckRole::class)->group(function () {
    // Dashboard
    Route::get('dashboard', [EmployeeNavigationController::class, 'dashboard'])->name('viewEmployeeDashboard');
    // Item
    Route::get('item', [EmployeeNavigationController::class, 'item'])->name('viewEmployeeItem');
    // Transaction
    Route::get('transaction', [EmployeeNavigationController::class, 'transaction'])->name('viewEmployeeTransaction');
});

Route::prefix('borrower')->middleware(CheckRole::class)->group(function () {
    // Dashboard
    Route::get('dashboard', [BorrowerNavigationController::class, 'dashboard'])->name('viewBorrowerDashboard');
    // Item
    Route::get('item', [BorrowerNavigationController::class, 'item'])->name('viewBorrowerItem');
    // Transaction
    Route::get('transaction', [BorrowerNavigationController::class, 'transaction'])->name('viewBorrowerTransaction');
});

Route::get('/myProfile', [UserProfileController::class, 'myProfile'])->name('myProfile');

// Route::get('/changePassword', function () {})->name('changePassword');
Route::post('/changePassword', [UserController::class, 'changePassword'])->name('changePassword');
Route::post('/changeUserPassword/{id}', [UserController::class, 'changeUserPassword'])->name('changeUserPassword');


//Resource
Route::prefix('laboratories')->group(function () {
    Route::get('/', [LaboratoryController::class, 'index'])->name('laboratories.index');
    Route::get('/{laboratory}', [LaboratoryController::class, 'show'])->name('laboratories.show');
    Route::post('/', [LaboratoryController::class, 'store'])->name('laboratories.store');
    Route::put('/{laboratory}', [LaboratoryController::class, 'update'])->name('laboratories.update');
    Route::delete('/{laboratory}', [LaboratoryController::class, 'destroy'])->name('laboratories.destroy');
});

Route::prefix('users')->group(function () {
    Route::get('/', [UserController::class, 'index'])->name('users.index');
    Route::get('/list', [UserController::class, 'list'])->name('users.list');
    Route::get('/{user}', [UserController::class, 'show'])->name('users.show');
    Route::post('/', [UserController::class, 'store'])->name('users.store');
    Route::put('/{user}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index'])->name('categories.index');
    Route::get('/{category}', [CategoryController::class, 'show'])->name('categories.show');
    Route::post('/', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/{category}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');
});

Route::prefix('items')->group(function () {
    Route::get('/', [ItemController::class, 'index'])->name('items.index');
    Route::get('/{item}', [ItemController::class, 'show'])->name('items.show');
    Route::post('/', [ItemController::class, 'store'])->name('items.store');
    Route::put('/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::delete('/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
});

Route::prefix('transactions')->group(function () {
    Route::get('/', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');
    Route::post('/', [TransactionController::class, 'store'])->name('transactions.store');
    Route::put('/{transaction}', [TransactionController::class, 'update'])->name('transactions.update');
    Route::delete('/{transaction}', [TransactionController::class, 'destroy'])->name('transactions.destroy');
    Route::post('/{transaction}/confirm', [TransactionController::class, 'confirm'])->name('transactions.confirm');
    Route::post('/{transaction}/cancel', [TransactionController::class, 'cancel'])->name('transactions.cancel');
    Route::post('/{transaction}/reject', [TransactionController::class, 'reject'])->name('transactions.reject');
    Route::post('/{transaction}/release', [TransactionController::class, 'release'])->name('transactions.release');
    Route::post('/{transaction}/return', [TransactionController::class, 'returnTransaction'])->name('transactions.return');
    Route::get('/check-user-limit/{userId}', [TransactionController::class, 'checkUserLimit'])->name('transactions.check-user-limit');
});

Route::get('/penalties/data', [PenaltyController::class, 'fetchData'])->name('penalties.data');

Route::prefix('inventories')->group(function () {
    Route::get('/', [InventoryController::class, 'index'])->name('inventories.index');
    Route::get('/{item}', [InventoryController::class, 'show'])->name('inventories.show');
    Route::post('/', [InventoryController::class, 'store'])->name('inventories.store');
});

Route::prefix('reports')->group(function () {
    Route::get('/stock_summary', [ReportController::class, 'stockSummary'])->name('reports.stock_summary');
    Route::get('/transaction_history', [ReportController::class, 'transactionHistory'])->name('reports.transaction_history');
    Route::get('/penalty_summary', [ReportController::class, 'penaltySummary'])->name('reports.penalty_summary');
    Route::get('/overdue_transactions', [ReportController::class, 'overdueTransactions'])->name('reports.overdue_transactions');
});

Route::post('login', [AuthController::class, 'login'])->name('login');
Route::post('register', [AuthController::class, 'register'])->name('register');
Route::post('logout', [AuthController::class, 'logout'])->name('logout');
Route::post('check-username', [AuthController::class, 'checkUsername'])->name('checkUsername');
Route::post('check-email', [AuthController::class, 'checkEmail'])->name('checkEmail');
Route::post('check-contact', [AuthController::class, 'checkContact'])->name('checkContact');
