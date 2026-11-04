# SIMBAR Inventory Management System - Technical Design Document

## Overview

SIMBAR (Sistem Manajemen Inventaris Barang Kantor) is a comprehensive web-based inventory management system designed for office environments. The system manages office equipment and supplies, enabling efficient tracking of items, borrowing requests, returns, and inventory status through a role-based access control model.

**Core objectives:**
- Centralized management of office inventory with real-time stock tracking
- Streamlined borrowing and return workflows with approval workflows
- Comprehensive audit logging and reporting capabilities
- Secure access control with role-based restrictions for Admin and Staff users
- High performance with support for large datasets and concurrent users

**Technology Stack:**
- Backend: Laravel 12 with PHP 8.4
- Database: MySQL 8.0+
- Frontend: Blade templates with Bootstrap 5 and JavaScript (Vanilla/Alpine.js)
- Authentication: Laravel's built-in Auth with bcrypt password hashing
- Session Management: Laravel Session driver with secure cookies

---

## Architecture

### High-Level Architecture Overview

```
┌─────────────────────────────────────────────────────────────────┐
│                        Presentation Layer                        │
│  (Blade Templates, Bootstrap 5, JavaScript, Bootstrap Tables)   │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                       Controller Layer                          │
│  (Request Handling, Input Validation, Business Logic Routing)  │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                     Middleware Layer                            │
│ (Authentication, Authorization, RBAC, Audit Logging)           │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                      Service/Business Logic Layer               │
│  (Domain Logic, Validation, Workflows, State Transitions)       │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                       Model Layer (Eloquent)                    │
│  (Data Access, Relationships, Query Builders)                   │
└─────────────────────────────────────────────────────────────────┘
                              │
                              ↓
┌─────────────────────────────────────────────────────────────────┐
│                       Database Layer                            │
│  (MySQL 8.0+, Migrations, Indexes, Relationships)              │
└─────────────────────────────────────────────────────────────────┘
```

### Architectural Patterns

**MVC Pattern:**
- Models: Eloquent ORM models representing database entities
- Views: Blade templates for rendering UI
- Controllers: Handle HTTP requests and coordinate between models and views

**Repository Pattern (Optional Enhancement):**
- Service classes can act as repositories for complex queries
- Improves testability and maintains separation of concerns

**RBAC (Role-Based Access Control):**
- Two roles: Admin (full system access) and Staff (limited access)
- Middleware enforces permissions at route and action levels
- Database-driven permission checks for flexibility

**Service Layer:**
- Encapsulates business logic separate from controllers
- Handles validation, state transitions, and workflow management
- Examples: LoanRequestService, ReturnVerificationService, StockAdjustmentService

### Request-Response Flow

```
User Request
    ↓
Route Matching (routes/web.php)
    ↓
Middleware Stack (Auth, RBAC, CSRF, etc.)
    ↓
Controller Action
    ↓
Service/Business Logic
    ↓
Model Query/Mutation
    ↓
Database Operation
    ↓
Response (View/JSON)
    ↓
User Response
```

---

## Components and Interfaces

### Core Components

#### 1. Authentication & Authorization Module
- **Responsibilities:**
  - User registration and login
  - Password hashing and validation
  - Session management
  - RBAC enforcement
  - URL access prevention for unauthorized roles

- **Key Components:**
  - `AuthController`: Login/Register/Logout logic
  - `Auth Middleware`: Verify authenticated sessions
  - `RBACMiddleware`: Verify user role permissions
  - `User Model`: Represents users with roles

- **Interfaces:**
  ```
  POST   /register                    → Register new user
  POST   /login                       → User login
  POST   /logout                      → User logout
  GET    /dashboard                   → Role-specific dashboard
  ```

#### 2. Inventory Management Module
- **Responsibilities:**
  - Manage items, categories, and storage locations
  - Track item conditions
  - Handle stock in/out transactions
  - Maintain audit trails

- **Key Components:**
  - `ItemController`: CRUD operations for items
  - `CategoryController`: Category management
  - `LocationController`: Location management
  - `StockTransactionController`: Stock in/out handling
  - Item, Category, Location, StockTransaction Models

- **Interfaces:**
  ```
  Admin Routes:
  GET    /admin/items                 → List items
  POST   /admin/items                 → Create item
  GET    /admin/items/{id}/edit       → Edit item
  PUT    /admin/items/{id}            → Update item
  DELETE /admin/items/{id}            → Delete item
  GET    /admin/categories            → Manage categories
  POST   /admin/categories            → Create category
  GET    /admin/locations             → Manage locations
  POST   /admin/locations             → Create location
  GET    /admin/stock-in              → Stock in form
  POST   /admin/stock-in              → Process stock in
  ```

#### 3. Loan Management Module
- **Responsibilities:**
  - Process loan requests from staff
  - Admin approval/rejection workflow
  - Track active loans
  - Enforce stock availability

- **Key Components:**
  - `LoanRequestController`: Handle loan requests
  - `LoanApprovalController`: Admin approval process
  - `LoanRequestService`: Business logic for loans
  - Loan, LoanItem Models

- **Interfaces:**
  ```
  Staff Routes:
  POST   /staff/loans                 → Submit loan request
  GET    /staff/loans                 → View own loans
  GET    /staff/loans/{id}            → View loan details
  
  Admin Routes:
  GET    /admin/loans/pending         → View pending approvals
  POST   /admin/loans/{id}/approve    → Approve loan
  POST   /admin/loans/{id}/reject     → Reject loan
  GET    /admin/loans/all             → All loans history
  ```

#### 4. Return Management Module
- **Responsibilities:**
  - Process return requests from staff
  - Admin verification of returned items
  - Track item condition upon return
  - Update inventory based on verified returns

- **Key Components:**
  - `ReturnRequestController`: Handle return submissions
  - `ReturnVerificationController`: Admin verification
  - `ReturnVerificationService`: Return logic
  - Return, ReturnItem Models

- **Interfaces:**
  ```
  Staff Routes:
  POST   /staff/returns               → Submit return request
  GET    /staff/returns               → View own returns
  
  Admin Routes:
  GET    /admin/returns/pending       → View pending verifications
  POST   /admin/returns/{id}/verify   → Verify return
  POST   /admin/returns/{id}/reject   → Reject return
  ```

#### 5. Reporting Module
- **Responsibilities:**
  - Generate inventory reports
  - Generate loan activity reports
  - Export data (PDF/Excel)
  - Time-based filtering and analytics

- **Key Components:**
  - `InventoryReportController`: Inventory analytics
  - `LoanReportController`: Loan analytics
  - Report services for data aggregation
  - Export utilities (PDF, CSV/Excel)

- **Interfaces:**
  ```
  Admin Routes:
  GET    /admin/reports/inventory     → Inventory report
  GET    /admin/reports/loans         → Loan report
  POST   /admin/reports/export        → Export report
  ```

#### 6. Admin Management Module
- **Responsibilities:**
  - Create and manage staff accounts
  - Activate/deactivate accounts
  - Manage roles and permissions
  - View audit logs

- **Key Components:**
  - `UserManagementController`: Staff account management
  - `AuditLogController`: View audit trails
  - User Model with role attribute

- **Interfaces:**
  ```
  Admin Routes:
  GET    /admin/users                 → List staff
  POST   /admin/users                 → Create staff
  PUT    /admin/users/{id}            → Update staff
  POST   /admin/users/{id}/toggle     → Activate/deactivate
  GET    /admin/audit-logs            → View audit logs
  ```

#### 7. Searching & Filtering Module
- **Responsibilities:**
  - Item search by name/code
  - Category filtering
  - Status-based filtering
  - Real-time search responses

