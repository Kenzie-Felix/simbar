# SIMBAR English Localization - Testing Guide

## Quick Test Checklist

Use this guide to manually verify the English localization and dashboard redesign.

---

## 🧪 TEST 1: Login Page Verification

**File**: `resources/views/auth/login.blade.php`

**Steps**:
1. Navigate to login page (`/login`)
2. Verify page title shows "Login - SIMBAR"
3. Check subtitle: "Office Inventory Management System"
4. Verify form labels in English:
   - "Email Address" (not "Alamat Email")
   - "Password" (not "Kata Sandi")
5. Check button text: "Login" (not "Masuk")
6. Verify error message format in English

**Expected Result**: ✅ All text is in English

---

## 🧪 TEST 2: Admin Dashboard - Grid Layout

**File**: `resources/views/admin/dashboard.blade.php`

**Desktop Test (1400px+ width)**:
1. Login as Admin user
2. Navigate to Admin Dashboard
3. **Verify Title**: "Admin Dashboard - SIMBAR"
4. **Check Key Metrics Section** (6 stat cards):
   - Total Items | Available Stock
   - Active Loans | Pending Approval
   - Pending Verification | Overdue Loans
   - Should display in auto-fit layout (3+ columns typically)

5. **Critical: Check Inventory Status Section** (4 stat cards):
   - Should display in **2 COLUMNS × 2 ROWS** layout
   - Row 1: Low Stock Items | Out of Stock
   - Row 2: Categories | Locations
   - Each card should be equal size
   - Spacing between cards should be uniform

6. **Verify Stock Movement is NOT visible**:
   - Should NOT see "Stock Movement" card on dashboard
   - Should NOT see "Stock Movement" anywhere on page

7. **Check Recent Activity Section**:
   - Table headers: User | Action | Entity | Time (all English)

**Mobile Test (≤768px width)**:
1. Resize browser to mobile size or use DevTools
2. Inventory Status cards should switch to **1 COLUMN** layout
3. Cards should stack vertically
4. All text should remain readable

**Expected Result**: ✅ 2x2 grid on desktop, 1 column on mobile, no Stock Movement visible

---

## 🧪 TEST 3: Staff Dashboard - Grid Layout

**File**: `resources/views/staff/dashboard.blade.php`

**Desktop Test (1400px+ width)**:
1. Login as Staff user
2. Navigate to Staff Dashboard
3. **Verify Title**: "Staff Dashboard - SIMBAR"
4. **Verify Subtitle**: "Welcome back [Name] • [Role]"
5. **Check Statistics Grid** (4 stat cards):
   - Should display in **2 COLUMNS × 2 ROWS** layout
   - Row 1: Active Loans | Overdue Loans
   - Row 2: Available Items | Total Borrowed
   - All 4 cards should be equal size
   - Spacing uniform between all cards

6. **Check Quick Action Buttons** (4 buttons):
   - "View Items"
   - "Request Loan"
   - "Return Item"
   - "My Loans"
   - All buttons should be in English

7. **Check Active Loans Section**:
   - Table headers: Item Name | Qty | Return Date | Status | Action
   - Status badges: "OVERDUE", "DUE SOON", "On Time", "Borrowed"

**Mobile Test (≤768px width)**:
1. Resize browser to mobile size
2. Stats grid cards should switch to **1 COLUMN** layout
3. Cards stack vertically
4. Action buttons should be readable and tappable

**Expected Result**: ✅ 2x2 grid on desktop, 1 column on mobile

---

## 🧪 TEST 4: Navigation Elements

**File**: `resources/views/layouts/navbar.blade.php` & `resources/views/layouts/sidebar.blade.php`

**Navbar Test**:
1. Check top right user menu
2. Verify text: "Dashboard", "Logout", "Role", "Staff Member" (all English)
3. User dropdown should show current role

**Sidebar Test (Admin)**:
1. Look for these sections (all in English):
   - Dashboard
   - Inventory (with: Items, Categories, Locations)
   - Loans (with: Pending Approval, All Loans)
   - Returns (with: Pending Verification, All Returns)
   - Reports (with: Dashboard, Loan Reports, Inventory Reports, Overdue Loans)

2. **Important**: Stock Management link should NOT be visible
3. Stock Movement should NOT appear in Reports > Stock Management

**Sidebar Test (Staff)**:
1. Should see: Dashboard, Loans, Returns sections
2. No Stock Movement should appear

**Expected Result**: ✅ All navigation in English, Stock Movement hidden

---

## 🧪 TEST 5: Items Management Page

**File**: `resources/views/admin/items/index.blade.php`

**Steps**:
1. Navigate to Admin > Inventory > Items
2. **Verify Title**: "Manage Items - SIMBAR"
3. **Verify Subtitle**: "Manage inventory items"
4. **Check Button**: "Add Item" (not "Tambah Barang")
5. **Check Table Headers** (all in English):
   - Code | Name | Category | Location
   - Total Stock | Available | Condition | Action
6. **Check Action Buttons**:
   - "View Details" / "View"
   - "Edit"
   - "Delete"
7. **Check Empty State**:
   - If no items: "No items found"
   - "Create an item to start managing your inventory"

**Expected Result**: ✅ All text is in English

---

## 🧪 TEST 6: Loan Management Page

**File**: `resources/views/admin/loans/index.blade.php`

**Steps**:
1. Navigate to Admin > Loans > Pending Approval
2. **Verify Title**: "Loan Approval - SIMBAR"
3. **Verify Subtitle**: "Review and manage pending loan requests"
4. **Check Statistics Cards**:
   - "Pending" (not "Menunggu Persetujuan")
   - "Approved" (not "Disetujui")
   - "Rejected" (not "Ditolak")
   - "Currently Borrowed"
