# HasCompanyAuthorization Trait

This trait provides centralized company authorization logic for controllers, eliminating duplicate code and providing consistent authorization patterns.

## Location
`app/Http/Traits/HasCompanyAuthorization.php`

## Methods

### `getAuthorizedCompany(int $companyId): Company`
**Purpose**: Get company and ensure user has access to it
**Returns**: Company model instance
**Throws**: 404 if company not found or user doesn't have access

```php
$company = $this->getAuthorizedCompany($companyId);
```

### `getCompanyAsOwner(int $companyId, string $action = 'perform this action'): Company`
**Purpose**: Get company and ensure user is owner
**Parameters**:
- `$companyId`: The company ID
- `$action`: Custom action description for error message (optional)
**Returns**: Company model instance
**Throws**: 403 if user is not owner, 404 if company not found

```php
$company = $this->getCompanyAsOwner($companyId, 'create invoices');
// Error message: "Only owners can create invoices"
```

### `isCompanyOwner(int $companyId): bool`
**Purpose**: Check if user is owner of the company
**Returns**: Boolean indicating ownership status

```php
if ($this->isCompanyOwner($companyId)) {
    // User is owner
}
```

### `getCompanyWithRole(int $companyId, array|string $roles, string $action = 'perform this action'): Company`
**Purpose**: Get company and ensure user has specific role(s)
**Parameters**:
- `$companyId`: The company ID
- `$roles`: Single role string or array of allowed roles
- `$action`: Custom action description for error message (optional)
**Returns**: Company model instance
**Throws**: 403 if user doesn't have required role, 404 if company not found

```php
// Single role
$company = $this->getCompanyWithRole($companyId, 'owner', 'delete data');

// Multiple roles
$company = $this->getCompanyWithRole($companyId, ['owner', 'accountant'], 'view reports');
```

## Usage in Controllers

### Before (Duplicate Logic)
```php
public function create($companyId)
{
    $company = Auth::user()->companies()->findOrFail($companyId);
    
    if ($company->pivot->role !== 'owner') {
        abort(403, 'Only owners can create accounts');
    }

    return view('accounts.create', compact('company'));
}

public function edit($companyId, $accountId)
{
    $company = Auth::user()->companies()->findOrFail($companyId);
    
    if ($company->pivot->role !== 'owner') {
        abort(403, 'Only owners can edit accounts');
    }

    $account = $company->accounts()->findOrFail($accountId);
    return view('accounts.edit', compact('company', 'account'));
}
```

### After (Using Trait)
```php
use HasCompanyAuthorization;

public function create($companyId)
{
    $company = $this->getCompanyAsOwner($companyId, 'create accounts');
    return view('accounts.create', compact('company'));
}

public function edit($companyId, $accountId)
{
    $company = $this->getCompanyAsOwner($companyId, 'edit accounts');
    $account = $company->accounts()->findOrFail($accountId);
    return view('accounts.edit', compact('company', 'account'));
}
```

## Controllers Using This Trait

All controllers have been refactored to use this trait:

- `AccountController`
- `BillController`
- `CategoryController`
- `ClientController`
- `CompanyController`
- `DocumentController`
- `InvoiceController`
- `SupplierController`
- `TransactionController`

## Benefits

### 1. **DRY Principle**
- Eliminates duplicate authorization logic across controllers
- Single source of truth for company authorization

### 2. **Consistent Error Messages**
- Standardized error messages with customizable action descriptions
- Better user experience with descriptive error messages

### 3. **Maintainability**
- Changes to authorization logic only need to be made in one place
- Easy to add new authorization methods

### 4. **Flexibility**
- Support for multiple roles
- Customizable error messages
- Different authorization levels (access vs ownership)

### 5. **Code Readability**
- Clear method names that express intent
- Reduced controller method complexity

## Error Messages

The trait generates descriptive error messages:

```php
// Default message
$this->getCompanyAsOwner($companyId);
// "Only owners can perform this action"

// Custom message
$this->getCompanyAsOwner($companyId, 'create invoices');
// "Only owners can create invoices"

// Multiple roles
$this->getCompanyWithRole($companyId, ['owner', 'accountant'], 'view reports');
// "Only owner, accountant can view reports"
```

## Implementation Pattern

### Step 1: Add trait to controller
```php
use App\Http\Traits\HasCompanyAuthorization;

class YourController extends Controller
{
    use HasCompanyAuthorization;
}
```

### Step 2: Replace authorization logic
```php
// Replace this:
$company = Auth::user()->companies()->findOrFail($companyId);
if ($company->pivot->role !== 'owner') {
    abort(403, 'Only owners can...');
}

// With this:
$company = $this->getCompanyAsOwner($companyId, 'specific action');
```

## Security Considerations

- All methods ensure the user is authenticated (via `Auth::user()`)
- Company access is verified through the user's company relationships
- Role-based authorization is enforced at the pivot table level
- 404 errors are thrown for non-existent or inaccessible companies
- 403 errors are thrown for insufficient permissions

This trait provides a robust, secure, and maintainable approach to company authorization across the entire application.