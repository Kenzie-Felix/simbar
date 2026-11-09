@extends('layouts.app')

@section('title', 'Staff Dashboard - SIMBAR')

@section('content')
<style>
    .page-header {
        margin-bottom: 3rem;
        padding-bottom: 2rem;
        border-bottom: 1px solid #f3f4f6;
    }

    .page-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .page-title i {
        font-size: 1.8rem;
        background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .page-subtitle {
        font-size: 0.875rem;
        color: #6b7280;
        margin: 0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
        margin-bottom: 3rem;
    }

    .stat-card {
        position: relative;
        overflow: hidden;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        padding: 1.5rem;
    }

    .stat-content {
        flex: 1;
        z-index: 2;
        position: relative;
    }

    .stat-label {
        font-size: 0.875rem;
        color: #4b5563;
        margin-bottom: 0.25rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        color: #111827;
    }

    .stat-icon {
        font-size: 2rem;
        opacity: 0.08;
        color: currentColor;
        position: absolute;
        bottom: 1.5rem;
        right: 1.5rem;
    }

    .stat-card.primary { color: #1e3a8a; }
    .stat-card.success { color: #10b981; }
    .stat-card.warning { color: #f59e0b; }
    .stat-card.info { color: #0ea5e9; }

    .action-buttons {
        display: flex;
        gap: 1rem;
        margin-bottom: 3rem;
        flex-wrap: wrap;
    }

    .action-buttons a {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        flex: 1;
        min-width: 200px;
        justify-content: center;
        padding: 0.875rem 1rem;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.375rem;
        text-decoration: none;
        color: #111827;
        font-weight: 500;
        transition: all 0.2s ease;
        font-size: 0.875rem;
    }

    .action-buttons a:hover {
        border-color: #1e40af;
        background: #f0f9ff;
        color: #1e40af;
    }

    /* Primary Button Style */
    .action-buttons .btn-primary {
        background: #1e40af;
        color: white;
        border-color: #1e40af;
    }

    .action-buttons .btn-primary:hover {
        background: #1e3a8a;
        border-color: #1e3a8a;
        color: white;
    }

    /* Success Button Style */
    .action-buttons .btn-success {
        background: #10b981;
        color: white;
        border-color: #10b981;
    }

    .action-buttons .btn-success:hover {
        background: #059669;
        border-color: #059669;
        color: white;
    }

    /* Info Button Style */
    .action-buttons .btn-info {
        background: #0ea5e9;
        color: white;
        border-color: #0ea5e9;
    }

    .action-buttons .btn-info:hover {
        background: #0284c7;
        border-color: #0284c7;
        color: white;
    }

    .loans-table {
        border-collapse: collapse;
        width: 100%;
    }

    .loans-table thead th {
        padding: 1rem;
        text-align: left;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        background: linear-gradient(135deg, #f9fafb 0%, white 100%);
        border-bottom: 2px solid #e5e7eb;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .loans-table tbody td {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
        font-size: 0.875rem;
        color: #374151;
    }

    .loans-table tbody tr {
        transition: all 0.2s ease;
    }

    .loans-table tbody tr:hover {
        background-color: #f0f9ff;
    }

    .loans-table tbody tr:last-child td {
        border-bottom: none;
    }

    .action-cell {
        display: flex;
        gap: 0.5rem;
    }

    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-success {
        background: #dcfce7;
        color: #166534;
    }

    .card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        margin-bottom: 3rem;
        overflow: hidden;
    }

    .card-header {
        background: #f9fafb;
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .card-body {
        padding: 1.5rem;
    }

    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        background: #f9fafb;
    }

    .empty-state-icon {
        font-size: 3.5rem;
        color: #d1d5db;
        margin-bottom: 1rem;
    }

    .empty-state-text {
        font-size: 1rem;
        color: #6b7280;
        margin-bottom: 1rem;
    }

    .alert {
        padding: 1rem;
        border-radius: 0.375rem;
        margin-bottom: 1.5rem;
    }

    .alert-success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
    }

    .alert-icon {
        font-size: 1.25rem;
        margin-right: 0.75rem;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .action-buttons {
            flex-direction: column;
        }

        .action-buttons a {
            min-width: auto;
        }

        .page-title {
            font-size: 1.25rem;
        }
    }
</style>

<div style="max-width: 1400px; margin: 0 auto;">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title">
            <i class="bi bi-speedometer2"></i>
            Dashboard
        </div>
        <p class="page-subtitle">Welcome back, <strong>{{ auth()->user()->name }}</strong> • {{ auth()->user()->role }}</p>
    </div>

    <!-- Alerts -->
    @if (session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle-fill alert-icon"></i>
            <div>
                <strong>Success</strong><br>
                {{ session('success') }}
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle-fill alert-icon"></i>
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
                @if ($overdueLoanCount > 0)
                    <small style="color: #f59e0b; margin-top: 1rem; display: block;">⚠️ Please return these items</small>
                @endif
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
                <div class="stat-label">Total Borrowed</div>
                <div class="stat-value">{{ $totalBorrowedCount }}</div>
                <small style="color: #6b7280; margin-top: 1rem; display: block;">All loans</small>
            </div>
            <div class="stat-icon">
                <i class="bi bi-box-seam"></i>
            </div>
        </div>
    </div>

    <!-- Quick Action Buttons -->
    <div class="action-buttons">
        <a href="{{ route('staff.items.index') }}" class="btn btn-primary">
            <i class="bi bi-search"></i> View Items
        </a>
        <a href="{{ route('staff.loans.create') }}" class="btn btn-success">
            <i class="bi bi-plus-circle"></i> Request Loan
        </a>
        <a href="{{ route('staff.returns.create') }}" class="btn btn-info">
            <i class="bi bi-box-arrow-down"></i> Return Item
        </a>
        <a href="{{ route('staff.loans.index') }}" style="background: white; color: #1e40af; border: 1px solid #1e40af;">
            <i class="bi bi-clock-history"></i> My Loans
        </a>
    </div>

    <!-- Active Loans Section -->
    <div class="card">
        <div class="card-header">
            <h5 style="margin: 0; display: flex; align-items: center; gap: 1rem; font-size: 1.125rem;">
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
                                <th>Item Name</th>
                                <th>Qty</th>
                                <th>Return Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($activeLoans as $loan)
                                @foreach ($loan->loanItems as $item)
                                    <tr>
                                        <td>
                                            <strong style="color: #111827;">{{ $item->item->name }}</strong><br>
                                            <small style="color: #6b7280;">{{ $item->item->code }} • {{ $item->item->category->name }}</small>
                                        </td>
                                        <td>
                                            <span style="font-weight: 600; font-size: 1.1rem;">{{ $item->quantity }}</span>
                                        </td>
                                        <td>
                                            <div style="color: #374151; font-weight: 500;">{{ $item->planned_return_at->format('M d, Y') }}</div>
                                            <div style="margin-top: 0.25rem;">
                                                @if ($item->isOverdue())
                                                    <span class="badge badge-danger">OVERDUE</span>
                                                @elseif ($item->daysUntilDue() <= 3)
                                                    <span class="badge badge-warning">DUE SOON</span>
                                                @else
                                                    <span class="badge badge-success">On Time</span>
                                                @endif
                                            </div>
                                        </td>
                                        <td>
                                            <span style="display: inline-block; background: #cfe2ff; color: #084298; padding: 0.25rem 0.75rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600;">Borrowed</span>
                                        </td>
                                        <td>
                                            <div class="action-cell">
                                <a href="{{ route('staff.loans.index') }}" style="background: white; color: #1e40af; border: 1px solid #1e40af; padding: 0.25rem 0.75rem; text-decoration: none; border-radius: 0.25rem; font-size: 0.875rem;">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                                <a href="{{ route('staff.returns.create', ['loan_id' => $loan->id]) }}" style="background: white; color: #10b981; border: 1px solid #10b981; padding: 0.25rem 0.75rem; text-decoration: none; border-radius: 0.25rem; font-size: 0.875rem;">
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
                    <div class="empty-state-text">No active loans</div>
                    <p style="color: #6b7280; margin: 1rem 0;">Start by viewing available items and requesting a loan.</p>
                    <a href="{{ route('staff.loans.create') }}" style="display: inline-block; background: #1e40af; color: white; border: none; padding: 0.5rem 1rem; text-decoration: none; border-radius: 0.375rem; margin-top: 1rem;">
                        <i class="bi bi-plus-circle"></i> Request Your First Loan
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
