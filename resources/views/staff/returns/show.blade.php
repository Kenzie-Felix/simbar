@extends('layouts.app')

@section('title', 'Return Request Details - SIMBAR')

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

    .detail-field {
        margin-bottom: var(--spacing-md);
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

    .items-table {
        border-collapse: collapse;
        width: 100%;
    }

    .items-table thead th {
        padding: var(--spacing-md) var(--spacing-lg);
        text-align: left;
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-700);
        background-color: var(--gray-50);
        border-bottom: 1px solid var(--gray-200);
    }

    .items-table tbody td {
        padding: var(--spacing-md) var(--spacing-lg);
        border-bottom: 1px solid var(--gray-200);
        font-size: var(--font-size-sm);
    }

    .items-table tbody tr:hover {
        background-color: var(--gray-50);
    }

    .items-table tbody tr:last-child td {
        border-bottom: none;
    }

    .sticky-action {
        position: sticky;
        top: 100px;
    }

    .alert-success {
        background-color: var(--success-light);
        color: var(--success);
        border: 1px solid var(--success);
        border-left: 4px solid var(--success);
    }

    .alert-danger {
        background-color: var(--danger-light);
        color: var(--danger);
        border: 1px solid var(--danger);
        border-left: 4px solid var(--danger);
    }

    .alert-warning {
        background-color: var(--warning-light);
        color: var(--warning);
        border: 1px solid var(--warning);
        border-left: 4px solid var(--warning);
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }

        .sticky-action {
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
                <i class="bi bi-info-circle"></i>
                Return Request #{{ $return->id }}
            </h1>
            <p>Details and verification status</p>
        </div>
        <a href="{{ route('staff.returns.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <div class="content-grid">
        <div>
            <!-- Request Information -->
            <div class="card" style="margin-bottom: var(--spacing-lg);">
                <div class="card-header">
                    <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                        <i class="bi bi-info-circle"></i> Request Information
                    </h5>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-lg);">
                        <div class="detail-field">
                            <span class="detail-label">Loan Reference</span>
                            <span class="detail-value" style="color: var(--primary);">#{{ $return->loan->id }}</span>
                        </div>
                        <div class="detail-field">
                            <span class="detail-label">Submitted Date</span>
                            <span class="detail-value">{{ $return->submitted_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--spacing-lg); padding-top: var(--spacing-md); border-top: 1px solid var(--gray-200);">
                        <div class="detail-field">
                            <span class="detail-label">Status</span>
                            <span class="badge badge-{{ $return->status === 'Pending Verification' ? 'warning' : ($return->status === 'Accepted' ? 'success' : 'danger') }}" style="display: inline-block;">
                                {{ $return->status }}
                            </span>
                        </div>
                        @if ($return->verified_at)
                            <div class="detail-field">
                                <span class="detail-label">Verified Date</span>
                                <span class="detail-value">{{ $return->verified_at->format('d M Y H:i') }}</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Returned Items -->
            <div class="card" style="margin-bottom: var(--spacing-lg);">
                <div class="card-header">
                    <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                        <i class="bi bi-collection"></i> Returned Items
                    </h5>
                </div>
                <div style="overflow-x: auto;">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Quantity</th>
                                <th>Condition</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($return->returnItems as $returnItem)
                                <tr>
                                    <td style="font-weight: var(--font-weight-semibold);">{{ $returnItem->loanItem->item->code }}</td>
                                    <td>{{ $returnItem->loanItem->item->name }}</td>
                                    <td style="font-weight: var(--font-weight-semibold);">{{ $returnItem->quantity }}</td>
                                    <td>
                                        <span class="badge badge-{{ $returnItem->loanItem->item->getConditionBadgeColor() }}">{{ $returnItem->condition }}</span>
                                    </td>
                                    <td>{{ $returnItem->notes ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Verification Details -->
            @if ($return->isAccepted())
                <div class="card" style="margin-bottom: var(--spacing-lg); border: 1px solid var(--success); border-left: 4px solid var(--success);">
                    <div class="card-header" style="background-color: var(--success-light); border-bottom: 1px solid var(--success);">
                        <h5 style="margin: 0; color: var(--success); display: flex; align-items: center; gap: var(--spacing-md);">
                            <i class="bi bi-check-circle"></i> Verification Details
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="detail-field">
                            <span class="detail-label">Verified By</span>
                            <span class="detail-value">{{ $return->verifier?->name ?? 'System' }}</span>
                        </div>
                        <div class="detail-field">
                            <span class="detail-label">Verification Date</span>
                            <span class="detail-value">{{ $return->verified_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            @elseif ($return->isRejected())
                <div class="card" style="margin-bottom: var(--spacing-lg); border: 1px solid var(--danger); border-left: 4px solid var(--danger);">
                    <div class="card-header" style="background-color: var(--danger-light); border-bottom: 1px solid var(--danger);">
                        <h5 style="margin: 0; color: var(--danger); display: flex; align-items: center; gap: var(--spacing-md);">
                            <i class="bi bi-x-circle"></i> Rejection Reason
                        </h5>
                    </div>
                    <div class="card-body">
                        <p style="color: var(--danger); margin: 0; white-space: pre-wrap;">{{ $return->rejection_reason }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Status Sidebar -->
        <div>
            <div class="card sticky-action">
                <div class="card-header">
                    <h5 style="margin: 0;">Return Status</h5>
                </div>
                <div class="card-body">
                    <p style="color: var(--gray-500); margin: 0 0 var(--spacing-xs) 0; font-size: var(--font-size-sm);">Current Status</p>
                    <span class="badge badge-{{ $return->status === 'Pending Verification' ? 'warning' : ($return->status === 'Accepted' ? 'success' : 'danger') }}" style="display: inline-block; padding: var(--spacing-sm) var(--spacing-md); font-size: var(--font-size-base);">
                        {{ $return->status }}
                    </span>

                    @if ($return->isPending())
                        <div class="alert alert-warning" style="padding: var(--spacing-md); border-radius: var(--radius-sm); margin: var(--spacing-md) 0 0 0;">
                            <i class="bi bi-hourglass-split"></i>
                            <strong>Awaiting Verification</strong>
                            <p style="margin: var(--spacing-xs) 0 0 0; font-size: var(--font-size-sm);">Admin is reviewing your return request.</p>
                        </div>
                    @elseif ($return->isAccepted())
                        <div class="alert alert-success" style="padding: var(--spacing-md); border-radius: var(--radius-sm); margin: var(--spacing-md) 0 0 0;">
                            <i class="bi bi-check-circle"></i>
                            <strong>Return Accepted</strong>
                            <p style="margin: var(--spacing-xs) 0 0 0; font-size: var(--font-size-sm);">Your items have been verified and stock has been restored.</p>
                        </div>
                    @elseif ($return->isRejected())
                        <div class="alert alert-danger" style="padding: var(--spacing-md); border-radius: var(--radius-sm); margin: var(--spacing-md) 0 0 0;">
                            <i class="bi bi-x-circle"></i>
                            <strong>Return Rejected</strong>
                            <p style="margin: var(--spacing-xs) 0 0 0; font-size: var(--font-size-sm);">Please contact admin for more information.</p>
                        </div>
                    @endif

                    <!-- Summary -->
                    <div style="margin-top: var(--spacing-lg); padding-top: var(--spacing-lg); border-top: 1px solid var(--gray-200);">
                        <p style="color: var(--gray-500); margin: 0 0 var(--spacing-xs) 0; font-size: var(--font-size-sm);">Total Items</p>
                        <h4 style="margin: 0 0 var(--spacing-lg) 0;">{{ $return->getReturnedItemsCount() }}</h4>

                        <p style="color: var(--gray-500); margin: 0 0 var(--spacing-xs) 0; font-size: var(--font-size-sm);">Total Quantity</p>
                        <h4 style="margin: 0;">{{ $return->getTotalReturnedQuantity() }} units</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
