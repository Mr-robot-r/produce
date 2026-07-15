# Inventory & BOM Management System

A modular Laravel application for managing warehouse operations, Bill of Materials (BOM), and inventory vouchers.

The project follows **Clean Architecture** principles by separating application logic, business rules, and data access into independent layers, making the system scalable, maintainable, and easy to test.

---

## Features

### Bill of Materials (BOM)

- Create and manage BOMs
- Recursive BOM tree generation
- Multi-level component support
- Circular dependency detection
- Cost calculation

### Inventory Voucher

- Create warehouse vouchers
- Confirm and cancel vouchers
- Status management
- Inventory validation

### Warehouse

- Product management
- Warehouse management
- Stock validation
- Inventory transactions

---

# Architecture

```
                HTTP Request
                      │
                      ▼
               Controller Layer
                      │
                      ▼
                 Action Layer
                      │
                      ▼
                Service Layer
                      │
                      ▼
             Repository Layer
                      │
                      ▼
               Eloquent Models
                      │
                      ▼
                  Database
```

Cross-cutting components:

- DTO
- Events
- Listeners
- Enums
- Custom Exceptions

---

# Project Structure

```
app
├── Actions
├── DTO
├── Enums
├── Events
├── Exceptions
├── Http
├── Listeners
├── Models
├── Repositories
│   ├── Contracts
│   └── Eloquent
├── Services
└── Providers
```

---

# Design Decisions

Instead of placing all business logic inside Controllers or Models, this project separates responsibilities into multiple layers.

This approach improves:

- Maintainability
- Testability
- Scalability
- Readability
- Separation of Concerns

---

# Why Action Pattern?

Each Action represents **one application use case**.

Examples:

- CreateVoucherAction
- ConfirmVoucherAction
- CalculateBomCostAction

### Why?

Without Actions, Controllers become large and difficult to maintain.

Benefits:

- Single Responsibility Principle
- Reusable use cases
- Easier Unit Testing
- Thin Controllers

---

# Why Service Layer?

Some business operations require coordinating multiple repositories and applying business rules.

Instead of placing these rules inside Controllers, they are encapsulated inside Services.

Examples:

- BOM calculations
- Inventory validation
- Voucher processing

Benefits:

- Keeps business logic centralized
- Prevents duplicated code
- Easier maintenance
- Independent from HTTP layer

---

# Why Repository Pattern?

Repositories abstract database operations from business logic.

Instead of writing Eloquent queries everywhere:

```
Controller
↓

Service

↓

Repository

↓

Database
```

Benefits:

- Loose coupling
- Easier testing through mocking
- Database implementation can change with minimal impact
- Cleaner business logic

---

# Why DTO (Data Transfer Object)?

DTOs encapsulate validated data before passing it between layers.

Instead of:

```php
create(
    $request->name,
    $request->price,
    $request->warehouse,
    ...
);
```

We pass

```php
VoucherData
```

Benefits:

- Strong typing
- Cleaner method signatures
- Easier validation
- Prevents leaking Request objects into business logic

---

# Why Events & Listeners?

Some operations should happen **after** the main business action.

Example:

Voucher Confirmed

↓

- Write Logs
- Send Notifications
- Update Reports

Instead of tightly coupling these operations, Events publish an event and Listeners react to it.

Benefits

- Loose coupling
- Extensible architecture
- Easier feature additions
- Better separation of concerns

---

# Why Dependency Injection?

All dependencies are injected through constructors.

Example:

```
VoucherService

↓

VoucherRepositoryInterface
```

instead of

```
new VoucherRepository()
```

Benefits

- Easier testing
- Better flexibility
- SOLID compliance
- Inversion of Control (IoC)

---

# SOLID Principles Applied

✔ Single Responsibility Principle

✔ Open/Closed Principle

✔ Liskov Substitution Principle

✔ Interface Segregation Principle

✔ Dependency Inversion Principle

---

# Technologies

- PHP 8+
- Laravel
- MySQL
- Eloquent ORM

---

# Installation

Clone project

```bash
git clone <repository-url>
```

Install dependencies

```bash
composer install
```

Configure environment

```bash
cp .env.example .env
```

Generate application key

```bash
php artisan key:generate
```

Run migrations

```bash
php artisan migrate
```

Start server

```bash
php artisan serve
```

---

# Future Improvements

- CQRS implementation
- Domain Events
- Event Sourcing
- Redis Caching
- Queue-based notifications
- API Versioning
- PHPUnit Integration Tests

---

# License

MIT License