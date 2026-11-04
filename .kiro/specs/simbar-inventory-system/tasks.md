# Implementation Plan: SIMBAR Inventory Management System

## Overview

This implementation task list organizes the SIMBAR inventory management system into 6 sequential phases with granular, actionable tasks. Each task is designed for 30-60 minute completion duration and uses Laravel 12, PHP 8.4, MySQL, Blade templates, Bootstrap 5, and JavaScript (Alpine.js).

All tasks assume the requirements and design documents are available during implementation. Tasks are organized to build incrementally with clear dependencies, ensuring no orphaned code.

## Tasks

---

## Phase 1: Core Infrastructure & Authentication

### Phase 1 Overview
Establish the foundation: database structure, authentication system, RBAC middleware, and basic dashboard framework. All subsequent phases depend on these core components.

**Dependencies:** None (bootstrap phase)  
**Estimated Duration:** 5-6 hours total

---

### 1.1 Create Database Schema and Migrations

**Objective:** Create all database tables and relationships as defined in the design document.

- Create migration for `users` table with enum role field
- Create migration for `categories` table
- Create migration for `locations` table  
- Create migration for `items` table with foreign keys to categories and locations
- Create migration for `loans` table with relationship to users
- Create migration for `loan_items` table
- Create migration for `returns` table
- Create migration for `return_items` table
- Create migration for `stock_movements` table with audit trail
- Create migration for `item_condition_logs` table
- Create migration for `audit_logs` table with JSON fields
- Run all migrations: `php artisan migrate`

**Files to Create:**
- `database/migrations/[timestamp]_create_users_table.php` (extend existing)
- `database/migrations/[timestamp]_create_categories_table.php`
- `database/migrations/[timestamp]_create_locations_table.php`
- `database/migrations/[timestamp]_create_items_table.php`
- `database/migrations/[timestamp]_create_loans_table.php`
- `database/migrations/[timestamp]_create_loan_items_table.php`
- `database/migrations/[timestamp]_create_returns_table.php`
- `database/migrations/[timestamp]_create_return_items_table.php`
- `database/migrations/[timestamp]_create_stock_movements_table.php`
- `database/migrations/[timestamp]_create_item_condition_logs_table.php`
- `database/migrations/[timestamp]_create_audit_logs_table.php`

**Complexity:** Medium  
**Validation:** All tables exist in database with correct columns, indexes, and foreign key constraints

---

### 1.2 Create Eloquent Models with Relationships

**Objective:** Define all Eloquent models with correct relationships, attributes, and casting.

- Create User model extending Authenticatable with role and timestamps
- Create Category model with factory and relationships
- Create Location model with relationships to items
- Create Item model with relationships to category, location, loans, and stock movements
- Create Loan model with relationships to user, loan items, and approver
- Create LoanItem model with relationships to loan and item
- Create Return model with relationships to loan and return items
- Create ReturnItem model with relationships to return and loan item
- Create StockMovement model with relationships to item and user
- Create ItemConditionLog model with relationships to item and user
- Create AuditLog model with JSON casting for old_values and new_values
- Define all model relationships and mutators (e.g., appending is_overdue computed attributes)

**Files to Create:**
- `app/Models/Category.php`
- `app/Models/Location.php`
- `app/Models/Item.php`
- `app/Models/Loan.php`
- `app/Models/LoanItem.php`
- `app/Models/Return.php`
- `app/Models/ReturnItem.php`
- `app/Models/StockMovement.php`
- `app/Models/ItemConditionLog.php`
- `app/Models/AuditLog.php`

**Files to Modify:**
- `app/Models/User.php` - Add relationships to loans, audit_logs, stock_movements

**Complexity:** Medium  
**Validation:** All models loadable via Tinker with relationships working correctly

---

### 1.3 Implement Authentication System

**Objective:** Set up Laravel authentication with login, registration, and session management.

- Configure Laravel Auth in `config/auth.php` to use User model
- Create AuthController with login, register, logout methods
- Create login form Blade template with email/password fields
- Create registration form Blade template with validation feedback
- Implement password hashing with bcrypt (Laravel default)
- Add CSRF token validation to auth forms
- Implement login rate limiting (5 attempts per 15 minutes)
- Implement session timeout (30 minutes inactivity)
- Add "Remember Me" functionality for persistent login
- Implement logout with session destruction
- Create middleware stack for auth routes

**Files to Create:**
- `app/Http/Controllers/AuthController.php`
- `resources/views/auth/login.blade.php`
- `resources/views/auth/register.blade.php`
- `app/Http/Middleware/Authenticate.php`

**Files to Modify:**
- `routes/web.php` - Add auth routes
- `config/auth.php` - Configure guards and providers

**Complexity:** Medium  
**Requirements:** 1, 17, 29  
**Validation:** Can register, login, logout; session persists across requests; rate limiting works

---

### 1.4 Implement RBAC Middleware and Authorization

**Objective:** Create middleware to enforce Role-Based Access Control at route and action level.

- Create RBACMiddleware to check user role
- Implement role verification in middleware stack
- Create Authorize policy classes for resource access
- Implement can/cannot authorization helpers in controllers
- Create web middleware to redirect unauthorized access to 403 Forbidden
- Create middleware to redirect unauthenticated users to login
- Implement route groups with middleware:
  - `/admin/*` routes require Admin role
  - `/staff/*` routes require Staff role
  - `/` and auth routes accessible to all authenticated users
- Add authorization checks before critical actions
- Create authorization exception handler with proper status codes

**Files to Create:**
- `app/Http/Middleware/CheckAdminRole.php`
- `app/Http/Middleware/CheckStaffRole.php`
- `app/Policies/ItemPolicy.php`
- `app/Policies/LoanPolicy.php`
- `app/Policies/ReturnPolicy.php`
- `app/Policies/UserPolicy.php`

**Files to Modify:**
- `routes/web.php` - Add middleware to route groups
- `app/Exceptions/Handler.php` - Add authorization exception handling

**Complexity:** Medium  
**Requirements:** 2, 27, 28  
**Validation:** Staff cannot access /admin URLs; unauthenticated users redirected to login; role checks enforced

---

### 1.5 Create Dashboard Controllers and Views

**Objective:** Implement role-specific dashboards showing overview statistics and key metrics.

