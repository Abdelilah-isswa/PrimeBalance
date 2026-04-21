
# Technologies Used

- **Laravel**: The main PHP framework powering the backend. Provides routing, middleware, service providers, and a modular structure for rapid API development.
- **Eloquent ORM**: Laravel’s built-in ORM for database access, relationships, and query building using PHP objects.
- **Sanctum**: Laravel package for API authentication using tokens, enabling secure SPA/mobile authentication.
- **PHP**: The core programming language for all backend logic.
- **Composer**: Dependency manager for PHP, used to install and manage libraries.
- **MySQL/PostgreSQL**: Supported relational databases for persistent storage (configured in `config/database.php`).
- **Mail**: Laravel’s mail system for sending emails (invitations, invoices, etc.).
- **Migrations & Seeders**: For version-controlled database schema and test data population.
- **Validation**: Laravel Form Requests for robust input validation.
- **RESTful API**: Follows REST principles for resource management and HTTP verbs.

# Backend Architecture Overview


## A Senior's Walkthrough: How This Backend Works

Welcome! If you're new to this codebase, let me walk you through how everything fits together, why it's structured this way, and how the data flows from the user to the database and back.

### The Big Picture

This backend is built with Laravel, a PHP framework that helps us organize code, handle requests, and talk to the database efficiently. The goal is to let businesses manage their finances—companies, users, invoices, bills, transactions, and more—using a clean, secure API.

### How a Request Flows

1. **A user (or frontend app) makes an HTTP request** to our API (for example, to create an invoice).
2. **Routes** (in `routes/api.php`) decide which controller should handle the request. Think of routes as traffic cops, sending each request to the right place.
3. **Controllers** (in `app/Http/Controllers/`) receive the request. They don't do heavy lifting themselves—instead, they validate the data (using Form Requests), call the right Service for business logic, and return a response.
4. **Form Requests** (in `app/Http/Requests/`) make sure the data is clean and valid before we do anything with it. This keeps our app safe and predictable.
5. **Services** (in `app/Services/`) contain the real business logic. For example, creating an invoice, updating balances, sending emails, etc. This keeps controllers simple and our logic reusable.
6. **Models** (in `app/Models/`) represent our database tables. They let us fetch, create, and update records using PHP objects instead of raw SQL.
7. **Database**: All data is stored in a relational database (like MySQL or PostgreSQL). Migrations define the structure, and Eloquent models handle the data.
8. **Response**: The controller sends a JSON response back to the user or frontend app.

### Why This Structure?

- **Separation of concerns**: Each part of the code has a clear job. This makes it easier to test, debug, and extend.
- **Security**: Validation and authentication (using Sanctum) are built-in at every step.
- **Scalability**: By keeping business logic in services, we can add features or change how things work without breaking everything else.

### Example: Creating an Invoice

1. The frontend sends a POST request to `/api/v1/companies/{companyId}/invoices` with invoice data.
2. The route sends this to `InvoiceController@store`.
3. The controller uses `StoreInvoiceRequest` to validate the data.
4. If valid, it calls `InvoiceService` to handle the creation logic (e.g., saving the invoice, updating balances, maybe sending an email).
5. The service uses the `Invoice` model to save data to the database.
6. A response is sent back—success or error.

### Multi-Tenancy & Roles

All business data is scoped to companies. Users can belong to multiple companies, each with their own role (admin, accountant, etc.). This is managed through pivot tables and role checks in the code.

### Invitations & Onboarding

