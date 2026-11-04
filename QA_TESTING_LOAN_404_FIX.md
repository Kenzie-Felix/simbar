# QA Testing - Bug #4: Loan Approval 404 Fix

**Bug:** Admin Loan Approval returns 404 Not Found  
**Fix:** Route ordering in routes/web.php  
**Date:** November 4, 2026  

---

## ✅ Pre-Testing

- [ ] Environment ready
- [ ] Laravel app running
- [ ] Browser DevTools ready (F12)
- [ ] Admin user account available
- [ ] Pending loans exist in database

---

## 🧪 CRITICAL TESTS (Must Pass)

### Test 1: Loan Approval - No 404 Error

**Objective:** Verify POST to approve route works (main bug fix)

**Steps:**
1. Login as admin
2. Go to Admin → Loans
3. Click "Review" on pending loan
4. Observe URL: `/admin/loans/1` (show page)
5. Click "Approve Request" button
6. Check result

**Expected:**
- [ ] Page doesn't show 404
- [ ] No "404 Not Found" error
- [ ] Form submits successfully
- [ ] Page processes approval
- [ ] Success message: "Loan request #X approved"

**Result:** ☐ PASS ☐ FAIL  
**Notes:** _______________________

---

### Test 2: Database - Stock Decremented

**Objective:** Verify stock deduction happened

**Setup:** Before approval
```sql
SELECT available_stock FROM items WHERE id = 1;
-- Note current stock: ___
```

**Steps:**
1. Approve loan with 3 units of item 1
2. Query database again

**After approval:**
```sql
SELECT available_stock FROM items WHERE id = 1;
-- Stock should be 3 less: ___
```

**Expected:**
- [ ] Stock decreased by exactly 3
- [ ] Not 0 decrease (bug = no change)
- [ ] Not 6 decrease (bug = double deduction)

**Result:** ☐ PASS ☐ FAIL  
**Initial Stock:** ___  
**Final Stock:** ___  
**Difference:** ___  

---

### Test 3: Stock Movement Record

**Objective:** Verify transaction created record

**Query:**
```sql
SELECT id, item_id, type, quantity, reference_type, reference_id
FROM stock_movements
WHERE reference_type = 'Loan' AND reference_id = 1
ORDER BY created_at DESC
LIMIT 1;
```

**Expected:**
- [ ] Record exists
- [ ] type = 'Out'
- [ ] quantity = requested amount
- [ ] reference_type = 'Loan'
- [ ] Only 1 record (not duplicated)

**Result:** ☐ PASS ☐ FAIL  
**Record ID:** ___  
**Quantity:** ___  

---

### Test 4: Loan Status Updated

**Objective:** Verify loan status changed

**Query:**
```sql
SELECT id, status, approved_at, approved_by_id
FROM loans
WHERE id = 1;
```

**Expected:**
- [ ] status = 'Dipinjam' (Borrowed)
- [ ] approved_at = now() (recent timestamp)
- [ ] approved_by_id = current admin user

**Result:** ☐ PASS ☐ FAIL  
**Status:** ___  
**Approved By:** ___  

---

## 🔍 FUNCTIONAL TESTS

### Test 5: Multiple Loan Approval

**Objective:** Verify sequential approvals work

**Steps:**
1. Approve loan 1
2. Go back to loans list
3. Approve loan 2
4. Go back to loans list
5. Approve loan 3

**Expected:**
- [ ] All 3 approvals successful
- [ ] No 404 on any
- [ ] Each creates own stock movement
- [ ] All stocks decremented correctly

**Result:** ☐ PASS ☐ FAIL  
**Loans Approved:** ___ / 3  

---

### Test 6: Bulk Approve

**Objective:** Verify bulk approval works

**Steps:**
1. Select 2-3 pending loans
2. Click "Bulk Approve Selected"
3. Confirm dialog
4. Check results

**Expected:**
- [ ] Bulk approval form submits
- [ ] No 404 error
- [ ] All selected loans approved
- [ ] Success shows count: "2 approved"

**Result:** ☐ PASS ☐ FAIL  
**Loans Selected:** ___  
**Loans Approved:** ___  

---

### Test 7: Reject Functionality

**Objective:** Verify reject form works

**Steps:**
1. Click "Review" on pending loan
2. Click "Reject Request" button
3. Check URL and page