- Create DashboardController with adminDashboard() and staffDashboard() methods
- Implement query optimization for dashboard stats (count items, pending loans, etc.)
- Add caching for frequently accessed dashboard data (2-hour TTL)
- Create admin dashboard Blade template with:
  - Total items, categories, locations counts
  - Pending loan approvals count
  - Pending return verifications count
  - Recent audit logs
  - Stock status summary
- Create staff dashboard Blade template with:
  - User's active loans
  - User's pending returns
  - Available items count
  - Quick action buttons (New Loan, View Items, My History)
- Implement role-based dashboard routing

**Files to Create:**
- `app/Http/Controllers/DashboardController.php`
- `resources/views/admin/dashboard.blade.php`
- `resources/views/staff/dashboard.blade.php`
- `resources/views/layouts/app.blade.php` (main layout)
- `resources/views/layouts/sidebar.blade.php` (navigation)

**Files to Modify:**
- `routes/web.php` - Add dashboard routes
- `resources/views/welcome.blade.php` - Link to login/dashboard

**Complexity:** Simple  
**Requirements:** 21  
**Validation:** Dashboard loads in <2 seconds; displays correct data for role; cached queries used

---

### 1.6 Create Audit Logging Service

**Objective:** Build centralized audit logging for tracking all critical system actions.

- Create AuditLogService class with methods:
  - logAction($action, $entityType, $entityId, $oldValues, $newValues, $user)
  - getLogsFor($entityType, $entityId)
  - getLogsBy($user)
- Implement automatic JSON serialization of old/new values
- Capture IP address from request
- Capture user agent from request
- Create middleware to automatically log model changes (create/update/delete)
- Implement sanitization to prevent logging sensitive data (passwords)
- Add timestamps to all audit log entries
- Create AuditLog view/retrieve methods

**Files to Create:**
- `app/Services/AuditLogService.php`
- `app/Observers/AuditableObserver.php`
- `app/Http/Middleware/LogAuditTrail.php`

**Files to Modify:**
- `app/Models/Model.php` or individual model boot() - Register observer
- `app/Providers/AppServiceProvider.php` - Register observer

**Complexity:** Medium  
**Requirements:** 19  
**Validation:** Audit logs created for create/update/delete actions; JSON values stored correctly; IP addresses captured

---

## Phase 2: Inventory Management (Items, Categories, Locations)

### Phase 2 Overview
Implement admin functionality for managing the inventory core: items, categories, and storage locations with full CRUD operations and stock tracking.

**Dependencies:** Phase 1 complete  
**Estimated Duration:** 6-7 hours total

---

### 2.1 Create Category Management

**Objective:** Implement CRUD operations for item categories.

- Create CategoryController with index, store, update, destroy methods
- Create Category list view with search and pagination (20 per page)
- Create category modal/form for creating and editing
- Implement validation: category name required, unique, max 100 characters
- Add success/error notifications after CRUD operations
- Implement bulk delete if needed
- Display item count for each category
- Add audit logging for category changes

**Files to Create:**
- `app/Http/Controllers/Admin/CategoryController.php`
- `resources/views/admin/categories/index.blade.php`
- `resources/views/admin/categories/form.blade.php` (modal component)

**Files to Modify:**
- `routes/web.php` - Add category routes under /admin

**Complexity:** Simple  
**Requirements:** 4  
**Validation:** CRUD operations work; validation enforced; categories displayed with item counts

---

### 2.2 Create Location Management

**Objective:** Implement CRUD operations for storage locations.

- Create LocationController with index, store, update, destroy, show methods
- Create locations list view with search and pagination
- Create location form (modal or dedicated page)
- Implement validation: location name required, unique, max 100 characters
- Display items count for each location in location detail view
- Show all items stored at a specific location
- Add descriptions field to locations
- Implement delete prevention if location has items (show warning)
- Add audit logging for location changes

**Files to Create:**
- `app/Http/Controllers/Admin/LocationController.php`
- `resources/views/admin/locations/index.blade.php`
- `resources/views/admin/locations/show.blade.php`
- `resources/views/admin/locations/form.blade.php`

**Files to Modify:**
- `routes/web.php` - Add location routes

**Complexity:** Simple  
**Requirements:** 5  
**Validation:** Location CRUD works; item counts accurate; deletion blocked when items present

---

### 2.3 Create Item CRUD Operations - Part 1 (Create/Read)

**Objective:** Implement item creation and retrieval with full detail views.

- Create ItemController with index, create, store, show methods
- Create items list view with pagination (30 per page), sortable columns
- Create item creation form with fields:
  - Code (unique, required)
  - Name (required)
  - Category dropdown (required)
  - Location dropdown (required)
  - Description textarea
  - Initial stock (required, numeric)
  - Condition dropdown (default "Baik")
- Implement item code validation (alphanumeric, unique constraint in DB)
- Create item detail view showing:
  - All item properties
  - Current stock (total and available)
  - Condition status with visual indicator
  - Recent stock movements
  - Loan history
  - Condition change history
- Implement search by item name or code
- Add category and location filters
- Add audit logging for item creation

**Files to Create:**
- `app/Http/Controllers/Admin/ItemController.php`
- `resources/views/admin/items/index.blade.php`
- `resources/views/admin/items/create.blade.php`
- `resources/views/admin/items/show.blade.php`
- `resources/views/components/item-card.blade.php`

**Files to Modify:**
- `routes/web.php` - Add item routes
- `app/Http/Requests/StoreItemRequest.php` (validation)

**Complexity:** Medium  
**Requirements:** 3, 13  
**Validation:** Items created with all fields; search/filter works; detail view displays all info; stock tracking accurate

---

### 2.4 Create Item CRUD Operations - Part 2 (Update/Delete)

**Objective:** Implement item updates and deletion with safety checks.

- Create item edit form similar to create but pre-populated
- Implement update method with stock reconciliation logic
- Allow partial edits (name, description, category, location, condition)
- Implement delete with safety check: prevent deletion if item has active loans
- Show warning before deletion with count of related records
- Implement bulk edit for category/location changes across multiple items
- Add condition update form for marking items as damaged/lost
- Create condition history timeline view
- Implement archive functionality as alternative to hard delete
- Add audit logging for all updates

**Files to Modify:**
- `app/Http/Controllers/Admin/ItemController.php` - Add edit, update, destroy methods
- `resources/views/admin/items/edit.blade.php` - Create edit view
- `resources/views/admin/items/show.blade.php` - Add condition update form

**Complexity:** Medium  
**Requirements:** 3, 12  
**Validation:** Updates save correctly; deletion prevented with active loans; condition changes logged; archive works

