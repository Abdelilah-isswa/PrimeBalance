
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

This project is a modern finance management backend built with Laravel. It provides a robust API for managing companies, users, invoices, bills, transactions, and more. Below is a detailed breakdown of the backend structure and its main components:

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
