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
    $companies = Auth::user()->companies;
    
    if ($companies->count() === 0) {
        return redirect('/companies/create');
    } elseif ($companies->count() === 1) {
        return redirect('/companies/' . $companies->first()->id);
    } else {
        return redirect('/companies');
    }
})->middleware('auth');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/invitations/{token}', [CompanyController::class, 'showInvitation']);
Route::post('/invitations/{token}/accept', [CompanyController::class, 'acceptInvitation']);
Route::post('/invitations/{token}/decline', [CompanyController::class, 'declineInvitation']);

Route::middleware('auth')->group(function () {
    Route::get('/companies', [CompanyController::class, 'index']);
    Route::get('/companies/create', [CompanyController::class, 'create']);
    Route::post('/companies', [CompanyController::class, 'store']);
    Route::get('/companies/{id}', [CompanyController::class, 'show']);
    Route::get('/companies/{id}/edit', [CompanyController::class, 'edit']);
    Route::put('/companies/{id}', [CompanyController::class, 'update']);
    Route::post('/companies/{id}/deactivate', [CompanyController::class, 'deactivate']);
    Route::post('/companies/{id}/invite', [CompanyController::class, 'inviteUser']);
    Route::delete('/companies/{companyId}/users/{userId}', [CompanyController::class, 'removeUser']);
    Route::put('/companies/{companyId}/users/{userId}/role', [CompanyController::class, 'updateUserRole']);
    Route::get('/companies/{id}/categories', [CategoryController::class, 'index']);
    Route::post('/companies/{id}/categories', [CategoryController::class, 'store']);
    Route::put('/companies/{companyId}/categories/{categoryId}', [CategoryController::class, 'update']);
    Route::delete('/companies/{companyId}/categories/{categoryId}', [CategoryController::class, 'destroy']);
    Route::get('/companies/{id}/clients/create', [ClientController::class, 'create']);
    Route::post('/companies/{id}/clients', [ClientController::class, 'store']);
    Route::get('/companies/{id}/clients/balances', [ClientController::class, 'balances']);
    Route::get('/companies/{companyId}/clients/{clientId}/edit', [ClientController::class, 'edit']);
    Route::put('/companies/{companyId}/clients/{clientId}', [ClientController::class, 'update']);
    Route::delete('/companies/{companyId}/clients/{clientId}', [ClientController::class, 'destroy']);
    Route::get('/companies/{id}/suppliers/create', [SupplierController::class, 'create']);
    Route::post('/companies/{id}/suppliers', [SupplierController::class, 'store']);
    Route::get('/companies/{id}/suppliers/balances', [SupplierController::class, 'balances']);
    Route::get('/companies/{companyId}/suppliers/{supplierId}/edit', [SupplierController::class, 'edit']);
    Route::put('/companies/{companyId}/suppliers/{supplierId}', [SupplierController::class, 'update']);
    Route::delete('/companies/{companyId}/suppliers/{supplierId}', [SupplierController::class, 'destroy']);
    Route::get('/companies/{id}/accounts', [AccountController::class, 'index']);
    Route::get('/companies/{id}/accounts/create', [AccountController::class, 'create']);
    Route::post('/companies/{id}/accounts', [AccountController::class, 'store']);
    Route::get('/companies/{companyId}/accounts/{accountId}/edit', [AccountController::class, 'edit']);
    Route::put('/companies/{companyId}/accounts/{accountId}', [AccountController::class, 'update']);
    Route::delete('/companies/{companyId}/accounts/{accountId}', [AccountController::class, 'destroy']);
    Route::get('/companies/{companyId}/invoices', [InvoiceController::class, 'index']);
    Route::get('/companies/{companyId}/clients/{clientId}/invoices/create', [InvoiceController::class, 'create']);
    Route::post('/companies/{companyId}/clients/{clientId}/invoices', [InvoiceController::class, 'store']);
    Route::get('/companies/{companyId}/invoices/{invoiceId}', [InvoiceController::class, 'show']);
    Route::get('/companies/{companyId}/invoices/{invoiceId}/edit', [InvoiceController::class, 'edit']);
    Route::put('/companies/{companyId}/invoices/{invoiceId}', [InvoiceController::class, 'update']);
    Route::delete('/companies/{companyId}/invoices/{invoiceId}', [InvoiceController::class, 'destroy']);
    Route::get('/companies/{companyId}/invoices/{invoiceId}/receive', [InvoiceController::class, 'showReceivePayment']);
    Route::post('/companies/{companyId}/invoices/{invoiceId}/receive', [InvoiceController::class, 'receivePayment']);
    Route::get('/companies/{companyId}/bills', [BillController::class, 'index']);
    Route::get('/companies/{companyId}/suppliers/{supplierId}/bills/create', [BillController::class, 'create']);
    Route::post('/companies/{companyId}/suppliers/{supplierId}/bills', [BillController::class, 'store']);
    Route::get('/companies/{companyId}/bills/{billId}', [BillController::class, 'show']);
    Route::get('/companies/{companyId}/bills/{billId}/edit', [BillController::class, 'edit']);
    Route::put('/companies/{companyId}/bills/{billId}', [BillController::class, 'update']);
    Route::delete('/companies/{companyId}/bills/{billId}', [BillController::class, 'destroy']);
    Route::get('/companies/{companyId}/bills/{billId}/pay', [BillController::class, 'showPayment']);
    Route::post('/companies/{companyId}/bills/{billId}/pay', [BillController::class, 'pay']);
    Route::get('/companies/{companyId}/transactions', [TransactionController::class, 'index']);
    Route::get('/companies/{companyId}/transactions/create', [TransactionController::class, 'create']);
    Route::post('/companies/{companyId}/transactions', [TransactionController::class, 'store']);
});