---

### 2.5 Implement Stock In/Out Transactions

**Objective:** Create system for recording inventory movements (stock in, adjustments).

- Create StockMovementController with methods for recording stock in/out
- Create stock-in form view with:
  - Item selection dropdown with search
  - Quantity input (positive integer validation)
  - Movement type (In/Out/Adjustment)
  - Reference type (Purchase, Return, Damage Report, etc.)
  - Notes field
  - Recorded date
- Implement automatic stock update when movement is recorded
- Create stock movement history view showing:
  - All movements for an item
  - Type, quantity, reference, date, user
  - Cumulative stock calculations
- Implement stock reconciliation reports
- Add transaction validation to prevent negative stock (except for adjustments)
- Create batch stock import capability (optional but recommended)
- Implement automatic stock availability calculation (total - loaned)
- Add audit logging for all movements

**Files to Create:**
- `app/Http/Controllers/Admin/StockMovementController.php`
- `resources/views/admin/stock/stock-in.blade.php`
- `resources/views/admin/stock/history.blade.php`
- `app/Services/StockMovementService.php`

**Files to Modify:**
- `app/Models/Item.php` - Add stock update methods
- `routes/web.php` - Add stock movement routes

**Complexity:** Medium  
**Requirements:** 7, 22  
**Validation:** Stock movements recorded; calculations accurate; audit trail complete; negative stock prevented

---

### 2.6 Implement Item Search and Filtering for Staff

**Objective:** Create comprehensive search and filtering system for staff users to find items.

- Create ItemSearchController for staff with index and search methods
- Implement real-time search via AJAX returning JSON
- Create filterable item listing with:
  - Category filter dropdown
  - Location filter dropdown
  - Stock status filter (Available/Out of Stock)
  - Condition filter
  - Search text input
- Implement database query optimization for large datasets (use indexes)
- Add pagination (20 items per page)
- Display for each item:
  - Name, code, category, location
  - Available stock count
  - Condition status with color indicator
  - Quick view button to see full details
- Add responsive layout for mobile devices
- Implement search result caching (1-hour TTL) for common searches
- Create staff-only view template without edit/delete options

**Files to Create:**
- `app/Http/Controllers/Staff/ItemSearchController.php`
- `resources/views/staff/items/index.blade.php`
- `resources/views/staff/items/show.blade.php`
- `resources/views/components/item-result-card.blade.php`

**Files to Modify:**
- `routes/web.php` - Add staff item routes
- `resources/js/search.js` - AJAX search handler

**Complexity:** Medium  
**Requirements:** 13, 21  
**Validation:** Search works in <3 seconds for 1000+ items; filters accurate; pagination works; responsive design

---

## Phase 3: Loan Management (Requests & Approvals)

### Phase 3 Overview
Implement the borrowing workflow: staff request loans, admins approve/reject, and track active loans with deadline monitoring.

**Dependencies:** Phase 1 and Phase 2 complete  
**Estimated Duration:** 6-7 hours total

---

### 3.1 Create Loan Request Controller and Views

**Objective:** Allow staff to submit loan requests for available items.

- Create LoanRequestController with index, create, store, show methods
- Create loan request creation form with:
  - Item selection (searchable dropdown, filtered for available items only)
  - Quantity input with validation against available stock
  - Planned return date picker (must be future date)
  - Optional notes field
  - Submit button with confirmation
- Create loan request list view showing:
  - Staff's own loan requests
  - Status (Menunggu Persetujuan, Disetujui, Dipinjam, Dikembalikan, Ditolak)
  - Item details
  - Request date
  - Planned return date
  - Action buttons based on status
- Implement validation:
  - Quantity > 0 and ≤ available stock
  - Return date in future
  - Required fields
- Add success notification after submission
- Create loan request detail view with:
  - All request information
  - Item details (description, location, current condition)
  - Timeline of status changes
  - Approval/rejection reason if applicable
- Implement staff can only see their own loans
- Add audit logging for loan requests

**Files to Create:**
- `app/Http/Controllers/Staff/LoanRequestController.php`
- `app/Http/Requests/StoreLoanRequest.php`
- `resources/views/staff/loans/create.blade.php`
- `resources/views/staff/loans/index.blade.php`
- `resources/views/staff/loans/show.blade.php`

**Files to Modify:**
- `routes/web.php` - Add staff loan routes
- `app/Models/Loan.php` - Add status constants/enums

**Complexity:** Medium  
**Requirements:** 8  
**Validation:** Requests submitted successfully; stock validation works; status displayed correctly; staff isolation enforced

---

### 3.2 Create Loan Approval Controller and Workflow

**Objective:** Implement admin workflow for approving or rejecting loan requests.

- Create LoanApprovalController with index, approve, reject, allLoans methods
- Create pending approvals view showing:
  - All loans with status "Menunggu Persetujuan"
  - Staff name, items, quantities, planned return date
  - Approval/rejection buttons
  - Sort by request date (oldest first)
- Implement approval workflow:
  - Check available stock before approval
  - If stock insufficient, reject automatically with reason
  - If approved: deduct from available_stock, change status to "Disetujui"
  - Create StockMovement record for deduction (type: Out)
  - Update approved_at, approved_by_id
  - Change loan status to "Dipinjam"
- Implement rejection workflow:
  - Show rejection reason modal/form
  - Store rejection reason
  - Change status to "Ditolak"
  - Do NOT deduct stock
  - Update rejected_at
- Add notifications/alerts after approval/rejection
- Create approval history view showing all past approvals/rejections
- Implement batch approval option (select multiple pending loans)
- Add audit logging for all approvals/rejections

**Files to Create:**
- `app/Http/Controllers/Admin/LoanApprovalController.php`
- `app/Services/LoanApprovalService.php`
- `resources/views/admin/loans/pending.blade.php`
- `resources/views/admin/loans/all.blade.php`
- `resources/views/components/loan-approval-card.blade.php`

**Files to Modify:**
- `routes/web.php` - Add admin loan routes
- `app/Models/Loan.php` - Add approval methods

**Complexity:** Medium  
**Requirements:** 9, 21  
**Validation:** Approvals/rejections work; stock deducted correctly; batch operations work; notifications sent

---

### 3.3 Implement Loan Status Tracking and Overdue Detection

**Objective:** Track active loans and identify overdue items requiring return.

