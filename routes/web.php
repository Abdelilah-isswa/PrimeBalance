<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\TransactionController;

Route::get('/', function () {
    return view('home');
})->middleware('auth');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/companies', [CompanyController::class, 'index']);
    Route::get('/companies/create', [CompanyController::class, 'create']);
    Route::post('/companies', [CompanyController::class, 'store']);
    Route::get('/companies/{id}', [CompanyController::class, 'show']);
    Route::post('/companies/{id}/categories', [CategoryController::class, 'store']);
    Route::get('/companies/{id}/clients/create', [ClientController::class, 'create']);
    Route::post('/companies/{id}/clients', [ClientController::class, 'store']);
    Route::get('/companies/{id}/suppliers/create', [SupplierController::class, 'create']);
    Route::post('/companies/{id}/suppliers', [SupplierController::class, 'store']);
    Route::get('/companies/{id}/accounts/create', [AccountController::class, 'create']);
    Route::post('/companies/{id}/accounts', [AccountController::class, 'store']);
    Route::get('/companies/{companyId}/invoices', [InvoiceController::class, 'index']);
    Route::get('/companies/{companyId}/clients/{clientId}/invoices/create', [InvoiceController::class, 'create']);
    Route::post('/companies/{companyId}/clients/{clientId}/invoices', [InvoiceController::class, 'store']);
    Route::get('/companies/{companyId}/invoices/{invoiceId}/receive', [InvoiceController::class, 'showReceivePayment']);
    Route::post('/companies/{companyId}/invoices/{invoiceId}/receive', [InvoiceController::class, 'receivePayment']);
    Route::get('/companies/{companyId}/bills', [BillController::class, 'index']);
    Route::get('/companies/{companyId}/suppliers/{supplierId}/bills/create', [BillController::class, 'create']);
    Route::post('/companies/{companyId}/suppliers/{supplierId}/bills', [BillController::class, 'store']);
    Route::get('/companies/{companyId}/bills/{billId}/pay', [BillController::class, 'showPayment']);
    Route::post('/companies/{companyId}/bills/{billId}/pay', [BillController::class, 'pay']);
    Route::get('/companies/{companyId}/transactions', [TransactionController::class, 'index']);
    Route::get('/companies/{companyId}/transactions/create', [TransactionController::class, 'create']);
    Route::post('/companies/{companyId}/transactions', [TransactionController::class, 'store']);
});