5. **Check Table Headers**:
   - Request | Staff Member | Items | Qty
   - Purpose | Requested | Status | Action
6. **Check Status Badges**:
   - "Pending Approval"
   - "Approved"
   - "Rejected"
   - "Borrowed"
7. **Check Filter Section**:
   - "Staff Name or Email"
   - "Status" dropdown
   - "Filter" button (not "Cari")

**Expected Result**: ✅ All text is in English

---

## 🧪 TEST 7: Staff Loan Request Page

**File**: `resources/views/staff/loans/create.blade.php` & `resources/views/staff/loans/index.blade.php`

**Create Loan Test**:
1. Navigate to Staff > Loans > Request Loan
2. **Verify Title**: "Request Loan - SIMBAR" (not "Permintaan Peminjaman")
3. **Check Form Sections**:
   - "Items to Borrow" (not "Barang yang Dipinjam")
   - "Loan Details" (not "Rincian Peminjaman")
4. **Check Form Fields**:
   - "Purpose of Loan"
   - "Expected Return Date"
5. **Check Buttons**:
   - "Submit Loan Request" (not "Ajukan")
   - "Cancel" (not "Batalkan")

**My Loans List Test**:
1. Navigate to Staff > Loans > My Loans
2. **Verify Title**: "My Loans - SIMBAR"
3. **Check Filter Pills**:
   - "All" | "Pending" | "Approved" | etc.
4. **Check Table Headers**:
   - Requested | Items | Qty | Purpose
   - Return Date | Status | Action
5. **Check Empty State**:
   - "You have not submitted any loan requests yet"

**Expected Result**: ✅ All text is in English

---

## 🧪 TEST 8: Return Management Page

**File**: `resources/views/admin/returns/index.blade.php`

**Steps**:
1. Navigate to Admin > Returns > Pending Verification
2. **Verify Title**: "Return Verification - SIMBAR"
3. **Check Statistics**:
   - "Pending" (not "Menunggu Verifikasi")
   - "Accepted" (not "Terima")
   - "Rejected" (not "Tolak")
4. **Check Table Headers**:
   - Return ID | Staff Member | Items | Qty
   - Submitted | Status | Action
5. **Check Buttons**:
   - "Review" (not "Tinjau")
   - Status badges in English

**Expected Result**: ✅ All text is in English

---

## 🧪 TEST 9: Categories & Locations Pages

**File**: 
- `resources/views/admin/categories/index.blade.php`
- `resources/views/admin/locations/index.blade.php`

**Categories Test**:
1. Navigate to Admin > Inventory > Categories
2. **Verify Title**: "Manage Categories - SIMBAR"
3. **Check Button**: "Add Category" (not "Tambah Kategori")
4. **Check Table Headers**: Name | Description | Items | Created | Action
5. **Check Empty State**: "No categories found"

**Locations Test**:
1. Navigate to Admin > Inventory > Locations
2. **Verify Title**: "Manage Locations - SIMBAR"
3. **Check Button**: "Add Location" (not "Tambah Lokasi")
4. **Check Table Headers**: Name | Description | Items | Created | Action
5. **Check Empty State**: "No locations found"

**Expected Result**: ✅ All text is in English

---

## 🧪 TEST 10: Responsive Design

**Steps**:
1. For each page tested above, resize browser window
2. **At 1400px+ (Desktop)**: Full layout with 2x2 grids
3. **At 768-1399px (Tablet)**: Grids adapt but remain 2 columns
4. **At <768px (Mobile)**:
   - Grids switch to 1 column
   - Navigation collapses
   - Buttons remain clickable
   - Text remains readable
   - No horizontal scrolling

**Expected Result**: ✅ Responsive design works at all breakpoints

---

## ✅ Test Summary

After completing all 10 tests above, you should see:

| Aspect | Status |
|--------|--------|
| Login page in English | ✅ |
| Admin Dashboard 2x2 grid | ✅ |
| Staff Dashboard 2x2 grid | ✅ |
| Navigation in English | ✅ |
| No Stock Movement visible | ✅ |
| CRUD pages in English | ✅ |
| Loan pages in English | ✅ |
| Return pages in English | ✅ |
| Responsive on mobile | ✅ |
| All buttons/labels English | ✅ |

---

## 🐛 If You Find Issues

| Issue | Solution |
|-------|----------|
| Still see Indonesian text | Clear browser cache (Ctrl+Shift+Del) |
| Grid not 2x2 | Refresh page, check browser zoom is 100% |
| Stock Movement appears | Check localStorage for old settings |
| Mobile layout broken | Test in actual mobile browser, not zoom |
| Button not working | Check JavaScript console for errors |

---

## 📸 Screenshots to Verify

- [ ] Admin Dashboard with 2x2 Inventory grid
- [ ] Staff Dashboard with 2x2 stats grid
- [ ] Admin Dashboard WITHOUT Stock Movement card
- [ ] Login page showing English text
- [ ] Sidebar showing English menu items
- [ ] Mobile view with 1-column grids

---

## ✨ Sign-Off Checklist

After testing, verify:
- [ ] All pages display in English
- [ ] Dashboard grids are 2x2 on desktop
- [ ] Dashboard grids are 1 column on mobile
- [ ] Stock Movement is not visible
- [ ] All buttons are clickable
- [ ] No console errors
- [ ] No broken links
- [ ] All CRUD operations work
- [ ] Responsive design works
- [ ] Ready for production

---

**Testing Date**: _______________
**Tester Name**: _______________
**Status**: ✅ PASS / ❌ FAIL / ⚠️ PARTIAL

---

**If all tests pass, the system is ready for production deployment!**