- Create computed property `is_overdue` on Loan model checking planned_return_at vs today
- Create computed property `is_overdue` on LoanItem model similarly
- Implement loan status view for admins showing:
  - All active loans (status = "Dipinjam")
  - Overdue indicator (red warning for overdue)
  - Days remaining until return deadline
  - Items in each loan with quantities
- Create staff loan status view showing their active loans with:
  - Days remaining calculation
  - Overdue warnings if applicable
  - "Request Return" button
- Implement database query to find overdue loans (performance optimized)
- Create overdue report/alert for admins (dashboard widget)
- Add highlighted styling for overdue items in lists
- Implement email notification for overdue items (optional but recommended)
- Add sorting/filtering by due date

**Files to Create:**
- `resources/views/admin/loans/active.blade.php`
- `resources/views/staff/loans/active.blade.php`

**Files to Modify:**
- `app/Models/Loan.php` - Add isOverdue(), daysUntilDue() methods
- `app/Models/LoanItem.php` - Add isOverdue() method
- `app/Http/Controllers/DashboardController.php` - Add overdue widget to dashboard

**Complexity:** Simple  
**Requirements:** 14, 21  
**Validation:** Overdue detection works; calculations accurate; visual indicators present; queries performant

---

### 3.4 Create Loan History and Reporting

**Objective:** Implement comprehensive loan history and reporting capabilities.

- Create loan history view for staff showing all personal loans
- Create loan history view for admins showing all system loans
- Implement filtering by:
  - Date range
  - Status
  - Item
  - Staff member (for admin)
  - Loan outcome (approved/rejected/completed)
- Create loan report generation with statistics:
  - Total loans requested, approved, rejected, completed
  - Average approval rate
  - Items most frequently loaned
  - Staff with most requests
  - Return compliance rate (on-time returns %)
- Implement export to CSV/Excel
- Create PDF report template
- Add date range picker for custom reporting periods
- Implement pagination for large result sets (50 per page)
- Create visual charts for loan trends (using Chart.js or similar)

**Files to Create:**
- `app/Http/Controllers/Admin/LoanReportController.php`
- `resources/views/admin/reports/loans.blade.php`
- `app/Services/ReportGenerationService.php`
- `resources/views/exports/loan-report.blade.php`

**Files to Modify:**
- `routes/web.php` - Add report routes
- `composer.json` - Add export packages if needed (e.g., maatwebsite/excel)

**Complexity:** Medium  
**Requirements:** 16, 21  
**Validation:** Filtering works; export generates correct files; statistics accurate; pagination works

---

## Phase 4: Return Management (Requests & Verification)

### Phase 4 Overview
Implement return workflows: staff request returns, admins verify condition, and update inventory based on actual received condition.

**Dependencies:** Phase 1, 2, 3 complete  
**Estimated Duration:** 6-7 hours total

---

### 4.1 Create Return Request Controller and Views

**Objective:** Allow staff to initiate return of borrowed items.

- Create ReturnRequestController with create, store, index, show methods
- Create return request form showing:
  - List of staff's active "Dipinjam" loans
  - Items in selected loan with originally borrowed quantity
  - For each item: quantity to return (≤ borrowed quantity), condition dropdown
  - Condition options: Baik, Rusak Ringan, Rusak Berat, Hilang
  - Notes field for describing damage if applicable
  - Submit button
- Implement validation:
  - Return quantity ≤ borrowed quantity
  - At least one item selected
  - Condition required
- Create return list view showing:
  - Staff's return requests
  - Status (Menunggu Verifikasi, Terima, Tolak)
  - Related loan information
  - Submission date
  - Verification date (if verified)
  - Rejection reason (if rejected)
- Create return request detail view with all information
- Add audit logging for return requests

**Files to Create:**
- `app/Http/Controllers/Staff/ReturnRequestController.php`
- `app/Http/Requests/StoreReturnRequest.php`
- `resources/views/staff/returns/create.blade.php`
- `resources/views/staff/returns/index.blade.php`
- `resources/views/staff/returns/show.blade.php`

**Files to Modify:**
- `routes/web.php` - Add staff return routes
- `app/Models/Return.php` - Add status constants

**Complexity:** Medium  
**Requirements:** 10  
**Validation:** Returns submitted; active loans listed; validation enforced; status tracked

---

### 4.2 Create Return Verification Controller and Workflow

**Objective:** Implement admin workflow for verifying returned items and updating inventory.

- Create ReturnVerificationController with index, verify, reject, show methods
- Create pending verifications view showing:
  - All returns with status "Menunggu Verifikasi"
  - Staff name, loan items, reported conditions
  - Verify/reject buttons
  - Sort by submission date (oldest first)
- Implement verification workflow:
  - Admin reviews reported condition and reported quantity
  - Admin enters verified condition (may differ from reported)
  - Admin confirms accepted quantity (may differ from returned quantity)
  - Admin adds verification notes (optional)
  - Change status to "Terima"
  - Create StockMovement record for returned items (type: In)
  - Update item available_stock by returned quantity
  - Update item condition if verification showed damage
  - Create ItemConditionLog for any condition changes
  - Mark loan status as "Dikembalikan"
  - Update returned_at on LoanItem
  - Update return_condition on LoanItem based on verification
- Implement rejection workflow:
  - Show rejection reason form
  - Store rejection reason
  - Change status to "Tolak"
  - Do NOT add items back to inventory
  - Require staff to resubmit return
- Add notifications after verification/rejection
- Create batch verification option
- Add audit logging for all verifications

**Files to Create:**
- `app/Http/Controllers/Admin/ReturnVerificationController.php`
- `app/Services/ReturnVerificationService.php`
- `resources/views/admin/returns/pending.blade.php`
- `resources/views/admin/returns/all.blade.php`
- `resources/views/components/return-verification-card.blade.php`

**Files to Modify:**
- `routes/web.php` - Add admin return routes
- `app/Models/Return.php` - Add verification methods
- `app/Models/Loan.php` - Track return status

**Complexity:** Medium  
**Requirements:** 11  
**Validation:** Verifications process correctly; stock updated accurately; condition logs created; notifications sent

---

### 4.3 Implement Item Condition Management

**Objective:** Track and manage item condition changes throughout lifecycle.

- Create condition status system with states: Baik, Rusak Ringan, Rusak Berat, Hilang
- Implement automatic condition updates when:
  - Item is returned in damaged condition
  - Admin manually changes condition
  - Item is marked as lost
