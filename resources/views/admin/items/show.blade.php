@extends('layouts.app')

@section('title', $item->name . ' - SIMBAR')

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

    .content-grid {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: var(--spacing-lg);
    }

    .detail-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--spacing-lg);
    }

    .detail-field {
        padding-bottom: var(--spacing-md);
    }

    .detail-label {
        font-size: var(--font-size-sm);
        color: var(--gray-500);
        margin-bottom: var(--spacing-xs);
        display: block;
        font-weight: var(--font-weight-medium);
    }

    .detail-value {
        font-size: var(--font-size-base);
        color: var(--gray-900);
        font-weight: var(--font-weight-semibold);
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

    .stat-box {
        text-align: center;
        padding: var(--spacing-lg);
        border-radius: var(--radius-lg);
        background-color: var(--gray-50);
    }

    .stat-box-label {
        font-size: var(--font-size-sm);
        color: var(--gray-500);
        margin-bottom: var(--spacing-sm);
    }

    .stat-box-value {
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
        color: var(--gray-900);
    }

    .sticky-sidebar {
        position: sticky;
        top: 100px;
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }

        .detail-row {
            grid-template-columns: 1fr;
        }

        .sticky-sidebar {
            position: relative;
            top: auto;
        }
    }
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 0 var(--spacing-lg);">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1>
                <i class="bi bi-collection"></i>
                {{ $item->name }}
            </h1>
            <p>Code: <strong>{{ $item->code }}</strong></p>
        </div>
        <div style="display: flex; gap: var(--spacing-sm);">
            <a href="{{ route('admin.items.edit', $item) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="content-grid">
        <!-- Main Content -->
        <div>
            <!-- Item Details -->
            <div class="card">
                <div class="card-header">
                    <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                        <i class="bi bi-info-circle"></i> Item Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <div class="detail-field">
                            <span class="detail-label">Category</span>
                            <span class="badge badge-secondary">{{ $item->category->name }}</span>
                        </div>
                        <div class="detail-field">
                            <span class="detail-label">Location</span>
                            <span class="detail-value">{{ $item->location->name }}</span>
                        </div>
                    </div>
                    @if ($item->description)
                        <div class="detail-field">
                            <span class="detail-label">Description</span>
                            <span class="detail-value" style="font-size: var(--font-size-sm);">{{ $item->description }}</span>
                        </div>
                    @endif
                    <div class="detail-row">
                        <div class="detail-field">
                            <span class="detail-label">Created</span>
                            <span class="detail-value" style="font-size: var(--font-size-sm);">{{ $item->created_at->format('M d, Y H:i') }}</span>
                        </div>
                        <div class="detail-field">
                            <span class="detail-label">Last Updated</span>
                            <span class="detail-value" style="font-size: var(--font-size-sm);">{{ $item->updated_at->format('M d, Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Summary -->
            <div class="card" style="margin-top: var(--spacing-lg);">
                <div class="card-header">
                    <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                        <i class="bi bi-graph-up"></i> Stock Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--spacing-lg);">
                        <div class="stat-box">
                            <div class="stat-box-label">Total Stock</div>
                            <div class="stat-box-value" style="color: var(--info);">{{ $item->total_stock }}</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-box-label">Available</div>
                            <div class="stat-box-value" style="color: var(--success);">{{ $item->available_stock }}</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-box-label">In Use</div>
                            <div class="stat-box-value" style="color: var(--warning);">{{ $item->total_stock - $item->available_stock }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Condition -->
            <div class="card" style="margin-top: var(--spacing-lg);">
                <div class="card-header">
                    <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                        <i class="bi bi-info-circle"></i> Item Condition
                    </h5>
                </div>
                <div class="card-body">
                    <span class="badge badge-{{ $item->getConditionBadgeColor() }}" style="padding: var(--spacing-sm) var(--spacing-md); font-size: var(--font-size-base);">
                        {{ $item->condition }}
                    </span>
                </div>
            </div>

            <!-- Condition History -->
            @if ($item->conditionLogs->count() > 0)
                <div class="card" style="margin-top: var(--spacing-lg);">
                    <div class="card-header">
                        <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                            <i class="bi bi-clock-history"></i> Condition History
                        </h5>
                    </div>
                    <div class="card-body">
                        <div style="position: relative; padding-left: 30px;">
                            @foreach ($item->conditionLogs as $log)
                                <div style="padding-bottom: 15px; border-left: 2px solid var(--gray-200); padding-left: 15px;">
                                    <small style="color: var(--gray-500);">{{ $log->created_at->format('M d, Y H:i') }}</small>
                                    <p style="small; margin: var(--spacing-xs) 0 0 0; font-weight: var(--font-weight-medium);">
                                        {{ $log->changedBy->name }} changed condition
                                    </p>
                                    <p style="font-size: var(--font-size-sm); color: var(--gray-600); margin: 0;">
                                        {{ $log->getConditionChangeLabel() }}
                                        @if ($log->reason)
                                            <br>Reason: {{ $log->reason }}
                                        @endif
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Stock Movements -->
            @if ($item->stockMovements->count() > 0)
                <div class="card" style="margin-top: var(--spacing-lg);">
                    <div class="card-header">
                        <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                            <i class="bi bi-arrow-left-right"></i> Stock Movements
                        </h5>
                    </div>
                    <div style="overflow-x: auto;">
                        <table class="movements-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Type</th>
                                    <th>Quantity</th>
                                    <th>By</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($item->stockMovements->sortByDesc('created_at')->take(10) as $movement)
                                    <tr>
                                        <td>{{ $movement->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            <span class="badge badge-{{ $movement->type === 'In' ? 'success' : ($movement->type === 'Out' ? 'danger' : 'secondary') }}">
                                                {{ $movement->type }}
                                            </span>
                                        </td>
                                        <td>{{ $movement->quantity }}</td>
                                        <td><small>{{ $movement->createdBy->name }}</small></td>
                                        <td><small style="color: var(--gray-500);">{{ $movement->notes ? Str::limit($movement->notes, 30) : '-' }}</small></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Quick Stats -->
            <div class="card sticky-sidebar">
                <div class="card-header">
                    <h5 style="margin: 0;">Quick Stats</h5>
                </div>
                <div class="card-body">
                    <div style="margin-bottom: var(--spacing-lg);">
                        <p class="detail-label">Loan Requests</p>
                        <h3 style="margin: 0; color: var(--primary);">{{ $item->loanItems()->count() }}</h3>
                    </div>
                    <div style="margin-bottom: var(--spacing-lg);">
                        <p class="detail-label">Condition Changes</p>
                        <h3 style="margin: 0; color: var(--warning);">{{ $item->conditionLogs()->count() }}</h3>
                    </div>
                    <div>
                        <p class="detail-label">Stock Movements</p>
                        <h3 style="margin: 0; color: var(--info);">{{ $item->stockMovements()->count() }}</h3>
                    </div>
                </div>
            </div>

            <!-- Availability Status -->
            <div class="card" style="margin-top: var(--spacing-lg);">
                <div class="card-body" style="border: 2px solid {{ $item->isAvailable() ? 'var(--success)' : 'var(--danger)' }}; border-radius: var(--radius-lg); padding: var(--spacing-lg);">
                    <h6 style="display: flex; align-items: center; gap: var(--spacing-sm); margin: 0 0 var(--spacing-md) 0;">
                        <i class="bi bi-{{ $item->isAvailable() ? 'check-circle-fill' : 'x-circle-fill' }}" style="color: {{ $item->isAvailable() ? 'var(--success)' : 'var(--danger)' }};"></i>
                        Availability
                    </h6>
                    @if ($item->isAvailable())
                        <p style="color: var(--success); margin: 0; font-size: var(--font-size-sm);">✓ Available for borrowing</p>
                    @else
                        <p style="color: var(--danger); margin: 0; font-size: var(--font-size-sm);">✗ Not available for borrowing</p>
                        <p style="color: var(--gray-500); margin: var(--spacing-md) 0 0 0; font-size: var(--font-size-xs);">
                            @if ($item->available_stock === 0)
                                Out of stock
                            @else
                                Condition: {{ $item->condition }}
                            @endif
                        </p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
