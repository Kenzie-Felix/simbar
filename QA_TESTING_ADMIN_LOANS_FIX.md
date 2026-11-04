# QA Testing Checklist - Admin Loans Class Error Fix

**Bug ID:** #3  
**Feature:** Admin → Loans (Approval Management)  
**Error Fixed:** Class "Loan" not found  
**Date:** November 4, 2026  

---

## ✅ Pre-Testing Setup

- [ ] Environment: Development/Staging ready
- [ ] Server: Laravel app running
- [ ] Browser: Modern browser with DevTools
- [ ] Access: Admin user account available
- [ ] Database: Has pending loan requests
- [ ] Files deployed: Both PHP files updated

---

## 🧪 Core Functionality Tests

### Test 1: Page Load
**Objective:** Verify page loads without class error

**Steps:**
1. Login as admin
2. Navigate to Admin → Loans
3. Check browser console

**Expected Result:**
- [ ] Page loads (no error)
- [ ] Console clean (no red errors)
- [ ] Page title shows "Loan Approvals"
- [ ] Stats cards visible (Pending, Approved, Rejected, Borrowed)

**Status:** ☐ PASS ☐ FAIL  
**Notes:** _____________________

---

### Test 2: Stats Display
**Objective:** Verify stats counts are correct

**Steps:**
1. On Admin Loans page
2. Check each stat card

**Expected Result:**
- [ ] Pending count correct (matches database)
- [ ] Approved count correct
- [ ] Rejected count correct
- [ ] Borrowed count correct
- [ ] Stats update after any action

**Status:** ☐ PASS ☐ FAIL  
**Notes:** _____________________

---

### Test 3: Loans Table Display
**Objective:** Verify table renders with all loan data

**Steps:**
1. Look at loans table
2. Verify columns and data

**Expected Result:**
- [ ] Table headers visible (ID, Staff, Items, Qty, Purpose, etc.)
- [ ] Loan data displays correctly
- [ ] Status badges show correct color (yellow/green/red/blue)
- [ ] Request date formatted correctly
- [ ] Pagination works if > 20 loans

**Status:** ☐ PASS ☐ FAIL  
**Notes:** _____________________

---

### Test 4: Filter by Status
**Objective:** Verify status filter works

**Steps:**
1. Click Status dropdown filter
2. Select "Pending Approval"
3. Click Filter button

**Expected Result:**
- [ ] Only pending loans display
- [ ] Count matches pending stat
- [ ] Clear filter button shows
- [ ] No error

**Repeat for:** Approved, Rejected, Borrowed, Returned  
**Status:** ☐ PASS ☐ FAIL  
**Notes:** _____________________

---

### Test 5: Search by Staff
**Objective:** Verify search functionality

**Steps:**
1. Enter staff name in search box
2. Click Filter

**Expected Result:**
- [ ] Only matching loans display
- [ ] Search works by partial name
- [ ] Search works by email
- [ ] Clear filter works
- [ ] No error

**Status:** ☐ PASS ☐ FAIL  
**Notes:** _____________________

---

### Test 6: Bulk Actions Visibility
**Objective:** Verify bulk approve section shows correctly

**Steps:**
1. When pending loans exist, check bottom
2. When no pending loans, check visibility

**Expected Result:**
- [ ] Bulk actions visible ONLY when pending loans exist
- [ ] Checkbox counter updates correctly
- [ ] "Bulk Approve Selected" button exists
- [ ] Button disabled when 0 selected
- [ ] Button enabled when ≥1 selected

**Status:** ☐ PASS ☐ FAIL  
**Notes:** _____________________

---

### Test 7: Single Loan Approval
**Objective:** Verify single loan approval process

**Steps:**
1. Click "Review" on a pending loan
2. Click "Approve" button
3. Confirm if prompted

**Expected Result:**
- [ ] Loan details page loads (no error)
- [ ] Approve button visible
- [ ] Click triggers approval
- [ ] Success message shows
- [ ] Redirects to loans list
- [ ] Loan status changed to "Borrowed" (Dipinjam)
- [ ] Approval timestamp recorded