- **Key Components:**
  - Search filters in controllers
  - Database query optimization
  - JSON responses for real-time search

- **Interfaces:**
  ```
  GET    /staff/items/search?q=keyword     → Real-time search
  GET    /staff/items?category=1&status=available
  ```

---

## Data Models

### Entity-Relationship Diagram (ERD)

```
┌────────────────────┐
│      users         │
├────────────────────┤
│ id (PK)            │
│ name               │
│ email (UNIQUE)     │
│ password           │
│ role               │
│ is_active          │
│ department         │
│ created_at         │
│ updated_at         │
└────────────────────┘
        │
        │ 1──→ N
        │
        ├─────────────────────────┐
        │                         │
        ↓                         ↓
┌──────────────────┐    ┌──────────────────┐
│     loans        │    │    audit_logs    │
├──────────────────┤    ├──────────────────┤
│ id (PK)          │    │ id (PK)          │
│ user_id (FK)     │    │ user_id (FK)     │
│ status           │    │ action           │
│ requested_at     │    │ entity_type      │
│ approved_at      │    │ entity_id        │
│ approved_by_id   │    │ old_values       │
│ rejection_reason │    │ new_values       │
│ created_at       │    │ ip_address       │
│ updated_at       │    │ created_at       │
└──────────────────┘    └──────────────────┘
        │
        │ 1──→ N
        │
        ↓
┌──────────────────┐
│   loan_items     │
├──────────────────┤
│ id (PK)          │
│ loan_id (FK)     │
│ item_id (FK)     │
│ quantity         │
│ planned_return   │
│ returned_at      │
│ return_quantity  │
│ return_condition │
└──────────────────┘
        │
        └─────────┐
                  │
                  ↓
         ┌──────────────────┐
         │      items       │
         ├──────────────────┤
         │ id (PK)          │
         │ code (UNIQUE)    │
         │ name             │
         │ category_id (FK) │
         │ location_id (FK) │
         │ description      │
         │ total_stock      │
         │ available_stock  │
         │ condition        │
         │ created_at       │
         │ updated_at       │
         └──────────────────┘
                  │
        ┌─────────┼─────────┐
        │         │         │
        ↓         ↓         ↓
┌──────────────┐ │  ┌──────────────────┐
│ categories   │ │  │ stock_movements  │
├──────────────┤ │  ├──────────────────┤
│ id (PK)      │ │  │ id (PK)          │
│ name (UNIQUE)│ │  │ item_id (FK)     │
│ created_at   │ │  │ type (in/out)    │
│ updated_at   │ │  │ quantity         │
└──────────────┘ │  │ reference_type   │
                 │  │ reference_id     │
                 │  │ notes            │
        ┌────────┘  │ created_by_id    │
        │           │ created_at       │
        │           └──────────────────┘
        │
        ↓
┌──────────────────┐
│    locations     │
├──────────────────┤
│ id (PK)          │
│ name (UNIQUE)    │
│ description      │
│ created_at       │
│ updated_at       │
└──────────────────┘


┌──────────────────┐
│     returns      │
├──────────────────┤
│ id (PK)          │
│ loan_id (FK)     │
│ status           │
│ submitted_at     │
│ verified_at      │
│ verified_by_id   │
│ rejection_reason │
│ created_at       │
│ updated_at       │
└──────────────────┘
        │
        │ 1──→ N
        │
        ↓
┌──────────────────┐
│  return_items    │
├──────────────────┤
│ id (PK)          │
│ return_id (FK)   │
│ loan_item_id (FK)│
│ condition        │
│ quantity         │
│ notes            │
└──────────────────┘


┌──────────────────────┐
│ item_condition_logs  │
├──────────────────────┤
│ id (PK)              │
│ item_id (FK)         │
│ old_condition        │
│ new_condition        │
│ reason               │
│ changed_by_id        │
│ created_at           │
└──────────────────────┘
```

### Database Tables

#### 1. **users**
```sql
CREATE TABLE users (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('Admin', 'Staff') NOT NULL DEFAULT 'Staff',
    is_active BOOLEAN DEFAULT TRUE,
    department VARCHAR(100),
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_is_active (is_active)
);
```

#### 2. **categories**
```sql
CREATE TABLE categories (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name)
);
```

#### 3. **locations**
```sql
CREATE TABLE locations (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) UNIQUE NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name)
);
```

#### 4. **items**
```sql
CREATE TABLE items (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(200) NOT NULL,
    category_id BIGINT UNSIGNED NOT NULL,
    location_id BIGINT UNSIGNED NOT NULL,
    description TEXT,
    total_stock INT UNSIGNED DEFAULT 0,
    available_stock INT UNSIGNED DEFAULT 0,
    condition ENUM('Baik', 'Rusak Ringan', 'Rusak Berat', 'Hilang') DEFAULT 'Baik',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY fk_items_category (category_id) REFERENCES categories(id) ON DELETE RESTRICT,
    FOREIGN KEY fk_items_location (location_id) REFERENCES locations(id) ON DELETE RESTRICT,
    INDEX idx_code (code),
    INDEX idx_name (name),
    INDEX idx_category_id (category_id),
    INDEX idx_location_id (location_id),
    INDEX idx_available_stock (available_stock),
    INDEX idx_condition (condition)
);
```

#### 5. **loans**
```sql
CREATE TABLE loans (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED NOT NULL,
    status ENUM('Menunggu Persetujuan', 'Disetujui', 'Ditolak', 'Dipinjam', 'Dikembalikan') DEFAULT 'Menunggu Persetujuan',
    requested_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    approved_at TIMESTAMP NULL,
    approved_by_id BIGINT UNSIGNED NULL,
    rejection_reason TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY fk_loans_user (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY fk_loans_approver (approved_by_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_status (status),
    INDEX idx_requested_at (requested_at),
    INDEX idx_approved_by (approved_by_id)
);
```

#### 6. **loan_items**
```sql
CREATE TABLE loan_items (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    loan_id BIGINT UNSIGNED NOT NULL,
    item_id BIGINT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    planned_return_at TIMESTAMP NOT NULL,
    returned_at TIMESTAMP NULL,
    return_quantity INT UNSIGNED DEFAULT 0,
    return_condition ENUM('Baik', 'Rusak Ringan', 'Rusak Berat', 'Hilang') NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY fk_loan_items_loan (loan_id) REFERENCES loans(id) ON DELETE CASCADE,
    FOREIGN KEY fk_loan_items_item (item_id) REFERENCES items(id) ON DELETE RESTRICT,
    INDEX idx_loan_id (loan_id),
    INDEX idx_item_id (item_id),
    INDEX idx_planned_return_at (planned_return_at)
);
```

#### 7. **returns**
```sql
CREATE TABLE returns (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    loan_id BIGINT UNSIGNED NOT NULL,
    status ENUM('Menunggu Verifikasi', 'Terima', 'Tolak') DEFAULT 'Menunggu Verifikasi',
    submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    verified_at TIMESTAMP NULL,
    verified_by_id BIGINT UNSIGNED NULL,
    rejection_reason TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY fk_returns_loan (loan_id) REFERENCES loans(id) ON DELETE CASCADE,
    FOREIGN KEY fk_returns_verifier (verified_by_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_loan_id (loan_id),
    INDEX idx_status (status),
    INDEX idx_submitted_at (submitted_at)
);
```

