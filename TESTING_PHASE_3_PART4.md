# TESTING GUIDE - Phase 3 Part 4: Reporting & Overdue Detection

## 📋 Overview

Comprehensive testing guide for Phase 3 Part 4: Reporting & Overdue Detection in SIMBAR.

**Test Date**: November 4, 2026
**Phase**: 3 Part 4 (Reporting & Overdue Detection)
**Total Test Scenarios**: 40+

---

## 🚀 Pre-Test Setup

### 1. Ensure Database is Populated
```bash
php artisan migrate:fresh
php artisan db:seed
```

### 2. Start Development Server
```bash
php artisan serve --port=8000
```

### 3. Login as Admin
```
Email: admin@example.com
Password: password
```

### 4. Verify Test Data
- Should have 5 sample items
- Should have 2 test users (admin + staff)
- Should have sample loans
- Should have stock movements

---

## ✅ Test Scenarios

### SECTION A: Dashboard Statistics

#### TEST A1: Dashboard Loads Successfully
**Goal**: Reporting dashboard displays without errors

**Steps**:
1. Login as admin
2. Navigate to Sidebar → Reporting → Reporting Dashboard
3. Page loads completely

**Expected Result**:
- [ ] Dashboard loads
- [ ] All statistics cards display
- [ ] Numbers are visible
- [ ] No errors in console

**Verification**:
- URL: `/admin/reports`
- Cards visible: 14+ (inventory, loans, returns, conditions)

---

#### TEST A2: Dashboard Statistics Accuracy - Inventory
**Goal**: Verify inventory statistics are correct

**Steps**:
1. Open dashboard
2. Note "Total Items" count
3. Count items via Items admin page
4. Compare counts

**Expected Result**:
- [ ] Count matches database
- [ ] Available stock total correct
- [ ] Low stock count accurate

**Verification**:
- Total Items = COUNT(items)
- Available Stock = SUM(available_stock)

---

#### TEST A3: Dashboard Statistics Accuracy - Loans
**Goal**: Verify loan statistics are correct

**Steps**:
1. Open dashboard
2. Note loan counts:
   - Active loans
   - Pending requests
   - Completed
   - Overdue
3. Verify manually in database

**Expected Result**:
- [ ] Active loans count correct
- [ ] Pending requests count correct
- [ ] Completed loans count correct
- [ ] Overdue count accurate

**Verification**:
- Active = Loans with status 'Dipinjam' and unreturn items
- Completed = Loans with status 'Dikembalikan'

---

#### TEST A4: Top Borrowed Items List
**Goal**: Verify top items are calculated correctly

**Steps**:
1. Open dashboard
2. Review "Top 5 Borrowed Items"
3. Verify top item has highest count

**Expected Result**:
- [ ] Top 5 items displayed
- [ ] Order is correct (highest first)
- [ ] Counts accurate

**Verification**:
- Items ordered by SUM(loan_items.quantity)

---

#### TEST A5: Staff Borrowing Frequency
**Goal**: Verify staff borrowing stats

**Steps**:
1. Open dashboard
2. Review "Top 5 Staff by Borrowing Frequency"
3. Verify top staff member

**Expected Result**:
- [ ] Top 5 staff displayed
- [ ] Order correct (most borrows first)
- [ ] Loan counts accurate

**Verification**:
- Staff ordered by COUNT(loans)

---

### SECTION B: Loan Reports

#### TEST B1: Loan Report Loads
**Goal**: Loan report page displays

**Steps**:
1. Click "Loan Reports" from sidebar
2. Page loads with table

**Expected Result**:
- [ ] Page loads successfully
- [ ] Table visible
- [ ] Filter form visible
- [ ] Export button visible

**Verification**:
- URL: `/admin/reports/loans`
- Columns: ID, Staff, Status, Items, Qty, Requested, Approved

---

#### TEST B2: Filter by Date Range
**Goal**: Date filtering works

**Steps**:
1. From Loan Reports
2. Enter From Date: 2026-01-01
3. Enter To Date: 2026-12-31
4. Click "Apply Filters"