When you invite a teammate, we generate a token and send an email (using Laravel's mail system). The invitee clicks the link, accepts, and joins the company. This flow is handled by the `InvitationController` and related services.

### Payments, Bills, and Transactions

Invoices and bills can be paid or received. When this happens, we update balances and log transactions. This ensures the financial data is always up to date and auditable.

### Soft Deletes & Data Safety

Most entities use soft deletes, so data isn't lost immediately—it's just hidden until permanently deleted. This helps prevent accidental data loss.

### Summary

This structure lets us build a secure, maintainable, and scalable finance backend. If you want to add a feature, start by thinking: What model does it affect? What business logic is needed? Should it be a new service or part of an existing one? Then wire up the route, controller, request, and service.

If you have questions, check the code in each directory or ask a teammate—no question is too basic!

## Main Directories & Their Roles

- **app/Models/**: Eloquent models for all main entities (User, Company, Invoice, Bill, etc.). Each model represents a database table and defines relationships and business logic.
- **app/Http/Controllers/**: Handles HTTP requests. Each controller manages a resource (e.g., `InvoiceController`, `CompanyController`).
	- **Api/**: Contains base and company-specific API controllers.
- **app/Http/Requests/**: Form request classes for validating incoming data (e.g., `RegisterRequest`, `StoreInvoiceRequest`).
- **app/Http/Traits/**: Reusable code for controllers (e.g., authorization logic).
- **app/Services/**: Business logic and service classes (e.g., `InvoiceService`, `AuthService`).
- **app/Mail/**: Mailable classes for sending emails (e.g., invoice, invitation emails).
- **app/Providers/**: Laravel service providers (e.g., `AppServiceProvider`).
- **database/migrations/**: All database schema migrations for tables like users, companies, invoices, bills, etc.
- **database/seeders/**: Seed classes for populating the database with test data.
- **routes/api.php**: Main API routes, grouped by version and protected by authentication where needed.
- **routes/web.php**: Web routes for serving the SPA and authenticated company listing.
- **routes/console.php**: Artisan console commands.

## API Structure & Flow

- **Authentication**: Uses Laravel Sanctum for API token authentication. Endpoints for login, register, logout, and profile update.
- **Companies**: CRUD operations for companies, user invitations, user management (roles, removal), and company deactivation.
- **Categories, Clients, Suppliers, Accounts**: Each company has its own categories, clients, suppliers, and accounts, managed via nested routes.
- **Invoices & Bills**: Full CRUD, PDF generation, payment/receiving endpoints, and status tracking. Invoice items are managed as a sub-resource.
- **Transactions**: CRUD for financial transactions, linked to companies.
- **Invitations**: Token-based invitation system for onboarding users to companies, with accept/decline endpoints.
- **Documents**: Endpoint for listing company documents.

## Example API Endpoints

- `POST /api/v1/login` — User login
- `POST /api/v1/register` — User registration
- `GET /api/v1/companies` — List companies (auth required)
- `POST /api/v1/companies/{id}/invite` — Invite user to company
- `GET /api/v1/companies/{companyId}/clients` — List clients for a company
- `POST /api/v1/companies/{companyId}/invoices` — Create invoice for a company
- `POST /api/v1/companies/{companyId}/bills/{billId}/pay` — Pay a bill

## Key Concepts

- **Multi-tenancy**: All business data is scoped to companies. Users can belong to multiple companies with different roles.
- **Role-based Access**: Roles (admin, accountant, etc.) are managed per company.
- **Soft Deletes**: Most entities use soft deletes for safe data removal.
- **Validation**: All input is validated using Form Request classes.
- **Service Layer**: Business logic is separated from controllers for maintainability.
- **Mailing**: Uses Laravel's mailables for sending invitations and invoices.

## Database

The database schema is defined in the migrations folder. Key tables include:
- users, companies, company_user (pivot), categories, clients, suppliers, accounts, invoices, invoice_items, bills, transactions, invitations, etc.

## How It Works (Flow Example)

1. **User registers** → receives token → creates a company.
2. **User invites teammates** → invitation email sent → teammate accepts and joins company.
3. **Company creates clients, suppliers, categories, accounts.**
4. **Invoices and bills** are created, sent, and tracked. Payments and receipts update balances and statuses.
5. **Transactions** are logged for all financial activity.

---
For more details, see the code in each directory or the API route definitions in `routes/api.php`.