#### 8. **return_items**
```sql
CREATE TABLE return_items (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    return_id BIGINT UNSIGNED NOT NULL,
    loan_item_id BIGINT UNSIGNED NOT NULL,
    quantity INT UNSIGNED NOT NULL,
    condition ENUM('Baik', 'Rusak Ringan', 'Rusak Berat', 'Hilang') NOT NULL DEFAULT 'Baik',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY fk_return_items_return (return_id) REFERENCES returns(id) ON DELETE CASCADE,
    FOREIGN KEY fk_return_items_loan_item (loan_item_id) REFERENCES loan_items(id) ON DELETE CASCADE,
    INDEX idx_return_id (return_id),
    INDEX idx_loan_item_id (loan_item_id)
);
```

#### 9. **stock_movements**
```sql
CREATE TABLE stock_movements (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    item_id BIGINT UNSIGNED NOT NULL,
    type ENUM('In', 'Out', 'Adjustment') NOT NULL,
    quantity INT NOT NULL,
    reference_type VARCHAR(50),
    reference_id BIGINT UNSIGNED,
    notes TEXT,
    created_by_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY fk_stock_movements_item (item_id) REFERENCES items(id) ON DELETE RESTRICT,
    FOREIGN KEY fk_stock_movements_user (created_by_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_item_id (item_id),
    INDEX idx_type (type),
    INDEX idx_created_at (created_at),
    INDEX idx_reference (reference_type, reference_id)
);
```

#### 10. **item_condition_logs**
```sql
CREATE TABLE item_condition_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    item_id BIGINT UNSIGNED NOT NULL,
    old_condition ENUM('Baik', 'Rusak Ringan', 'Rusak Berat', 'Hilang'),
    new_condition ENUM('Baik', 'Rusak Ringan', 'Rusak Berat', 'Hilang') NOT NULL,
    reason VARCHAR(255),
    changed_by_id BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY fk_condition_logs_item (item_id) REFERENCES items(id) ON DELETE CASCADE,
    FOREIGN KEY fk_condition_logs_user (changed_by_id) REFERENCES users(id) ON DELETE RESTRICT,
    INDEX idx_item_id (item_id),
    INDEX idx_created_at (created_at)
);
```

#### 11. **audit_logs**
```sql
CREATE TABLE audit_logs (
    id BIGINT UNSIGNED PRIMARY KEY AUTO_INCREMENT,
    user_id BIGINT UNSIGNED,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50) NOT NULL,
    entity_id BIGINT UNSIGNED,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY fk_audit_logs_user (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user_id (user_id),
    INDEX idx_action (action),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_created_at (created_at)
);
```

---

## Models and Controllers

### Eloquent Models

#### 1. **User Model** (`app/Models/User.php`)
```php
Relationships:
- hasMany('loans')         → User's loan requests
- hasMany('loanApprovals') → Loans approved by admin
- hasMany('returnVerifications') → Returns verified by admin
- hasMany('auditLogs')     → User's audit log entries
- hasMany('stockMovements')→ Stock movements created by user

Attributes:
- id, name, email, password, role, is_active, department, last_login_at, created_at, updated_at

Methods:
- isAdmin(): boolean
- isStaff(): boolean
- isActive(): boolean
- deactivate(): void
- activate(): void
```

#### 2. **Item Model** (`app/Models/Item.php`)
```php
Relationships:
- belongsTo('category')     → Item's category
- belongsTo('location')     → Item's storage location
- hasMany('loanItems')      → Active loans for this item
- hasMany('stockMovements') → Stock transaction history
- hasMany('conditionLogs')  → Condition change history

Attributes:
- id, code, name, category_id, location_id, description, total_stock, available_stock, condition

Methods:
- isAvailable(): boolean
- canBorrow(quantity): boolean
- updateStockIn(quantity, notes): void
- updateStockOut(quantity, notes): void
- updateCondition(newCondition, reason): void
- getConditionLabel(): string
```

#### 3. **Category Model** (`app/Models/Category.php`)
```php
Relationships:
- hasMany('items') → All items in this category

Attributes:
- id, name, description, created_at, updated_at

Methods:
- (Standard CRUD, no complex business logic)
```

#### 4. **Location Model** (`app/Models/Location.php`)
```php
Relationships:
- hasMany('items') → All items at this location

Attributes:
- id, name, description, created_at, updated_at

Methods:
- itemCount(): integer
```

#### 5. **Loan Model** (`app/Models/Loan.php`)
```php
Relationships:
- belongsTo('user')         → Staff who requested loan
- belongsTo('approver', 'approved_by_id', User) → Admin who approved
- hasMany('loanItems')      → Items in this loan
- hasOne('return')          → Return for this loan

Attributes:
- id, user_id, status, requested_at, approved_at, approved_by_id, rejection_reason

Methods:
- isPending(): boolean
- isApproved(): boolean
- isRejected(): boolean
- isBorrowed(): boolean
- isReturned(): boolean
- approve(userId): void
- reject(reason, userId): void
- markAsBorrowed(): void
- getTotalItemsCount(): integer
- isOverdue(): boolean
```

#### 6. **LoanItem Model** (`app/Models/LoanItem.php`)
```php
Relationships:
- belongsTo('loan')  → Parent loan
- belongsTo('item')  → The loaned item
- hasOne('returnItem') → Corresponding return item

Attributes:
- id, loan_id, item_id, quantity, planned_return_at, returned_at, return_quantity, return_condition

Methods:
- isOverdue(): boolean
- isReturned(): boolean
```

#### 7. **Return Model** (`app/Models/Return.php`)
```php
Relationships:
- belongsTo('loan')         → The loan being returned
- hasMany('returnItems')    → Items being returned
- belongsTo('verifier', 'verified_by_id', User) → Admin who verified

Attributes:
- id, loan_id, status, submitted_at, verified_at, verified_by_id, rejection_reason

Methods:
- isPending(): boolean
- isAccepted(): boolean
- isRejected(): boolean
- verify(userId): void
- reject(reason, userId): void
```

#### 8. **ReturnItem Model** (`app/Models/ReturnItem.php`)
```php
Relationships:
- belongsTo('return')    → Parent return
- belongsTo('loanItem')  → Original loan item

Attributes:
- id, return_id, loan_item_id, quantity, condition, notes

Methods:
- (Data holder, minimal business logic)
```

#### 9. **StockMovement Model** (`app/Models/StockMovement.php`)
```php
Relationships:
- belongsTo('item')  → Item being moved
- belongsTo('user')  → User who created movement

Attributes:
- id, item_id, type (In/Out/Adjustment), quantity, reference_type, reference_id, notes, created_by_id

Methods:
- (Event log, minimal business logic)
```

#### 10. **ItemConditionLog Model** (`app/Models/ItemConditionLog.php`)
```php
Relationships:
- belongsTo('item') → Item whose condition changed
- belongsTo('user') → User who made change

Attributes:
- id, item_id, old_condition, new_condition, reason, changed_by_id

Methods:
- (Event log, minimal business logic)
```

#### 11. **AuditLog Model** (`app/Models/AuditLog.php`)
```php
Relationships:
- belongsTo('user') → User who performed action

Attributes:
- id, user_id, action, entity_type, entity_id, old_values (JSON), new_values (JSON), ip_address, user_agent

Methods:
- getOldValuesArray(): array
- getNewValuesArray(): array
```

### Controllers

#### **AuthController** (`app/Http/Controllers/AuthController.php`)
```php
Methods:
- showLoginForm()              → GET /login
- login(Request)              → POST /login
- showRegisterForm()           → GET /register
- register(Request)            → POST /register
- logout()                     → POST /logout
- showForgotPasswordForm()      → GET /forgot-password (optional)
- sendPasswordResetLink()       → POST /forgot-password (optional)
```

#### **DashboardController** (`app/Http/Controllers/DashboardController.php`)
```php
Methods:
- adminDashboard()             → GET /admin/dashboard
- staffDashboard()             → GET /staff/dashboard
- getStatsData()               → Dashboard statistics (cached)
```

