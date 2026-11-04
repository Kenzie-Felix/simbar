# QA Testing Checklist - Add Item Button Bug Fix

**Bug ID:** #2  
**Feature:** Staff Loan Request - Add Item  
**Date:** November 4, 2026  
**Fix Status:** Ready for Testing  

---

## 📋 Pre-Testing Setup

- [ ] Environment: Development/Staging ready
- [ ] Database: Test data prepared (items with stock, staff users)
- [ ] Server: Laravel app running (`php artisan serve`)
- [ ] Browser: Modern browser with DevTools (Chrome, Firefox, Safari)
- [ ] Access: Able to login as staff user
- [ ] Documentation: Read `TESTING_LOAN_REQUEST_FIX.md`

---

## 🧪 Functional Testing

### Test 1: Page Load - Empty Container
**Steps:**
1. Login as staff user
2. Navigate to Staff → Request Loan
3. Observe the page

**Expected Result:**
- [ ] Page loads without errors
- [ ] "Items to Borrow" section empty (no rows)
- [ ] "Add Item" button visible and clickable
- [ ] Summary shows "Total Items: 0, Total Quantity: 0"

**Status:** ☐ PASS ☐ FAIL  
**Notes:** ___________________________________________

---

### Test 2: Add Single Item
**Steps:**
1. Click "Add Item" button
2. Select an item with available stock > 0
3. Observe the row and summary

**Expected Result:**
- [ ] New item row appears
- [ ] Row has: Item select (filled), Quantity input (value=1), Remove button
- [ ] Stock message shows: "X units available" (green)
- [ ] Summary updates: "Total Items: 1, Total Quantity: 1"

**Status:** ☐ PASS ☐ FAIL  
**Notes:** ___________________________________________

---

### Test 3: Change Quantity
**Steps:**
1. From Test 2, change quantity to 5
2. Observe stock message
3. Change quantity to more than available stock

**Expected Result:**
- [ ] Summary updates: "Total Items: 1, Total Quantity: 5"
- [ ] Stock message stays green if quantity ≤ available
- [ ] Stock message turns orange warning if quantity > available
- [ ] Quantity input gets red border (is-invalid) when exceeding stock

**Status:** ☐ PASS ☐ FAIL  
**Notes:** ___________________________________________

---

### Test 4: Add Multiple Items
**Steps:**
1. Click "Add Item" 3 times
2. Select different items in each row
3. Set different quantities (e.g., 2, 5, 1)

**Expected Result:**
- [ ] 3 item rows appear
- [ ] Each row has independent controls
- [ ] Summary: "Total Items: 3, Total Quantity: 8" (or correct totals)
- [ ] Each row shows correct stock message

**Status:** ☐ PASS ☐ FAIL  
**Notes:** ___________________________________________

---

### Test 5: Remove Item
**Steps:**
1. From Test 4 (3 items), click Remove on middle item
2. Click Remove on another item
3. Click Remove on last item

**Expected Result:**
- [ ] First remove: 2 rows remain, summary updates to "Total Items: 2"
- [ ] Second remove: 1 row remains, summary updates to "Total Items: 1"
- [ ] Third remove: Container empty, summary "Total Items: 0"

**Status:** ☐ PASS ☐ FAIL  
**Notes:** ___________________________________________

---

### Test 6: Stock Validation
**Steps:**
1. Add item with available stock = 5
2. Try quantity 3 (less than available)
3. Try quantity 5 (equal to available)
4. Try quantity 6 (more than available)
5. Change back to 4

**Expected Result:**
- [ ] Qty 3: Green message "5 units available"
- [ ] Qty 5: Green message "5 units available"
- [ ] Qty 6: Orange warning "Only 5 units available", red border
- [ ] Qty 4: Back to green, border removed

**Status:** ☐ PASS ☐ FAIL  
**Notes:** ___________________________________________

---

