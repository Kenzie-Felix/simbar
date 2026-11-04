# TESTING GUIDE - Phase 3 Part 3: Return Management System

## 📋 Overview

This document provides comprehensive testing instructions for Phase 3 Part 3: Return Management System in SIMBAR.

**Test Date**: November 4, 2026
**Phase**: 3 Part 3 (Return Management)
**Test Environment**: Local Development

---

## 🚀 Pre-Test Setup

### 1. Verify Database State
```bash
# Ensure fresh database
php artisan migrate:fresh

# Seed with test data
php artisan db:seed

# Verify test users exist
php artisan tinker
>>> App\Models\User::where('email', 'admin@example.com')->first()
>>> App\Models\User::where('email', 'staff@example.com')->first()
```

### 2. Start Development Server
```bash
# Terminal 1: Start Laravel server
php artisan serve --port=8000

# Terminal 2: Run tests (optional)
php artisan test
```

### 3. Access Application
```
Admin: http://localhost:8000/login
Email: admin@example.com
Password: password

Staff: http://localhost:8000/login
Email: staff@example.com
Password: password
```

---

## ✅ Test Scenarios

### SECTION A: Staff Return Request Submission

#### TEST A1: View My Loans
**Goal**: Staff can see their approved/borrowed loans

**Steps**:
1. Login as staff@example.com
2. Navigate to "My Loans" (Sidebar → Request Return → My Returns)
3. Click "Request Return" button

**Expected Result**:
- [ ] Create form loads successfully
- [ ] Dropdown shows approved/borrowed loans
- [ ] Each loan shows: ID, total items, total quantity
- [ ] Status shown for each loan

**Verification**:
- Loans list includes only approved/borrowed loans
- Loans don't include rejected/pending loans
- User isolation verified (only own loans shown)

---

#### TEST A2: Submit Return Request - Single Item
**Goal**: Staff can submit return for one item

