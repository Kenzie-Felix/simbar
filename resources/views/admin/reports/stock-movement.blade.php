@extends('layouts.app')

@section('title', 'Stock Movement Reports - SIMBAR')

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
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 0 var(--spacing-lg);">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1>
                <i class="bi bi-arrow-left-right"></i>
                Stock Movement History
            </h1>
            <p>Track all stock in and out transactions</p>
        </div>
        <div style="display: flex; gap: var(--spacing-md);">
            <a href="{{ route('admin.reports.export-stock-movement', request()->query()) }}" class="btn btn-success">
                <i class="bi bi-download"></i> Export to CSV
            </a>
            <a href="{{ route('admin.reports.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card" style="margin-bottom: var(--spacing-lg);">
        <div class="card-header">
            <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                <i class="bi bi-funnel"></i> Filters
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: var(--spacing-md); align-items: end;">
                <div>
                    <label for="date_from" class="form-label" style="font-size: var(--font-size-sm); color: var(--gray-700);">From Date</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div>
                    <label for="date_to" class="form-label" style="font-size: var(--font-size-sm); color: var(--gray-700);">To Date</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div>
                    <label for="type" class="form-label" style="font-size: var(--font-size-sm); color: var(--gray-700);">Type</label>
                    <select class="form-select" id="type" name="type">
                        <option value="">-- All Types --</option>
                        <option value="In" {{ request('type') === 'In' ? 'selected' : '' }}>Stock In</option>
                        <option value="Out" {{ request('type') === 'Out' ? 'selected' : '' }}>Stock Out</option>
                        <option value="Adjustment" {{ request('type') === 'Adjustment' ? 'selected' : '' }}>Adjustment</option>
                    </select>
                </div>
                <div>
                    <label for="reference_type" class="form-label" style="font-size: var(--font-size-sm); color: var(--gray-700);">Reference</label>
                    <select class="form-select" id="reference_type" name="reference_type">
                        <option value="">-- All References --</option>
                        <option value="Loan" {{ request('reference_type') === 'Loan' ? 'selected' : '' }}>Loan</option>
                        <option value="Return" {{ request('reference_type') === 'Return' ? 'selected' : '' }}>Return</option>
                        <option value="Purchase" {{ request('reference_type') === 'Purchase' ? 'selected' : '' }}>Purchase</option>
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="bi bi-search"></i> Apply
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Results -->
    <div class="table-card">
        <div style="padding: var(--spacing-lg); background-color: var(--gray-50); border-bottom: 1px solid var(--gray-200);">
            <h5 style="margin: 0;">Stock Movements ({{ $movements->total() }} total)</h5>
        </div>
        @if ($movements->count() > 0)
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Date/Time</th>
                            <th>Item Code</th>
                            <th>Item Name</th>
                            <th>Type</th>
                            <th>Quantity</th>
                            <th>Reference</th>
                            <th>Created By</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($movements as $movement)
                            <tr>
                                <td>{{ $movement->created_at->format('Y-m-d H:i') }}</td>
                                <td style="font-weight: var(--font-weight-semibold);">{{ $movement->item->code }}</td>
                                <td>{{ $movement->item->name }}</td>
                                <td>
                                    <span class="badge badge-{{ $movement->type === 'In' ? 'success' : ($movement->type === 'Out' ? 'danger' : 'warning') }}">
                                        {{ $movement->getTypeLabel() }}
                                    </span>
                                </td>
                                <td style="font-weight: var(--font-weight-semibold);">{{ $movement->quantity }}</td>
                                <td>{{ $movement->getReference() ?? '-' }}</td>
                                <td>{{ $movement->createdBy->name }}</td>
                                <td style="font-size: var(--font-size-xs);">
                                    @if ($movement->notes)
                                        <span title="{{ $movement->notes }}" style="color: var(--gray-500);">
                                            {{ substr($movement->notes, 0, 30) }}{{ strlen($movement->notes) > 30 ? '...' : '' }}
                                        </span>
                                    @else
                                        <span style="color: var(--gray-400);">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="padding: var(--spacing-2xl); text-align: center; color: var(--gray-500);">
                No stock movements found
            </div>
        @endif
    </div>

    <!-- Pagination -->
    <div style="display: flex; justify-content: center; margin-top: var(--spacing-2xl);">
        {{ $movements->links() }}
    </div>
</div>
@endsection
