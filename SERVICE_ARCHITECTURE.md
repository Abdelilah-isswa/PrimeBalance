# Service Layer Architecture

This document outlines the refactored controller structure using the Service Layer pattern to improve code readability, maintainability, and separation of concerns.

## Services Created

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

## Usage Example

```php
// Before (in Controller)
public function store(Request $request, $companyId)
{
    // Validation
    // Complex business logic
    // Database operations
    // Email sending
    // Response handling
}

// After (in Controller)
public function store(Request $request, $companyId)
{
    $request->validate([...]);
    
    $result = $this->service->createEntity($request->validated());
    
    return redirect()->back()->with('success', 'Created successfully');
}

// Business logic moved to Service
public function createEntity(array $data): Entity
{
    // All business logic here
    // Database operations
    // Email sending
    // Return entity
}
```

This architecture makes the codebase more maintainable, testable, and follows Laravel best practices for organizing business logic.