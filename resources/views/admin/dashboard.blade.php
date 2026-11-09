@extends('layouts.app')

@section('title', 'Admin Dashboard - SIMBAR')

@section('content')
<style>
    .page-header {
        margin-bottom: var(--spacing-2xl);
        padding-bottom: var(--spacing-xl);
        border-bottom: 1px solid var(--gray-100);
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

    .page-title i {
        font-size: 1.8rem;
        background: linear-gradient(135deg, var(--primary) 0%, #1e40af 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .page-subtitle {
        font-size: var(--font-size-sm);
        color: var(--gray-500);
        margin: 0;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-2xl);
    }

    .stats-section-title {
        font-size: var(--font-size-lg);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-900);
        margin-top: var(--spacing-2xl);
        margin-bottom: var(--spacing-lg);
        padding-bottom: var(--spacing-md);
        border-bottom: 2px solid var(--gray-100);
    }

    .stat-card {
        position: relative;
        overflow: hidden;
    }

    .stat-content {
        flex: 1;
        z-index: 2;
        position: relative;
    }

    .stat-label {
        font-size: var(--font-size-sm);
        color: var(--gray-600);
        margin-bottom: var(--spacing-xs);
        font-weight: var(--font-weight-medium);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: var(--font-weight-bold);
        color: var(--gray-900);
        margin-bottom: var(--spacing-md);
    }

    .stat-icon {
        font-size: 2rem;
        opacity: 0.08;
        color: currentColor;
        position: absolute;
        bottom: var(--spacing-lg);
        right: var(--spacing-lg);
    }

    .stat-card.primary { color: var(--primary); }
    .stat-card.success { color: var(--success); }
    .stat-card.warning { color: var(--warning); }
    .stat-card.danger { color: var(--danger); }
    .stat-card.info { color: var(--info); }

    .action-btn {
        display: inline-block;
        margin-top: var(--spacing-md);
        font-size: var(--font-size-xs);
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
        background: linear-gradient(135deg, var(--gray-50) 0%, white 100%);
        border-bottom: 2px solid var(--gray-100);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .activity-table tbody td {
        padding: var(--spacing-md) var(--spacing-lg);
        border-bottom: 1px solid var(--gray-100);
        font-size: var(--font-size-sm);
    }

    .activity-table tbody tr {
        transition: all 0.2s ease;
    }

    .activity-table tbody tr:hover {
        background-color: var(--primary-light);
    }

    .activity-table tbody tr:last-child td {
        border-bottom: none;
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .page-title {
            font-size: var(--font-size-xl);
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
        <p class="page-subtitle">System overview, statistics, and pending actions</p>
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

    <!-- Primary Statistics -->
    <div class="stats-section-title">
        <i class="bi bi-speedometer2" style="margin-right: var(--spacing-sm);"></i> Key Metrics
    </div>
    <div class="stats-grid">
        <!-- Total Items -->
        <div class="stat-card card primary">
            <div class="stat-content">
                <div class="stat-label">Total Items</div>
                <div class="stat-value">{{ $stats['total_items'] ?? 0 }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-boxes"></i>
            </div>
        </div>

        <!-- Available Stock -->
        <div class="stat-card card success">
            <div class="stat-content">
                <div class="stat-label">Available Stock</div>
                <div class="stat-value">{{ $stats['total_available_stock'] ?? 0 }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-check-circle"></i>
            </div>
        </div>

        <!-- Active Loans -->
        <div class="stat-card card info">
            <div class="stat-content">
                <div class="stat-label">Active Loans</div>
                <div class="stat-value">{{ $stats['total_active_loans'] ?? 0 }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-clipboard-check"></i>
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="stat-card card warning">
            <div class="stat-content">
                <div class="stat-label">Pending Approval</div>
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
        <div class="stat-card card info">
            <div class="stat-content">
                <div class="stat-label">Pending Verification</div>
                <div class="stat-value">{{ $stats['pending_verifications'] ?? 0 }}</div>
                <a href="{{ route('admin.returns.index', ['status' => 'Menunggu Verifikasi']) }}" class="btn btn-sm btn-outline-primary action-btn">
                    <i class="bi bi-arrow-right"></i> Review
                </a>
            </div>
            <div class="stat-icon">
                <i class="bi bi-check2-circle"></i>
            </div>
        </div>

        <!-- Overdue Loans -->
        <div class="stat-card card danger">
            <div class="stat-content">
                <div class="stat-label">Overdue Loans</div>
                <div class="stat-value">{{ $stats['overdue_loans'] ?? 0 }}</div>
                <a href="{{ route('admin.reports.overdue') }}" class="btn btn-sm btn-outline-primary action-btn">
                    <i class="bi bi-arrow-right"></i> View
                </a>
            </div>
            <div class="stat-icon">
                <i class="bi bi-exclamation-triangle"></i>
            </div>
        </div>
    </div>

    <!-- Inventory Status -->
    <div class="stats-section-title">
        <i class="bi bi-boxes" style="margin-right: var(--spacing-sm);"></i> Inventory Status
    </div>
    <div class="stats-grid" style="grid-template-columns: repeat(2, 1fr);">
        <!-- Low Stock Items -->
        <div class="stat-card card warning">
            <div class="stat-content">
                <div class="stat-label">Low Stock Items (≤5)</div>
                <div class="stat-value">{{ $stats['low_stock_items'] ?? 0 }}</div>
                <a href="{{ route('admin.reports.inventory') }}" class="btn btn-sm btn-outline-primary action-btn">
                    <i class="bi bi-arrow-right"></i> View
                </a>
            </div>
            <div class="stat-icon">
                <i class="bi bi-exclamation-circle"></i>
            </div>
        </div>

        <!-- Out of Stock -->
        <div class="stat-card card danger">
            <div class="stat-content">
                <div class="stat-label">Out of Stock</div>
                <div class="stat-value">{{ $stats['out_of_stock_items'] ?? 0 }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-x-circle"></i>
            </div>
        </div>

        <!-- Categories -->
        <div class="stat-card card primary">
            <div class="stat-content">
                <div class="stat-label">Categories</div>
                <div class="stat-value">{{ $stats['total_categories'] ?? 0 }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-tags"></i>
            </div>
        </div>

        <!-- Locations -->
        <div class="stat-card card primary">
            <div class="stat-content">
                <div class="stat-label">Locations</div>
                <div class="stat-value">{{ $stats['total_locations'] ?? 0 }}</div>
            </div>
            <div class="stat-icon">
                <i class="bi bi-geo-alt"></i>
            </div>
        </div>
    </div>

    <!-- Recent Activity Section -->
    <div class="card" style="margin-top: var(--spacing-2xl);">
        <div class="card-header">
            <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md); font-size: var(--font-size-lg);">
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
                                        <span style="color: var(--gray-700); font-weight: var(--font-weight-medium);">
                                            {{ $log->user?->name ?? 'System' }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ $log->action }}</span>
                                    </td>
                                    <td>
                                        <span style="color: var(--gray-600);">{{ $log->entity_type }}</span>
                                        <span style="color: var(--primary); font-weight: var(--font-weight-semibold);">#{{ $log->entity_id }}</span>
                                    </td>
                                    <td>
                                        <span style="color: var(--gray-500); font-size: var(--font-size-xs);">{{ $log->created_at?->diffForHumans() }}</span>
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