#### **ItemController** (`app/Http/Controllers/Admin/ItemController.php`)
```php
Methods:
- index()                      → GET /admin/items
- create()                     → GET /admin/items/create
- store(Request)               → POST /admin/items
- show(Item)                   → GET /admin/items/{id}
- edit(Item)                   → GET /admin/items/{id}/edit
- update(Request, Item)        → PUT /admin/items/{id}
- destroy(Item)                → DELETE /admin/items/{id}
- updateCondition(Request, Item) → PUT /admin/items/{id}/condition
```

#### **CategoryController** (`app/Http/Controllers/Admin/CategoryController.php`)
```php
Methods:
- index()                      → GET /admin/categories
- store(Request)               → POST /admin/categories
- update(Request, Category)    → PUT /admin/categories/{id}
- destroy(Category)            → DELETE /admin/categories/{id}
```

#### **LocationController** (`app/Http/Controllers/Admin/LocationController.php`)
```php
Methods:
- index()                      → GET /admin/locations
- store(Request)               → POST /admin/locations
- update(Request, Location)    → PUT /admin/locations/{id}
- destroy(Location)            → DELETE /admin/locations/{id}
- show(Location)               → GET /admin/locations/{id}
```

#### **StockMovementController** (`app/Http/Controllers/Admin/StockMovementController.php`)
```php
Methods:
- showStockInForm()            → GET /admin/stock-in
- recordStockIn(Request)       → POST /admin/stock-in
- index()                      → GET /admin/stock-movements (history)
```

#### **LoanRequestController** (`app/Http/Controllers/Staff/LoanRequestController.php`)
```php
Methods:
- index()                      → GET /staff/loans
- create()                     → GET /staff/loans/create
- store(Request)               → POST /staff/loans
- show(Loan)                   → GET /staff/loans/{id}
```

#### **LoanApprovalController** (`app/Http/Controllers/Admin/LoanApprovalController.php`)
```php
Methods:
- index()                      → GET /admin/loans/pending
- approve(Request, Loan)       → POST /admin/loans/{id}/approve
- reject(Request, Loan)        → POST /admin/loans/{id}/reject
- allLoans()                   → GET /admin/loans/all
```

#### **ReturnRequestController** (`app/Http/Controllers/Staff/ReturnRequestController.php`)
```php
Methods:
- create(LoanItem)             → GET /staff/returns/create/{loanId}
- store(Request)               → POST /staff/returns
- index()                      → GET /staff/returns
```

#### **ReturnVerificationController** (`app/Http/Controllers/Admin/ReturnVerificationController.php`)
```php
Methods:
- index()                      → GET /admin/returns/pending
- verify(Request, Return)      → POST /admin/returns/{id}/verify
- reject(Request, Return)      → POST /admin/returns/{id}/reject
```

#### **ItemSearchController** (`app/Http/Controllers/Staff/ItemSearchController.php`)
```php
Methods:
- index()                      → GET /staff/items (list with filter)
- search(Request)              → GET /staff/items/search (AJAX)
- show(Item)                   → GET /staff/items/{id}
```

#### **InventoryReportController** (`app/Http/Controllers/Admin/InventoryReportController.php`)
```php
Methods:
- index()                      → GET /admin/reports/inventory
- generateReport(Request)      → POST /admin/reports/inventory/generate
- exportPDF(Request)           → GET /admin/reports/inventory/export/pdf
- exportExcel(Request)         → GET /admin/reports/inventory/export/excel
```

#### **LoanReportController** (`app/Http/Controllers/Admin/LoanReportController.php`)
```php
Methods:
- index()                      → GET /admin/reports/loans
- generateReport(Request)      → POST /admin/reports/loans/generate
- exportPDF(Request)           → GET /admin/reports/loans/export/pdf
- exportExcel(Request)         → GET /admin/reports/loans/export/excel
```

#### **UserManagementController** (`app/Http/Controllers/Admin/UserManagementController.php`)
```php
Methods:
- index()                      → GET /admin/users
- create()                     → GET /admin/users/create
- store(Request)               → POST /admin/users
- edit(User)                   → GET /admin/users/{id}/edit
- update(Request, User)        → PUT /admin/users/{id}
- toggleActive(User)           → POST /admin/users/{id}/toggle
```

#### **AuditLogController** (`app/Http/Controllers/Admin/AuditLogController.php`)
```php
Methods:
- index()                      → GET /admin/audit-logs
- search(Request)              → GET /admin/audit-logs/search (filtered)
```

---

## Authentication & Authorization

### RBAC Implementation Strategy

**Two-Tier Access Control:**

1. **Route-Level Access (Middleware)**
   - `auth`: Verify user is authenticated
   - `admin`: Verify user role is Admin
   - `staff`: Verify user role is Staff

2. **Action-Level Authorization (Policy)**
   - Fine-grained permissions for specific operations
   - Example: Only admin can approve loans

**Middleware Stack:**
```php
// Middleware chain for admin routes
Route::middleware(['auth', 'admin'])->group(function () {
    // Admin routes
});

// Middleware chain for staff routes
Route::middleware(['auth', 'staff'])->group(function () {
    // Staff routes
});

// Public routes
Route::middleware(['auth'])->group(function () {
    // Authenticated routes
});
```

### Session Management

**Session Configuration:**
```php
- Driver: database or file (Laravel default)
- Lifetime: 480 minutes (8 hours)
- Timeout: Automatic logout after 30 minutes inactivity
- Secure Cookies: HttpOnly=true, Secure=true, SameSite=Lax
```

**Login Process:**
1. User submits credentials
2. Laravel validates credentials
3. Password verified against bcrypt hash
4. Session created with encrypted token
5. Session stored in database/files
6. User redirected to dashboard based on role

**Logout Process:**
1. User clicks logout
2. Session destroyed
3. All tokens invalidated
4. Redirect to login page

### CSRF Protection

**Implementation:**
- All POST/PUT/DELETE requests require CSRF token
- Token stored in session and form fields
- Verified by middleware before processing
- Different token per session for security

---

## API/Routes Structure

### Route Organization

**File Structure:**
```
routes/
├── web.php                 # Main web routes
├── api.php                 # API routes (optional)
└── breadcrumbs.php         # Breadcrumb definitions
```

### Web Routes Structure

#### Authentication Routes
```php
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
```

