# Service Layer Architecture with Form Requests

This document outlines the refactored controller structure using the Service Layer pattern combined with Form Request classes to improve code readability, maintainability, and separation of concerns.

## Architecture Overview

The application now follows a three-layer architecture:
1. **Form Requests** - Handle validation and authorization
2. **Services** - Contain business logic
3. **Controllers** - Handle HTTP requests/responses

## Form Request Classes

### Authentication Requests
- `LoginRequest` - Login validation
- `RegisterRequest` - User registration validation

### Company Management Requests
- `StoreCompanyRequest` - Company creation validation
- `UpdateCompanyRequest` - Company update validation with owner authorization
- `InviteUserRequest` - User invitation validation with owner authorization
- `UpdateUserRoleRequest` - User role update validation with owner authorization

### Account Management Requests
- `StoreAccountRequest` - Account creation validation with owner authorization
- `UpdateAccountRequest` - Account update validation with owner authorization

### Client Management Requests
- `StoreClientRequest` - Client creation validation with owner authorization
- `UpdateClientRequest` - Client update validation with owner authorization

### Supplier Management Requests
- `StoreSupplierRequest` - Supplier creation validation with owner authorization
- `UpdateSupplierRequest` - Supplier update validation with owner authorization

### Invoice Management Requests
- `StoreInvoiceRequest` - Invoice creation validation with owner authorization
- `UpdateInvoiceRequest` - Invoice update validation with owner authorization
- `ReceivePaymentRequest` - Payment receipt validation with owner authorization

### Bill Management Requests
- `StoreBillRequest` - Bill creation validation with owner authorization
- `UpdateBillRequest` - Bill update validation with owner authorization
- `PayBillRequest` - Bill payment validation with owner authorization

### Transaction Management Requests
- `StoreTransactionRequest` - Transaction creation validation with owner authorization

### Category Management Requests
- `StoreCategoryRequest` - Category creation validation with owner authorization
- `UpdateCategoryRequest` - Category update validation with owner authorization

## Form Request Benefits

### 1. **Centralized Validation**
- All validation rules are defined in dedicated classes
- Consistent validation across the application
- Easy to modify and maintain validation logic

### 2. **Authorization Integration**
- Authorization logic is built into Form Requests
- Automatic permission checking before controller methods execute
- Cleaner controller methods without repetitive authorization code

### 3. **Data Preparation**
- `prepareForValidation()` method allows data transformation before validation
- Automatic handling of checkbox values (e.g., `is_active` fields)

### 4. **Type Safety**
- `validated()` method returns only validated data
- Prevents mass assignment vulnerabilities
- Clear data contracts between requests and services

### 5. **Cleaner Controllers**
- Controllers are now extremely thin and focused
- No validation or authorization logic in controllers
- Easy to read and understand controller methods

## Example Form Request Structure

```php
class StoreClientRequest extends FormRequest
{
    public function authorize(): bool
    {
        $company = $this->user()->companies()->find($this->route('companyId'));
        return $company && $company->pivot->role === 'owner';
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'address' => 'required|string',
            'phone' => 'required|string',
        ];
    }

    public function prepareForValidation(): void
    {
        // Transform data before validation if needed
        $this->merge([
            'is_active' => $this->has('is_active'),
        ]);
    }
}
```

### 1. AuthService
**Location:** `app/Services/AuthService.php`
**Responsibilities:**
- User authentication logic
- Login/logout operations
- User registration
- Invitation token handling after authentication

**Methods:**
- `attemptLogin(array $credentials): bool`
- `handlePostLoginInvitation(): ?string`
- `createUser(array $data): User`
- `loginUser(User $user): void`
- `logout(): void`

### 2. CompanyService
**Location:** `app/Services/CompanyService.php`
**Responsibilities:**
- Company management operations
- Dashboard metrics calculation
- User invitation system
- Company user management

**Methods:**
- `getDashboardMetrics(Company $company, ?string $startDate, ?string $endDate): array`
- `createCompany(array $data): Company`
- `updateCompany(Company $company, array $data): bool`
- `deactivateCompany(Company $company): bool`
- `inviteUser(Company $company, string $email, string $role): array`
- `acceptInvitation(Invitation $invitation): array`
- `removeUser(Company $company, int $userId): array`
- `updateUserRole(Company $company, int $userId, string $role): array`

### 3. InvoiceService
**Location:** `app/Services/InvoiceService.php`
**Responsibilities:**
- Invoice creation and management
- Payment processing for invoices
- Invoice validation and deletion