**Expected Result**:
- [ ] Loans filtered by date range
- [ ] Only relevant loans shown
- [ ] Pagination updates

**Verification**:
- Loans outside date range not shown
- Requested date within range

---

#### TEST B3: Filter by Staff Member
**Goal**: Staff member filtering works

**Steps**:
1. From Loan Reports
2. Select a staff member from dropdown
3. Click "Apply Filters"

**Expected Result**:
- [ ] Only selected staff's loans shown
- [ ] Other staff loans hidden
- [ ] Count updated

**Verification**:
- All rows have same staff member

---

#### TEST B4: Filter by Status
**Goal**: Status filtering works

**Steps**:
1. From Loan Reports
2. Select status: "Completed"
3. Click "Apply Filters"

**Expected Result**:
- [ ] Only completed loans shown
- [ ] All other statuses hidden
- [ ] Badge shows correct status

**Verification**:
- All rows have status 'Dikembalikan'

---

#### TEST B5: Loan Report Export to CSV
**Goal**: CSV export works

**Steps**:
1. From Loan Reports (with or without filters)
2. Click "Export to CSV" button
3. File downloads

**Expected Result**:
- [ ] CSV file downloads
- [ ] Filename has timestamp
- [ ] Headers correct
- [ ] Data matches table

**Verification**:
- File: `loan_report_YYYY-MM-DD_HH-MM-SS.csv`
- Headers: Loan ID, Staff Name, Email, Status, Items, Qty, Dates

---

#### TEST B6: Loan Report Pagination
**Goal**: Pagination works

**Steps**:
1. From Loan Reports with many loans
2. Verify pagination controls visible
3. Click next page
4. Verify new loans load

**Expected Result**:
- [ ] 25 items per page
- [ ] Page numbers correct
- [ ] Next/prev buttons work
- [ ] URL updates

**Verification**:
- paginate(25) working
- Page indicator shows current page

---

### SECTION C: Inventory Reports

#### TEST C1: Inventory Report Loads
**Goal**: Inventory report page displays

**Steps**:
1. From Reporting Dashboard or Sidebar
2. Click "Inventory Reports"
3. Page loads

**Expected Result**:
- [ ] Page loads without errors
- [ ] Table shows items
- [ ] Filter form visible
- [ ] Export button available

**Verification**:
- URL: `/admin/reports/inventory`
- Shows all items initially

---

#### TEST C2: Filter by Category
**Goal**: Category filtering works

**Steps**:
1. From Inventory Reports
2. Select a category
3. Click "Apply Filters"

**Expected Result**:
- [ ] Only items from selected category shown
- [ ] Other categories hidden
- [ ] Category name correct in row

**Verification**:
- All rows have same category

---

#### TEST C3: Filter by Stock Level
**Goal**: Stock level filtering works

**Steps**:
1. From Inventory Reports
2. Select "Low Stock (1-5)"
3. Click Apply

**Expected Result**:
- [ ] Only low stock items shown
- [ ] Available stock between 1-5
- [ ] Status badge shows "Low Stock"

**Verification**:
- All items have 1 <= available_stock <= 5

---

#### TEST C4: Inventory Export to CSV
**Goal**: CSV export includes all data

**Steps**:
1. From Inventory Reports
2. Click "Export to CSV"
3. File downloads and open

**Expected Result**:
- [ ] CSV file downloads
- [ ] All columns present
- [ ] Data matches displayed table

**Verification**:
- Columns: Code, Name, Category, Location, Total, Available, Condition, Status

---

### SECTION D: Stock Movement Reports

#### TEST D1: Stock Movement Report Loads
**Goal**: Stock movement page displays

**Steps**:
1. From Reporting Dashboard
2. Click "Stock Movement"
3. Page loads

**Expected Result**:
- [ ] Page loads successfully
- [ ] Movement table visible
- [ ] Many rows (historical data)

**Verification**:
- URL: `/admin/reports/stock-movement`
- 50 items per page

