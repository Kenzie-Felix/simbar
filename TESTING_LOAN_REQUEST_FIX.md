# 🧪 Testing Guide - Loan Request Add Item Fix

**Quick Start Testing untuk Bug Fix**

---

## ⚡ Quick Verification (2-3 minutes)

### Step 1: Start the App
```bash
php artisan serve
```
Access: http://localhost:8000

### Step 2: Login as Staff
- Email: `staff@example.com` (atau staff user apapun)
- Password: (sesuaikan dengan data di database)

### Step 3: Navigate to Request Loan
- Menu: Staff → Request Loan
- URL: `/staff/loans/create`

### Step 4: Quick Functional Test
1. **Page loads - Items container empty?** ✓ = PASS
2. **Click "Add Item" - row appears?** ✓ = PASS
3. **Select item - stock message shows?** ✓ = PASS
4. **Change quantity - message updates?** ✓ = PASS
5. **Click Remove - row disappears?** ✓ = PASS
6. **Fill form & submit - success?** ✓ = PASS

---

## 🔧 Detailed Testing Protocol

### Prerequisites:
- [ ] Ensure you have test data (items, loans)
- [ ] Have at least 1 staff user account
- [ ] Browser DevTools ready (F12)

---

## Test Scenario 1: Single Item Addition

**Objective:** Verify that adding a single item works end-to-end

**Steps:**
1. Open Request Loan page
2. Observe container is empty (no auto-generated row)
3. Click "Add Item" button
   - **Check:** Item row appears with:
     - [ ] Item select dropdown (empty default)
     - [ ] Quantity input (value="1")
     - [ ] Remove button
     - [ ] Hidden stock alert
4. Select an item with available stock > 0
   - **Check:** Stock alert shows "X units available" (green)
   - **Check:** Summary shows "Total Items: 1, Total Quantity: 1"
5. Change quantity to 3
   - **Check:** Summary shows "Total Items: 1, Total Quantity: 3"
   - **Check:** Stock alert updates if quantity exceeds available

**Result:** ✓ PASS / ✗ FAIL

---

## Test Scenario 2: Multiple Items Addition

**Objective:** Verify that multiple items can be added

**Steps:**
1. Start from empty Request Loan page
2. Click "Add Item" 3 times
   - **Check:** 3 item rows appear
3. In Row 1: Select Item A, Qty 2
   - **Check:** Summary: "Total Items: 1, Total Quantity: 2"
4. In Row 2: Select Item B, Qty 5
   - **Check:** Summary: "Total Items: 2, Total Quantity: 7"
5. In Row 3: Select Item C, Qty 1
   - **Check:** Summary: "Total Items: 3, Total Quantity: 8"
6. Change Row 2 quantity to 10
   - **Check:** Summary: "Total Items: 3, Total Quantity: 13"
   - **Check:** Stock alert in Row 2 shows warning (if exceeds available)

**Result:** ✓ PASS / ✗ FAIL

---

## Test Scenario 3: Item Removal

**Objective:** Verify that items can be properly removed

**Steps:**
1. Add 3 items (same as Scenario 2)
2. Summary shows: "Total Items: 3"
3. Click Remove on Row 2
   - **Check:** Row 2 disappears
   - **Check:** Row 1 and Row 3 remain intact
   - **Check:** Summary updates to "Total Items: 2"
4. Click Remove on Row 1
   - **Check:** Summary updates to "Total Items: 1"
5. Click Remove on remaining row
   - **Check:** Container becomes empty
   - **Check:** Summary shows "Total Items: 0, Total Quantity: 0"

**Result:** ✓ PASS / ✗ FAIL

---

## Test Scenario 4: Stock Validation

**Objective:** Verify that stock availability is properly checked and warned

**Steps:**
1. Add an item with known available stock (e.g., 5 units)
2. Quantity 3 (less than available)
   - **Check:** Alert shows green "5 units available"
3. Quantity 5 (equal to available)
   - **Check:** Alert shows green "5 units available"
4. Quantity 6 (more than available)
   - **Check:** Alert shows orange warning "Only 5 units available"
   - **Check:** Quantity input has red border (is-invalid class)
5. Change back to 4
   - **Check:** Alert back to green
   - **Check:** Red border removed

**Result:** ✓ PASS / ✗ FAIL

---

## Test Scenario 5: Form Submission

**Objective:** Verify that form submits correctly with all items

**Steps:**
1. Add 2 items:
   - Item A, Qty 2
   - Item B, Qty 3
2. Fill Loan Details:
   - Purpose: "Testing loan request form"
   - Return Date: Tomorrow's date
3. Click "Submit Loan Request"
   - **Check:** Page redirects to loan show page OR displays success message
   - **Check:** Loan request created in database
   - **Check:** Loan has 2 items with correct quantities

**Backend Verification:**
```bash
# Check database
php artisan tinker
>>> Loan::latest()->first()->loanItems()->get()
```

Expected output shows 2 loan items with correct quantities.

**Result:** ✓ PASS / ✗ FAIL

---

## Test Scenario 6: Validation Errors

**Objective:** Verify that validation catches errors

**Steps:**

### Test 6a: No Items
1. Don't add any items
2. Fill Purpose and Return Date
3. Click "Submit Loan Request"
   - **Check:** Error message: "Please select at least one item."
   - **Check:** Form stays on same page

