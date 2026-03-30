<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CompanyController;
use App\Http\Controllers\InvitationController;
use App\Http\Controllers\CompanyUserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\BillController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\DocumentController;

Route::get('/', function () {
    $companies = Auth::user()->companies;
    
    if ($companies->count() === 0) {
        return redirect()->route('companies.create');
    } elseif ($companies->count() === 1) {
        return redirect()->route('companies.show', $companies->first()->id);
    } else {
        return redirect()->route('companies.index');
    }
})->middleware('auth')->name('home');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/invitations/{token}', [InvitationController::class, 'show'])->name('invitations.show');
Route::post('/invitations/{token}/accept', [InvitationController::class, 'accept'])->name('invitations.accept');
Route::post('/invitations/{token}/decline', [InvitationController::class, 'decline'])->name('invitations.decline');

Route::middleware('auth')->group(function () {
    Route::get('/companies', [CompanyController::class, 'index'])->name('companies.index');
    Route::get('/companies/create', [CompanyController::class, 'create'])->name('companies.create');
    Route::post('/companies', [CompanyController::class, 'store'])->name('companies.store');
    Route::get('/companies/{id}', [CompanyController::class, 'show'])->name('companies.show');
    Route::get('/companies/{id}/edit', [CompanyController::class, 'edit'])->name('companies.edit');
    Route::put('/companies/{id}', [CompanyController::class, 'update'])->name('companies.update');
    Route::post('/companies/{id}/deactivate', [CompanyController::class, 'deactivate'])->name('companies.deactivate');
    Route::post('/companies/{id}/invite', [InvitationController::class, 'store'])->name('companies.invite');
    Route::get('/companies/{id}/users', [CompanyUserController::class, 'index'])->name('companies.users.index');
    Route::delete('/companies/{companyId}/users/{userId}', [CompanyUserController::class, 'destroy'])->name('companies.users.remove');
    Route::put('/companies/{companyId}/users/{userId}/role', [CompanyUserController::class, 'updateRole'])->name('companies.users.role');
    
    Route::get('/companies/{id}/categories', [CategoryController::class, 'index'])->name('categories.index');
    Route::post('/companies/{id}/categories', [CategoryController::class, 'store'])->name('categories.store');
    Route::put('/companies/{companyId}/categories/{categoryId}', [CategoryController::class, 'update'])->name('categories.update');
    Route::delete('/companies/{companyId}/categories/{categoryId}', [CategoryController::class, 'destroy'])->name('categories.destroy');
    
    Route::get('/companies/{id}/clients/create', [ClientController::class, 'create'])->name('clients.create');
    Route::post('/companies/{id}/clients', [ClientController::class, 'store'])->name('clients.store');
    Route::get('/companies/{id}/clients/balances', [ClientController::class, 'balances'])->name('clients.balances');
    Route::get('/companies/{companyId}/clients/{clientId}/edit', [ClientController::class, 'edit'])->name('clients.edit');
    Route::put('/companies/{companyId}/clients/{clientId}', [ClientController::class, 'update'])->name('clients.update');
    Route::delete('/companies/{companyId}/clients/{clientId}', [ClientController::class, 'destroy'])->name('clients.destroy');
    
    Route::get('/companies/{id}/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
    Route::post('/companies/{id}/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
    Route::get('/companies/{id}/suppliers/balances', [SupplierController::class, 'balances'])->name('suppliers.balances');
    Route::get('/companies/{companyId}/suppliers/{supplierId}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
    Route::put('/companies/{companyId}/suppliers/{supplierId}', [SupplierController::class, 'update'])->name('suppliers.update');
    Route::delete('/companies/{companyId}/suppliers/{supplierId}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    
    Route::get('/companies/{id}/accounts', [AccountController::class, 'index'])->name('accounts.index');
    Route::get('/companies/{id}/accounts/create', [AccountController::class, 'create'])->name('accounts.create');
    Route::post('/companies/{id}/accounts', [AccountController::class, 'store'])->name('accounts.store');
    Route::get('/companies/{companyId}/accounts/{accountId}/edit', [AccountController::class, 'edit'])->name('accounts.edit');
    Route::put('/companies/{companyId}/accounts/{accountId}', [AccountController::class, 'update'])->name('accounts.update');
    Route::delete('/companies/{companyId}/accounts/{accountId}', [AccountController::class, 'destroy'])->name('accounts.destroy');
    
    Route::get('/companies/{id}/documents', [DocumentController::class, 'index'])->name('documents.index');
    
    Route::get('/companies/{companyId}/invoices', [InvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/companies/{companyId}/clients/{clientId}/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
    Route::post('/companies/{companyId}/clients/{clientId}/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
    Route::get('/companies/{companyId}/invoices/{invoiceId}', [InvoiceController::class, 'show'])->name('invoices.show');
    Route::get('/companies/{companyId}/invoices/{invoiceId}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
    Route::get('/companies/{companyId}/invoices/{invoiceId}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
    Route::put('/companies/{companyId}/invoices/{invoiceId}', [InvoiceController::class, 'update'])->name('invoices.update');
    Route::delete('/companies/{companyId}/invoices/{invoiceId}', [InvoiceController::class, 'destroy'])->name('invoices.destroy');
    Route::get('/companies/{companyId}/invoices/{invoiceId}/receive', [InvoiceController::class, 'showReceivePayment'])->name('invoices.receive');
    Route::post('/companies/{companyId}/invoices/{invoiceId}/receive', [InvoiceController::class, 'receivePayment'])->name('invoices.receive.store');
    
    Route::get('/companies/{companyId}/bills', [BillController::class, 'index'])->name('bills.index');
    Route::get('/companies/{companyId}/suppliers/{supplierId}/bills/create', [BillController::class, 'create'])->name('bills.create');
    Route::post('/companies/{companyId}/suppliers/{supplierId}/bills', [BillController::class, 'store'])->name('bills.store');
    Route::get('/companies/{companyId}/bills/{billId}', [BillController::class, 'show'])->name('bills.show');
    Route::get('/companies/{companyId}/bills/{billId}/edit', [BillController::class, 'edit'])->name('bills.edit');
    Route::put('/companies/{companyId}/bills/{billId}', [BillController::class, 'update'])->name('bills.update');
    Route::delete('/companies/{companyId}/bills/{billId}', [BillController::class, 'destroy'])->name('bills.destroy');
    Route::get('/companies/{companyId}/bills/{billId}/pay', [BillController::class, 'showPayment'])->name('bills.pay');
    Route::post('/companies/{companyId}/bills/{billId}/pay', [BillController::class, 'pay'])->name('bills.pay.store');
    
    Route::get('/companies/{companyId}/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/companies/{companyId}/transactions/create', [TransactionController::class, 'create'])->name('transactions.create');
    Route::post('/companies/{companyId}/transactions', [TransactionController::class, 'store'])->name('transactions.store');
});