### Test 7: Form Submission - Valid Data
**Steps:**
1. Add 2 items with valid quantities
2. Fill Purpose: "Test loan request"
3. Fill Return Date: Tomorrow
4. Click "Submit Loan Request"

**Expected Result:**
- [ ] Form submits successfully
- [ ] Redirected to loan show page OR success message shown
- [ ] Loan created in database with 2 items

**Verification (Backend):**
- [ ] Database: SELECT * FROM loans WHERE user_id = [current_user] ORDER BY id DESC LIMIT 1;
- [ ] Should show new loan with status 'Pending'
- [ ] SELECT * FROM loan_items WHERE loan_id = [new_loan_id];
- [ ] Should show 2 rows with correct item_id and quantities

**Status:** ☐ PASS ☐ FAIL  
**Notes:** ___________________________________________

---

### Test 8: Form Validation - No Items
**Steps:**
1. Don't add any items
2. Fill Purpose and Return Date
3. Click "Submit Loan Request"

**Expected Result:**
- [ ] Error message shown: "Please select at least one item."
- [ ] Form stays on same page
- [ ] Data not lost (old values retained)

**Status:** ☐ PASS ☐ FAIL  
**Notes:** ___________________________________________

---

### Test 9: Form Validation - Invalid Data
**Steps:**
1. Add item with quantity exceeding available stock
2. Try to submit

**Expected Result:**
- [ ] Error message shows: "Insufficient stock for [item name]" OR warning prevents submission
- [ ] Form stays on same page
- [ ] Data retained for correction

**Status:** ☐ PASS ☐ FAIL  
**Notes:** ___________________________________________

---

### Test 10: Duplicate Item Prevention
**Steps:**
1. Add Item A in row 1, Qty 2
2. Add Item A in row 2, Qty 3
3. Try to submit

**Expected Result:**
- [ ] Error message: "Cannot request the same item multiple times."
- [ ] Form stays on same page

**Status:** ☐ PASS ☐ FAIL  
**Notes:** ___________________________________________

---

## 🔍 Browser Console Testing

### Test 11: JavaScript Errors
**Steps:**
1. Open DevTools (F12)
2. Click Console tab
3. Clear console
4. Perform all operations:
   - Add items
   - Remove items
   - Change quantities
   - Submit form

**Expected Result:**
- [ ] No red error messages in console
- [ ] No warnings related to this feature
- [ ] Console clean throughout all operations

**Status:** ☐ PASS ☐ FAIL  
**Notes:** ___________________________________________

---

### Test 12: Network Tab Verification
**Steps:**
1. Open DevTools → Network tab
2. Submit form with 2 items
3. Look at POST request

**Expected Result:**
- [ ] Request includes form data: `items[0][item_id]`, `items[0][quantity]`, etc.
- [ ] Response status 200 or 302 (redirect)
- [ ] No 400+ errors

**Status:** ☐ PASS ☐ FAIL  
**Notes:** ___________________________________________

---

## 🎨 UI/UX Testing

### Test 13: Layout & Responsiveness
**Steps:**
1. Add 5 items
2. Observe layout on desktop
3. Test on mobile/tablet (if applicable)

**Expected Result:**
- [ ] No layout breaks
- [ ] All buttons clickable
- [ ] Text readable
- [ ] Scrolling works if needed
- [ ] Mobile: Touch targets adequate (>44px)

**Status:** ☐ PASS ☐ FAIL  
**Notes:** ___________________________________________

---

### Test 14: Visual Consistency
**Steps:**
1. Compare styling with Phase 4 design system
2. Check CSS variables usage
3. Verify button styles, colors, spacing

**Expected Result:**
- [ ] Uses CSS variables (--primary, --spacing-*, --gray-*)
- [ ] Consistent with other pages
- [ ] Colors match design system
- [ ] Spacing appropriate
- [ ] Icons display correctly

**Status:** ☐ PASS ☐ FAIL  
**Notes:** ___________________________________________

---