**Status:** ☐ PASS ☐ FAIL  
**Notes:** _____________________

---

### Test 8: Single Loan Rejection
**Objective:** Verify single loan rejection process

**Steps:**
1. Click "Review" on a pending loan
2. Click "Reject" button
3. Enter rejection reason
4. Submit

**Expected Result:**
- [ ] Rejection form displays
- [ ] Reason field shows
- [ ] Can submit with reason
- [ ] Success message shows
- [ ] Status changed to "Rejected" (Ditolak)
- [ ] Rejection reason saved

**Status:** ☐ PASS ☐ FAIL  
**Notes:** _____________________

---

### Test 9: Bulk Approve Selected
**Objective:** Verify bulk approval works

**Steps:**
1. Check 2-3 pending loans using checkboxes
2. Watch counter update
3. Click "Bulk Approve Selected"
4. Confirm in dialog

**Expected Result:**
- [ ] Checkboxes work
- [ ] Counter updates (e.g., "Selected: 3 loans")
- [ ] Approval dialog shows
- [ ] Shows count in confirmation
- [ ] All selected loans approved
- [ ] Success message shows count

**Status:** ☐ PASS ☐ FAIL  
**Notes:** _____________________

---

### Test 10: Select All Checkbox
**Objective:** Verify select all functionality

**Steps:**
1. Click "Select all" header checkbox
2. All loans should be selected
3. Uncheck header checkbox

**Expected Result:**
- [ ] All visible loans selected
- [ ] Counter shows total
- [ ] Uncheck deselects all
- [ ] Approve button disabled when none selected

**Status:** ☐ PASS ☐ FAIL  
**Notes:** _____________________

---

## 🗄️ Database Verification Tests

### Test 11: Stock Deduction on Approval
**Objective:** Verify stock decrements correctly

**Setup:**
- Known item with available_stock = 10
- Request loan with qty = 3

**Steps:**
1. Approve the loan request
2. Query database

**SQL to Check:**
```sql
SELECT available_stock FROM items WHERE id = [item_id];
```

**Expected Result:**
- [ ] Stock was 10, now 7 (10 - 3)
- [ ] Stock correctly decremented
- [ ] No error during approval

**Status:** ☐ PASS ☐ FAIL  
**Notes:** _____________________

---

### Test 12: Loan Status Update
**Objective:** Verify loan record updated

**Steps:**
1. Approve a loan
2. Check loan record

**SQL to Check:**
```sql
SELECT id, status, approved_at, approved_by_id FROM loans 
WHERE id = [loan_id];
```

**Expected Result:**
- [ ] Status = 'Dipinjam' (Borrowed)
- [ ] approved_at = timestamp of approval
- [ ] approved_by_id = current admin user ID

**Status:** ☐ PASS ☐ FAIL  
**Notes:** _____________________

---

### Test 13: Rejection Doesn't Decrement Stock
**Objective:** Verify rejected loans don't decrement stock

**Setup:**
- Known item with available_stock = 10
- Pending loan with qty = 3

**Steps:**
1. Reject the loan
2. Check stock

**SQL to Check:**
```sql
SELECT available_stock FROM items WHERE id = [item_id];
```

**Expected Result:**
- [ ] Stock still 10 (unchanged)
- [ ] Stock NOT decremented for rejected loans

**Status:** ☐ PASS ☐ FAIL  
**Notes:** _____________________

---

## 🔍 Browser Console Tests

### Test 14: No JavaScript Errors
**Objective:** Verify no console errors

**Steps:**
1. Open DevTools (F12)
2. Go to Console tab
3. Refresh Admin Loans page
4. Perform all operations

**Expected Result:**
- [ ] No red error messages
- [ ] No warnings related to this feature
- [ ] Console clean throughout

**Status:** ☐ PASS ☐ FAIL  
**Notes:** _____________________

---

### Test 15: Network Tab Verification
**Objective:** Verify no failed requests

**Steps:**
1. Open DevTools → Network tab
2. Perform approval/rejection
3. Check requests

**Expected Result:**
- [ ] All requests status 200/201/302
- [ ] No 404 errors
- [ ] No 500 errors
- [ ] JSON responses valid

