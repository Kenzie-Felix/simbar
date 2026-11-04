# Phase 4 Frontend Redesign - Developer Guide

**Quick Reference for Continuing Phase 4 Redesign**

---

## Project Structure

```
iventaris-kantor/
├── resources/
│   ├── css/
│   │   ├── design-system.css    ← Design variables & components
│   │   └── app.css              ← Imports design-system.css
│   ├── js/
│   │   └── app.js
│   └── views/
│       ├── admin/
│       │   ├── categories/       ✅ DONE
│       │   ├── items/            ✅ DONE
│       │   ├── locations/        ✅ DONE
│       │   ├── loans/            ✅ DONE
│       │   ├── reports/          ⏳ TODO (5 files)
│       │   ├── returns/          ✅ DONE
│       │   └── stock/            ⏳ TODO (2 files)
│       ├── auth/                 ✅ DONE
│       ├── layouts/              ✅ DONE
│       └── staff/                ⏳ TODO (6 files)
├── public/
│   └── build/                   ← Vite compiled assets
└── vite.config.js               ← Vite configuration
```

---

## Build Commands

```bash
# Development (with HMR)
npm run dev

# Production build
npm run build

# Verify assets generated
ls -la public/build/
```

---

## Design System Reference

### CSS Variables (Use These!)

```css
/* Colors */
var(--primary)              /* #2563eb - Main blue */
var(--primary-dark)         /* #1d4ed8 */
var(--primary-light)        /* #eff6ff */

var(--secondary)            /* #64748b - Slate */
var(--secondary-light)      /* #f1f5f9 */

var(--success)              /* #10b981 - Green */
var(--success-light)        /* #ecfdf5 */

var(--danger)               /* #ef4444 - Red */
var(--danger-light)         /* #fef2f2 */

var(--warning)              /* #f59e0b - Amber */
var(--warning-light)        /* #fffbeb */

var(--info)                 /* #0ea5e9 - Cyan */
var(--info-light)           /* #f0f9ff */

/* Grays */
var(--gray-50) through var(--gray-900)

/* Spacing */
var(--spacing-xs)           /* 0.25rem */
var(--spacing-sm)           /* 0.5rem */
var(--spacing-md)           /* 1rem */
var(--spacing-lg)           /* 1.5rem */
var(--spacing-xl)           /* 2rem */
var(--spacing-2xl)          /* 3rem */

/* Typography */
var(--font-size-xs) through var(--font-size-2xl)
var(--font-weight-regular)  /* 400 */
var(--font-weight-medium)   /* 500 */
var(--font-weight-semibold) /* 600 */
var(--font-weight-bold)     /* 700 */

/* Radius */
var(--radius-sm)            /* 4px */
var(--radius-md)            /* 8px */
var(--radius-lg)            /* 12px */
var(--radius-xl)            /* 16px */

/* Shadows */
var(--shadow-sm) through var(--shadow-xl)
```

---

## Form Page Template

Use this template for creating new form pages:

```blade
@extends('layouts.app')

@section('title', 'Page Title - SIMBAR')

@section('content')
<style>
    .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: var(--spacing-2xl);
    }

    .page-header-content h1 {
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
        color: var(--gray-900);
        margin-bottom: var(--spacing-sm);
        display: flex;
        align-items: center;
        gap: var(--spacing-md);
    }

    .page-header-content p {
        font-size: var(--font-size-sm);
        color: var(--gray-500);
        margin: 0;
    }

    .form-container {
        display: grid;
        grid-template-columns: 1fr 280px;
        gap: var(--spacing-xl);
        max-width: 1100px;
    }

    .form-card {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        padding: var(--spacing-xl);
        box-shadow: var(--shadow-sm);
    }

    .info-card {
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        height: fit-content;
    }

    .info-card h6 {
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-900);
        margin-bottom: var(--spacing-md);
    }

    @media (max-width: 900px) {
        .form-container {
            grid-template-columns: 1fr;
        }

        .page-header {
            flex-direction: column;
        }
    }
</style>

<div style="max-width: 1400px; margin: 0 auto;">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1><i class="bi bi-icon"></i> Page Title</h1>
            <p>Page description</p>
        </div>
        <a href="{{ route('previous.page') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <!-- Main Content -->
    <div class="form-container">
        <!-- Form Card -->
        <div class="form-card">
            <!-- Form content here -->
        </div>

        <!-- Sidebar -->
        <div>
            <div class="info-card">
                <h6><i class="bi bi-info-circle"></i> Info Title</h6>
                <!-- Sidebar content here -->
            </div>
        </div>
    </div>
</div>
@endsection
```

