@extends('layouts.app')

@section('title', 'My Loan Requests - SIMBAR')

@section('content')
<style>
    .page-header {
        margin-bottom: var(--spacing-2xl);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: var(--spacing-lg);
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
        margin: 0;
    }

    .table-card {
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        overflow: hidden;
    }

    .table-card table {
        border-collapse: collapse;
        width: 100%;
    }

    .table-card thead th {
        padding: var(--spacing-md) var(--spacing-lg);
        text-align: left;
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-700);
        background-color: var(--gray-50);
        border-bottom: 1px solid var(--gray-200);
    }

    .table-card tbody td {
        padding: var(--spacing-md) var(--spacing-lg);
        border-bottom: 1px solid var(--gray-200);
        font-size: var(--font-size-sm);
    }

    .table-card tbody tr:hover {
        background-color: var(--gray-50);
    }

    .table-card tbody tr:last-child td {
        border-bottom: none;
    }

    .empty-state {
        text-align: center;
        padding: var(--spacing-2xl) var(--spacing-lg);
        background-color: var(--gray-50);
        border-radius: var(--radius-lg);
        border: 1px solid var(--gray-200);
    }

    .empty-state-icon {
        font-size: 3rem;
        color: var(--gray-300);
        margin-bottom: var(--spacing-md);
    }

    .empty-state-text {
        color: var(--gray-500);
        margin-bottom: var(--spacing-md);
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 0 var(--spacing-lg);">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <div class="page-title">
                <i class="bi bi-clock-history"></i>
                My Loan Requests
            </div>
            <p class="page-subtitle">View and manage your loan requests</p>
        </div>
        <a href="{{ route('staff.loans.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> New Loan Request
        </a>
    </div>

    <!-- Filter Card -->
    <div class="card" style="margin-bottom: var(--spacing-lg);">
        <div class="card-body">
            <form method="GET" action="{{ route('staff.loans.index') }}" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: var(--spacing-md); align-items: end;">
                <div>
                    <label for="status" class="form-label" style="margin-bottom: var(--spacing-xs); font-size: var(--font-size-sm); color: var(--gray-700);">Filter by Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">-- All Status --</option>
                        @foreach ($statuses as $statusKey => $statusLabel)
                            <option value="{{ $statusKey }}" @if (request('status') == $statusKey) selected @endif>
                                {{ $statusLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="bi bi-filter"></i> Apply Filter
                    </button>
                </div>

                @if (request('status'))
                    <div>
                        <a href="{{ route('staff.loans.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i> Clear
                        </a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    <!-- Loan Requests Table -->
    @if ($loans->count() > 0)
        <div class="table-card" style="margin-bottom: var(--spacing-lg);">
            <table>
                <thead>
                    <tr>
                        <th>Requested Date</th>
                        <th>Items</th>
                        <th>Quantity</th>
                        <th>Purpose</th>
                        <th>Return Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($loans as $loan)
                        <tr>
                            <td style="color: var(--gray-600);">
                                {{ $loan->requested_at->format('d M Y H:i') }}
                            </td>
                            <td style="color: var(--gray-600);">
                                {{ $loan->getTotalItemsCount() }}
                            </td>
                            <td style="color: var(--gray-600);">
                                {{ $loan->getTotalQuantity() }} units
                            </td>
                            <td style="color: var(--gray-600); max-width: 200px;">
                                <div style="overflow: hidden; text-overflow: ellipsis;">
                                    {{ $loan->purpose ?? '-' }}
                                </div>
                            </td>
                            <td style="color: var(--gray-600);">
                                @php
                                    $returnDate = $loan->getEarliestReturnDate();
                                @endphp
                                @if ($returnDate)
                                    {{ $returnDate->format('d M Y') }}
                                    @if ($loan->isOverdue())
                                        <span class="badge badge-danger" style="margin-left: var(--spacing-sm);">Overdue</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $loan->status === 'Menunggu Persetujuan' ? 'warning' : ($loan->status === 'Disetujui' ? 'info' : ($loan->status === 'Ditolak' ? 'danger' : ($loan->status === 'Dipinjam' ? 'primary' : 'success'))) }}">
                                    {{ $statuses[$loan->status] ?? $loan->status }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('staff.loans.show', $loan) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i> View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div style="display: flex; justify-content: center; margin-top: var(--spacing-2xl);">
            {{ $loans->links() }}
        </div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="bi bi-inbox"></i>
            </div>
            <div class="empty-state-text">You haven't submitted any loan requests yet.</div>
            <a href="{{ route('staff.loans.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create First Loan Request
            </a>
        </div>
    @endif
</div>
@endsection
