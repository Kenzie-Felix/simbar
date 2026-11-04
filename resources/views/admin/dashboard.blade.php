@extends('layouts.app')

@section('title', 'Admin Dashboard - SIMBAR')

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

    .action-btn {
        display: inline-block;
        margin-top: var(--spacing-md);
    }

    .activity-table {
        border-collapse: collapse;
        width: 100%;
    }

    .activity-table thead th {
        padding: var(--spacing-md) var(--spacing-lg);
        text-align: left;
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-700);
        background-color: var(--gray-50);
        border-bottom: 1px solid var(--gray-200);
    }

    .activity-table tbody td {
        padding: var(--spacing-md) var(--spacing-lg);
        border-bottom: 1px solid var(--gray-200);
        font-size: var(--font-size-sm);
    }

    .activity-table tbody tr:hover {
        background-color: var(--gray-50);
    }

    .activity-table tbody tr:last-child td {
        border-bottom: none;
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
    }
</style>

<div style="max-width: 1400px; margin: 0 auto;">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title">
            <i class="bi bi-speedometer2"></i>
            Admin Dashboard
        </div>
        <p class="page-subtitle">Overview of inventory system status and pending actions</p>
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
        <!-- Total Items -->
        <div class="stat-card primary">
            <div class="stat-content">
                <div class="stat-label">Total Items</div>
                <div class="stat-value">{{ $stats['total_items'] ?? 0 }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-boxes"></i>
            </div>
        </div>

        <!-- Available Stock -->
        <div class="stat-card success">
            <div class="stat-content">
                <div class="stat-label">Available Stock</div>
                <div class="stat-value">{{ $stats['total_available_stock'] ?? 0 }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-check-circle"></i>
            </div>
        </div>

        <!-- Active Loans -->
        <div class="stat-card info">
            <div class="stat-content">
                <div class="stat-label">Active Loans</div>
                <div class="stat-value">{{ $stats['total_active_loans'] ?? 0 }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-clipboard-check"></i>
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="stat-card warning">
            <div class="stat-content">
                <div class="stat-label">Pending Approvals</div>
                <div class="stat-value">{{ $stats['pending_approvals'] ?? 0 }}</div>
                <a href="{{ route('admin.loans.index', ['status' => 'Menunggu Persetujuan']) }}" class="btn btn-sm btn-outline-primary action-btn">
                    <i class="bi bi-arrow-right"></i> Review
                </a>
            </div>
            <div class="stat-icon">
                <i class="bi bi-hourglass-split"></i>
            </div>
        </div>

        <!-- Pending Verifications -->
        <div class="stat-card danger">
            <div class="stat-content">
                <div class="stat-label">Pending Verifications</div>
                <div class="stat-value">{{ $stats['pending_verifications'] ?? 0 }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-check2-circle"></i>
            </div>
        </div>

        <!-- Overdue Loans -->
        <div class="stat-card danger">
            <div class="stat-content">
                <div class="stat-label">Overdue Loans</div>
                <div class="stat-value">{{ $stats['overdue_loans'] ?? 0 }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
        </div>
    </div>

    <!-- Secondary Stats Row -->
    <div class="stats-grid" style="margin-bottom: var(--spacing-2xl);">
        <!-- Low Stock Items -->
        <div class="stat-card warning">
            <div class="stat-content">
                <div class="stat-label">Low Stock Items</div>
                <div class="stat-value">{{ $stats['low_stock_items'] ?? 0 }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-exclamation-circle"></i>
            </div>
        </div>

        <!-- Out of Stock -->
        <div class="stat-card danger">
            <div class="stat-content">
                <div class="stat-label">Out of Stock</div>
                <div class="stat-value">{{ $stats['out_of_stock_items'] ?? 0 }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-x-circle"></i>
            </div>
        </div>

        <!-- Completed Loans -->
        <div class="stat-card success">
            <div class="stat-content">
                <div class="stat-label">Completed Loans</div>
                <div class="stat-value">{{ $stats['total_completed_loans'] ?? 0 }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-check-all"></i>
            </div>
        </div>

        <!-- Return Requests -->
        <div class="stat-card info">
            <div class="stat-content">
                <div class="stat-label">Return Requests</div>
                <div class="stat-value">{{ $stats['total_return_requests'] ?? 0 }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-box-arrow-down"></i>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="card">
        <div class="card-header">
            <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                <i class="bi bi-activity"></i>
                Recent Activity
            </h5>
        </div>
        <div class="card-body">
            @if ($recentLogs->count() > 0)
                <div style="overflow-x: auto;">
                    <table class="activity-table">
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Action</th>
                                <th>Entity</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($recentLogs as $log)
                                <tr>
                                    <td>
                                        <span style="color: var(--gray-600); font-weight: var(--font-weight-medium);">
                                            {{ $log->user?->name ?? 'System' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ $log->action }}</span>
                                    </td>
                                    <td>
                                        {{ $log->entity_type }} <span style="color: var(--primary);">#{{ $log->entity_id }}</span>
                                    </td>
                                    <td>
                                        <span style="color: var(--gray-500);">{{ $log->created_at?->diffForHumans() }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <div class="empty-state-text">No recent activity</div>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
