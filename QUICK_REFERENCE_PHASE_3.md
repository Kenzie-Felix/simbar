# SIMBAR Phase 3 - Quick Reference Guide

## Phase 3: Loan & Return Management

### Current Status
✅ **Part 1 Complete**: Loan Request Submission
- Staff can submit loan requests
- Items selection with stock validation
- Purpose and return date collection
- Request list with filtering
- Request detail view

⏳ **Part 2 Pending**: Loan Approval & Stock Management
⏳ **Part 3 Pending**: Return Management
⏳ **Part 4 Pending**: Overdue Detection & Reporting

---

## Testing Phase 3 Part 1

### Prerequisites
1. MySQL database running with `simbar` database
2. PHP and Composer installed
3. Laravel dev server running: `php artisan serve --port=8000`
4. Database migrated: `php artisan migrate`
5. Database seeded: `php artisan db:seed`

### Test Users
```
Admin Account:
- Email: admin@example.com
- Password: password
- Role: Admin

Staff Account:
- Email: staff@example.com
- Password: password
- Role: Staff
```

### Test Data
**Categories**: Electronics, Furniture, Tools
**Locations**: Warehouse A, Office Floor 1, Office Floor 2

**Items Created**:
| Code | Name | Category | Stock | Available | Condition |
|------|------|----------|-------|-----------|-----------|
| ELEC-001 | Projector | Electronics | 5 | 5 | Baik |
| ELEC-002 | Laptop | Electronics | 10 | 8 | Baik |
| FURN-001 | Office Chair | Furniture | 20 | 15 | Rusak Ringan |
| TOOL-001 | Drill Machine | Tools | 3 | 3 | Baik |
| ELEC-003 | USB Cables | Electronics | 50 | 40 | Baik |

### Testing Scenarios

#### Scenario 1: Create Basic Loan Request
1. Login as Staff user
2. Navigate to "Request Loan"
3. Click "Add Item" button
4. Select "ELEC-001 - Projector (5 available)"
5. Enter quantity: 2
6. Select return date: 7 days from today
7. Enter purpose: "Department meeting and presentations"
8. Click "Submit Loan Request"
9. **Expected**: Loan created successfully, redirected to detail view

#### Scenario 2: Multi-Item Request
1. Follow Scenario 1 up to step 4
2. Add another item by clicking "Add Item"
3. Select "FURN-001 - Office Chair (15 available)"
4. Enter quantity: 3
5. Click "Remove" on first item
6. Add item back and complete request
7. **Expected**: Request shows 2 items, 5 total quantity

#### Scenario 3: Stock Validation
1. In "Request Loan" form
2. Select "TOOL-001 - Drill Machine (3 available)"
3. Enter quantity: 5
4. **Expected**: Stock availability alert shows "Only 3 units available" and field is marked invalid

#### Scenario 4: View My Loans
1. Login as Staff user
2. Click "My Loans" in sidebar
3. See previously created requests
4. Filter by status "Pending" or "Approved"
5. Click "View" button
6. **Expected**: Request details page displays with all information

#### Scenario 5: Authorization Check
1. Login as Admin user
2. Try accessing `/staff/loans` directly
3. **Expected**: 403 Forbidden error (Admin cannot access staff routes)

---

## File Locations Reference

### Controllers
- Staff Loan Request: `app/Http/Controllers/Staff/LoanRequestController.php`

### Views
- Create Request: `resources/views/staff/loans/create.blade.php`
- List Requests: `resources/views/staff/loans/index.blade.php`
- Show Request: `resources/views/staff/loans/show.blade.php`

### Models
- Loan: `app/Models/Loan.php`
- LoanItem: `app/Models/LoanItem.php`
- Item: `app/Models/Item.php`

### Routes
- File: `routes/web.php`
- Prefix: `staff.loans.*`

### Database
- Migration: `database/migrations/0001_01_01_000131_add_purpose_to_loans_table.php`
- Seeder: `database/seeders/DatabaseSeeder.php`

### Navigation
- Sidebar: `resources/views/layouts/sidebar.blade.php`

---

## Database Relationships

```
User (Staff)
  ├─ Loan (many)
  │  ├─ LoanItem (many)
  │  │  └─ Item (one)
  │  │     ├─ Category (one)
  │  │     └─ Location (one)
  │  └─ User (approver - many to one)
  └─ BorrowReturn (many) - for Phase 3 Part 3
```