---

#### TEST D2: Filter by Type
**Goal**: Type filtering works

**Steps**:
1. From Stock Movement Report
2. Select Type: "Stock In"
3. Click Apply

**Expected Result**:
- [ ] Only "In" type movements shown
- [ ] "Out" and "Adjustment" hidden
- [ ] Badge shows correct type

**Verification**:
- All rows have type = 'In'

---

#### TEST D3: Filter by Reference Type
**Goal**: Reference type filtering works

**Steps**:
1. From Stock Movement Report
2. Select Reference: "Loan"
3. Click Apply

**Expected Result**:
- [ ] Only Loan reference movements shown
- [ ] Returns/Purchase hidden
- [ ] Reference shows "Loan #X"

**Verification**:
- All references are loan-related

---

#### TEST D4: Stock Movement Export
**Goal**: Stock movement export works

**Steps**:
1. From Stock Movement Report
2. Click "Export to CSV"

**Expected Result**:
- [ ] CSV downloads
- [ ] Includes created_by user info
- [ ] Notes field populated where available

**Verification**:
- File format correct
- All data included

---

### SECTION E: Overdue Detection

#### TEST E1: Overdue Report Loads
**Goal**: Overdue report page displays

**Steps**:
1. Click "Overdue Loans" from sidebar or dashboard
2. Page loads

**Expected Result**:
- [ ] Page loads without errors
- [ ] Filters visible
- [ ] Export button available
- [ ] Alert if overdue loans exist

**Verification**:
- URL: `/admin/reports/overdue`

---

#### TEST E2: Overdue Detection Accuracy
**Goal**: Correctly identifies overdue loans

**Setup**:
1. Create loan with return date in past
2. Do NOT create return for this loan
3. Open Overdue Report

**Expected Result**:
- [ ] Loan appears in overdue list
- [ ] Days overdue calculated correctly
- [ ] Loan status is "Dipinjam"

**Verification**:
- Loan visible in table
- Days overdue = TODAY - planned_return_at

---

#### TEST E3: Exclude Completed Loans
**Goal**: Completed loans not shown as overdue

**SETUP**:
1. Create loan with past return date
2. Verify return for this loan
3. Loan status becomes "Dikembalikan"
4. Open Overdue Report

**Expected Result**:
- [ ] Loan NOT in overdue list
- [ ] Completed loans excluded
- [ ] Only "Dipinjam" status shown

**Verification**:
- Loan not visible in overdue report

---

#### TEST E4: Exclude Rejected Loans
**Goal**: Rejected loans not shown as overdue

**Setup**:
1. Create pending loan
2. Reject it
3. Status becomes "Ditolak"
4. Open Overdue Report

**Expected Result**:
- [ ] Rejected loan not in overdue list
- [ ] Only "Dipinjam" loans included

**Verification**:
- Rejected loans never appear as overdue

---

#### TEST E5: Filter by Days Overdue
**Goal**: Days overdue filtering works

**Steps**:
1. From Overdue Report
2. Select "1+ weeks" (7+ days)
3. Click Apply

**Expected Result**:
- [ ] Only loans overdue 7+ days shown
- [ ] Less than 7 days hidden
- [ ] Count updated

**Verification**:
- All loans have days_overdue >= 7

---

#### TEST E6: Overdue Export
**Goal**: Overdue report exports correctly

**Steps**:
1. From Overdue Report
2. Click "Export to CSV"

**Expected Result**:
- [ ] CSV downloads
- [ ] Includes days overdue
- [ ] Includes item details
- [ ] Data matches screen

**Verification**:
- File: `overdue_report_YYYY-MM-DD_HH-MM-SS.csv`

---

### SECTION F: Overdue Detection Command

#### TEST F1: Command Runs Successfully
**Goal**: Artisan command executes

**Steps**:
1. Open terminal
2. Run: `php artisan loans:detect-overdue`
3. Command completes

**Expected Result**:
- [ ] Command runs without error
- [ ] Statistics displayed
- [ ] Returns exit code 0

