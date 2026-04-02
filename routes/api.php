<?php

use Illuminate\Http\Request;
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

Route::middleware('auth:sanctum')->prefix('v1')->name('api.')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Auth (public for login/register)
    Route::post('/login', [AuthController::class, 'login']);
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout']);

    // Invitations
    Route::get('/invitations/{token}', [InvitationController::class, 'show']);
    Route::post('/invitations/{token}/accept', [InvitationController::class, 'accept']);
    Route::post('/invitations/{token}/decline', [InvitationController::class, 'decline']);

    // Companies
    Route::apiResource('companies', CompanyController::class);
    Route::post('companies/{id}/invite', [InvitationController::class, 'store']);
    Route::get('companies/{id}/users', [CompanyUserController::class, 'index']);
    Route::delete('companies/{companyId}/users/{userId}', [CompanyUserController::class, 'destroy']);
    Route::put('companies/{companyId}/users/{userId}/role', [CompanyUserController::class, 'updateRole']);
    Route::post('companies/{id}/deactivate', [CompanyController::class, 'deactivate']);

    // Categories
    Route::prefix('companies/{companyId}')->group(function () {
        Route::apiResource('categories', CategoryController::class);
    });

    // Clients
    Route::prefix('companies/{companyId}')->group(function () {
        Route::get('clients/balances', [ClientController::class, 'balances']);
        Route::apiResource('clients', ClientController::class);
    });

    // Suppliers
    Route::prefix('companies/{companyId}')->group(function () {
        Route::get('suppliers/balances', [SupplierController::class, 'balances']);
        Route::apiResource('suppliers', SupplierController::class);
    });

    // Accounts
    Route::prefix('companies/{companyId}')->group(function () {
        Route::apiResource('accounts', AccountController::class);
    });

    // Documents
    Route::prefix('companies/{companyId}')->group(function () {
        Route::get('documents', [DocumentController::class, 'index']);
    });

    // Invoices
    Route::prefix('companies/{companyId}')->group(function () {
        Route::get('invoices/{invoiceId}/receive', [InvoiceController::class, 'showReceivePayment']);
        Route::post('invoices/{invoiceId}/receive', [InvoiceController::class, 'receivePayment']);
        Route::get('invoices/{invoiceId}/pdf', [InvoiceController::class, 'downloadPdf']);
        Route::apiResource('invoices', InvoiceController::class);
    });

    // Bills
    Route::prefix('companies/{companyId}')->group(function () {
        Route::get('bills/{billId}/pay', [BillController::class, 'showPayment']);
        Route::post('bills/{billId}/pay', [BillController::class, 'pay']);
        Route::apiResource('bills', BillController::class);
    });

    // Transactions
    Route::prefix('companies/{companyId}')->group(function () {
        Route::apiResource('transactions', TransactionController::class)->only(['index', 'store']);
    });
});