### Test 15: Accessibility Compliance (Basic)
**Steps:**
1. Check ARIA labels on inputs
2. Verify keyboard navigation (Tab key)
3. Check color contrast (warning/success colors)

**Expected Result:**
- [ ] Inputs have associated labels
- [ ] Can navigate with keyboard
- [ ] Color contrast acceptable for readability
- [ ] Error messages have color + icon + text

**Status:** ☐ PASS ☐ FAIL  
**Notes:** ___________________________________________

---

## 🔄 Edge Cases & Regression Testing

### Test 16: Edge Case - Item Selection Change
**Steps:**
1. Add item and select Item A
2. Change dropdown to Item B
3. Verify behavior

**Expected Result:**
- [ ] Stock message updates to new item's stock
- [ ] Quantity resets or stays (depending on design)
- [ ] Summary updates

**Status:** ☐ PASS ☐ FAIL  
**Notes:** ___________________________________________

---

### Test 17: Edge Case - Zero Stock Item
**Steps:**
1. Check if items with 0 available_stock appear in dropdown
2. Try to manually select via DevTools if hidden

**Expected Result:**
- [ ] Items with 0 stock NOT in dropdown (controller filters)
- [ ] If somehow selected (hack attempt), backend rejects

**Status:** ☐ PASS ☐ FAIL  
**Notes:** ___________________________________________

---

### Test 18: Regression - Other Loan Features
**Steps:**
1. Test existing loan features (view, cancel, return, etc.)
2. Verify no breakage

**Expected Result:**
- [ ] View loan: Works normally
- [ ] Return request: Works normally
- [ ] Admin approval: Works normally
- [ ] Loan history: Works normally

**Status:** ☐ PASS ☐ FAIL  
**Notes:** ___________________________________________

---

### Test 19: Regression - Browse Items Page
**Steps:**
1. Go to Staff → Browse Items
2. Verify it works unchanged

**Expected Result:**
- [ ] Items display correctly
- [ ] Search/filter works
- [ ] No errors

**Status:** ☐ PASS ☐ FAIL  
**Notes:** ___________________________________________

---

## 📊 Test Summary

### Results Count
- Total Tests: 19
- Passed: ___
- Failed: ___
- Skipped: ___

### Test Score
Pass Rate: ___% (Passed / Total)

Required: ≥ 95% (18/19) for approval

---

## ✅ Final QA Approval

### Overall Assessment
- [ ] All critical tests passed (Tests 1-12)
- [ ] No JavaScript errors
- [ ] Form submission works
- [ ] Database updates correctly
- [ ] No regressions detected

### Approval Decision
- [ ] ✅ APPROVED - Ready for deployment
- [ ] ⚠️  CONDITIONAL - Needs fixes, list below
- [ ] ❌ REJECTED - Critical issues, see below

### Issues Found (if any)
```
1. Issue: ___________________________
   Severity: ☐ Critical ☐ Major ☐ Minor
   Fix: ____________________________

2. Issue: ___________________________
   Severity: ☐ Critical ☐ Major ☐ Minor
   Fix: ____________________________
```

---

## 📝 Tester Information

**Tester Name:** ___________________________  
**Date Tested:** ___________________________  
**Browser/Version:** ________________________  
**Environment:** ☐ Dev ☐ Staging ☐ Production  
**Database:** ☐ Fresh ☐ Existing data  

**Signature:** ___________________________

---

## 📧 Sign-Off

**QA Lead:** ___________________________  
**Date:** ___________________________  
**Status:** ☐ APPROVED ☐ REJECTED ☐ PENDING  

**Notes:** 
```
_________________________________________
_________________________________________
_________________________________________
```

---

## 🔗 Related Documents

- Testing Guide: `TESTING_LOAN_REQUEST_FIX.md`
- Bug Report: `BUG_FIX_LOAN_REQUEST_ADD_ITEM.md`
- Before/After: `BUG_FIX_BEFORE_AFTER.md`
- Code Changes: `resources/views/staff/loans/create.blade.php`

---

**End of QA Testing Checklist**
