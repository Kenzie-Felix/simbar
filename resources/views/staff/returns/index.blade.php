@extends('layouts.app')

@section('title', 'My Return Requests - SIMBAR')

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
        color: var(--gray-600);
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
            gap: var(--spacing-md);
        }

        .page-title {
            font-size: var(--font-size-lg);
        }

        /* Convert table to cards on mobile */
        .table-card table thead {
            display: none;
        }

        .table-card table tbody,
        .table-card table tr,
        .table-card table td {
            display: block;
            width: 100%;
        }

        .table-card table tr {
            margin-bottom: var(--spacing-lg);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .table-card table td {
            padding: var(--spacing-md) !important;
            padding-left: 40% !important;
            position: relative;
            border: none;
            border-bottom: 1px solid var(--gray-100);
            font-size: var(--font-size-sm);
        }

        .table-card table td:last-child {
            border-bottom: none;
        }

        .table-card table td:before {
            content: attr(data-label);
            position: absolute;
            left: var(--spacing-md);
            top: var(--spacing-md);
            font-weight: var(--font-weight-semibold);
            font-size: var(--font-size-xs);
            color: var(--gray-600);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        form {
            grid-template-columns: 1fr !important;
        }

        .btn {
            width: 100%;
        }

        .card {
            border-radius: var(--radius-md);
        }

        .card-body {
            padding: var(--spacing-md);
        }
    }

    @media (max-width: 480px) {
        .page-title {
            font-size: var(--font-size-base);
            gap: var(--spacing-sm);
        }

        .page-subtitle {
            font-size: var(--font-size-xs);
        }

        .table-card table td {
            font-size: var(--font-size-xs);
        }

        .table-card table td:before {
            font-size: var(--font-size-2xs);
        }

        .badge {
            font-size: var(--font-size-2xs);
        }
    }
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 0 var(--spacing-lg);">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <div class="page-title">
                <i class="bi bi-list-check"></i>
                My Return Requests
            </div>
            <p class="page-subtitle">View and manage your return requests</p>
        </div>
        <a href="{{ route('staff.returns.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> New Return Request
        </a>
    </div>

    <!-- Filter Card -->
    <div class="card" style="margin-bottom: var(--spacing-lg);">
        <div class="card-body">
            <form method="GET" action="{{ route('staff.returns.index') }}" style="display: grid; grid-template-columns: 1fr 1fr auto; gap: var(--spacing-md); align-items: end;">
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
            </form>
        </div>
    </div>

    <!-- Returns List -->
    @if ($returns->count() > 0)
        <div class="table-card" style="margin-bottom: var(--spacing-lg);">
            <table>
                <thead>
                    <tr>
                        <th>Return ID</th>
                        <th>Items</th>
                        <th>Quantity</th>
                        <th>Submitted</th>
                        <th>Status</th>
                        <th>Verified By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($returns as $return)
                        <tr>
                            <td data-label="Return ID" style="font-weight: var(--font-weight-semibold); color: var(--primary);">#{{ $return->id }}</td>
                            <td data-label="Items">{{ $return->getReturnedItemsCount() }}</td>
                            <td data-label="Quantity">{{ $return->getTotalReturnedQuantity() }} units</td>
                            <td data-label="Submitted">{{ $return->submitted_at->format('d M Y H:i') }}</td>
                            <td data-label="Status">
                                <span class="badge badge-{{ $return->status === 'Pending Verification' ? 'warning' : ($return->status === 'Accepted' ? 'success' : 'danger') }}">
                                    {{ $statuses[$return->status] ?? $return->status }}
                                </span>
                            </td>
                            <td data-label="Verified By">{{ $return->verifier?->name ?? '-' }}</td>
                            <td data-label="Action">
                                <a href="{{ route('staff.returns.show', $return) }}" class="btn btn-sm btn-outline-primary">
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
            {{ $returns->links() }}
        </div>
    @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="bi bi-inbox"></i>
            </div>
            <div class="empty-state-text">
                @if (request('status'))
                    No return requests match your filter.
                @else
                    You haven't submitted any return requests yet.
                @endif
            </div>
            <a href="{{ route('staff.returns.create') }}" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Create First Return Request
            </a>
        </div>
    @endif
</div>
@endsection