- Create ItemConditionLog model to track all condition changes
- Implement condition change audit trail showing:
  - Previous condition
  - New condition
  - Reason for change
  - Changed by (admin name)
  - Timestamp
  - Reference (loan, return, manual update)
- Create admin interface for manually updating item condition with form:
  - Current condition (read-only)
  - New condition dropdown
  - Reason dropdown (Damaged in Loan, Damaged in Warehouse, Lost, Found, Repaired, etc.)
  - Notes textarea
  - Submit button
- Implement automatic stock adjustment when condition changes:
  - Items marked "Rusak Berat" or "Hilang" removed from available_stock
  - Items marked "Baik" added back to available_stock if previously unavailable
- Create condition change history view
- Add visual indicators/badges for damaged/lost items across all views
- Implement report of items by condition status

**Files to Create:**
- `resources/views/admin/items/condition-change.blade.php`
- `resources/views/components/item-condition-badge.blade.php`

**Files to Modify:**
- `app/Http/Controllers/Admin/ItemController.php` - Add condition update methods
- `app/Models/Item.php` - Add condition management methods
- `routes/web.php` - Add condition change routes

**Complexity:** Medium  
**Requirements:** 12  
**Validation:** Condition changes logged; stock adjusted; history accurate; badges display correctly

---

### 4.4 Implement Return History and Reconciliation

**Objective:** Create comprehensive return tracking and reconciliation reporting.

- Create return history view for staff showing all their returns
- Create return history view for admins showing all system returns
- Implement filtering by:
  - Date range
  - Status (Menunggu Verifikasi, Terima, Tolak)
  - Item
  - Staff member (for admin)
- Create reconciliation report showing:
  - Items returned vs items borrowed
  - Condition change statistics (how many items came back damaged)
  - Overdue returns
  - Items not yet returned
  - Replacement/repair needs
- Implement return compliance metrics:
  - On-time return rate per staff
  - Damage rate per item/category
  - Average return processing time
- Create export to CSV/Excel
- Implement PDF report template
- Add data visualization for return trends

**Files to Create:**
- `app/Http/Controllers/Admin/ReturnReportController.php`
- `resources/views/admin/reports/returns.blade.php`

**Files to Modify:**
- `routes/web.php` - Add return report routes
- `app/Services/ReportGenerationService.php` - Add return metrics

**Complexity:** Medium  
**Requirements:** 16  
**Validation:** Reports generate correctly; metrics accurate; exports work; filtering accurate

---

## Phase 5: Reporting & Admin Management

### Phase 5 Overview
Implement comprehensive reporting, user management, and admin tools for system oversight and analytics.

**Dependencies:** Phase 1-4 complete  
**Estimated Duration:** 5-6 hours total

---

### 5.1 Create Inventory Reporting System

**Objective:** Build comprehensive inventory reports with analytics and export capabilities.

- Create InventoryReportController with index method
- Create inventory report view showing:
  - Total items count
  - Total stock count (all items combined)
  - Items by category (table with counts)
  - Items by location (table with counts)
  - Items by condition status (Baik, Rusak, Hilang)
  - Available vs. currently loaned summary
  - Low stock items (stock < threshold, e.g., < 2 units)
  - Out of stock items
- Implement date range filtering:
  - Show inventory status at specific date
  - Show stock movements during period
  - Compare periods (e.g., month-over-month)
- Implement category/location filtering
- Implement export functionality:
  - Export to CSV with all item details
  - Export to Excel with multiple sheets (items, categories, locations, summary)
  - Export to PDF with formatted report
- Add visual charts showing:
  - Stock distribution by category (pie chart)
  - Stock distribution by location (bar chart)
  - Stock trend over time (line chart)
  - Condition status breakdown (pie chart)
- Implement pagination for large result sets
- Create print-friendly layout option

**Files to Create:**
- `app/Http/Controllers/Admin/InventoryReportController.php`
- `app/Services/InventoryReportService.php`
- `resources/views/admin/reports/inventory.blade.php`
- `resources/views/exports/inventory-report.blade.php`

**Files to Modify:**
- `routes/web.php` - Add report routes

**Complexity:** Medium  
**Requirements:** 15, 21  
**Validation:** Reports generate correctly; exports work; charts display; date filtering works

---

### 5.2 Create User Management System

**Objective:** Implement admin controls for staff account lifecycle management.

- Create UserManagementController with index, create, store, update, show, toggle methods
- Create user list view showing:
  - User email, name, department
  - Role (Admin or Staff)
  - Status (Active/Inactive)
  - Last login timestamp
  - Created date
  - Action buttons (Edit, Toggle Active/Inactive, Delete)
  - Search by email or name
  - Pagination (50 per page)
- Create user creation form with:
  - Email (unique, required, validated)
  - Name (required, max 100 chars)
  - Department (optional)
  - Role dropdown (Admin or Staff)
  - Temporary password generation or email invite
  - Submit button
- Implement validation:
  - Email unique
  - Email valid format
  - Name required
  - Role required
- Create user edit form for name, department, role updates
- Implement activate/deactivate toggle:
  - Inactive users cannot login
  - Deactivating user ends all active sessions
  - Cannot deactivate last Admin user (safety check)
- Implement password reset functionality:
  - Admin can force password reset
  - Send reset link to user email
  - User must set new password on next login
- Create delete functionality with safety checks:
  - Warn if user has active loans/returns
  - Allow deletion only if no active loans
  - Offer to reassign loans before deletion
- Add audit logging for all user management actions

**Files to Create:**
- `app/Http/Controllers/Admin/UserManagementController.php`
- `app/Http/Requests/StoreUserRequest.php`
- `resources/views/admin/users/index.blade.php`
- `resources/views/admin/users/create.blade.php`
- `resources/views/admin/users/edit.blade.php`

**Files to Modify:**
- `routes/web.php` - Add user management routes
- `app/Models/User.php` - Add status/role management methods

**Complexity:** Medium  
**Requirements:** 6  
**Validation:** Users created/updated correctly; status toggle works; password reset works; safety checks enforced

---

### 5.3 Create Audit Log Viewer

**Objective:** Implement comprehensive audit log viewing and analysis for admins.

- Create AuditLogController with index and show methods
- Create audit log list view with:
  - All audit log entries with pagination (100 per page)
  - Columns: timestamp, user, action, entity type, old values, new values
  - Time-based sorting (newest first by default)
  - Color-coded action types (Create=green, Update=blue, Delete=red, etc.)