---

## Form Components Template

```blade
<!-- Error Alert -->
@if ($errors->any())
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle alert-icon"></i>
        <div>
            <strong>Validation Error</strong>
            <ul style="margin: var(--spacing-md) 0 0 0; padding-left: var(--spacing-lg);">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<!-- Form Group -->
<div class="form-group" style="margin-bottom: var(--spacing-lg);">
    <label class="form-label" style="display: block; margin-bottom: var(--spacing-sm); font-weight: var(--font-weight-medium); color: var(--gray-700); font-size: var(--font-size-sm);">
        Field Name <span style="color: var(--danger);">*</span>
    </label>
    <input 
        type="text" 
        class="form-control @error('field') is-invalid @enderror" 
        name="field"
        value="{{ old('field') }}"
    >
    @error('field')
        <div style="color: var(--danger); font-size: var(--font-size-xs); margin-top: var(--spacing-xs);">
            {{ $message }}
        </div>
    @enderror
    <div style="font-size: var(--font-size-xs); color: var(--gray-500); margin-top: var(--spacing-xs);">
        Help text
    </div>
</div>

<!-- Buttons -->
<div style="display: flex; gap: var(--spacing-md); justify-content: flex-end; margin-top: var(--spacing-xl);">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-circle"></i> Submit
    </button>
    <a href="{{ route('cancel') }}" class="btn btn-secondary">Cancel</a>
</div>
```

---

## Alert Components

```blade
<!-- Success Alert -->
<div class="alert alert-success">
    <i class="bi bi-check-circle alert-icon"></i>
    <div>Success message</div>
</div>

<!-- Danger Alert -->
<div class="alert alert-danger">
    <i class="bi bi-exclamation-circle alert-icon"></i>
    <div>Error message</div>
</div>

<!-- Warning Alert -->
<div class="alert alert-warning">
    <i class="bi bi-exclamation-triangle alert-icon"></i>
    <div>Warning message</div>
</div>

<!-- Info Alert -->
<div class="alert alert-info">
    <i class="bi bi-info-circle alert-icon"></i>
    <div>Information message</div>
</div>
```

---

## Remaining Files to Redesign

### Admin Stock Pages (2 files)

**`admin/stock/adjustment.blade.php`**
- Similar to stock-in page
- Include reason dropdown
- Show current stock info
- Add negative number explanation

**`admin/stock/history.blade.php`**
- Table-based data display
- Columns: Date, Type, Item, Quantity, Reference, User
- Use table styling from design-system.css
- Add pagination if needed

### Admin Report Pages (5 files)

**`admin/reports/dashboard.blade.php`**
- Summary stat cards (use stat-card class)
- Chart containers (Bootstrap-compatible)
- Date range filters

**`admin/reports/loans.blade.php`**
- Table with loan data
- Status badges
- Approve/reject action buttons

**`admin/reports/inventory.blade.php`**
- Item listing table
- Stock status indicators
- Category filters

**`admin/reports/stock-movement.blade.php`**
- Movement history table
- Reference type display
- Date filtering

**`admin/reports/overdue.blade.php`**
- Overdue loans table
- Alert styling for urgent items
- Staff member information

### Staff Pages (6 files)

