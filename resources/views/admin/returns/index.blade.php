@extends('layouts.app')

@section('title', 'Return Verifications - SIMBAR')

@section('content')
<style>
    .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: var(--spacing-2xl);
        gap: var(--spacing-md);
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

    .returns-table {
        border-collapse: collapse;
        width: 100%;
    }

    .returns-table thead th {
        padding: var(--spacing-md) var(--spacing-lg);
        text-align: left;
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-700);
        background-color: var(--gray-50);
        border-bottom: 1px solid var(--gray-200);
    }

    .returns-table tbody td {
        padding: var(--spacing-md) var(--spacing-lg);
        border-bottom: 1px solid var(--gray-200);
        font-size: var(--font-size-sm);
    }

    .returns-table tbody tr:hover {
        background-color: var(--gray-50);
    }

    .returns-table tbody tr:last-child td {
        border-bottom: none;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 0 var(--spacing-lg);">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1>
                <i class="bi bi-check2-circle"></i>
                Return Verifications
            </h1>
            <p>Review and verify return requests</p>
        </div>
        <a href="{{ route('admin.returns.exportPending') }}" class="btn btn-outline-primary">
            <i class="bi bi-download"></i> Export Pending
        </a>
    </div>

    <!-- Statistics Grid -->
    <div class="stats-grid">
        <div class="stat-card" style="color: var(--warning);">
            <div class="stat-content">
                <div class="stat-label">Pending</div>
                <div class="stat-value" style="color: var(--warning);">{{ $stats['pending_count'] }}</div>
            </div>
        </div>
        <div class="stat-card" style="color: var(--success);">
            <div class="stat-content">
                <div class="stat-label">Accepted</div>
                <div class="stat-value" style="color: var(--success);">{{ $stats['accepted_count'] }}</div>
            </div>
        </div>
        <div class="stat-card" style="color: var(--danger);">
            <div class="stat-content">
                <div class="stat-label">Rejected</div>
                <div class="stat-value" style="color: var(--danger);">{{ $stats['rejected_count'] }}</div>
            </div>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card" style="margin-bottom: var(--spacing-lg);">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.returns.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-md); align-items: end;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: var(--font-size-sm);">Search</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        name="search" 
                        placeholder="Search by staff name or email..." 
                        value="{{ request('search') }}"
                    >
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: var(--font-size-sm);">Status</label>
                    <select class="form-select" name="status">
                        <option value="">-- All Status --</option>
                        @foreach ($statuses as $statusKey => $statusLabel)
                            <option value="{{ $statusKey }}" @if (request('status') == $statusKey) selected @endif>
                                {{ $statusLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel"></i> Filter
                </button>
            </form>
        </div>
    </div>

    <!-- Returns Table -->
    @if ($returns->count() > 0)
        <div class="card">
            <div class="card-body p-0">
                <div style="overflow-x: auto;">
                    <table class="returns-table">
                        <thead>
                            <tr>
                                <th>Return ID</th>
                                <th>Staff Member</th>
                                <th>Items</th>
                                <th>Quantity</th>
                                <th>Submitted</th>
                                <th>Status</th>
                                <th style="text-align: center;">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($returns as $return)
                                <tr>
                                    <td>
                                        <strong>#{{ $return->id }}</strong>
                                    </td>
                                    <td>
                                        <small>
                                            <strong>{{ $return->loan->user->name }}</strong><br>
                                            <span style="color: var(--gray-500);">{{ $return->loan->user->email }}</span>
                                        </small>
                                    </td>
                                    <td><small>{{ $return->getReturnedItemsCount() }}</small></td>
                                    <td><small>{{ $return->getTotalReturnedQuantity() }} units</small></td>
                                    <td><small style="color: var(--gray-500);">{{ $return->submitted_at->format('d M Y H:i') }}</small></td>
                                    <td>
                                        <span class="badge badge-{{ $return->status === 'Menunggu Verifikasi' ? 'warning' : ($return->status === 'Terima' ? 'success' : 'danger') }}">
                                            {{ $statuses[$return->status] ?? $return->status }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="{{ route('admin.returns.show', $return) }}" class="btn btn-sm btn-outline-primary">
                                            <i class="bi bi-eye"></i> Review
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if ($returns->hasPages())
            <div style="display: flex; justify-content: center; margin-top: var(--spacing-xl);">
                {{ $returns->links() }}
            </div>
        @endif
    @else
        <div class="card">
            <div class="empty-state" style="text-align: center; padding: var(--spacing-xl) var(--spacing-lg);">
                <div class="empty-state-icon" style="margin-bottom: var(--spacing-lg);">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: var(--gray-400);"></i>
                </div>
                <p class="empty-state-text" style="font-size: var(--font-size-base); color: var(--gray-500);">
                    @if (request('status') || request('search'))
                        No return requests match your filters.
                    @else
                        No return requests to verify.
                    @endif
                </p>
            </div>
        </div>
    @endif
</div>

@endsection
