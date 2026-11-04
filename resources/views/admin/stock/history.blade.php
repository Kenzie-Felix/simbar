@extends('layouts.app')

@section('title', 'Stock Movement History - SIMBAR')

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

    .movements-table {
        border-collapse: collapse;
        width: 100%;
    }

    .movements-table thead th {
        padding: var(--spacing-md) var(--spacing-lg);
        text-align: left;
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-700);
        background-color: var(--gray-50);
        border-bottom: 1px solid var(--gray-200);
    }

    .movements-table tbody td {
        padding: var(--spacing-md) var(--spacing-lg);
        border-bottom: 1px solid var(--gray-200);
        font-size: var(--font-size-sm);
    }

    .movements-table tbody tr:hover {
        background-color: var(--gray-50);
    }

    .movements-table tbody tr:last-child td {
        border-bottom: none;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
        }

        .movements-table thead th,
        .movements-table tbody td {
            padding: var(--spacing-sm) var(--spacing-md);
            font-size: var(--font-size-xs);
        }
    }
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 0 var(--spacing-lg);">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1>
                <i class="bi bi-clock-history"></i>
                Stock Movement History
            </h1>
            <p>Track all stock in and out transactions</p>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card" style="margin-bottom: var(--spacing-lg);">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.stock.history') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-md); align-items: end;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: var(--font-size-sm);">Item</label>
                    <select class="form-select" name="item_id">
                        <option value="">-- All Items --</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" @if (request('item_id') == $item->id) selected @endif>
                                {{ $item->code }} - {{ $item->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: var(--font-size-sm);">Type</label>
                    <select class="form-select" name="type">
                        <option value="">-- All Types --</option>
                        <option value="In" @if (request('type') == 'In') selected @endif>Stock In</option>
                        <option value="Out" @if (request('type') == 'Out') selected @endif>Stock Out</option>
                        <option value="Adjustment" @if (request('type') == 'Adjustment') selected @endif>Adjustment</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel"></i> Apply Filters
                </button>
                
                @if (request('item_id') || request('type'))
                    <a href="{{ route('admin.stock.history') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Movements Table -->
    @if ($movements->count() > 0)
        <div class="card">
            <div class="card-body p-0">
                <div style="overflow-x: auto;">
                    <table class="movements-table">
                        <thead>
                            <tr>
                                <th>Date/Time</th>
                                <th>Item</th>
                                <th>Type</th>
                                <th style="text-align: center;">Quantity</th>
                                <th>Reference</th>
                                <th>By</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($movements as $movement)
                                <tr>
                                    <td>
                                        <small style="color: var(--gray-600);">{{ $movement->created_at->format('M d, Y H:i') }}</small>
                                    </td>
                                    <td>
                                        <strong>{{ $movement->item->name }}</strong><br>
                                        <small style="color: var(--gray-500);">{{ $movement->item->code }}</small>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $movement->type === 'In' ? 'success' : ($movement->type === 'Out' ? 'danger' : 'warning') }}">
                                            {{ $movement->type }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;">
                                        <strong>{{ $movement->quantity }}</strong>
                                    </td>
                                    <td>
                                        <small style="color: var(--gray-500);">{{ $movement->getReference() ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <small>{{ $movement->createdBy->name }}</small>
                                    </td>
                                    <td>
                                        <small style="color: var(--gray-500);">
                                            {{ $movement->notes ? Str::limit($movement->notes, 40) : '-' }}
                                        </small>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if ($movements->hasPages())
            <div style="display: flex; justify-content: center; margin-top: var(--spacing-xl);">
                {{ $movements->links() }}
            </div>
        @endif
    @else
        <div class="card">
            <div class="empty-state" style="text-align: center; padding: var(--spacing-xl) var(--spacing-lg);">
                <div class="empty-state-icon" style="margin-bottom: var(--spacing-lg);">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: var(--gray-400);"></i>
                </div>
                <p class="empty-state-text" style="font-size: var(--font-size-base); color: var(--gray-500);">
                    @if (request('item_id') || request('type'))
                        No stock movements match your filters.
                    @else
                        No stock movements found.
                    @endif
                </p>
            </div>
        </div>
    @endif
</div>

@endsection