**Verification**:
- Command output shows:
  - Overdue loans detected
  - Statistics summary
  - Success message

---

#### TEST F2: Command Output Accuracy
**Goal**: Command output matches report

**Steps**:
1. Run command
2. Note overdue count
3. Open Overdue Report
4. Compare counts

**Expected Result**:
- [ ] Command count matches report
- [ ] Statistics accurate
- [ ] Loan details correct

**Verification**:
- Numbers match exactly

---

### SECTION G: Authorization & Security

#### TEST G1: Staff Cannot Access Reports
**Goal**: Staff blocked from reporting pages

**Steps**:
1. Login as staff@example.com
2. Try to access: `/admin/reports`
3. Try direct link to loan reports

**Expected Result**:
- [ ] 403 Forbidden error
- [ ] Cannot view any reports
- [ ] Sidebar doesn't show reports

**Verification**:
- Redirect to login or 403 page
- No report data accessible

---

#### TEST G2: Admin Can Access All Reports
**Goal**: Admin has full access

**Steps**:
1. Login as admin@example.com
2. Access all reporting pages:
   - Dashboard
   - Loan Reports
   - Inventory
   - Stock Movement
   - Overdue

**Expected Result**:
- [ ] All pages accessible
- [ ] All data visible
- [ ] All exports work

**Verification**:
- No 403 errors
- All pages load

---

#### TEST G3: Filter Injection Prevention
**Goal**: Filters validate input

**Steps**:
1. Manually edit URL with invalid filter
2. Example: `?category_id=9999`
3. Submit

**Expected Result**:
- [ ] Invalid filters ignored
- [ ] Page loads normally
- [ ] No errors thrown

**Verification**:
- Invalid ID returns empty results or ignored

---

### SECTION H: Performance & Scalability

#### TEST H1: Dashboard Load Time
**Goal**: Dashboard performs well

**Steps**:
1. Open dashboard
2. Measure load time
3. Repeat 3 times

**Expected Result**:
- [ ] Loads in < 2 seconds (cached)
- [ ] No timeout
- [ ] Responsive

**Verification**:
- Statistics cached for 2 hours

---

#### TEST H2: Large Report Load Time
**Goal**: Reports handle large datasets

**Steps**:
1. Generate 100+ loans (manual or seeder)
2. Open Loan Reports
3. Measure load time

**Expected Result**:
- [ ] Loads in < 3 seconds
- [ ] Pagination works
- [ ] Not too much memory

**Verification**:
- 25 items per page
- Efficient queries

---

#### TEST H3: Export Performance
**Goal**: Exports handle large datasets

**Steps**:
1. Export 50+ records to CSV
2. File generates
3. Check file size

**Expected Result**:
- [ ] Export completes < 5 seconds
- [ ] File is reasonable size
- [ ] No timeout

**Verification**:
- CSV generated successfully

---

### SECTION I: Data Accuracy

#### TEST I1: Stock Movement Totals
**Goal**: Stock movement records are accurate

**Setup**:
1. Create a loan (stock -X)
2. Create return (stock +Y based on condition)
3. Check Stock Movement Report

**Expected Result**:
- [ ] "Out" record exists (Loan)
- [ ] "In" record exists (Return)
- [ ] Quantities correct
- [ ] Reference correct

**Verification**:
- Stock movements match actions
- Quantities match loan items

---

#### TEST I2: Overdue Calculation Accuracy
**Goal**: Days overdue calculated correctly

**Setup**:
1. Create loan due 10 days ago
2. Do not return
3. Check Overdue Report

**Expected Result**:
- [ ] Days overdue = ~10
- [ ] Calculation accurate
- [ ] Color coding correct

**Verification**:
- Days overdue = TODAY - planned_return_at

---

### SECTION J: Filter Combinations

#### TEST J1: Multiple Filters Together
**Goal**: Combined filters work

**Steps**:
1. From Loan Reports
2. Set date range AND status filter
3. Apply