**Expected:**
- [ ] No 404 error
- [ ] Reject form page loads
- [ ] URL: `/admin/loans/X/reject`
- [ ] Can enter reason and submit

**Result:** ☐ PASS ☐ FAIL  

---

### Test 8: Returns Verification

**Objective:** Verify same fix applied to returns

**Steps:**
1. Go to Admin → Returns
2. Click on pending return
3. Click "Verify" button

**Expected:**
- [ ] No 404 error
- [ ] Form submits
- [ ] Return verified

**Result:** ☐ PASS ☐ FAIL  

---

## 🔧 TECHNICAL TESTS

### Test 9: Route List

**Objective:** Verify route order in Laravel

**Terminal:**
```bash
php artisan route:list | grep -A 5 "loans"
```

**Expected Output Order:**
```
GET    admin/loans
POST   admin/loans/bulk/approve    ← Specific
GET    admin/loans/api/stats        ← Specific
GET    admin/loans/export/pending   ← Specific
POST   admin/loans/{loan}/approve   ← Generic (must be after specifics)
GET    admin/loans/{loan}/reject    ← Generic
POST   admin/loans/{loan}/reject    ← Generic
GET    admin/loans/{loan}           ← Generic (most general, last)
```

**Expected:**
- [ ] Specific routes listed before generic routes
- [ ] `{loan}/approve` before `{loan}` show route
- [ ] Order matches fixed routes/web.php

**Result:** ☐ PASS ☐ FAIL  

---

### Test 10: Console Errors

**Objective:** Verify no JavaScript errors

**Steps:**
1. Open DevTools (F12)
2. Go to Console tab
3. Clear console
4. Approve loan
5. Check console

**Expected:**
- [ ] No red error messages
- [ ] No 404 errors logged
- [ ] Console clean

**Result:** ☐ PASS ☐ FAIL  

---

### Test 11: Network Tab

**Objective:** Verify POST request succeeds

**Steps:**
1. Open DevTools → Network
2. Approve loan
3. Look at POST request

**Expected:**
- [ ] Request URL: `/admin/loans/1/approve`
- [ ] Status: 302 (redirect) or 200
- [ ] NOT 404
- [ ] Response shows success redirect

**Result:** ☐ PASS ☐ FAIL  
**Status Code:** ___  

---

## 🔒 TRANSACTION TESTS

### Test 12: Rollback on Error

**Objective:** Verify transaction rollback works

**Setup:** Manually set item stock to 0

**Steps:**
1. Approve loan that requests 5 units
2. (Insufficient stock should trigger error)
3. Check database

**Expected:**
- [ ] Error message shown
- [ ] Stock still 0 (not changed)
- [ ] Loan status still 'Pending'
- [ ] No stock movement created

**Result:** ☐ PASS ☐ FAIL  

---

## 📊 Summary

### Test Results

| # | Test | Result | Pass |
|---|------|--------|------|
| 1 | No 404 Error | ☐ ☐ | ☐ |
| 2 | Stock Decremented | ☐ ☐ | ☐ |
| 3 | Movement Record | ☐ ☐ | ☐ |
| 4 | Status Updated | ☐ ☐ | ☐ |
| 5 | Multiple Approval | ☐ ☐ | ☐ |
| 6 | Bulk Approve | ☐ ☐ | ☐ |
| 7 | Reject Form | ☐ ☐ | ☐ |
| 8 | Returns Fixed | ☐ ☐ | ☐ |
| 9 | Route Order | ☐ ☐ | ☐ |
| 10 | No Console Errors | ☐ ☐ | ☐ |
| 11 | Network Status | ☐ ☐ | ☐ |
| 12 | Transaction Rollback | ☐ ☐ | ☐ |

**Total Passed:** ___ / 12  
**Pass Rate:** ___%  

**Required:** ≥ 11 / 12 (92%) for approval

---

## ✅ Approval

**QA Tester:** _____________________  
**Date:** _____________________  
**Status:** ☐ APPROVED ☐ NEEDS FIXES ☐ REJECTED  

**Issues Found (if any):**
```
1. _________________________________
2. _________________________________
```

**Comments:**
```
_________________________________________
_________________________________________
```

---

**END OF CHECKLIST**