- Implement filtering by:
  - Date range (from/to date pickers)
  - User (dropdown of all users)
  - Action type (dropdown: Create, Read, Update, Delete, Login, Logout, etc.)
  - Entity type (dropdown: Item, Loan, Return, User, Category, Location, etc.)
  - Entity ID (search field)
- Create audit log detail view showing:
  - Full audit entry with all data
  - Old values (if update/delete) - formatted JSON
  - New values (if create/update) - formatted JSON
  - IP address and user agent
  - Timestamp with timezone
  - User who performed action
  - Related entity with link
- Implement export functionality:
  - Export filtered logs to CSV
  - Export filtered logs to PDF
- Create search across all audit fields
- Implement performance optimization for large log sets (use indexes on created_at, user_id, action)

**Files to Create:**
- `app/Http/Controllers/Admin/AuditLogController.php`
- `resources/views/admin/audit-logs/index.blade.php`
- `resources/views/admin/audit-logs/show.blade.php`

**Files to Modify:**
- `routes/web.php` - Add audit log routes

**Complexity:** Medium  
**Requirements:** 19  
**Validation:** Logs display correctly; filtering works; export functions; performance acceptable

---

### 5.4 Create Admin Dashboard with System Health Metrics

**Objective:** Enhance admin dashboard with comprehensive system monitoring and quick actions.

- Extend DashboardController adminDashboard method with metrics:
  - System overview statistics (items, users, loans, returns counts)
  - Recent activities widget (last 10 audit log entries)
  - Pending approvals count (with link)
  - Pending verifications count (with link)
  - Overdue loans count (with link to list)
  - Low stock alerts (items with < 2 units)
  - Today's statistics (loans approved, returns verified, items added)
  - Weekly trends (loans per day, returns per day charts)
- Implement real-time data refresh (AJAX, 5-minute refresh interval)
- Create cached queries for performance (2-hour TTL on stats)
- Add quick action buttons:
  - New Item
  - Stock In
  - New User
  - View Pending Approvals
  - View Pending Verifications
- Implement permission to clear cache if needed
- Add system health indicators:
  - Database connection status
  - Disk space available (if applicable)
  - Last backup time
- Create role-specific dashboard display

**Files to Modify:**
- `app/Http/Controllers/DashboardController.php` - Enhance adminDashboard
- `resources/views/admin/dashboard.blade.php` - Add widgets and metrics

**Complexity:** Medium  
**Requirements:** 21, 22  
**Validation:** Dashboard loads <2 seconds; metrics accurate; AJAX refresh works; caching functional

---

## Phase 6: Security & Optimization

### Phase 6 Overview
Implement security hardening, performance optimization, and testing to ensure production readiness.

**Dependencies:** Phase 1-5 complete  
**Estimated Duration:** 5-6 hours total

---

### 6.1 Implement Security Hardening

**Objective:** Apply comprehensive security best practices across the system.

- Implement HTTPS configuration:
  - Force HTTPS in production via `.htaccess` or middleware
  - Configure secure cookies with HttpOnly and Secure flags
  - Add HSTS header (Strict-Transport-Security)
- Implement CSRF protection:
  - Verify CSRF tokens on all POST/PUT/DELETE requests
  - Configure CSRF exception routes if needed
  - Validate token freshness
- Implement XSS prevention:
  - Escape all output in Blade templates (Laravel default)
  - Validate and sanitize all input
  - Use Content-Security-Policy headers
- Implement SQL injection prevention:
  - Use parameterized queries throughout (Laravel default with Eloquent)
  - Validate all raw queries
  - Use prepared statements
- Implement authentication hardening:
  - Rate limit login attempts (done in 1.3 but verify)
  - Implement account lockout after failed attempts
  - Add login attempt logging
  - Implement password requirements (done in 1.3 but verify)
- Implement session security:
  - Regenerate session ID after login
  - Expire sessions on logout
  - Implement session timeout (30 minutes, done in 1.3)
  - Use secure session storage
- Implement authorization hardening:
  - Check authorization on every action (done throughout)
  - Implement principle of least privilege
  - Validate role before every sensitive operation
- Add security headers:
  - X-Content-Type-Options: nosniff
  - X-Frame-Options: DENY (or SAMEORIGIN)
  - X-XSS-Protection: 1; mode=block
  - Referrer-Policy: strict-origin-when-cross-origin
- Implement input validation:
  - Validate all form input (length, type, format)
  - Reject invalid input with clear error messages
  - Log validation failures for security monitoring
- Implement file upload security (if applicable):
  - Validate file types
  - Store uploads outside web root
  - Disable script execution in upload directories
- Implement API security (if APIs exposed):
  - Rate limiting on API endpoints
  - API authentication/authorization
  - Request validation

**Files to Create:**
- `app/Http/Middleware/SecurityHeaders.php`
- `.env.example` - Add security configuration options

**Files to Modify:**
- `app/Http/Middleware/EncryptCookies.php` - Ensure secure cookies
- `config/session.php` - Configure secure session settings
- `app/Providers/RouteServiceProvider.php` - Add HTTPS forcing

**Complexity:** Complex  
**Requirements:** 17, 18, 20, 27  
**Validation:** HTTPS working; CSRF tokens validated; security headers present; rate limiting active; input validation enforced

---

### 6.2 Implement Database Optimization and Indexing

**Objective:** Optimize database performance for large datasets and concurrent users.

- Analyze current queries for N+1 problems and optimize:
  - Use eager loading (with()) in all controllers
  - Identify and optimize slow queries
  - Add missing eager load relationships
- Create database indexes for frequently queried columns:
  - `users.email` (unique)
  - `items.code` (unique)
  - `categories.name` (unique)
  - `locations.name` (unique)
  - `items.category_id`, `items.location_id`, `items.condition`
  - `loans.user_id`, `loans.status`, `loans.requested_at`
  - `loan_items.loan_id`, `loan_items.item_id`, `loan_items.planned_return_at`
  - `returns.loan_id`, `returns.status`, `returns.submitted_at`
  - `stock_movements.item_id`, `stock_movements.created_at`
  - `audit_logs.user_id`, `audit_logs.action`, `audit_logs.created_at`, `audit_logs.entity_type`
  - All foreign keys
- Implement query result caching:
  - Cache frequently accessed data (categories, locations)
  - Implement cache invalidation on updates
  - Use 2-hour TTL for read-heavy data
  - Use 30-minute TTL for medium-change data
