@extends('layouts.app')

@section('title', 'My Loans - SIMBAR')

@section('content')
<style>
    .page-header {
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
    }

    .page-title {
        font-size: 1.875rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .page-subtitle {
        font-size: 0.875rem;
        color: #6b7280;
        margin: 0;
    }

    /* Status Pills */
    .status-pills {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        flex-wrap: wrap;
    }

    .status-pill {
        padding: 0.25rem 1rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        border: 1px solid #d1d5db;
        text-decoration: none;
        display: inline-block;
        background: white;
        color: #4b5563;
        transition: all 0.2s ease;
    }

    .status-pill:hover {
        border-color: #1e40af;
        color: #1e40af;
    }

    .status-pill.active {
        background: #1e40af;
        color: white;
        border-color: #1e40af;
    }

    /* Table Container */
    .table-container {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        overflow: hidden;
        box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        margin-bottom: 2rem;
    }

    .table {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
        font-size: 0.875rem;
    }

    .table thead {
        background-color: #f9fafb;
        border-bottom: 2px solid #e5e7eb;
    }

    .table th {
        padding: 1rem;
        text-align: left;
        font-size: 0.75rem;
        font-weight: 600;
        color: #6b7280;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .table td {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
        color: #374151;
    }

    .table tbody tr:hover {
        background-color: #f9fafb;
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-warning {
        background-color: #fef3c7;
        color: #92400e;
    }

    .badge-info {
        background-color: #d1e7f7;
        color: #1e3a8a;
    }

    .badge-danger {
        background-color: #fee2e2;
        color: #991b1b;
    }

    .badge-primary {
        background-color: #dbeafe;
        color: #1e40af;
    }

    .badge-success {
        background-color: #dcfce7;
        color: #166534;
    }

    /* Empty State */
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
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 1rem;">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <div class="page-title">
                <i class="bi bi-clock-history"></i>
                My Loans
            </div>
            <p class="page-subtitle">View and manage your loan requests</p>
        </div>
        <a href="{{ route('staff.loans.create') }}" class="btn btn-primary" style="background-color: #1e40af; color: white; border: none; padding: 0.5rem 1rem; text-decoration: none; border-radius: 0.375rem;">
            <i class="bi bi-plus-circle"></i> New Request
        </a>
    </div>

    <!-- Status Filter Pills -->
    <div class="status-pills">
        <a href="{{ route('staff.loans.index') }}" class="status-pill {{ !request('status') ? 'active' : '' }}">
            <i class="bi bi-list"></i> All
        </a>
        @if(isset($statuses))
            @foreach ($statuses as $statusKey => $statusLabel)
                <a href="{{ route('staff.loans.index', ['status' => $statusKey]) }}" class="status-pill {{ request('status') == $statusKey ? 'active' : '' }}">
                    {{ $statusLabel }}
                </a>
            @endforeach
        @endif
    </div>

    <!-- Loan Requests Table -->
    @if(isset($loans) && $loans->count() > 0)
        <div class="table-container">
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Requested</th>
                            <th style="text-align: center;">Items</th>
                            <th style="text-align: center;">Qty</th>
                            <th>Purpose</th>
                            <th>Return Date</th>
                            <th>Status</th>
                            <th style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loans as $loan)
                            <tr>
                                <td>
                                    <div style="font-weight: 500;">{{ $loan->requested_at ? $loan->requested_at->format('d M Y') : '—' }}</div>
                                    <div style="font-size: 0.75rem; color: #6b7280;">{{ $loan->requested_at ? $loan->requested_at->format('H:i') : '—' }}</div>
                                </td>
                                <td style="text-align: center;">
                                    <span style="display: inline-block; background: #dbeafe; color: #1e40af; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600;">
                                        {{ $loan->getTotalItemsCount() }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <span style="display: inline-block; background: #f3f4f6; color: #374151; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-weight: 500;">
                                        {{ $loan->getTotalQuantity() }} unit{{ $loan->getTotalQuantity() !== 1 ? 's' : '' }}
                                    </span>
                                </td>
                                <td style="max-width: 250px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; color: #6b7280;">
                                    {{ $loan->purpose ?? 'Purpose not specified' }}
                                </td>
                                <td>
                                    @php
                                        $returnDate = $loan->getEarliestReturnDate();
                                    @endphp
                                    @if ($returnDate)
                                        {{ $returnDate->format('d M Y') }}
                                        @if ($loan->isOverdue())
                                            <span style="display: inline-block; background: #fee2e2; color: #991b1b; padding: 0.25rem 0.5rem; border-radius: 0.25rem; font-size: 0.75rem; font-weight: 600; margin-left: 0.5rem;">
                                                <i class="bi bi-exclamation-circle"></i> Overdue
                                            </span>
                                        @endif
                                    @else
                                        <span style="color: #d1d5db;">—</span>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $statusColors = [
                                            'Pending Approval' => 'warning',
                                            'Approved' => 'info',
                                            'Rejected' => 'danger',
                                            'Borrowed' => 'primary',
                                            'Returned' => 'success',
                                        ];
                                    @endphp
                                    <span class="badge badge-{{ $statusColors[$loan->status] ?? 'secondary' }}">
                                        {{ $statuses[$loan->status] ?? $loan->status }}
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <a href="{{ route('staff.loans.show', $loan) }}" class="btn btn-sm" style="background-color: white; color: #1e40af; border: 1px solid #1e40af; padding: 0.25rem 0.75rem; text-decoration: none; border-radius: 0.25rem; font-size: 0.875rem;">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        <div style="display: flex; justify-content: center; margin-top: 2rem;">
            {{ $loans->links() }}
        </div>
    @else
        <div class="table-container">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-inbox"></i>
                </div>
                <div class="empty-state-text">
                    @if (request('status'))
                        No loan requests with this status.
                    @else
                        You have not submitted any loan requests yet.
                    @endif
                </div>
                <a href="{{ route('staff.loans.create') }}" class="btn btn-primary" style="background-color: #1e40af; color: white; border: none; padding: 0.5rem 1rem; text-decoration: none; border-radius: 0.375rem;">
                    <i class="bi bi-plus-circle"></i> Create First Request
                </a>
            </div>
        </div>
    @endif
</div>
@endsection
