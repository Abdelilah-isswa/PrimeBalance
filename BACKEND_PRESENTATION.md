# Laravel 11 Multi-Tenant Accounting Backend - Senior Technical Deep Dive

## 🏗️ **Architecture: Vertical Slice + Clean Layers**

**Primary Pattern:** FormRequest → Controller → Service → Model  
**Laravel 11** with **Sanctum** for SPA API tokens

```
Request → FormRequest::authorize() + validate() 
       → Controller::method(FormRequest $request) 
       → Service::businessMethod($validatedData)
       → Eloquent::query() / mass-assign
```

**Dependency Injection:** Service auto-resolved via constructor:
```php
class CompanyController extends BaseController {
    public function __construct(CompanyService $companyService) {
        $this->companyService = $companyService;
    }
}
```

---

## 🔐 **Multi-Tenant Authorization: HasCompanyAuthorization Trait**

**Pivot Table Enforcement:** `company_user` with `role` enum + `left_at`

```php
trait HasCompanyAuthorization {
    protected function getCompanyAsOwner(int $companyId, string $action): Company {
        $company = Auth::user()
            ->companies()
            ->where('id', $companyId)
            ->wherePivot('role', 'owner')
            ->whereNull('left_at')  // Soft-remove via pivot
            ->firstOrFail();
        
        return $company;
    }
}
```

**FormRequest Integration:**
```php
class StoreInvoiceRequest extends FormRequest {
    use HasCompanyAuthorization;
    
    public function authorize(): bool {
        return $this->isCompanyOwner($this->route('companyId'));
    }
}
```

**Benefits:** 
- **Route Model Binding** safe-guarded
- **Zero controller auth boilerplate**
- **Consistent 403/404** with action context

---

## 📡 **API Design: Domain-Grouped Resource Routes**

**routes/api.php** (Laravel `apiResource` + custom):
```php
Route::prefix('companies/{companyId}')->group(function () {
    Route::apiResource('invoices', InvoiceController::class);
    Route::post('invoices/{invoice}/receive', [InvoiceController::class, 'receivePayment']);
    // Domain isolation via prefix binding
});
```

**Sanctum Token Flow:**
```
Login → $user->createToken('api-token')->plainTextToken
Each Request → Bearer {token}
Guard → auth:sanctum → User model with companies() scope
```

**Response Standardization** (`BaseController`):
```php
protected $sendResponse($result, $message = '') {
    return response()->json([
        'success' => true,
        'data'    => $result,
        'message' => $message,
    ]);
}
```

---

## 🗄️ **Eloquent Schema & Relationships**

**Critical Tables:**

```php
// company_user pivot (soft-remove via left_at)
Schema::table('company_user', function (Blueprint $table) {
    $table->timestamp('left_at')->nullable();
});

// Transactions polymorphic? No - explicit FKs
Schema::create('transactions', function (Blueprint $table) {
    $table->foreignId('invoice_id')->nullable()->constrained()->cascadeOnDelete();
    $table->foreignId('bill_id')->nullable()->constrained()->cascadeOnDelete();
});
```

**Scoped Queries:**
```php
// CompanyService::getDashboardMetrics()
$totalIncome = $company->transactions()
    ->where('type', 'income')
    ->whereBetween('date', [$start, $end])
    ->sum('amount');
```

**Mass Assignment Protection:** `$fillable` on all models.

---

## ⚙️ **Service Layer: Transactional Business Operations**

**Invoice Payment Atomicity** (`InvoiceService::receivePayment()`):
```php
public function receivePayment(Invoice $invoice, array $data): bool {
    DB::transaction(function () use ($invoice, $data) {
        // 1. Create income transaction
        Transaction::create([...]);
        
        // 2. Update account balance (query + increment)
        Account::find($data['account_id'])->increment('balance', $invoice->total_amount);
        
        // 3. Mark invoice paid
        $invoice->update(['status' => 'paid']);
    });
    
    return true;
}
```

**Invitation Workflow** (`CompanyService::inviteUser()`):
```php
// Idempotency check
if (Invitation::pendingForEmail($companyId, $email)->exists()) {
    return ['success' => false, 'message' => 'Pending invitation exists'];
}

// Token + Expiry (Str::random(64), now()->addDays(7))
Invitation::create([...]);
Mail::to($email)->queue(new CompanyInvitationMail(...));  // Queue for perf
```

---

## 🔍 **Form Request Deep Dive**

**Validation + Auth Compound:**
```php
class StoreCompanyRequest extends FormRequest {
    public function rules(): array {
        return [
            'name' => ['required', 'string', 'max:255'],
            'currency' => ['required', 'string', 'size:3', 'in:USD,EUR,GBP'],  // ISO 4217
        ];
    }
    
    // Implicit: authorize() via trait
}
```

**Custom Rules:** Can extend with `after()` hooks for complex invariants.

---

## 📊 **Performance Considerations**

**Eager Loading:** Service methods use `with()`:
```php
$companies = User::find(auth()->id())
    ->companies()
    ->with(['accounts', 'unpaidInvoices'])  // N+1 prevention
    ->get();
```

**Query Optimization:**
- Dashboard metrics: `sum()` + `whereBetween()` + cloned queries
- Balances: `accounts()->sum('balance')` aggregate
- Sanctum tokens: PersonalAccessTokens table pruning via queue

**Queue:** Mail::queue() for invitations/invoices.

---

## 🧪 **Testing Strategy**

**Layered Tests:**
```
Feature: API endpoints (Pest/Dusk)
Unit: Service methods (mock Eloquent)
Request: FormRequest validation
Model: Relationship assertions
```

**Example:** 
```php
test('invoice payment updates account balance', function () {
    $service = app(InvoiceService::class);
    
    $result = $service->receivePayment($invoice, $data);
    
    expect(Account::first()->balance)->toBe($expected);
});
```

---

## 🚀 **Deployment Checklist**

```
✅ Laravel 11 + Sanctum + Horizon (queues)
✅ .env: MAIL_MAILER=smtp, SANCTUM_STATEFUL_DOMAINS
✅ php artisan migrate --seed
✅ Queue worker: php artisan queue:work
✅ Storage symlink: public/storage
✅ Rate limiting: throttle:api
```

**Scalability:** Per-company sharding ready via tenant DBs.

**Technical Decisions Summary:**
- **No policies/gates** - FormRequest auth faster + explicit
- **Services > Repos** - Laravel Eloquent sufficient
- **JSON API responses** - Frontend-first
- **Pivot soft-deletes** - Audit trail preserved

Ready for **10k companies** with horizontal scaling.