**`staff/items/index.blade.php`**
- Item browsing with search/filter
- Category and location filters
- Stock availability display

**`staff/items/show.blade.php`**
- Item detail display
- Availability check
- Loan request button

**`staff/loans/create.blade.php`**
- Loan request form
- Item quantity selection
- Purpose input
- Date range selection

**`staff/loans/index.blade.php`**
- My loans list table
- Status indicators
- Return action buttons

**`staff/loans/show.blade.php`**
- Loan detail display
- Borrowed items list
- Return due date

**`staff/returns/create.blade.php`**
- Return form
- Item selection from active loans
- Condition rating
- Return notes

**`staff/returns/index.blade.php`**
- Return requests list
- Status indicators
- Timeline display

**`staff/returns/show.blade.php`**
- Return detail display
- Items being returned
- Return status

---

## Common Blade Classes

```blade
<!-- Buttons -->
<button class="btn btn-primary">Primary</button>
<button class="btn btn-secondary">Secondary</button>
<button class="btn btn-success">Success</button>
<button class="btn btn-danger">Danger</button>
<button class="btn btn-sm">Small</button>
<button class="btn btn-lg">Large</button>
<a class="btn btn-outline-primary">Outline</a>

<!-- Badges -->
<span class="badge badge-primary">Primary</span>
<span class="badge badge-success">Success</span>
<span class="badge badge-danger">Danger</span>
<span class="badge badge-warning">Warning</span>

<!-- Status -->
<span class="status-badge status-pending">Pending</span>
<span class="status-badge status-approved">Approved</span>
<span class="status-badge status-completed">Completed</span>
<span class="status-badge status-rejected">Rejected</span>

<!-- Cards -->
<div class="card">
    <div class="card-body">Content</div>
</div>

<!-- Stat Cards -->
<div class="stat-card">
    <div>
        <div class="stat-label">Label</div>
        <div class="stat-value">123</div>
    </div>
    <div class="stat-icon">📊</div>
</div>
```

---

## CSS Grid Patterns

```css
/* Two column form layout -->
.form-container {
    display: grid;
    grid-template-columns: 1fr 280px;
    gap: var(--spacing-xl);
}

/* Responsive: 2 columns on desktop, 1 on mobile -->
@media (max-width: 900px) {
    .form-container {
        grid-template-columns: 1fr;
    }
}

/* Three column stat card layout -->
.stats-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: var(--spacing-lg);
}

@media (max-width: 768px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
}
```

---

## Testing Checklist

Before committing changes:

- [ ] `npm run build` passes
- [ ] No console errors
- [ ] No CSS warnings
- [ ] Responsive design works
- [ ] Form submission works
- [ ] Error handling displays
- [ ] Navigation functions
- [ ] Table data displays
- [ ] Buttons are clickable
- [ ] Colors are correct
- [ ] Spacing looks aligned

---

## Quick Tips

1. **Always use CSS variables** - Never hardcode colors or spacing
2. **Use the form template** - Copy-paste and customize
3. **Keep sidebar width 280px** - Consistent with design
4. **Test responsiveness** - Resize browser to 900px
5. **Use Bootstrap Icons** - Icon library is loaded from CDN
6. **Follow established patterns** - Look at completed pages for reference
7. **Run build after changes** - Verify CSS compiles correctly
8. **Keep forms simple** - Focus on functionality, not aesthetics

---

## Resources

- Design System: `resources/css/design-system.css`
- Bootstrap Icons: https://icons.getbootstrap.com/
- Bootstrap 5 Docs: https://getbootstrap.com/docs/5.3/
- Vite Docs: https://vitejs.dev/
- Laravel Vite: https://laravel.com/docs/vite

---

## Support

For questions or issues:

1. Check existing redesigned pages as reference
2. Review design-system.css for available variables
3. Look at this guide's templates
4. Check build output for errors: `npm run build`

---

**Last Updated**: November 4, 2026
**Status**: Phase 4 - 79% Complete

