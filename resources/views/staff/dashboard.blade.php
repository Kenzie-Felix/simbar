@extends('layouts.app')

@section('title', 'Staff Dashboard - SIMBAR')

@section('content')
<style>
    .page-header {
        margin-bottom: var(--spacing-2xl);
    }

    .page-title {
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
        color: var(--gray-900);
        margin-bottom: var(--spacing-sm);
        display: flex;
        align-items: center;
        gap: var(--spacing-md);
    }

    .page-subtitle {
        font-size: var(--font-size-sm);
        color: var(--gray-500);
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
    }

    .stat-icon {
        font-size: 2rem;
        opacity: 0.1;
        color: currentColor;
    }

    .stat-card.primary { color: var(--primary); }
    .stat-card.success { color: var(--success); }
    .stat-card.warning { color: var(--warning); }
    .stat-card.info { color: var(--info); }

    .action-buttons {
        display: flex;
        gap: var(--spacing-md);
        margin-bottom: var(--spacing-2xl);
    }

    .action-buttons a {
        display: inline-flex;
        align-items: center;
        gap: var(--spacing-sm);
    }

    .loans-table {
        border-collapse: collapse;
        width: 100%;
    }

    .loans-table thead th {
        padding: var(--spacing-md) var(--spacing-lg);
        text-align: left;
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-700);
        background-color: var(--gray-50);
        border-bottom: 1px solid var(--gray-200);
    }

    .loans-table tbody td {
        padding: var(--spacing-md) var(--spacing-lg);
        border-bottom: 1px solid var(--gray-200);
        font-size: var(--font-size-sm);
    }

    .loans-table tbody tr:hover {
        background-color: var(--gray-50);
    }

    .loans-table tbody tr:last-child td {
        border-bottom: none;
    }

    .action-cell {
        display: flex;
        gap: var(--spacing-sm);
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .stat-card {
            flex-direction: column;
        }

        .stat-icon {
            align-self: flex-end;
            margin-top: var(--spacing-md);
        }

        .action-buttons {
            flex-direction: column;
        }

        .action-buttons a {
            justify-content: center;
        }
    }
</style>

<div style="max-width: 1400px; margin: 0 auto;">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title">
            <i class="bi bi-speedometer2"></i>
            My Dashboard
        </div>
        <p class="page-subtitle">Welcome, {{ auth()->user()->name }}</p>
    </div>

    <!-- Alerts -->
    @if (session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle alert-icon"></i>
            <div>
                <strong>Success</strong><br>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle alert-icon"></i>
            <div>
                <strong>Error</strong><br>
                {{ session('error') }}
            </div>
        </div>
    @endif

    <!-- Statistics Grid -->
    <div class="stats-grid">
        <!-- Active Loans -->
        <div class="stat-card primary">
            <div class="stat-content">
                <div class="stat-label">Active Loans</div>
                <div class="stat-value">{{ $activeLoans->count() }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-clipboard-check"></i>
            </div>
        </div>

        <!-- Overdue Loans -->
        <div class="stat-card warning">
            <div class="stat-content">
                <div class="stat-label">Overdue Loans</div>
                <div class="stat-value">{{ $overdueLoanCount }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
        </div>

        <!-- Available Items -->
        <div class="stat-card info">
            <div class="stat-content">
                <div class="stat-label">Available Items</div>
                <div class="stat-value">{{ $availableItemsCount }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-boxes"></i>
            </div>
        </div>

        <!-- Total Borrowed -->
        <div class="stat-card success">
            <div class="stat-content">
                <div class="stat-label">Total Borrowed (All Time)</div>
                <div class="stat-value">{{ $totalBorrowedCount }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-box-seam"></i>
            </div>
        </div>
    </div>

    <!-- Quick Action Buttons -->
    <div class="action-buttons">
        <a href="{{ route('staff.items.index') }}" class="btn btn-primary">
            <i class="bi bi-search"></i> Browse Items
        </a>
        <a href="{{ route('staff.loans.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Request New Loan
        </a>
        <a href="{{ route('staff.returns.create') }}" class="btn btn-info">
            <i class="bi bi-box-arrow-down"></i> Request Return
        </a>
    </div>

    <!-- Active Loans Section -->
    <div class="card">
        <div class="card-header">
            <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                <i class="bi bi-clipboard-check"></i>
                Active Loans
            </h5>
        </div>
        <div class="card-body">
            @if ($activeLoans->count() > 0)
                <div style="overflow-x: auto;">
                    <table class="loans-table">
                        <thead>
                            <tr>
                                <th>Item</th>
                                <th>Quantity</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activeLoans as $loan)
                                @foreach ($loan->loanItems as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item->item->name }}</strong><br>
                                            <small style="color: var(--gray-500);">{{ $item->item->category->name }}</small>
                                        </td>
                                        <td>
                                            <span style="font-weight: var(--font-weight-semibold);">{{ $item->quantity }}</span>
                                        </td>
                                        <td>
                                            <div>{{ $item->planned_return_at->format('M d, Y') }}</div>
                                            <div>
                                                @if ($item->isOverdue())
                                                    <span class="badge badge-danger">OVERDUE</span>
                                                @elseif ($item->daysUntilDue() <= 3)
                                                    <span class="badge badge-warning">DUE SOON</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">Borrowed</span>
                                        </td>
                                        <td>
                                            <div class="action-cell">
                                                <a href="{{ route('staff.loans.show', $loan) }}" class="btn btn-sm btn-outline-primary" title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                <a href="{{ route('staff.returns.create', ['loan_id' => $loan->id]) }}" class="btn btn-sm btn-outline-success" title="Request Return">
                                                    <i class="bi bi-box-arrow-down"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <div class="empty-state-text">You have no active loans</div>
                    <a href="{{ route('staff.loans.create') }}" class="btn btn-sm btn-primary" style="margin-top: var(--spacing-md);">
                        <i class="bi bi-plus-circle"></i> Request Your First Loan
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