#### Admin Routes
```php
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'adminDashboard'])->name('dashboard');
    
    // Item Management
    Route::resource('items', ItemController::class);
    Route::put('/items/{item}/condition', [ItemController::class, 'updateCondition'])->name('items.updateCondition');
    
    // Category Management
    Route::resource('categories', CategoryController::class)->only(['index', 'store', 'update', 'destroy']);
    
    // Location Management
    Route::resource('locations', LocationController::class);
    
    // Stock Management
    Route::get('/stock-in', [StockMovementController::class, 'showStockInForm'])->name('stock.in.form');
    Route::post('/stock-in', [StockMovementController::class, 'recordStockIn'])->name('stock.in.store');
    Route::get('/stock-movements', [StockMovementController::class, 'index'])->name('stock.index');
    
    // Loan Management
    Route::get('/loans/pending', [LoanApprovalController::class, 'index'])->name('loans.pending');
    Route::post('/loans/{loan}/approve', [LoanApprovalController::class, 'approve'])->name('loans.approve');
    Route::post('/loans/{loan}/reject', [LoanApprovalController::class, 'reject'])->name('loans.reject');
    Route::get('/loans/all', [LoanApprovalController::class, 'allLoans'])->name('loans.all');
    
    // Return Management
    Route::get('/returns/pending', [ReturnVerificationController::class, 'index'])->name('returns.pending');
    Route::post('/returns/{return}/verify', [ReturnVerificationController::class, 'verify'])->name('returns.verify');
    Route::post('/returns/{return}/reject', [ReturnVerificationController::class, 'reject'])->name('returns.reject');
    
    // User Management
    Route::resource('users', UserManagementController::class);
    Route::post('/users/{user}/toggle', [UserManagementController::class, 'toggleActive'])->name('users.toggle');
    
    // Reports
    Route::get('/reports/inventory', [InventoryReportController::class, 'index'])->name('reports.inventory');
    Route::post('/reports/inventory/generate', [InventoryReportController::class, 'generateReport'])->name('reports.inventory.generate');
    Route::get('/reports/inventory/export/pdf', [InventoryReportController::class, 'exportPDF'])->name('reports.inventory.pdf');
    Route::get('/reports/inventory/export/excel', [InventoryReportController::class, 'exportExcel'])->name('reports.inventory.excel');
    
    Route::get('/reports/loans', [LoanReportController::class, 'index'])->name('reports.loans');
    Route::post('/reports/loans/generate', [LoanReportController::class, 'generateReport'])->name('reports.loans.generate');
    Route::get('/reports/loans/export/pdf', [LoanReportController::class, 'exportPDF'])->name('reports.loans.pdf');
    Route::get('/reports/loans/export/excel', [LoanReportController::class, 'exportExcel'])->name('reports.loans.excel');
    
    // Audit Logs
    Route::get('/audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');
    Route::get('/audit-logs/search', [AuditLogController::class, 'search'])->name('audit-logs.search');
});
```

#### Staff Routes
```php
Route::middleware(['auth', 'staff'])->prefix('staff')->name('staff.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'staffDashboard'])->name('dashboard');
    
    // Item Browsing
    Route::get('/items', [ItemSearchController::class, 'index'])->name('items.index');
    Route::get('/items/search', [ItemSearchController::class, 'search'])->name('items.search');
    Route::get('/items/{item}', [ItemSearchController::class, 'show'])->name('items.show');
    
    // Loan Management
    Route::get('/loans', [LoanRequestController::class, 'index'])->name('loans.index');
    Route::get('/loans/create', [LoanRequestController::class, 'create'])->name('loans.create');
    Route::post('/loans', [LoanRequestController::class, 'store'])->name('loans.store');
    Route::get('/loans/{loan}', [LoanRequestController::class, 'show'])->name('loans.show');
    
    // Return Management
    Route::get('/returns/create/{loan}', [ReturnRequestController::class, 'create'])->name('returns.create');
    Route::post('/returns', [ReturnRequestController::class, 'store'])->name('returns.store');
    Route::get('/returns', [ReturnRequestController::class, 'index'])->name('returns.index');
});
```

#### Authenticated Routes (Both Admin & Staff)
```php
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('home');
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
});
```

---

## Security Implementation

### Password Security

**Hashing Strategy:**
```php
// Laravel's bcrypt with 10 rounds
Hash::make($password)                    // Encrypt password
Hash::check($inputPassword, $hash)       // Verify password

// Requirements in validation:
- Minimum 8 characters
- At least one uppercase letter
- At least one lowercase letter
- At least one number
- At least one special character (!@#$%^&*)
```

**Password Validation Rule:**
```php
'password' => 'required|min:8|regex:/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[!@#$%^&*])[A-Za-z\d!@#$%^&*]{8,}$/'
```

**Account Lockout:**
- Lock after 5 failed login attempts in 15 minutes
- Lock duration: 30 minutes
- Stored in cache for performance

### HTTPS & Secure Cookies

**Configuration:**
```php
// config/session.php
'secure' => env('SESSION_SECURE_COOKIES', true),      // HTTPS only
'http_only' => true,                                    // No JavaScript access
'same_site' => 'Lax',                                   // CSRF protection

// Force HTTPS redirect (app/Http/Middleware/RedirectHttps.php)
if (!$request->secure() && env('APP_ENV') === 'production') {
    return redirect()->secure($request->getRequestUri());
}
```

### CSRF Protection

**Implementation:**
- All forms include `@csrf` token
- Token stored in session
- Verified on POST/PUT/DELETE requests
- Different token per user session

**Middleware:** `\App\Http\Middleware\VerifyCsrfToken`

### Input Validation

**Validation Strategy:**
1. Client-side: HTML5 validation for UX
2. Server-side: Laravel validation rules
3. Database constraints: NOT NULL, UNIQUE, CHECK

**Validation Examples:**
```php
// Item creation
$validated = $request->validate([
    'code' => 'required|unique:items|regex:/^[A-Z0-9]{3,}$/',
    'name' => 'required|string|max:200',
    'category_id' => 'required|exists:categories,id',
    'location_id' => 'required|exists:locations,id',
    'total_stock' => 'required|integer|min:0',
    'description' => 'nullable|string|max:1000'
]);

// Loan request
$validated = $request->validate([
    'items' => 'required|array|min:1',
    'items.*.item_id' => 'required|exists:items,id',
    'items.*.quantity' => 'required|integer|min:1',
    'items.*.planned_return_at' => 'required|date|after:today'
]);
```

### SQL Injection Prevention

**Eloquent ORM Usage:**
- Always use parameterized queries
- Never use raw SQL unless necessary
- Use query builder for complex queries
- Validate/sanitize input before queries

```php
// Safe (Eloquent parameterization)
$items = Item::where('name', 'LIKE', "%{$searchTerm}%")->get();

// Unsafe (raw concatenation)
$items = DB::select("SELECT * FROM items WHERE name LIKE '%{$searchTerm}%'");
```

### XSS Prevention

**Implementation:**
- Blade auto-escaping: `{{ $variable }}` escapes HTML
- Raw output only when necessary: `{!! $html !!}`
- Content Security Policy headers
- Validate file uploads

### Authorization Checks

**Policy-Based Authorization:**
```php
// Example: Only admin can approve loans
public function approve(User $user, Loan $loan)
{
    return $user->role === 'Admin';
}

// In controller
$this->authorize('approve', $loan);
$loan->approve($user->id);
```

---

## Data Flow

### Loan Request Workflow

```
Staff initiates loan request
    ↓
1. Staff selects items and quantity (checked against available_stock)
2. Staff enters planned return date (validated: > today)
3. System creates Loan with status "Menunggu Persetujuan"
4. System creates LoanItem records
5. Reduces available_stock (locks quantity)
6. Stores in stock_movements (reference_type='Loan')
    ↓
Admin reviews pending loans
    ↓
Admin approves:
1. Verifies item availability
2. Confirms quantity sufficient
3. Updates Loan status to "Disetujui"
4. Records approval_at, approved_by_id
5. Updates Loan status to "Dipinjam"
6. Sends notification to Staff
    ↓
OR Admin rejects:
1. Updates Loan status to "Ditolak"
2. Records rejection_reason
3. Restores available_stock (unlock quantity)
4. Sends notification to Staff with reason
```

### Return Verification Workflow

```
Staff returns items
    ↓
1. Staff selects active loan from history
2. Staff indicates return quantity and condition
3. System creates Return with status "Menunggu Verifikasi"
4. System creates ReturnItem records
5. Sends notification to Admin
    ↓
Admin verifies returned items
    ↓
Admin accepts:
1. Confirms item condition from receipt
2. Updates Return status to "Terima"
3. Updates LoanItem with returned_at, return_quantity, return_condition
4. If all LoanItems returned: Updates Loan status to "Dikembalikan"
5. Adds stock to available_stock (unlock quantity)
6. Records in stock_movements (reference_type='Return')
7. If condition damaged: Updates Item.condition, creates ItemConditionLog
8. Sends notification to Staff
    ↓
OR Admin rejects:
1. Updates Return status to "Tolak"
2. Records rejection_reason
3. Sends notification to Staff (item not received properly)
4. LoanItem remains in "Dipinjam" state
```