- Implement pagination optimization:
  - Use cursor-based pagination for very large datasets if needed
  - Implement reasonable page sizes (20-50 items)
  - Add count() to queries carefully (can be slow on large tables)
- Optimize complex queries:
  - Use database-level aggregations instead of PHP collections
  - Minimize data transferred from database
  - Use select() to fetch only needed columns
- Implement database connection pooling if applicable
- Create migration to add indexes to existing tables

**Files to Create:**
- `database/migrations/[timestamp]_add_indexes_to_tables.php`

**Files to Modify:**
- All controllers - Use eager loading (with())
- `config/cache.php` - Configure cache settings
- Various models - Add cache() calls where appropriate

**Complexity:** Complex  
**Requirements:** 21, 22  
**Validation:** Queries execute <3 seconds for large datasets; N+1 problems eliminated; indexes verify in DB; caching working

---

### 6.3 Implement Caching Strategy

**Objective:** Implement comprehensive caching to improve performance.

- Configure cache driver (.env): file, redis, or memcached (file for initial deployment)
- Implement caching for:
  - Category list (2-hour TTL)
  - Location list (2-hour TTL)
  - Dashboard statistics (2-hour TTL, invalidate on changes)
  - Item search results (1-hour TTL)
  - Item details (1-hour TTL)
  - User permissions/roles (1-hour TTL)
  - Report generation (24-hour TTL, manual invalidation)
- Implement cache invalidation:
  - On model update/create/delete, invalidate related caches
  - Create CacheInvalidationService
  - Use event listeners to trigger invalidation
  - Manual cache clear command for admins
- Implement query caching using remember():
  - Category::remember(2*60, 'categories')
  - Location::remember(2*60, 'locations')
  - Item search results cache
- Implement view fragment caching for expensive computations
- Create artisan command to clear specific caches
- Monitor cache hit/miss rates (optional but recommended)

**Files to Create:**
- `app/Services/CacheInvalidationService.php`
- `app/Console/Commands/ClearAppCache.php`

**Files to Modify:**
- All controllers - Add caching to appropriate queries
- `config/cache.php` - Configure cache settings
- `.env.example` - Add cache configuration options

**Complexity:** Medium  
**Requirements:** 21, 22  
**Validation:** Cache working; TTLs correct; invalidation working; performance improved

---

### 6.4 Implement Application Testing

**Objective:** Create comprehensive test suite for critical functionality.

- Create Unit Tests for:
  - Model calculations (stock availability, overdue detection)
  - Authorization policies
  - Service classes (LoanService, ReturnService)
  - Validation rules
- Create Feature Tests for:
  - Authentication (login, register, logout, session management)
  - RBAC enforcement (staff cannot access admin URLs)
  - Loan workflow (request → approve → return → verify)
  - Return workflow (request → verify)
  - Item management (CRUD operations)
  - Audit logging
  - Stock tracking
  - Role enforcement on every protected route
- Create Database Tests:
  - Migrations run correctly
  - Foreign key constraints
  - Unique constraints
  - Data integrity
- Implement test database setup and cleanup
- Create test fixtures and factories:
  - UserFactory (Admin and Staff)
  - ItemFactory
  - CategoryFactory
  - LocationFactory
  - LoanFactory
  - ReturnFactory
- Configure PHPUnit for test execution
- Create CI/CD pipeline configuration (if applicable)
- Target >80% code coverage for critical paths

**Files to Create:**
- `tests/Unit/Models/LoanTest.php`
- `tests/Unit/Models/ItemTest.php`
- `tests/Unit/Services/LoanServiceTest.php`
- `tests/Feature/AuthenticationTest.php`
- `tests/Feature/RBACTest.php`
- `tests/Feature/LoanWorkflowTest.php`
- `tests/Feature/ReturnWorkflowTest.php`
- `tests/Feature/ItemManagementTest.php`
- `tests/Feature/AuditLoggingTest.php`
- `database/factories/ItemFactory.php`
- `database/factories/LoanFactory.php`
- Extend existing factories

**Files to Modify:**
- `phpunit.xml` - Configure test database
- `.env.testing` - Test environment configuration

**Complexity:** Complex  
**Requirements:** All (verification that requirements implemented)  
**Validation:** All tests pass; code coverage >80%; CI/CD green

---

### 6.5 Implement Error Handling and Logging

**Objective:** Implement comprehensive error handling and system logging.

- Configure error handlers:
  - Production error pages (user-friendly, no technical details)
  - Development error pages (detailed stack traces)
  - Error logging to file
  - Error logging to external service (Sentry, if desired)
- Implement logging throughout application:
  - Log authentication events (login success/failure, logout)
  - Log authorization failures (access denied attempts)
  - Log all CRUD operations
  - Log errors and exceptions
  - Log slow queries (>1 second)
  - Log suspicious activities
- Configure logging channels:
  - Single file channel
  - Daily rotating channel
  - Slack channel for critical errors
- Implement exception handling:
  - Custom exceptions for domain errors
  - Proper HTTP status codes (403, 404, 500, etc.)
  - User-friendly error messages
  - Stack trace logging without exposing to users
- Implement validation error handling:
  - Flash messages for validation errors
  - Preserve form input on validation failure
  - Clear error messages for each field
- Implement database error handling:
  - Handle connection errors gracefully
  - Log database errors
  - Show maintenance page if database unavailable
- Create error reporting dashboard for admins (optional)

**Files to Create:**
- `app/Exceptions/Handler.php` (customize)
- `app/Exceptions/CustomException.php` (domain exceptions)
- `resources/views/errors/403.blade.php`
- `resources/views/errors/404.blade.php`
- `resources/views/errors/500.blade.php`

**Files to Modify:**
- `config/logging.php` - Configure logging channels
- `.env.example` - Add logging configuration

**Complexity:** Medium  
**Requirements:** All (implicit in error handling requirement)  
**Validation:** Errors handled gracefully; logs created; error pages display correctly; no technical details exposed

---

### 6.6 Performance Tuning and Load Testing

**Objective:** Optimize system performance for target concurrent users and data volume.

- Implement performance monitoring:
  - Monitor page load times
  - Monitor query execution times
  - Monitor API response times
  - Use Laravel Telescope or similar tool
- Optimize slow queries:
  - Identify queries >1 second
  - Add necessary indexes
  - Refactor queries if needed
  - Use database-level aggregations
  - Cache expensive computations
- Implement compression:
  - Enable GZIP compression
  - Minify CSS and JavaScript
  - Optimize images