---

## API Endpoints (AJAX)

### Get Item Details
**Endpoint**: `GET /staff/loans/{item}/details`
**Returns**: 
```json
{
  "id": 1,
  "code": "ELEC-001",
  "name": "Projector",
  "category": "Electronics",
  "location": "Warehouse A",
  "available_stock": 5,
  "condition": "Baik"
}
```

### Check Stock Availability
**Endpoint**: `GET /staff/loans/check-availability`
**Parameters**: `item_id`, `quantity`
**Returns**:
```json
{
  "available": true,
  "required": 2,
  "stock": 5,
  "message": "Available: 5 units"
}
```

---

## Common Tasks

### Add New Test Category
```php
php artisan tinker
Category::create(['name' => 'Test', 'description' => 'Test category']);
```

### Add New Test Item
```php
Item::create([
  'code' => 'TEST-001',
  'name' => 'Test Item',
  'category_id' => 1,
  'location_id' => 1,
  'description' => 'Test',
  'total_stock' => 10,
  'available_stock' => 10,
  'condition' => 'Baik',
]);
```

### View All Loans
```php
php artisan tinker
Loan::with('user', 'loanItems.item')->get();
```

### Reset Database
```bash
php artisan migrate:fresh --seed
```

---

## Debugging Tips

### Check if migrations ran
```php
php artisan migrate:status
```

### View application log
```bash
tail -f storage/logs/laravel.log
```

### Test database connection
```php
php artisan tinker
DB::connection()->getPDO();
```

### Clear Laravel cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## Next Phase: Loan Approval (Phase 3 Part 2)

### What will be implemented:
1. **Admin/LoanApprovalController.php**
   - `index()` - List pending approvals
   - `show()` - View request details
   - `approve()` - Approve and decrement stock
   - `reject()` - Reject with reason

2. **Admin Views**
   - Pending approvals list
   - Approval detail view
   - Rejection form

3. **Stock Management**
   - Decrement available_stock on approval
   - Create StockMovement record

4. **Database**
   - No new tables needed
   - Use existing Loan model

5. **Routes**
   - Admin prefix: `admin.loans.*`

---

## Phase 3 Implementation Checklist

- [x] Part 1: Loan Request Submission
  - [x] Migration for purpose field
  - [x] Controller with validation
  - [x] Create request view
  - [x] List requests view
  - [x] Show request view
  - [x] Routes and navigation
  - [x] Test data seeding

- [ ] Part 2: Loan Approval
  - [ ] Admin approval controller
  - [ ] Approval views
  - [ ] Stock decrement logic
  - [ ] Rejection workflow
  - [ ] Admin navigation

- [ ] Part 3: Return Management
  - [ ] Staff return submission
  - [ ] Admin return verification
  - [ ] Stock restoration
  - [ ] Return views

- [ ] Part 4: Overdue Detection & Reporting
  - [ ] Overdue detection logic
  - [ ] Dashboard widgets
  - [ ] Loan reports
  - [ ] Return reports

---

## Useful Laravel Commands

```bash
# Start development server
php artisan serve --port=8000

# Run migrations
php artisan migrate

# Run seeders
php artisan db:seed

# Fresh migration (drop all tables)
php artisan migrate:fresh

# Fresh migration with seed
php artisan migrate:fresh --seed

# Check migrations status
php artisan migrate:status

# Create new migration
php artisan make:migration <name>

# Create new model
php artisan make:model <name>

# Create new controller
php artisan make:controller <name>

# Clear all caches
php artisan cache:clear

# Interactive tinker shell
php artisan tinker
```

---

## Technical Stack Recap

- **Framework**: Laravel 12
- **Database**: MySQL 8.0+
- **Frontend**: Bootstrap 5, JavaScript
- **Template**: Blade
- **Authentication**: Laravel built-in
- **Authorization**: Middleware-based RBAC
- **Transactions**: Database transactions for critical operations

---

## Support & Documentation

For more detailed information, see:
- `IMPLEMENTATION_PHASE_3_PART1.md` - Complete Phase 3 Part 1 documentation
- `IMPLEMENTATION_PHASE_2.md` - Phase 2 documentation
- `IMPLEMENTATION_STAGE_1.md` - Phase 1 documentation