### Stock Management Workflow

```
Stock In (Admin records incoming items)
    ↓
1. Admin selects Item
2. Admin enters quantity, source, date
3. System adds to total_stock and available_stock
4. Creates StockMovement record (type='In')
5. Creates AuditLog entry
6. Updates Item.updated_at

Stock Out (via approved loans)
    ↓
1. Loan approved by Admin
2. System reduces available_stock
3. Creates StockMovement record (type='Out', reference_type='Loan')

Partial Returns (staff returns less than borrowed)
    ↓
1. Admin verifies partial return
2. System adds partial quantity to available_stock
3. Remaining quantity stays unavailable until returned
4. Creates StockMovement record
```

### Inventory Audit Trail

```
Every modification tracked:
    ↓
- Item created/updated: AuditLog + ItemHistory
- Stock changed: StockMovement + AuditLog
- Condition changed: ItemConditionLog + AuditLog
- Loan approved/rejected: AuditLog
- Return verified: AuditLog
- User account changed: AuditLog
    ↓
Retrieval for reporting:
1. Filter by date range
2. Filter by user
3. Filter by entity type
4. View old and new values
```

---

## Error Handling & Validation

### Input Validation Strategy

**Layer 1: Client-Side Validation**
- HTML5 form validation
- JavaScript validation with user-friendly messages
- Real-time feedback on form inputs
- Reduces server load

**Layer 2: Server-Side Validation**
- Laravel Form Request validation
- Custom validation rules
- Business logic validation (stock availability, dates, etc.)
- Returns validation errors in standardized format

**Layer 3: Database Constraints**
- Primary keys, foreign keys
- Unique constraints
- NOT NULL constraints
- CHECK constraints for enums

### Validation Request Classes

**Example Structure:**
```php
// app/Http/Requests/StoreLoanRequest.php
class StoreLoanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->user()->isStaff();
    }

    public function rules(): array
    {
        return [
            'items' => 'required|array|min:1|max:20',
            'items.*.item_id' => [
                'required',
                'exists:items,id',
                new ItemAvailableRule(),
            ],
            'items.*.quantity' => 'required|integer|min:1|max:1000',
            'planned_return_at' => 'required|date|after:now',
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Pilih minimal 1 barang untuk dipinjam',
            'planned_return_at.after' => 'Tanggal pengembalian harus di masa depan',
        ];
    }
}
```

### Error Responses

**Standard Error Response Format:**
```php
// Validation error
Response::json([
    'success' => false,
    'message' => 'Validasi gagal',
    'errors' => [
        'email' => ['Email sudah terdaftar'],
        'password' => ['Password terlalu pendek']
    ]
], 422)

// Authorization error
Response::json([
    'success' => false,
    'message' => 'Anda tidak berhak mengakses resource ini',
], 403)

// Not found error
Response::json([
    'success' => false,
    'message' => 'Resource tidak ditemukan',
], 404)

// Server error
Response::json([
    'success' => false,
    'message' => 'Terjadi kesalahan pada server',
], 500)
```

### Custom Validation Rules

**Example: Item Availability Validation**
```php
class ItemAvailableRule implements Rule
{
    public function passes($attribute, $value): bool
    {
        $item = Item::find($value);
        
        if (!$item) {
            return false;
        }
        
        return $item->condition !== 'Hilang' && $item->condition !== 'Rusak Berat';
    }

    public function message(): string
    {
        return 'Barang tidak tersedia untuk dipinjam';
    }
}
```

### Exception Handling

**Exception Types:**
```php
// Authorization exception
throw new AuthorizationException('Action not allowed');

// Validation exception (auto-thrown by Form Request)
throw new ValidationException($validator);

// Model not found
throw new ModelNotFoundException('Item not found');

// Business logic exception
class InsufficientStockException extends Exception {}
class LoanAlreadyReturnedException extends Exception {}
```

**Global Exception Handler:**
```php
// app/Exceptions/Handler.php
public function render($request, Throwable $exception)
{
    if ($exception instanceof ValidationException) {
        return response()->json([
            'success' => false,
            'errors' => $exception->errors()
        ], 422);
    }
    
    if ($exception instanceof AuthorizationException) {
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized'
        ], 403);
    }
    
    // Default error response
    return response()->json([
        'success' => false,
        'message' => 'Internal Server Error'
    ], 500);
}
```

### User-Friendly Error Messages

**Validation Messages in Indonesian:**
```php
'required' => 'Kolom :attribute harus diisi',
'unique' => ':attribute sudah terdaftar dalam sistem',
'email' => 'Format :attribute tidak valid',
'min' => ':attribute minimal harus :min karakter',
'date_format' => 'Format tanggal :attribute tidak valid',
'exists' => ':attribute yang dipilih tidak valid',
```

---

## Performance Considerations

### Database Indexing Strategy

**Critical Indexes:**
```sql
-- Users table
INDEX idx_email (email)
INDEX idx_role (role)
INDEX idx_is_active (is_active)

-- Items table
INDEX idx_code (code)
INDEX idx_name (name)
INDEX idx_category_id (category_id)
INDEX idx_location_id (location_id)
INDEX idx_available_stock (available_stock)
INDEX idx_condition (condition)

-- Loans table
INDEX idx_user_id (user_id)
INDEX idx_status (status)
INDEX idx_requested_at (requested_at)
INDEX idx_approved_by (approved_by_id)

-- LoanItems table
INDEX idx_loan_id (loan_id)
INDEX idx_item_id (item_id)
INDEX idx_planned_return_at (planned_return_at)

-- Returns table
INDEX idx_loan_id (loan_id)
INDEX idx_status (status)
INDEX idx_submitted_at (submitted_at)

-- StockMovements table
INDEX idx_item_id (item_id)
INDEX idx_type (type)
INDEX idx_created_at (created_at)
INDEX idx_reference (reference_type, reference_id)

-- AuditLogs table
INDEX idx_user_id (user_id)
INDEX idx_action (action)
INDEX idx_entity (entity_type, entity_id)
INDEX idx_created_at (created_at)
```

### Pagination Strategy

**Implementation:**
```php
// Default: 20 items per page
$items = Item::paginate(20);

// For large datasets: 50 items per page
$loans = Loan::with('user', 'items')->paginate(50);

// Manual pagination control
$perPage = request('per_page', 20);
$items = Item::paginate($perPage);

// In Blade
{{ $items->links('pagination::bootstrap-4') }}
```

**Performance Impact:**
- Reduces memory usage
- Improves initial page load
- Provides better UX with navigation

### Lazy Loading Prevention

**Eager Loading Pattern:**
```php
// Prevent N+1 queries
$loans = Loan::with('user', 'items.item', 'approver')->get();

// Specific relations
$items = Item::with('category', 'location')->get();

// Nested relations
$loans = Loan::with(['user' => function($q) {
    $q->select('id', 'name', 'email');
}, 'items.item' => function($q) {
    $q->select('id', 'name', 'code');
}])->get();
```

### Query Optimization

**Techniques:**
```php
// Select only needed columns
$items = Item::select('id', 'name', 'code', 'available_stock')->get();

// Chunk large datasets
Item::chunk(1000, function ($items) {
    foreach ($items as $item) {
        // Process item
    }
});

// Use exists() instead of count()
if (Loan::where('user_id', $userId)->exists()) {
    // User has loans
}

// Optimize reports with aggregation
$report = Item::selectRaw('category_id, SUM(total_stock) as total, SUM(available_stock) as available')
    ->groupBy('category_id')
    ->get();
```