- Optimize assets:
  - Bundle and minify CSS (Vite)
  - Bundle and minify JavaScript (Vite)
  - Lazy load images if applicable
  - Use CDN for static assets if applicable
- Conduct load testing:
  - Test with 50 concurrent users
  - Measure response times
  - Identify bottlenecks
  - Verify database handles 100,000+ records
  - Verify system meets SLA (page load <2 seconds)
- Document performance benchmarks
- Create performance optimization recommendations

**Files to Create:**
- `tests/Performance/LoadTest.php` (optional but recommended)

**Files to Modify:**
- `config/app.php` - Enable/disable debug mode appropriately
- `vite.config.js` - Configure asset optimization

**Complexity:** Complex  
**Requirements:** 21, 22  
**Validation:** Page load <2 seconds; handle 50 concurrent users; database queries <1 second; all benchmarks met

---

### 6.7 Create Deployment Documentation

**Objective:** Document deployment process, configuration, and maintenance procedures.

- Create deployment checklist:
  - Environment setup
  - Database setup
  - Configuration
  - SSL/HTTPS setup
  - Backup procedures
  - Monitoring setup
- Document system requirements:
  - PHP 8.4+
  - MySQL 8.0+
  - Composer dependencies
  - Node.js (for build tools)
- Document configuration:
  - .env file setup
  - Database configuration
  - Mail configuration
  - Cache configuration
  - Session configuration
  - API configuration
- Document maintenance procedures:
  - Regular backups
  - Database optimization
  - Cache clearing
  - Log rotation
  - Security updates
- Create README.md with setup instructions
- Create TROUBLESHOOTING.md with common issues and solutions
- Document API endpoints if exposed

**Files to Create:**
- `DEPLOYMENT.md` - Deployment procedures
- `MAINTENANCE.md` - Maintenance procedures
- `TROUBLESHOOTING.md` - Common issues
- `ARCHITECTURE.md` - System architecture overview
- Enhanced `README.md` - Project overview

**Complexity:** Simple  
**Requirements:** 26  
**Validation:** Documentation complete; instructions clear; deployment reproducible

---

## Notes

### Testing Strategy

**Property-Based Tests (if applicable):**
- Loan stock calculation properties (ensure stock never goes negative)
- Date calculation properties (overdue detection always correct for future dates)
- Status transition properties (valid state transitions only)

**Example: Stock Availability Property**
```
For any item, available_stock = total_stock - (sum of borrowed items)
For any item, available_stock >= 0
For any item, available_stock <= total_stock
```

### Task Dependency Rules

1. **No orphaned code** - Every code change connects to previous implementation
2. **Incremental validation** - Each task validates with tests or manual verification
3. **Phase dependencies** - Each phase builds on previous phase completion
4. **Feature completeness** - Each feature (loans, returns, etc.) completed before next

### Optional Task Marking

Tasks marked with `*` are optional test/verification sub-tasks. They are NOT included in the core implementation but are STRONGLY RECOMMENDED for production quality:

- All unit/integration tests are marked optional for MVP speed
- But testing is critical for production stability

### Estimated Timeline

- **Phase 1** (Infrastructure): 5-6 hours
- **Phase 2** (Inventory): 6-7 hours
- **Phase 3** (Loans): 6-7 hours
- **Phase 4** (Returns): 6-7 hours
- **Phase 5** (Reports/Admin): 5-6 hours
- **Phase 6** (Security/Optimization): 5-6 hours

**Total: 34-39 hours** (approximately 1 week of focused development)

---

## Task Dependency Graph

```json
{
  "waves": [
    {
      "id": 0,
      "tasks": [
        "1.1",
        "1.2"
      ],
      "description": "Database foundation - schemas and models"
    },
    {
      "id": 1,
      "tasks": [
        "1.3",
        "1.4",
        "1.5",
        "1.6"
      ],
      "description": "Authentication and core infrastructure"
    },
    {
      "id": 2,
      "tasks": [
        "2.1",
        "2.2"
      ],
      "description": "Category and location management"
    },
    {
      "id": 3,
      "tasks": [
        "2.3",
        "2.4"
      ],
      "description": "Item CRUD operations"
    },
    {
      "id": 4,
      "tasks": [
        "2.5",
        "2.6"
      ],
      "description": "Stock management and staff search"
    },
    {
      "id": 5,
      "tasks": [
        "3.1",
        "3.2"
      ],
      "description": "Loan request and approval workflow"
    },
    {
      "id": 6,
      "tasks": [
        "3.3",
        "3.4"
      ],
      "description": "Loan tracking and reporting"
    },
    {
      "id": 7,
      "tasks": [
        "4.1",
        "4.2"
      ],
      "description": "Return request and verification workflow"
    },
    {
      "id": 8,
      "tasks": [
        "4.3",
        "4.4"
      ],
      "description": "Item condition and return reconciliation"
    },
    {
      "id": 9,
      "tasks": [
        "5.1",
        "5.2",
        "5.3"
      ],
      "description": "Inventory reports, user management, audit logs"
    },
    {
      "id": 10,
      "tasks": [
        "5.4"
      ],
      "description": "Admin dashboard enhancements"
    },
    {
      "id": 11,
      "tasks": [
        "6.1",
        "6.2",
        "6.3"
      ],
      "description": "Security hardening and database optimization"
    },
    {
      "id": 12,
      "tasks": [
        "6.4",
        "6.5",
        "6.6"
      ],
      "description": "Testing, error handling, and performance tuning"
    },
    {
      "id": 13,
      "tasks": [
        "6.7"
      ],
      "description": "Deployment documentation"
    }
  ]
}
```

---

## Summary

This comprehensive implementation task list provides:

- **30 detailed tasks** organized into 6 logical phases
- **Granular scope** - Each task is 30-60 minutes of work
- **Clear dependencies** - No orphaned code; phases build sequentially
- **Production-ready** - Includes security, optimization, testing, and documentation
- **Laravel best practices** - Uses Laravel 12, Eloquent ORM, Blade templates, and modern patterns
- **Requirement traceability** - Each task maps to requirements from the specification
- **Testing strategy** - Property tests and unit tests for critical functionality
- **Real-world timing** - Realistic estimates for complete implementation in 1 week

Begin with Phase 1 to establish the database and authentication foundation, then proceed sequentially through each phase. Each task builds on previous work, ensuring a cohesive, maintainable codebase.