**Expected Result**:
- [ ] Both filters applied
- [ ] Results combined correctly
- [ ] AND logic works

**Verification**:
- Date range respected AND status matched

---

#### TEST J2: Clear Filters
**Goal**: Clearing filters works

**Steps**:
1. Apply filters
2. Remove all filter values
3. Click Apply

**Expected Result**:
- [ ] All records shown again
- [ ] Filters reset
- [ ] Table shows all data

**Verification**:
- Empty filter fields = no filter applied

---

## 📊 Test Results Summary

### Date: _______________
### Tester Name: _______________

### Results:

| Section | Test ID | Status | Issues | Notes |
|---------|---------|--------|--------|-------|
| A | A1 | [ ] PASS [ ] FAIL | | |
| A | A2 | [ ] PASS [ ] FAIL | | |
| A | A3 | [ ] PASS [ ] FAIL | | |
| A | A4 | [ ] PASS [ ] FAIL | | |
| A | A5 | [ ] PASS [ ] FAIL | | |
| B | B1 | [ ] PASS [ ] FAIL | | |
| B | B2 | [ ] PASS [ ] FAIL | | |
| B | B3 | [ ] PASS [ ] FAIL | | |
| B | B4 | [ ] PASS [ ] FAIL | | |
| B | B5 | [ ] PASS [ ] FAIL | | |
| B | B6 | [ ] PASS [ ] FAIL | | |
| C | C1 | [ ] PASS [ ] FAIL | | |
| C | C2 | [ ] PASS [ ] FAIL | | |
| C | C3 | [ ] PASS [ ] FAIL | | |
| C | C4 | [ ] PASS [ ] FAIL | | |
| D | D1 | [ ] PASS [ ] FAIL | | |
| D | D2 | [ ] PASS [ ] FAIL | | |
| D | D3 | [ ] PASS [ ] FAIL | | |
| D | D4 | [ ] PASS [ ] FAIL | | |
| E | E1 | [ ] PASS [ ] FAIL | | |
| E | E2 | [ ] PASS [ ] FAIL | | |
| E | E3 | [ ] PASS [ ] FAIL | | |
| E | E4 | [ ] PASS [ ] FAIL | | |
| E | E5 | [ ] PASS [ ] FAIL | | |
| E | E6 | [ ] PASS [ ] FAIL | | |
| F | F1 | [ ] PASS [ ] FAIL | | |
| F | F2 | [ ] PASS [ ] FAIL | | |
| G | G1 | [ ] PASS [ ] FAIL | | |
| G | G2 | [ ] PASS [ ] FAIL | | |
| G | G3 | [ ] PASS [ ] FAIL | | |
| H | H1 | [ ] PASS [ ] FAIL | | |
| H | H2 | [ ] PASS [ ] FAIL | | |
| H | H3 | [ ] PASS [ ] FAIL | | |
| I | I1 | [ ] PASS [ ] FAIL | | |
| I | I2 | [ ] PASS [ ] FAIL | | |
| J | J1 | [ ] PASS [ ] FAIL | | |
| J | J2 | [ ] PASS [ ] FAIL | | |

### Total Tests: 36
### Passed: ______
### Failed: ______
### Pass Rate: ______%

### Critical Issues Found:
1. _______________
2. _______________

### Sign-Off:
- [ ] All tests passed
- [ ] Ready for production
- [ ] Needs fixes

**Tester Signature**: _________________ **Date**: _____________

---

## 🎯 Quick Test Checklist

### Minimal Test (30 minutes)
- [ ] Dashboard loads (A1)
- [ ] Loan reports work (B1)
- [ ] Overdue detection works (E1)
- [ ] Staff blocked from access (G1)

### Standard Test (2 hours)
- Sections A, B, C, E, G

### Full Test (4-5 hours)
- All sections A through J

---

## 📝 Notes

- Use fresh database for consistent testing
- Create sample data for overdue loans
- Test with both admin and staff accounts
- Check browser console for errors
- Verify database queries are efficient