### Caching Strategy

**Cache Types:**
```php
// Query caching (5 minutes)
$categories = Cache::remember('categories', 300, function() {
    return Category::all();
});

// Dashboard statistics (cache for 1 hour)
$stats = Cache::remember('dashboard_stats', 3600, function() {
    return [
        'total_items' => Item::count(),
        'available_stock' => Item::sum('available_stock'),
        'active_loans' => Loan::where('status', 'Dipinjam')->count(),
    ];
});

// Cache invalidation on update
Cache::forget('categories');
Cache::forget('dashboard_stats');

// Use Cache::flush() for full clear (use carefully)
```

**Cache Drivers:**
- File-based: Development, small deployments
- Redis: Production, high-traffic systems
- Database: When Redis unavailable

### Response Time Targets

**Performance SLA:**
- Dashboard load: < 2 seconds
- Item search/filter (1000+ items): < 3 seconds
- Report generation: < 5 seconds
- API response (normal operations): < 1 second

### Database Connection Pooling

**Configuration:**
```php
// config/database.php
'mysql' => [
    'driver' => 'mysql',
    'url' => env('DATABASE_URL'),
    'host' => env('DB_HOST'),
    'port' => env('DB_PORT'),
    'pool' => [
        'min' => 2,
        'max' => 10,
    ],
],
```

### Monitoring & Profiling

**Tools:**
- Laravel Debugbar (development only)
- New Relic or Datadog (production)
- MySQL Slow Query Log
- Log analysis for performance bottlenecks

---

## Correctness Properties

### Data Consistency Invariants

The following correctness properties must be maintained throughout all system operations:

#### Property 1: Stock Invariant

*For any* item in the system, the relationship between stock quantities MUST satisfy:
- `available_stock ≤ total_stock`
- `available_stock = total_stock - sum(quantity from active loans)`
- Stock never becomes negative in database

**Validates: Requirements 3.1, 7.1, 8.1, 8.2**

**Enforcement:**
- Transactions ensure atomicity
- Stock adjustments verified before execution
- Active loans deducted from available calculation

#### Property 2: Loan Lifecycle Integrity

*For any* loan request in the system, the status transitions MUST follow a valid state machine:
- Initial state: "Menunggu Persetujuan"
- Valid transitions:
  - "Menunggu Persetujuan" → "Disetujui" (admin approval)
  - "Menunggu Persetujuan" → "Ditolak" (admin rejection)
  - "Disetujui" → "Dipinjam" (admin marks as borrowed)
  - "Dipinjam" → "Dikembalikan" (all items returned and verified)
- Invalid transitions are rejected

**Validates: Requirements 9.1, 9.4**

#### Property 3: Return Consistency

*For any* return request, the returned quantity MUST NOT exceed the original borrowed quantity:
- `sum(return_quantity) ≤ loan_item.quantity`
- Partial returns are allowed and tracked
- Return condition accurately reflects item state upon reception

**Validates: Requirements 10.3, 11.3**

#### Property 4: Loan-Item Relationship Integrity

*For any* loan, the associated loan_items MUST:
- Contain at least one item
- Have quantity > 0 for each item
- Reference items that exist and are borrowable
- All items belong to the same loan

**Validates: Requirements 8.1**

#### Property 5: Approval Authorization

*For any* loan approval or return verification:
- ONLY users with Admin role can approve/verify
- Approved-by user MUST be different from requesting staff
- Approval timestamp MUST be after request timestamp
- Approval creates audit log entry

**Validates: Requirements 2.1, 9.1, 11.1, 27.1**

#### Property 6: Audit Trail Completeness

*For any* critical action (loan approval, stock adjustment, return verification):
- An audit_log entry MUST be created
- Audit log MUST contain: timestamp, user, action, old values, new values
- Audit logs MUST NOT be deleted (only archived)
- Audit logs MUST be queryable for compliance review

**Validates: Requirements 3.5, 19.1, 19.2**

#### Property 7: User Role Enforcement