**Steps**:
1. From create form, select a loan (e.g., Loan #1)
2. Loan details appear automatically
3. Click "Add Item to Return"
4. Select first item (e.g., "Projector")
5. Enter quantity: 1
6. Select condition: "Good" (Baik)
7. Enter notes: "Projector works fine"
8. Click "Submit Return Request"

**Expected Result**:
- [ ] Form validates successfully
- [ ] Return request created
- [ ] Redirect to return details page
- [ ] Status shows "Pending Verification"
- [ ] Success message displayed
- [ ] Return ID visible

**Verification**:
- Return saved in database
- Status = "Menunggu Verifikasi"
- User ID matches current staff member
- submitted_at timestamp recorded

---

#### TEST A3: Submit Return Request - Multiple Items
**Goal**: Staff can return multiple items from one loan

**Steps**:
1. Select loan with multiple borrowed items
2. Click "Add Item to Return" multiple times
3. Add 2-3 different items with:
   - Item 1: Qty 1, Condition: "Good"
   - Item 2: Qty 2, Condition: "Minor Damage"
   - Item 3: Qty 1, Condition: "Damaged"
4. Enter return notes: "Multi-item return test"
5. Submit form

**Expected Result**:
- [ ] All items added successfully
- [ ] Return request created
- [ ] All 3 items appear in details page
- [ ] Quantities correct
- [ ] Conditions recorded

**Verification**:
- 3 ReturnItem records created
- Each has correct quantity and condition
- Return summary shows all items

---

#### TEST A4: Submit Return Request - Partial Return
**Goal**: Staff can return less than borrowed quantity

**Steps**:
1. Select loan where item borrowed in quantity: 5
2. Click "Add Item to Return"
3. Select that item
4. Enter quantity: 3 (less than 5)
5. Submit form

**Expected Result**:
- [ ] Form accepts quantity < borrowed
- [ ] Return created successfully
- [ ] Quantity shows: 3 out of 5
- [ ] Loan still marked as borrowed (not fully returned)

**Verification**:
- ReturnItem.quantity = 3
- LoanItem still has unfullfilled return potential
- Loan status unchanged

---

#### TEST A5: Validation - Exceed Borrowed Quantity
**Goal**: System prevents returning more than borrowed

**Steps**:
1. Select loan with item borrowed: 2
2. Add item to return
3. Enter quantity: 5 (exceeds borrowed)
4. Try to submit

**Expected Result**:
- [ ] Form submission blocked
- [ ] Error message: "Cannot return more X than borrowed"
- [ ] Form not submitted
- [ ] User can correct and resubmit

**Verification**:
- Error caught on server-side
- Quantity overflow prevented

---

#### TEST A6: Validation - No Items Selected
**Goal**: System requires at least one item

**Steps**:
1. Select a loan
2. Don't add any items
3. Click "Submit Return Request"

**Expected Result**:
- [ ] Form rejected
- [ ] Error: "Please select at least one item"
- [ ] Form not submitted

**Verification**:
- Validation rule enforced (min:1)

---

#### TEST A7: View My Returns - Status Filtering
**Goal**: Staff can filter return requests by status

**Steps**:
1. Navigate to "My Returns" (Sidebar)
2. Create 3 returns (will all be pending initially)
3. Filter dropdown: "Pending Verification"
4. Only pending returns shown
5. Switch filter to "Accepted"
6. List is empty (none accepted yet)

**Expected Result**:
- [ ] Filter controls appear
- [ ] Filtering works by status
- [ ] Correct returns displayed
- [ ] Pagination works if many returns

**Verification**:
- Only returns with selected status shown
- Empty state if no matching returns
- Correct counts

---

#### TEST A8: View Return Details - Submitted Status
**Goal**: Staff can view details of submitted return

**Steps**:
1. Create a return request
2. Click on the return in list
3. View return details page

**Expected Result**:
- [ ] Return ID displayed
- [ ] Submitted date/time shown
- [ ] Items table shows all returned items
- [ ] Quantities and conditions shown
- [ ] Status badge: "Pending Verification"
- [ ] Notes displayed

**Verification**:
- All information accurate
- No admin verification info shown yet
- Status correct

---

#### TEST A9: Return Authorization Check
**Goal**: Staff can only see their own returns

**Steps**:
1. Create 2 different staff users (if not available)
2. Staff A: Create return request
3. Staff B: Try to access Staff A's return URL directly
   - URL pattern: `/staff/returns/{return_id}`

**Expected Result**:
- [ ] Staff B gets 403 Forbidden error
- [ ] Cannot view other staff's returns
- [ ] User isolation enforced

**Verification**:
- Authorization check working
- User ID validation on queries

---

#### TEST A10: AJAX Loan Details Loading
**Goal**: Form dynamically loads item details

**Steps**:
1. Open return create form
2. Select different loans from dropdown
3. Observe items table updates in real-time

**Expected Result**:
- [ ] AJAX request sent
- [ ] Items table populates
- [ ] Shows: Code, Name, Borrowed Qty, Returned Qty, Remaining
- [ ] Remaining = Borrowed - Previously Returned
- [ ] No page reload

**Verification**:
- AJAX endpoint working
- Data correct and fresh
- UI updates smoothly

---

### SECTION B: Admin Return Verification

#### TEST B1: View All Returns Dashboard
**Goal**: Admin can see all return requests

**Steps**:
1. Login as admin@example.com
2. Navigate to "Pending Verifications" (Sidebar)
3. Dashboard loads

**Expected Result**:
- [ ] Statistics cards visible:
  - Pending Verifications count
  - Accepted Returns count
  - Rejected Returns count
- [ ] Returns table shows all returns
- [ ] Search and filter controls visible
- [ ] Pagination working (20 per page)

**Verification**:
- Statistics counts accurate
- All returns listed
- Admin-only access enforced

---

#### TEST B2: Filter Returns by Status
**Goal**: Admin can filter returns by verification status

**Steps**:
1. From returns dashboard
2. Status dropdown: Select "Pending Verification"
3. Only pending returns shown
4. Switch to "Accepted"
5. Only accepted returns shown

**Expected Result**:
- [ ] Filtering works correctly
- [ ] Counts update
- [ ] List reflects filter
- [ ] Empty state if no matches

**Verification**:
- Filtering query correct
- Counts accurate

---

#### TEST B3: Search by Staff Member
**Goal**: Admin can search returns by staff name/email

**Steps**:
1. From returns dashboard
2. Search box: Enter staff name (e.g., "John")
3. Only returns from that staff shown
4. Search box: Clear, Enter email (e.g., "staff@")
5. Returns by that email domain shown

**Expected Result**:
- [ ] Search works by name
- [ ] Search works by email
- [ ] Results filter correctly
- [ ] Partial matches work

**Verification**:
- Search SQL correct
- Case-insensitive matching

---

#### TEST B4: View Return Details for Verification
**Goal**: Admin can review complete return details

**Steps**:
1. From returns dashboard
2. Click "Review" on any pending return
3. Details page loads

**Expected Result**:
- [ ] Staff information displayed
- [ ] Return information (date, status)
- [ ] Returned items table shows:
  - Item code and name
  - Quantity returned
  - Condition specified
  - Staff notes
- [ ] Accept/Reject buttons visible
- [ ] No verification info shown yet (not processed)

**Verification**:
- All data loaded correctly
- Related data populated
- Action buttons present

---

#### TEST B5: Verify Return - Accept Request
**Goal**: Admin can accept return and restore stock

**Steps**:
1. Note current stock level (for testing item)
   - Example: Item "Projector" has available_stock = 3
2. View pending return with Projector (qty: 1, condition: Good)
3. Click "Accept Return" button
4. Optional: Add verification notes
5. Confirm action

**Expected Result**:
- [ ] Return status changes to "Accepted"
- [ ] Verified by admin ID recorded
- [ ] Verified timestamp recorded
- [ ] Success message shown
- [ ] Stock increased:
  - Projector available_stock = 4 (was 3)
- [ ] Return details page updates
- [ ] Verification info visible (Admin name, Date)

**Verification**:
- Return.status = "Terima"
- Return.verified_by_id = admin_id
- Item.available_stock incremented
- StockMovement record created
- LoanItem.returned_at updated

---

#### TEST B6: Verify Return - Damaged Item Condition
**Goal**: Damaged items don't restore stock

**Steps**:
1. Note current stock: Item "Drill Machine" = 2 available
2. Submit return: Drill (qty: 1, condition: "Damaged" - Rusak Berat)
3. Admin verifies return
4. Check stock after verification

**Expected Result**:
- [ ] Return marked as "Accepted"
- [ ] Stock NOT increased (remains 2)
- [ ] Item condition updated to "Damaged"
- [ ] StockMovement record shows quantity change
- [ ] Item becomes unavailable for borrowing

**Verification**:
- Stock logic: No increment for Rusak Berat/Hilang
- Item.available_stock = 2 (unchanged)
- Condition log created

---

#### TEST B7: Verify Return - Lost Item Condition
**Goal**: Lost items marked as lost, stock not restored

**Steps**:
1. Note stock: Item "USB Cables" = 10 available
2. Submit return: USB (qty: 3, condition: "Lost" - Hilang)
3. Admin verifies return
4. Check item and stock

**Expected Result**:
- [ ] Return accepted
- [ ] Stock NOT increased (remains 10)
- [ ] Item marked as "Lost"
- [ ] Item unavailable
- [ ] Audit trail recorded

**Verification**:
- Lost items prevent stock restoration
- Condition updated correctly

---

#### TEST B8: Verify Return - Mixed Conditions
**Goal**: Partial stock restoration with multiple conditions

**Steps**:
1. Submit return with 3 items:
   - Item A: Qty 2, Condition "Good" → Should restore
   - Item B: Qty 1, Condition "Minor Damage" → Should restore
   - Item C: Qty 1, Condition "Damaged" → Should NOT restore
2. Note stock before: A=3, B=5, C=8
3. Admin verifies
4. Check stock after

**Expected Result**:
- [ ] Item A: 3 + 2 = 5 available
- [ ] Item B: 5 + 1 = 6 available
- [ ] Item C: 8 (unchanged)
- [ ] 3 StockMovement records created
- [ ] 2 for successful restorations
- [ ] 1 for tracking lost item

**Verification**:
- Conditional logic working
- Stock calculations correct

---

#### TEST B9: Verify Return - Auto-Complete Loan
**Goal**: Loan marked as returned when all items verified

**Steps**:
1. Create loan with 2 items: Projector (qty 1), Chair (qty 1)
2. Submit return for ONLY Projector
3. Verify Projector return ✓
4. Check loan status → Should still be "Borrowed"
5. Submit return for Chair
6. Verify Chair return ✓
7. Check loan status → Should be "Returned" (Dikembalikan)

**Expected Result**:
- [ ] After first verification: Loan status = "Borrowed"
- [ ] After second verification: Loan status = "Returned"
- [ ] System detects all items returned
- [ ] Loan auto-complete works

**Verification**:
- Loan status updated correctly
- All items checked for returned_at
- Loan.status = "Dikembalikan"

---

#### TEST B10: Reject Return - With Reason
**Goal**: Admin can reject return requests

**Steps**:
1. View pending return
2. Click "Reject" button
3. Rejection form opens
4. Enter reason: "Items do not match inventory records"
5. Submit rejection

**Expected Result**:
- [ ] Return status changes to "Rejected"
- [ ] Rejection reason saved
- [ ] Verified by admin ID recorded
- [ ] Verified timestamp recorded
- [ ] Success message shown
- [ ] Stock NOT changed
- [ ] Staff can see rejection reason

**Verification**:
- Return.status = "Tolak"
- Return.rejection_reason saved
- Stock unchanged
- LoanItem.returned_at NOT updated

---

#### TEST B11: Reject Return - Quick Preset Reason
**Goal**: Admin can use preset rejection reasons

**Steps**:
1. View pending return
2. Click "Reject"
3. See quick presets: "Items damaged", "Quantity mismatch", etc.
4. Click "Quantity mismatch" preset
5. Reason auto-populated
6. Submit

**Expected Result**:
- [ ] Preset reason appears in textarea
- [ ] Admin can submit immediately
- [ ] Reason saved correctly

**Verification**:
- Preset system working
- Reason saved as submitted

---

#### TEST B12: Prevent Duplicate Verification
**Goal**: Cannot verify already processed return

**Steps**:
1. Verify a return ✓
2. Stay on details page
3. Click "Accept Return" again
4. OR manually POST to verify endpoint again

**Expected Result**:
- [ ] Button disabled/not shown after verification
- [ ] If POST attempted: Error "Already been Accepted"
- [ ] No duplicate processing
- [ ] Stock not affected

**Verification**:
- Status check prevents duplicate processing
- Idempotent operation

---

#### TEST B13: CSV Export - Pending Returns
**Goal**: Admin can export pending returns to CSV

**Steps**:
1. From returns dashboard
2. Click "Export to CSV" button
3. File downloads: pending_returns_YYYY-MM-DD_HH-MM-SS.csv
4. Open CSV file

**Expected Result**:
- [ ] File downloaded
- [ ] Filename has timestamp
- [ ] CSV has headers: ID, Staff Name, Email, Items Count, Total Qty, Date
- [ ] Each row: Return ID, Staff info, Item count, Total quantity, Date
- [ ] Only pending returns included
- [ ] Data matches dashboard

**Verification**:
- CSV generation working
- Data accurate
- File format correct

---

#### TEST B14: Authorization Check - Staff Cannot Access
**Goal**: Staff blocked from admin verification

**Steps**:
1. Login as staff@example.com
2. Try to access: `/admin/returns`
3. Try direct URL to return: `/admin/returns/1`

**Expected Result**:
- [ ] Both return 403 Forbidden
- [ ] Cannot see admin panel
- [ ] Redirected appropriately
- [ ] Error message shown

**Verification**:
- Admin middleware working
- Staff blocked correctly

---

#### TEST B15: Database Transaction - Rollback on Error
**Goal**: Partial updates prevented via transactions

**Steps**:
1. Create scenario where verification would fail
   (Difficult to simulate naturally - skip or manual test)
2. Monitor database logs
3. Verify no partial updates

**Expected Result**:
- [ ] On error: Complete rollback
- [ ] No partial stock changes
- [ ] Return status unchanged
- [ ] Error message displayed

**Verification**:
- DB::beginTransaction() working
- DB::rollBack() on exception
- Atomic operations

---

### SECTION C: Stock Management Accuracy

#### TEST C1: Stock Tracking - Initial State
**Goal**: Verify stock levels before any operations

**Steps**:
1. Open database or check via API
2. View items table
3. Note total_stock and available_stock for each item

**Expected Result**:
- [ ] total_stock = initial count
- [ ] available_stock = unreserved items
- [ ] available_stock ≤ total_stock
- [ ] Both > 0 for active items

**Verification**:
- Stock initialized correctly
- Relationship makes sense

---

#### TEST C2: Stock Impact - Loan Approval
**Goal**: Stock decremented on loan approval

**Steps**:
1. Note: Projector available_stock = 5
2. Create loan request: Projector qty 2
3. Admin approves loan
4. Check stock again

**Expected Result**:
- [ ] available_stock = 3 (was 5 - 2)
- [ ] total_stock unchanged (5)
- [ ] StockMovement record shows "Out" type

**Verification**:
- Stock movement accurate
- Phase 3 Part 2 integration working
- Decrement correct

---

#### TEST C3: Stock Impact - Return Verification (Good)
**Goal**: Stock incremented on good return

**Steps**:
1. After loan approval (from C2): available_stock = 3
2. Staff returns Projector (qty 2, condition: Good)
3. Admin verifies return
4. Check stock

**Expected Result**:
- [ ] available_stock = 5 (was 3 + 2)
- [ ] Back to original level
- [ ] StockMovement shows "In" type
- [ ] Reference: Return ID

**Verification**:
- Stock restored correctly
- Cycles through: Approve → Decrement → Verify → Increment

---

#### TEST C4: Stock Impact - Multiple Items per Loan
**Goal**: Stock accurate with multi-item loans

**Steps**:
1. Loan: Projector (qty 2) + Chair (qty 3)
2. Note stock: Projector = 5, Chair = 10
3. Approve loan → Projector = 3, Chair = 7
4. Return both items, verify → Projector = 5, Chair = 10

**Expected Result**:
- [ ] Each item tracked independently
- [ ] All increments/decrements correct
- [ ] No mixing between items

**Verification**:
- Multi-item math accurate
- Individual item tracking

---

#### TEST C5: Stock Impact - Negative Prevention
**Goal**: Prevent stock from going negative

**Steps**:
1. Item: Projector, available = 2
2. Try to approve loan: Projector qty 5 (exceeds available)

**Expected Result**:
- [ ] Approval blocked
- [ ] Error shown
- [ ] Stock remains 2

**Verification**:
- Validation prevents approval
- Stock never negative

---

#### TEST C6: Stock Accuracy - After Multiple Cycles
**Goal**: Stock accurate through multiple transactions

**Steps**:
1. Create 5 loans for same item
2. Approve all 5 → Stock decremented 5x
3. Return and verify 3 of them
4. Reject 1 return (stock doesn't change)
5. Return and verify remaining

**Expected Result**:
- [ ] All stock changes tracked
- [ ] Final available_stock = initial - (2 approved & not returned)
- [ ] Audit trail complete

**Verification**:
- StockMovement records match operations
- Math is correct throughout

---

### SECTION D: Workflow Integration

#### TEST D1: Complete Workflow - Happy Path
**Goal**: Full cycle from request to completion

**Steps**:
1. **Staff creates loan**: 2 Projectors
2. **Admin approves**: Stock decremented
3. **Time passes** (loan period)
4. **Staff submits return**: 2 Projectors, Good condition
5. **Admin verifies**: Stock restored, Loan completed

**Expected Result**:
- [ ] Each step succeeds
- [ ] Status updates properly
- [ ] Stock accurate throughout
- [ ] Loan marked "Returned"
- [ ] Return marked "Accepted"

**Verification**:
- Full workflow functional
- All models updated
- No errors

---

#### TEST D2: Workflow - Partial Return
**Goal**: Partial returns and multiple return requests

**Steps**:
1. Staff loans 5 items
2. Returns 2 items in Request #1 → Verified
3. Still owes 3 items
4. Returns 2 more in Request #2 → Verified
5. Still owes 1 item
6. Check loan status: Should still be "Borrowed"

**Expected Result**:
- [ ] Loan remains "Borrowed" after partial returns
- [ ] Only marked "Returned" when all returned
- [ ] Multiple return requests per loan work

**Verification**:
- Loan status logic correct
- Multiple returns handled

---

#### TEST D3: Workflow - Damaged Items
**Goal**: Damaged items handled correctly

**Steps**:
1. Loan: 5 Drills
2. Return 2 Good, 1 Damaged, 1 Lost, 1 Missing
3. Only 2 restored to stock
4. Check item conditions
5. Check loan status

**Expected Result**:
- [ ] Stock: +2 only
- [ ] Conditions updated
- [ ] Loan marked "Returned" (all accounted for)
- [ ] Audit trail complete

**Verification**:
- Conditional logic working
- Item condition tracking

---

### SECTION E: Data Integrity

#### TEST E1: User Isolation - Staff Data
**Goal**: Staff A cannot see Staff B's returns

**Steps**:
1. Staff A creates return
2. Staff B logs in
3. Staff B navigates to own returns list
4. Staff B tries direct URL: `/staff/returns/{staffA_return_id}`

**Expected Result**:
- [ ] Staff B's list shows only own returns
- [ ] Direct URL access: 403 Forbidden
- [ ] No cross-staff data leakage

**Verification**:
- user_id filtering working
- Authorization enforced

---

#### TEST E2: Referential Integrity
**Goal**: Database relationships maintained

**Steps**:
1. Create return
2. Check database:
   - return.loan_id exists in loans
   - return.verified_by_id exists in users (or null)
   - return_items.return_id references correct return
   - return_items.loan_item_id references correct item

**Expected Result**:
- [ ] All foreign keys valid
- [ ] No orphaned records
- [ ] Cascading delete if needed

**Verification**:
- Referential integrity maintained

---

#### TEST E3: Timestamp Accuracy
**Goal**: All timestamps recorded correctly

**Steps**:
1. Create return at time T1
2. Verify return at time T2
3. Check database timestamps

**Expected Result**:
- [ ] submitted_at = T1
- [ ] verified_at = T2
- [ ] Timestamps timezone-aware
- [ ] Timezone consistent

**Verification**:
- Timestamps correct
- Timezone handling proper

---

### SECTION F: Performance & Scalability

#### TEST F1: Dashboard Load Time
**Goal**: Admin dashboard performs well

**Steps**:
1. Create 100+ return records
2. Navigate to admin returns dashboard
3. Measure load time
4. Try filtering/search

**Expected Result**:
- [ ] Dashboard loads in < 2 seconds
- [ ] Filtering responsive
- [ ] Pagination works
- [ ] No timeout errors

**Verification**:
- Performance acceptable
- Pagination prevents slow queries

---

#### TEST F2: CSV Export with Large Data
**Goal**: Export handles many records

**Steps**:
1. Create 50+ pending returns
2. Click "Export to CSV"
3. Verify file downloads
4. Check file size and content

**Expected Result**:
- [ ] Export completes quickly
- [ ] No timeout
- [ ] File size reasonable
- [ ] All records included

**Verification**:
- Bulk export working
- Memory usage acceptable

---

### SECTION G: Error Handling

#### TEST G1: Invalid Return ID
**Goal**: System handles non-existent returns

**Steps**:
1. Navigate to: `/staff/returns/99999`
2. Try: `/admin/returns/99999`

**Expected Result**:
- [ ] 404 Not Found error
- [ ] Friendly error message
- [ ] No SQL errors exposed
- [ ] Redirect to appropriate page

**Verification**:
- Error handling complete
- No information leakage

---

#### TEST G2: Missing Required Fields
**Goal**: Validation catches missing data

**Steps**:
1. Submit return without:
   - No loan selected
   - No items added
   - No conditions selected
2. Verify each error separately

**Expected Result**:
- [ ] Each validation error caught
- [ ] Specific error messages
- [ ] Form re-displays with errors
- [ ] Data retained for correction

**Verification**:
- Validation comprehensive
- UX helpful

---

#### TEST G3: Concurrent Verification Attempts
**Goal**: Race conditions prevented

**Steps**:
1. Open return in 2 browser windows
2. Verify return in Window 1
3. Try to verify same return in Window 2

**Expected Result**:
- [ ] First verification succeeds
- [ ] Second returns error: "Already verified"
- [ ] Stock changed only once

**Verification**:
- Concurrency handled
- Idempotent operations

---

## 📊 Test Results Summary

### Date: _______________

### Tester Name: _______________

### Test Environment:
- [ ] PHP Version: __________
- [ ] Laravel Version: __________
- [ ] Database: MySQL
- [ ] Browser: __________

### Results:

| Section | Test ID | Status | Issues | Notes |
|---------|---------|--------|--------|-------|
| A | A1 | [ ] PASS [ ] FAIL | | |
| A | A2 | [ ] PASS [ ] FAIL | | |
| A | A3 | [ ] PASS [ ] FAIL | | |
| A | A4 | [ ] PASS [ ] FAIL | | |
| A | A5 | [ ] PASS [ ] FAIL | | |
| A | A6 | [ ] PASS [ ] FAIL | | |
| A | A7 | [ ] PASS [ ] FAIL | | |
| A | A8 | [ ] PASS [ ] FAIL | | |
| A | A9 | [ ] PASS [ ] FAIL | | |
| A | A10 | [ ] PASS [ ] FAIL | | |
| B | B1 | [ ] PASS [ ] FAIL | | |
| B | B2 | [ ] PASS [ ] FAIL | | |
| B | B3 | [ ] PASS [ ] FAIL | | |
| B | B4 | [ ] PASS [ ] FAIL | | |
| B | B5 | [ ] PASS [ ] FAIL | | |
| B | B6 | [ ] PASS [ ] FAIL | | |
| B | B7 | [ ] PASS [ ] FAIL | | |
| B | B8 | [ ] PASS [ ] FAIL | | |
| B | B9 | [ ] PASS [ ] FAIL | | |
| B | B10 | [ ] PASS [ ] FAIL | | |
| B | B11 | [ ] PASS [ ] FAIL | | |
| B | B12 | [ ] PASS [ ] FAIL | | |
| B | B13 | [ ] PASS [ ] FAIL | | |
| B | B14 | [ ] PASS [ ] FAIL | | |
| B | B15 | [ ] PASS [ ] FAIL | | |
| C | C1 | [ ] PASS [ ] FAIL | | |
| C | C2 | [ ] PASS [ ] FAIL | | |
| C | C3 | [ ] PASS [ ] FAIL | | |
| C | C4 | [ ] PASS [ ] FAIL | | |
| C | C5 | [ ] PASS [ ] FAIL | | |
| C | C6 | [ ] PASS [ ] FAIL | | |
| D | D1 | [ ] PASS [ ] FAIL | | |
| D | D2 | [ ] PASS [ ] FAIL | | |
| D | D3 | [ ] PASS [ ] FAIL | | |
| E | E1 | [ ] PASS [ ] FAIL | | |
| E | E2 | [ ] PASS [ ] FAIL | | |
| E | E3 | [ ] PASS [ ] FAIL | | |
| F | F1 | [ ] PASS [ ] FAIL | | |
| F | F2 | [ ] PASS [ ] FAIL | | |
| G | G1 | [ ] PASS [ ] FAIL | | |
| G | G2 | [ ] PASS [ ] FAIL | | |
| G | G3 | [ ] PASS [ ] FAIL | | |

### Total Tests: 40
### Passed: ______
### Failed: ______
### Pass Rate: ______%

### Critical Issues Found:
1. _______________
2. _______________
3. _______________

### Minor Issues Found:
1. _______________
2. _______________

### Recommendations:
_______________________________________________________________________________

### Sign-Off:
- [ ] All tests passed
- [ ] Ready for production
- [ ] Needs fixes before deployment

**Tester Signature**: _________________ **Date**: _____________

**Lead Reviewer**: _________________ **Date**: _____________

---

## 🎯 Quick Test Checklist

### Minimal Test (15 minutes)
- [ ] Staff can create return (A2)
- [ ] Admin can verify return (B5)
- [ ] Stock restored correctly (C3)
- [ ] Authorization working (A9, B14)

### Standard Test (1 hour)
- Sections A, B, C, D, E

### Full Test (2-3 hours)
- All sections A through G

---

## 📝 Notes

- Keep test data consistent
- Document any issues with screenshots
- Save test results for compliance
- Test on multiple browsers if possible
- Clear browser cache between tests

