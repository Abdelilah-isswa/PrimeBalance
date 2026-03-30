# Form Request Classes Reference

This document provides a comprehensive list of all Form Request classes created for the Laravel application.

## Directory Structure
```
app/Http/Requests/
├── LoginRequest.php
├── RegisterRequest.php
├── StoreCompanyRequest.php
├── UpdateCompanyRequest.php
├── InviteUserRequest.php
├── UpdateUserRoleRequest.php
├── StoreAccountRequest.php
├── UpdateAccountRequest.php
├── StoreClientRequest.php
├── UpdateClientRequest.php
├── StoreSupplierRequest.php
├── UpdateSupplierRequest.php
├── StoreInvoiceRequest.php
├── UpdateInvoiceRequest.php
├── ReceivePaymentRequest.php
├── StoreBillRequest.php
├── UpdateBillRequest.php
├── PayBillRequest.php
├── StoreTransactionRequest.php
├── StoreCategoryRequest.php
└── UpdateCategoryRequest.php
```

## Authentication Requests

### LoginRequest
- **Purpose**: Validates user login credentials
- **Rules**: email (required|email), password (required)
- **Authorization**: Always true (public access)

### RegisterRequest
- **Purpose**: Validates user registration data
- **Rules**: name (required|string|max:255), email (required|email|unique:users), password (required|min:8|confirmed)
- **Authorization**: Always true (public access)

## Company Management Requests

### StoreCompanyRequest
- **Purpose**: Validates company creation data
- **Rules**: name (required|string|max:255), address (required|string), currency (required|string|max:10)
- **Authorization**: Always true (authenticated users can create companies)

### UpdateCompanyRequest
- **Purpose**: Validates company update data
- **Rules**: name (required|string|max:255), address (required|string), currency (required|string|max:10)
- **Authorization**: Only company owners can update

### InviteUserRequest
- **Purpose**: Validates user invitation data
- **Rules**: email (required|email), role (required|in:owner,accountant,standard_user,viewer)
- **Authorization**: Only company owners can invite users

### UpdateUserRoleRequest
- **Purpose**: Validates user role update data
- **Rules**: role (required|in:owner,accountant,standard_user,viewer)
- **Authorization**: Only company owners can update user roles

## Account Management Requests

### StoreAccountRequest
- **Purpose**: Validates account creation data
- **Rules**: name (required|string|max:255), balance (required|numeric), is_active (boolean)
- **Authorization**: Only company owners can create accounts
- **Special**: Handles checkbox transformation for is_active field

### UpdateAccountRequest
- **Purpose**: Validates account update data
- **Rules**: name (required|string|max:255), is_active (boolean)
- **Authorization**: Only company owners can update accounts
- **Special**: Handles checkbox transformation for is_active field

## Client Management Requests

### StoreClientRequest
- **Purpose**: Validates client creation data
- **Rules**: name (required|string|max:255), email (required|email), address (required|string), phone (required|string)
- **Authorization**: Only company owners can create clients

### UpdateClientRequest
- **Purpose**: Validates client update data
- **Rules**: name (required|string|max:255), email (required|email), address (required|string), phone (required|string)
- **Authorization**: Only company owners can update clients

## Supplier Management Requests

### StoreSupplierRequest
- **Purpose**: Validates supplier creation data
- **Rules**: name (required|string|max:255), email (required|email), address (required|string), phone (required|string)
- **Authorization**: Only company owners can create suppliers

### UpdateSupplierRequest
- **Purpose**: Validates supplier update data
- **Rules**: name (required|string|max:255), email (required|email), address (required|string), phone (required|string)
- **Authorization**: Only company owners can update suppliers

## Invoice Management Requests

### StoreInvoiceRequest
- **Purpose**: Validates invoice creation data
- **Rules**: total_amount (required|numeric|min:0), status (required|in:draft,sent,paid,cancelled), send_email (boolean)
- **Authorization**: Only company owners can create invoices

### UpdateInvoiceRequest
- **Purpose**: Validates invoice update data
- **Rules**: total_amount (required|numeric|min:0), status (required|in:draft,sent,paid,cancelled)
- **Authorization**: Only company owners can update invoices

### ReceivePaymentRequest
- **Purpose**: Validates payment receipt data
- **Rules**: account_id (required|exists:accounts,id), category_id (nullable|exists:categories,id), date (required|date)
- **Authorization**: Only company owners can receive payments

## Bill Management Requests

### StoreBillRequest
- **Purpose**: Validates bill creation data
- **Rules**: total_amount (required|numeric|min:0), status (required|in:draft,sent,paid,cancelled)
- **Authorization**: Only company owners can create bills

### UpdateBillRequest
- **Purpose**: Validates bill update data
- **Rules**: total_amount (required|numeric|min:0), status (required|in:draft,sent,paid,cancelled)
- **Authorization**: Only company owners can update bills

### PayBillRequest
- **Purpose**: Validates bill payment data
- **Rules**: account_id (required|exists:accounts,id), category_id (nullable|exists:categories,id), date (required|date)
- **Authorization**: Only company owners can pay bills

## Transaction Management Requests

### StoreTransactionRequest
- **Purpose**: Validates transaction creation data
- **Rules**: account_id (required|exists:accounts,id), category_id (nullable|exists:categories,id), type (required|in:income,expense), amount (required|numeric|min:0), description (required|string), date (required|date)
- **Authorization**: Only company owners can create transactions

## Category Management Requests

### StoreCategoryRequest
- **Purpose**: Validates category creation data
- **Rules**: name (required|string|max:255)
- **Authorization**: Only company owners can create categories

### UpdateCategoryRequest
- **Purpose**: Validates category update data
- **Rules**: name (required|string|max:255)
- **Authorization**: Only company owners can update categories

## Authorization Pattern

Most Form Requests follow this authorization pattern:
```php
public function authorize(): bool
{
    $company = $this->user()->companies()->find($this->route('companyId'));
    return $company && $company->pivot->role === 'owner';
}
```

This ensures that:
1. The user is authenticated
2. The user belongs to the specified company
3. The user has the 'owner' role in that company

## Benefits Summary

1. **Centralized Validation**: All validation rules are in dedicated classes
2. **Built-in Authorization**: Permission checks are automatic
3. **Clean Controllers**: Controllers are now extremely thin
4. **Type Safety**: Only validated data reaches services
5. **Maintainability**: Easy to modify validation rules
6. **Consistency**: Uniform validation patterns across the application
7. **Security**: Prevents mass assignment and unauthorized access