*For any* authenticated user:
- User MUST have exactly one role (Admin OR Staff)
- Role MUST be enforced at route level (RBAC middleware)
- Staff users MUST NOT access /admin/* URLs
- Admin users MUST access admin dashboard only

**Validates: Requirements 2.1, 2.2, 2.3, 27.1, 28.1**

#### Property 8: Item Condition Tracking

*For any* item condition change:
- Condition change MUST be recorded in item_condition_logs
- Condition change MUST include: old value, new value, reason, changed_by user
- Item.condition field MUST match latest condition_log entry
- Rusak Berat/Hilang items MUST NOT appear in available borrowing lists

**Validates: Requirements 12.2, 12.4**

#### Property 9: Password Security

*For any* user password:
- Password MUST be hashed using bcrypt with minimum 10 rounds
- Plain-text password MUST NEVER be stored in database
- Password MUST NOT appear in logs or error messages
- Password verification MUST use bcrypt comparison (not equality)

**Validates: Requirements 17.1, 17.3, 17.4**

#### Property 10: Session Token Uniqueness

*For any* authenticated session:
- Session token MUST be cryptographically random
- Session token MUST be unique across all active sessions
- Session token MUST NOT be predictable or derivable from user data
- Session MUST expire after 30 minutes of inactivity

**Validates: Requirements 29.1, 29.2, 29.4**

#### Property 11: CSRF Token Validation

*For any* state-changing request (POST/PUT/DELETE):
- Request MUST include valid CSRF token
- CSRF token MUST match session token
- CSRF token MUST be different per session
- Invalid CSRF token MUST result in 419 response

**Validates: Requirements 20.1**

#### Property 12: Email Uniqueness

*For any* user in the system:
- Email MUST be unique in users table
- Duplicate email registration MUST be rejected
- Email validation MUST follow RFC 5322 standard

**Validates: Requirements 1.2**

#### Property 13: Stock Movement Auditability

*For any* stock transaction:
- Stock movement MUST reference source transaction (Loan, Return, or StockIn)
- Stock movement MUST be immutable after creation
- Stock movement quantity MUST match actual stock change
- Stock movement MUST have created_by user for accountability

**Validates: Requirements 7.3**

---

## Testing Strategy

### Testing Pyramid

```
          /\
         /  \    Feature Tests (End-to-End)
        /    \   - User workflows
       /      \  - Integration points
      /--------\
     /          \   Integration Tests
    /   Auth     \  - Database interactions
   /   RBAC      \ - Service layer
  /   Workflows  \
 /-----------+----\
/             \     Unit Tests
/   Models     \    - Model relationships
/   Services   \    - Business logic
/   Helpers    \    - Validation rules
/               \
```

### Unit Tests

**Coverage: 80%+ of classes**

**Test Categories:**

1. **Model Tests**
   - Relationship loading
   - Attribute casting
   - Scope queries
   - Model methods

2. **Service Tests**
   - Business logic validation
   - State transitions
   - Error conditions
   - Edge cases

3. **Validation Rule Tests**
   - Custom validation rules
   - Edge cases for validation
   - Error messages

**Example Unit Test:**
```php
class LoanRequestTest extends TestCase
{
    public function test_loan_requires_valid_item()
    {
        $loan = new Loan();
        $this->expectException(ValidationException::class);
        $loan->validate(['item_id' => 999]);
    }
    
    public function test_loan_quantity_must_not_exceed_stock()
    {
        $item = Item::factory()->create(['available_stock' => 5]);
        $request = ['item_id' => $item->id, 'quantity' => 10];
        
        $this->assertFalse($this->validateRequest($request));
    }
}
```

### Integration Tests

**Coverage: 70%+ of API endpoints**

**Test Scenarios:**

1. **Authentication Flow**
   - User registration with validation
   - Login with correct/incorrect credentials
   - Session creation
   - Logout and session cleanup

2. **RBAC Enforcement**
   - Staff cannot access /admin routes
   - Admin can access all routes
   - Unauthenticated users redirected to login
   - Authorization checks at action level

3. **Loan Workflow**
   - Staff submits loan request
   - Admin approves/rejects
   - Stock decreases on approval
   - Stock restored on rejection
   - Notifications sent

4. **Return Workflow**
   - Staff submits return
   - Admin verifies/rejects
   - Stock increases on verify
   - Item condition updated
   - Loan marked as returned

5. **Stock Management**
   - Stock in increases total and available
   - Stock out (loan) decreases available
   - Partial returns handled correctly
   - Stock history tracked

**Example Integration Test:**
```php
class LoanApprovalTest extends TestCase
{
    public function test_admin_can_approve_pending_loan()
    {
        $admin = User::factory()->create(['role' => 'Admin']);
        $staff = User::factory()->create(['role' => 'Staff']);
        $item = Item::factory()->create(['available_stock' => 10]);
        
        $loan = Loan::factory()->create([
            'user_id' => $staff->id,
            'status' => 'Menunggu Persetujuan'
        ]);
        
        $loan->items()->create([
            'item_id' => $item->id,
            'quantity' => 5
        ]);
        
        $response = $this->actingAs($admin)->post("/admin/loans/{$loan->id}/approve");
        
        $this->assertEquals(200, $response->status());
        $this->assertEquals('Disetujui', $loan->fresh()->status);
        $this->assertEquals(5, $item->fresh()->available_stock);
    }
    
    public function test_staff_cannot_approve_loan()
    {
        $staff = User::factory()->create(['role' => 'Staff']);
        $loan = Loan::factory()->create();
        
        $response = $this->actingAs($staff)->post("/admin/loans/{$loan->id}/approve");
        
        $this->assertEquals(403, $response->status());
    }
}
```

### Feature Tests

**Coverage: Main user workflows**

**Test Scenarios:**

1. **Complete Loan Request Workflow**
   - Staff browses items
   - Staff submits loan request
   - Admin reviews and approves
   - Stock correctly managed
   - Notifications sent
   - Loan appears in history

2. **Item Search and Filter**
   - Staff searches by keyword
   - Results display correctly
   - Unavailable items hidden
   - Category filter works
   - Pagination functions properly

3. **Admin Dashboard**
   - Statistics display correctly
   - Pending loans show
   - Recent transactions visible
   - Reports accessible

4. **Report Generation**
   - Inventory report contains all required data
   - Loan report shows correct statistics
   - Export to PDF/Excel works
   - Date filtering functional

**Example Feature Test:**
```php
class StaffLoanWorkflowTest extends TestCase
{
    public function test_complete_loan_workflow()
    {
        $staff = User::factory()->create(['role' => 'Staff']);
        $admin = User::factory()->create(['role' => 'Admin']);
        $item = Item::factory()->create(['available_stock' => 10]);
        
        // Staff browses and submits loan request
        $response = $this->actingAs($staff)->get('/staff/items');
        $this->assertSee($item->name);
        
        $response = $this->actingAs($staff)->post('/staff/loans', [
            'items' => [
                ['item_id' => $item->id, 'quantity' => 3]
            ],
            'planned_return_at' => now()->addDays(7)->toDateString()
        ]);
        
        $this->assertDatabaseHas('loans', [
            'user_id' => $staff->id,
            'status' => 'Menunggu Persetujuan'
        ]);
        
        // Admin approves
        $loan = Loan::first();
        $response = $this->actingAs($admin)->post("/admin/loans/{$loan->id}/approve");
        
        $this->assertDatabaseHas('loans', [
            'id' => $loan->id,
            'status' => 'Disetujui'
        ]);
        
        // Verify stock decreased
        $this->assertEquals(7, $item->fresh()->available_stock);
    }
}
```

### Property-Based Tests

**Coverage: Core invariants**

**Properties to Test:**

1. **Stock Invariant Property**
   - Generate random loans and returns
   - Verify: `available_stock ≤ total_stock` always holds
   - Verify: `available_stock + borrowed = total_stock`

2. **Loan State Machine Property**
   - Generate random valid transitions
   - Verify: Invalid transitions are rejected
   - Verify: State is mutually exclusive

3. **Audit Trail Property**
   - Generate random database modifications
   - Verify: All changes logged
   - Verify: Logs match database state changes

**Note:** If using property-based testing library (e.g., QuickCheck-style generators), implement with minimum 100 iterations per property.

### Test Execution

**Test Commands:**
```bash
# Run all tests
php artisan test

# Run specific test class
php artisan test tests/Unit/Models/LoanTest.php

# Run with coverage
php artisan test --coverage

# Run feature tests only
php artisan test tests/Feature

# Run unit tests only
php artisan test tests/Unit

# Run with specific filter
php artisan test --filter=loan
```

**CI/CD Integration:**
```yaml
# GitHub Actions example
test:
  runs-on: ubuntu-latest
  steps:
    - uses: actions/checkout@v2
    - name: Setup PHP
      uses: shivammathur/setup-php@v2
      with:
        php-version: '8.4'
    - name: Install dependencies
      run: composer install
    - name: Run tests
      run: php artisan test
    - name: Check coverage
      run: php artisan test --coverage --min=80
```

### Test Data Factories

**Purpose: Generate realistic test data**

```php
// UserFactory
User::factory()
    ->count(10)
    ->create(['role' => 'Staff']);

// ItemFactory
Item::factory()
    ->count(50)
    ->create()
    ->each(function ($item) {
        $item->loanItems()->createMany(
            LoanItem::factory()->count(3)->make()->toArray()
        );
    });

// Seeding for testing
DatabaseSeeder::class or use custom TestSeeder
```

### Test Environment Configuration

```php
// .env.testing
APP_ENV=testing
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
CACHE_DRIVER=array
SESSION_DRIVER=array
QUEUE_DRIVER=sync
```

### Performance Testing

**Tools:**
- Laravel Telescope (development)
- Apache Bench (load testing)
- Postman/Insomnia (API testing)

**Performance Targets:**
- Dashboard: < 2 seconds
- Search (1000+ items): < 3 seconds
- Loan approval: < 1 second
- Report generation: < 5 seconds

---

## Implementation Notes

### Phase 1: Core Infrastructure
1. Database setup with migrations
2. Authentication system
3. Basic models and relationships
4. RBAC middleware

### Phase 2: Inventory Management
5. Item, Category, Location CRUD
6. Stock management
7. Search and filtering

### Phase 3: Loan Workflows
8. Loan request submission
9. Loan approval process
10. Return request and verification

### Phase 4: Reporting & Admin
11. Reports (inventory and loans)
12. Audit logs
13. User management

### Phase 5: Security & Optimization
14. Security hardening
15. Performance optimization
16. Comprehensive testing
17. Documentation

---

## Deployment Considerations

### Server Requirements
- PHP 8.4+
- MySQL 8.0+
- Composer
- Node.js (for asset compilation)
- Git

### Environment Variables
```
APP_NAME=SIMBAR
APP_ENV=production
APP_DEBUG=false
APP_URL=https://simbar.example.com

DB_HOST=localhost
DB_DATABASE=simbar
DB_USERNAME=simbar_user
DB_PASSWORD=***

MAIL_DRIVER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=***
MAIL_PASSWORD=***

SESSION_SECURE_COOKIES=true
HTTPS_REDIRECT=true
```

### Migration Strategy
```bash
php artisan migrate --force
php artisan optimize:clear
php artisan storage:link
```

---

**Design Review Status: Ready for user feedback and approval before moving to task creation phase.**