**Status:** ☐ PASS ☐ FAIL  
**Notes:** _____________________

---

## 🚀 Edge Cases & Error Handling

### Test 16: No Pending Loans
**Objective:** Verify page works with no pending loans

**Steps:**
1. Approve/reject all pending loans
2. Navigate to Admin Loans
3. Check display

**Expected Result:**
- [ ] Page loads (no error)
- [ ] Loan table empty or shows other statuses
- [ ] Bulk actions NOT visible
- [ ] Empty state message if applicable

**Status:** ☐ PASS ☐ FAIL  
**Notes:** _____________________

---

### Test 17: High Volume (Pagination)
**Objective:** Verify pagination works

**Steps:**
1. If > 20 loans exist
2. Check pagination controls
3. Navigate pages

**Expected Result:**
- [ ] Pagination shows page numbers
- [ ] Can navigate between pages
- [ ] Correct loans on each page
- [ ] No error

**Status:** ☐ PASS ☐ FAIL  
**Notes:** _____________________

---

### Test 18: Concurrent Approvals
**Objective:** Verify no race conditions

**Steps:**
1. Open two browsers (same admin)
2. Approve same loan from both
3. Check result

**Expected Result:**
- [ ] One succeeds
- [ ] Other gets appropriate error/redirect
- [ ] Stock correct
- [ ] No double-decrement

**Status:** ☐ PASS ☐ FAIL  
**Notes:** _____________________

---

### Test 19: Insufficient Stock
**Objective:** Verify error when stock insufficient

**Setup:**
- Item with available_stock = 2
- Loan request qty = 5

**Steps:**
1. Try to approve
2. Check error

**Expected Result:**
- [ ] Error shown: "Insufficient stock"
- [ ] Approval prevented
- [ ] Stock unchanged

**Status:** ☐ PASS ☐ FAIL  
**Notes:** _____________________

---

## 📊 Test Summary

| Test # | Test Name | Status | Notes |
|--------|-----------|--------|-------|
| 1 | Page Load | ☐ PASS ☐ FAIL | |
| 2 | Stats Display | ☐ PASS ☐ FAIL | |
| 3 | Table Display | ☐ PASS ☐ FAIL | |
| 4 | Filter by Status | ☐ PASS ☐ FAIL | |
| 5 | Search by Staff | ☐ PASS ☐ FAIL | |
| 6 | Bulk Actions | ☐ PASS ☐ FAIL | |
| 7 | Single Approval | ☐ PASS ☐ FAIL | |
| 8 | Single Rejection | ☐ PASS ☐ FAIL | |
| 9 | Bulk Approve | ☐ PASS ☐ FAIL | |
| 10 | Select All | ☐ PASS ☐ FAIL | |
| 11 | Stock Deduction | ☐ PASS ☐ FAIL | |
| 12 | Loan Status | ☐ PASS ☐ FAIL | |
| 13 | Rejection Stock | ☐ PASS ☐ FAIL | |
| 14 | Console Errors | ☐ PASS ☐ FAIL | |
| 15 | Network Tab | ☐ PASS ☐ FAIL | |
| 16 | No Pending | ☐ PASS ☐ FAIL | |
| 17 | Pagination | ☐ PASS ☐ FAIL | |
| 18 | Concurrent | ☐ PASS ☐ FAIL | |
| 19 | Low Stock | ☐ PASS ☐ FAIL | |

**Total Passed:** ___ / 19  
**Pass Rate:** ___%  

**Required:** ≥ 95% (18/19) for approval

---

## ✅ Sign-Off

**QA Lead Signature:** _____________________  
**Date:** _____________________  
**Status:** ☐ APPROVED ☐ NEEDS FIXES ☐ REJECTED  

**Issues Found (if any):**
```
1. Issue: _________________________________
   Severity: ☐ Critical ☐ Major ☐ Minor
   
2. Issue: _________________________________
   Severity: ☐ Critical ☐ Major ☐ Minor
```

**Comments:**
```
_____________________________________________
_____________________________________________
_____________________________________________
```

---

**Testing Checklist Complete**
