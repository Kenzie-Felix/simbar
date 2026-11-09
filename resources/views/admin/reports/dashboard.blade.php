@extends('layouts.app')

@section('title', 'Reporting Dashboard - SIMBAR')

@section('content')
<style>
    .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: var(--spacing-2xl);
        gap: var(--spacing-lg);
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

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-2xl);
    }

    .stat-card {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        transition: all 0.2s ease;
    }

    .stat-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
        border-color: var(--primary);
    }

    .stat-content {
        flex: 1;
    }

    .stat-label {
        font-size: var(--font-size-sm);
        color: var(--gray-500);
        margin-bottom: var(--spacing-xs);
        font-weight: var(--font-weight-medium);
    }

    .stat-value {
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
        color: var(--gray-900);
        margin-bottom: var(--spacing-md);
    }

    .stat-icon {
        font-size: 2rem;
        opacity: 0.1;
        color: currentColor;
    }

    .stat-card.primary { color: var(--primary); }
    .stat-card.success { color: var(--success); }
    .stat-card.warning { color: var(--warning); }
    .stat-card.danger { color: var(--danger); }
    .stat-card.info { color: var(--info); }

    .quick-access-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-2xl);
    }

    /* Hide Stock Movement card */
    .quick-access-btn[href*="stock-movement"] {
        display: none;
    }

    .quick-access-btn {
        display: flex;
        flex-direction: column;
        padding: var(--spacing-lg);
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        text-decoration: none;
        color: var(--gray-900);
        transition: all 0.2s ease;
    }

    .quick-access-btn:hover {
        border-color: currentColor;
        box-shadow: var(--shadow-sm);
    }

    .quick-access-icon {
        font-size: 1.5rem;
        margin-bottom: var(--spacing-md);
    }

    .quick-access-title {
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-semibold);
        margin-bottom: var(--spacing-xs);
    }

    .quick-access-desc {
        font-size: var(--font-size-xs);
        color: var(--gray-500);
    }

    .list-item {
        padding: var(--spacing-md) 0;
        border-bottom: 1px solid var(--gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .list-item:last-child {
        border-bottom: none;
    }

    .list-item-title {
        font-weight: var(--font-weight-semibold);
        color: var(--gray-900);
    }

    .list-item-subtitle {
        font-size: var(--font-size-xs);
        color: var(--gray-500);
    }

    .loan-stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-2xl);
    }
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 0 var(--spacing-lg);">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1>
                <i class="bi bi-graph-up"></i>
                Reporting Dashboard
            </h1>
            <p>Comprehensive inventory and loan reporting</p>
        </div>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>

    <!-- Inventory Statistics -->
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-content">
                <div class="stat-label">Total Items</div>
                <div class="stat-value">{{ $stats['total_items'] }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-collection"></i>
            </div>
        </div>

        <div class="stat-card success">
            <div class="stat-content">
                <div class="stat-label">Available Stock</div>
                <div class="stat-value">{{ $stats['total_available_stock'] }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-box-seam"></i>
            </div>
        </div>

        <div class="stat-card warning">
            <div class="stat-content">
                <div class="stat-label">Low Stock Items</div>
                <div class="stat-value">{{ $stats['low_stock_items'] }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
        </div>

        <div class="stat-card danger">
            <div class="stat-content">
                <div class="stat-label">Out of Stock</div>
                <div class="stat-value">{{ $stats['out_of_stock_items'] }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-x-circle"></i>
            </div>
        </div>
    </div>

    <!-- Loan Statistics -->
    <div class="loan-stats-grid">
        <div class="stat-card warning">
            <div class="stat-content">
                <div class="stat-label">Pending</div>
                <div class="stat-value">{{ $stats['pending_requests'] }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-hourglass-split"></i>
            </div>
        </div>

        <div class="stat-card info">
            <div class="stat-content">
                <div class="stat-label">Active</div>
                <div class="stat-value">{{ $stats['active_loans'] }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-clipboard-check"></i>
            </div>
        </div>

        <div class="stat-card danger">
            <div class="stat-content">
                <div class="stat-label">Overdue</div>
                <div class="stat-value">{{ $stats['overdue_loans'] }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-exclamation-circle"></i>
            </div>
        </div>

        <div class="stat-card success">
            <div class="stat-content">
                <div class="stat-label">Completed</div>
                <div class="stat-value">{{ $stats['completed_loans'] }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-check-circle"></i>
            </div>
        </div>

        <div class="stat-card" style="color: var(--gray-600);">
            <div class="stat-content">
                <div class="stat-label">Rejected</div>
                <div class="stat-value">{{ $stats['rejected_loans'] }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-x-circle"></i>
            </div>
        </div>
    </div>

    <!-- Return Statistics -->
    <div class="stats-grid">
        <div class="stat-card warning">
            <div class="stat-content">
                <div class="stat-label">Pending Verifications</div>
                <div class="stat-value">{{ $stats['pending_verifications'] }}</div>
            </div>
        </div>

        <div class="stat-card success">
            <div class="stat-content">
                <div class="stat-label">Accepted Returns</div>
                <div class="stat-value">{{ $stats['accepted_returns'] }}</div>
            </div>
        </div>

        <div class="stat-card danger">
            <div class="stat-content">
                <div class="stat-label">Rejected Returns</div>
                <div class="stat-value">{{ $stats['rejected_returns'] }}</div>
            </div>
        </div>
    </div>

    <!-- Condition Statistics -->
    <div class="stats-grid">
        <div class="stat-card danger">
            <div class="stat-content">
                <div class="stat-label">Severely Damaged Items</div>
                <div class="stat-value">{{ $stats['damaged_items'] }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-exclamation"></i>
            </div>
        </div>

        <div class="stat-card" style="color: var(--gray-700);">
            <div class="stat-content">
                <div class="stat-label">Lost Items</div>
                <div class="stat-value">{{ $stats['lost_items'] }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-question-circle"></i>
            </div>
        </div>
    </div>

    <!-- Quick Access Reports -->
    <div style="margin-bottom: var(--spacing-lg);">
        <h5 style="margin: 0 0 var(--spacing-lg) 0; display: flex; align-items: center; gap: var(--spacing-md); font-size: var(--font-size-lg); color: var(--gray-900);">
            <i class="bi bi-link-45deg"></i> Quick Access
        </h5>
    </div>

    <div class="quick-access-grid">
        <a href="{{ route('admin.reports.loans') }}" class="quick-access-btn" style="color: var(--primary);">
            <i class="bi bi-file-earmark-text quick-access-icon"></i>
            <div class="quick-access-title">Loan Reports</div>
            <div class="quick-access-desc">View all loan transactions</div>
        </a>

        <a href="{{ route('admin.reports.inventory') }}" class="quick-access-btn" style="color: var(--success);">
            <i class="bi bi-archive quick-access-icon"></i>
            <div class="quick-access-title">Inventory Reports</div>
            <div class="quick-access-desc">Current stock status</div>
        </a>

        <a href="{{ route('admin.reports.stock-movement') }}" class="quick-access-btn" style="color: var(--info);">
            <i class="bi bi-arrow-left-right quick-access-icon"></i>
            <div class="quick-access-title">Stock Movement</div>
            <div class="quick-access-desc">Stock in/out history</div>
        </a>

        <a href="{{ route('admin.reports.overdue') }}" class="quick-access-btn" style="color: var(--danger);">
            <i class="bi bi-exclamation-triangle quick-access-icon"></i>
            <div class="quick-access-title">Overdue Loans</div>
            <div class="quick-access-desc">{{ $stats['overdue_loans'] }} loans overdue</div>
        </a>
    </div>

    <!-- Top Borrowed Items -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: var(--spacing-lg);">
        <div class="card">
            <div class="card-header">
                <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                    <i class="bi bi-star"></i> Top 5 Borrowed Items
                </h5>
            </div>
            <div class="card-body">
                @if (!empty($topBorrowedItems))
                    @foreach ($topBorrowedItems as $item)
                        <div class="list-item">
                            <div>
                                <div class="list-item-title">{{ $item['name'] }}</div>
                                <div class="list-item-subtitle">{{ $item['code'] }}</div>
                            </div>
                            <span class="badge badge-primary">{{ $item['total_borrowed'] }} times</span>
                        </div>
                    @endforeach
                @else
                    <p style="color: var(--gray-500); margin: 0;">No borrowing history yet</p>
                @endif
            </div>
        </div>

        <!-- Staff Borrowing Frequency -->
        <div class="card">
            <div class="card-header">
                <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                    <i class="bi bi-people"></i> Top 5 Staff by Borrowing Frequency
                </h5>
            </div>
            <div class="card-body">
                @if (!empty($staffBorrowingFrequency))
                    @foreach ($staffBorrowingFrequency as $staff)
                        <div class="list-item">
                            <div>
                                <div class="list-item-title">{{ $staff['name'] }}</div>
                                <div class="list-item-subtitle">{{ $staff['email'] }}</div>
                            </div>
                            <span class="badge badge-info">{{ $staff['total_loans'] }} loans</span>
                        </div>
                    @endforeach
                @else
                    <p style="color: var(--gray-500); margin: 0;">No staff data yet</p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