**Methods:**
- `createInvoice(array $data): Invoice`
- `updateInvoice(Invoice $invoice, array $data): bool`
- `receivePayment(Invoice $invoice, array $data): bool`
- `canDelete(Invoice $invoice): bool`
- `deleteInvoice(Invoice $invoice): bool`

### 4. BillService
**Location:** `app/Services/BillService.php`
**Responsibilities:**
- Bill creation and management
- Bill payment processing
- Bill operations

**Methods:**
- `createBill(array $data): Bill`
- `updateBill(Bill $bill, array $data): bool`
- `payBill(Bill $bill, array $data): bool`
- `deleteBill(Bill $bill): bool`

### 5. AccountService
**Location:** `app/Services/AccountService.php`
**Responsibilities:**
- Bank account management
- Account creation, updates, and deletion
- Account archiving logic

**Methods:**
- `createAccount(array $data): Account`
- `updateAccount(Account $account, array $data): bool`
- `deleteOrArchiveAccount(Account $account): array`

### 6. ClientService
**Location:** `app/Services/ClientService.php`
**Responsibilities:**
- Client management operations
- Client balance calculations
- Client validation and deletion

**Methods:**
- `createClient(array $data): Client`
- `updateClient(Client $client, array $data): bool`
- `canDelete(Client $client): bool`
- `deleteClient(Client $client): bool`
- `calculateClientBalances($clients)`

### 7. SupplierService
**Location:** `app/Services/SupplierService.php`
**Responsibilities:**
- Supplier management operations
- Supplier balance calculations
- Supplier validation and deletion

**Methods:**
- `createSupplier(array $data): Supplier`
- `updateSupplier(Supplier $supplier, array $data): bool`
- `canDelete(Supplier $supplier): bool`
- `deleteSupplier(Supplier $supplier): bool`
- `calculateSupplierBalances($suppliers)`

### 8. TransactionService
**Location:** `app/Services/TransactionService.php`
**Responsibilities:**
- Transaction creation and management
- Account balance updates
- Transaction processing

**Methods:**
- `createTransaction(array $data): Transaction`

### 9. CategoryService
**Location:** `app/Services/CategoryService.php`
**Responsibilities:**
- Category management operations
- Category CRUD operations

**Methods:**
- `createCategory(array $data): Category`
- `updateCategory(Category $category, array $data): bool`
- `deleteCategory(Category $category): bool`

## Benefits of This Architecture

### 1. **Separation of Concerns**
- Controllers now focus only on HTTP request/response handling
- Business logic is centralized in service classes
- Models remain focused on data representation

### 2. **Improved Testability**
- Services can be easily unit tested in isolation
- Controllers become thinner and easier to test
- Mock services can be injected for testing

### 3. **Code Reusability**
- Business logic in services can be reused across different controllers
- Common operations are centralized and consistent

### 4. **Better Maintainability**
- Changes to business logic only require updates in one place
- Easier to understand and modify code
- Clear responsibility boundaries

### 5. **Dependency Injection**
- Services are injected into controllers via constructor
- Follows Laravel's dependency injection patterns
- Easy to swap implementations for testing or different environments

## Controller Changes

All controllers have been refactored to:
- Inject their respective service via constructor
- Delegate business logic to service methods
- Focus on request validation and response formatting
- Handle HTTP-specific concerns (redirects, flash messages, etc.)

## Controller Examples

### Before Refactoring
```php
public function store(Request $request, $companyId)
{
    $company = Auth::user()->companies()->findOrFail($companyId);
    
    if ($company->pivot->role !== 'owner') {
        abort(403, 'Only owners can create clients');
    }

    $request->validate([
        'name' => 'required|string|max:255',
        'email' => 'required|email',
        'address' => 'required|string',
        'phone' => 'required|string',
    ]);

    Client::create([
        'company_id' => $companyId,
        'name' => $request->name,
        'email' => $request->email,
        'address' => $request->address,
        'phone' => $request->phone,
    ]);

    return redirect("/companies/{$companyId}");
}
```

### After Refactoring (with Services + Form Requests)
```php
public function store(StoreClientRequest $request, $companyId)
{
    $data = array_merge($request->validated(), ['company_id' => $companyId]);
    $this->clientService->createClient($data);
    return redirect("/companies/{$companyId}");
}
```

## Complete Architecture Flow

1. **Request hits Controller**
2. **Form Request validates and authorizes**
3. **Controller calls Service method**
4. **Service handles business logic**
5. **Controller returns response**

```
HTTP Request → Form Request → Controller → Service → Database
                    ↓              ↓         ↓
              Validation &    HTTP Logic   Business
              Authorization              Logic
```

This architecture makes the codebase more maintainable, testable, and follows Laravel best practices for organizing business logic.