### Test 6b: Invalid Return Date
1. Add 1 item
2. Fill Purpose
3. Try to select a date in the past
   - **Check:** Date input doesn't allow selection (browser constraint)
   - **Check:** If bypassed, backend validation catches it

### Test 6c: Empty Item Select
1. Add item row but don't select anything
2. Fill other fields
3. Submit
   - **Check:** Error message about item selection

### Test 6d: Invalid Quantity
1. Add item and select it
2. Try to enter 0 or negative quantity
   - **Check:** Input prevents it (min="1" attribute)

**Result:** ✓ PASS / ✗ FAIL

---

## Test Scenario 7: Browser Console Check

**Objective:** Verify no JavaScript errors occur

**Steps:**
1. Open Request Loan page
2. Press F12 to open DevTools
3. Click "Console" tab
4. Check for any red errors
   - **Check:** No JavaScript errors
   - **Check:** No console warnings related to this feature
5. Perform all operations:
   - Add items
   - Remove items
   - Change quantities
   - Submit form
6. Console remains clean
   - **Check:** No errors appear during any operation

**Result:** ✓ PASS / ✗ FAIL

---

## Test Scenario 8: UI/UX Verification

**Objective:** Verify UI is clean and responsive

**Steps:**
1. Open Request Loan page
   - **Check:** Layout is clean
   - **Check:** Items container is empty (no ghost row)
   - **Check:** "Add Item" button is visible and clickable
2. Add 5 items
   - **Check:** Layout doesn't break
   - **Check:** All rows are visible (may need scroll)
   - **Check:** Remove buttons are accessible
3. Check on mobile (if applicable)
   - **Check:** Layout responsive
   - **Check:** Buttons clickable on mobile
   - **Check:** Text readable

**Result:** ✓ PASS / ✗ FAIL

---

## Test Scenario 9: Edge Cases

**Objective:** Test unusual but valid scenarios

### Edge Case 1: Item Selection Change
1. Add item and select Item A (5 available)
2. Change dropdown to Item B (10 available)
   - **Check:** Stock message updates to "10 units available"
   - **Check:** Quantity stays same (or resets appropriately)

### Edge Case 2: Same Item Multiple Times (Should not work)
1. Add 2 items
2. Select same item in both rows
3. Submit
   - **Check:** Backend validation rejects: "Cannot request the same item multiple times"

### Edge Case 3: Item with Zero Stock
1. Check if items with 0 stock appear in dropdown
   - **Check:** Should NOT appear (controller filters `available_stock > 0`)
2. If somehow selected (API hack), backend should reject

**Result:** ✓ PASS / ✗ FAIL

---

## 🎯 Final Checklist

Before declaring bug FIXED:

- [ ] All 9 test scenarios PASS
- [ ] No JavaScript errors in console
- [ ] Form data structure correct (items[INDEX][...])
- [ ] Backend receives and processes data
- [ ] Database updates correctly
- [ ] No regressions in other loan features
- [ ] UI is clean and responsive
- [ ] Stock validation works
- [ ] Duplicate item prevention works

---

## 📊 Test Result Summary

| Test Scenario | Status | Notes |
|---|---|---|
| 1. Single Item | ✓ PASS / ✗ FAIL | _______ |
| 2. Multiple Items | ✓ PASS / ✗ FAIL | _______ |
| 3. Item Removal | ✓ PASS / ✗ FAIL | _______ |
| 4. Stock Validation | ✓ PASS / ✗ FAIL | _______ |
| 5. Form Submission | ✓ PASS / ✗ FAIL | _______ |
| 6. Validation Errors | ✓ PASS / ✗ FAIL | _______ |
| 7. Console Check | ✓ PASS / ✗ FAIL | _______ |
| 8. UI/UX Check | ✓ PASS / ✗ FAIL | _______ |
| 9. Edge Cases | ✓ PASS / ✗ FAIL | _______ |

**Overall Status:** 
- All Pass? → ✅ **BUG FIXED - Ready for Production**
- Some Fail? → ❌ **Further Investigation Needed**

---

## 🐛 If Tests Fail

### Common Issues & Solutions:

**Issue:** Items row doesn't appear when clicking "Add Item"
- Check: Browser console for errors (F12 → Console)
- Check: `#itemRowTemplate` exists in HTML (F12 → Elements)
- Check: `#itemsContainer` div exists
- Check: CSS doesn't have `display: none` on container

**Issue:** Summary doesn't update
- Check: `updateSummary()` function exists
- Check: `.item-row` elements are correctly targeted
- Check: Item select has `value` attribute set

**Issue:** Stock alert doesn't show
- Check: Item option has `data-stock` attribute
- Check: Alert box has `alert-stock` class
- Check: CSS for `.alert-stock` exists

**Issue:** Remove button doesn't work
- Check: `.remove-item-btn` selector matches button
- Check: Event listener attached correctly
- Check: No JavaScript errors in console

---

## 📧 Report Template

When reporting test results:

```
Bug Fix Verification Report
Date: [TODAY]
Tester: [NAME]
Status: [PASS / FAIL]

Test Results:
- Scenario 1: [PASS/FAIL] [notes]
- Scenario 2: [PASS/FAIL] [notes]
...

Issues Found:
[List any issues]

Screenshots: [Attach if any issues]
```

---

**End of Testing Guide**  
Last Updated: November 4, 2026
