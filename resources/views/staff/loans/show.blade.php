@extends('layouts.app')

@section('title', 'Loan Request Details - SIMBAR')

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

    .alert-info {
        background-color: var(--info-light);
        color: var(--info);
        border: 1px solid var(--info);
        border-left: 4px solid var(--info);
    }

    .alert-warning {
        background-color: var(--warning-light);
        color: var(--warning);
        border: 1px solid var(--warning);
        border-left: 4px solid var(--warning);
    }

    .alert-danger {
        background-color: var(--danger-light);
        color: var(--danger);
        border: 1px solid var(--danger);
        border-left: 4px solid var(--danger);
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
                Loan Request Details
            </h1>
            <p>Request #{{ $loan->id }}</p>
        </div>
        <a href="{{ route('staff.loans.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to List
        </a>
    </div>

    <!-- Success Message -->
    @if (session('success'))
        <div class="alert alert-success" style="padding: var(--spacing-lg); border-radius: var(--radius-lg); margin-bottom: var(--spacing-lg); display: flex; align-items: center; gap: var(--spacing-md);">
            <i class="bi bi-check-circle"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="content-grid">
        <!-- Request Info -->
        <div>
            <!-- Status Card -->
            <div class="card" style="margin-bottom: var(--spacing-lg);">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                            <i class="bi bi-info-circle"></i> Request Information
                        </h5>
                        <span class="badge badge-{{ $loan->status === 'Menunggu Persetujuan' ? 'warning' : ($loan->status === 'Disetujui' ? 'info' : ($loan->status === 'Ditolak' ? 'danger' : ($loan->status === 'Dipinjam' ? 'primary' : 'success'))) }}">
                            {{ $statuses[$loan->status] ?? $loan->status }}
                        </span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <div class="detail-field">
                            <span class="detail-label">Requested Date</span>
                            <span class="detail-value">{{ $loan->requested_at->format('d M Y H:i') }}</span>
                        </div>
                        <div class="detail-field">
                            <span class="detail-label">Requested By</span>
                            <span class="detail-value">{{ $loan->user->name }}</span>
                        </div>
                    </div>

                    @if ($loan->approved_at)
                        <div class="detail-row" style="padding-top: var(--spacing-md); border-top: 1px solid var(--gray-200);">
                            <div class="detail-field">
                                <span class="detail-label">Approved Date</span>
                                <span class="detail-value">{{ $loan->approved_at->format('d M Y H:i') }}</span>
                            </div>
                            <div class="detail-field">
                                <span class="detail-label">Approved By</span>
                                <span class="detail-value">{{ $loan->approver?->name ?? '-' }}</span>
                            </div>
                        </div>
                    @elseif ($loan->rejection_reason)
                        <div style="padding-top: var(--spacing-md); border-top: 1px solid var(--gray-200);">
                            <span class="detail-label">Rejection Reason</span>
                            <p style="color: var(--danger); margin: var(--spacing-xs) 0 0 0; white-space: pre-wrap;">{{ $loan->rejection_reason }}</p>
                        </div>
                    @endif

                    @if ($loan->purpose)
                        <div style="padding-top: var(--spacing-md); border-top: 1px solid var(--gray-200);">
                            <span class="detail-label">Purpose of Loan</span>
                            <p style="color: var(--gray-700); margin: var(--spacing-xs) 0 0 0; white-space: pre-wrap;">{{ $loan->purpose }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Items Card -->
            <div class="card" style="margin-bottom: var(--spacing-lg);">
                <div class="card-header">
                    <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                        <i class="bi bi-collection"></i> Items ({{ $loan->getTotalItemsCount() }} items, {{ $loan->getTotalQuantity() }} units)
                    </h5>
                </div>
                <div style="overflow-x: auto;">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Quantity</th>
                                <th>Planned Return</th>
                                <th>Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($loan->loanItems as $loanItem)
                                <tr>
                                    <td style="font-weight: var(--font-weight-semibold);">{{ $loanItem->item->code }}</td>
                                    <td>{{ $loanItem->item->name }}</td>
                                    <td style="font-weight: var(--font-weight-semibold);">{{ $loanItem->quantity }} units</td>
                                    <td>
                                        {{ $loanItem->planned_return_at->format('d M Y') }}
                                        @if ($loanItem->planned_return_at->isPast() && !$loanItem->returned_at)
                                            <span class="badge badge-danger" style="margin-left: var(--spacing-sm);">Overdue</span>
                                        @endif
                                    </td>
                                    <td>{{ $loanItem->item->category->name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Summary Sidebar -->
        <div>
            <!-- Summary Card -->
            <div class="card sticky-action" style="margin-bottom: var(--spacing-lg);">
                <div class="card-header">
                    <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                        <i class="bi bi-speedometer2"></i> Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div style="margin-bottom: var(--spacing-lg);">
                        <p style="color: var(--gray-500); margin: 0 0 var(--spacing-xs) 0; font-size: var(--font-size-sm);">Total Items</p>
                        <h3 style="margin: 0; color: var(--gray-900);">{{ $loan->getTotalItemsCount() }}</h3>
                    </div>

                    <div style="margin-bottom: var(--spacing-lg); padding-bottom: var(--spacing-lg); border-bottom: 1px solid var(--gray-200);">
                        <p style="color: var(--gray-500); margin: 0 0 var(--spacing-xs) 0; font-size: var(--font-size-sm);">Total Quantity</p>
                        <h3 style="margin: 0; color: var(--gray-900);">{{ $loan->getTotalQuantity() }} units</h3>
                    </div>

                    <div style="margin-bottom: var(--spacing-lg); padding-bottom: var(--spacing-lg); border-bottom: 1px solid var(--gray-200);">
                        <p style="color: var(--gray-500); margin: 0 0 var(--spacing-xs) 0; font-size: var(--font-size-sm);">Status</p>
                        <span class="badge badge-{{ $loan->status === 'Menunggu Persetujuan' ? 'warning' : ($loan->status === 'Disetujui' ? 'info' : ($loan->status === 'Ditolak' ? 'danger' : ($loan->status === 'Dipinjam' ? 'primary' : 'success'))) }}">
                            {{ $statuses[$loan->status] ?? $loan->status }}
                        </span>
                    </div>

                    @if ($loan->isOverdue())
                        <div class="alert alert-danger" style="padding: var(--spacing-md); border-radius: var(--radius-sm); margin-bottom: 0;">
                            <i class="bi bi-exclamation-triangle"></i>
                            <strong>Overdue</strong>
                            <p style="margin: var(--spacing-xs) 0 0 0; font-size: var(--font-size-sm);">This loan has passed its return date. Please submit a return request immediately.</p>
                        </div>
                    @elseif ($loan->isBorrowed())
                        <div class="alert alert-info" style="padding: var(--spacing-md); border-radius: var(--radius-sm); margin-bottom: 0;">
                            <i class="bi bi-info-circle"></i>
                            <strong>Days Until Due</strong>
                            <p style="margin: var(--spacing-xs) 0 0 0; font-size: var(--font-size-sm);">{{ $loan->daysUntilDue() }} days until return date</p>
                        </div>
                    @endif

                    @if ($loan->isPending())
                        <div class="alert alert-warning" style="padding: var(--spacing-md); border-radius: var(--radius-sm); margin: var(--spacing-md) 0 0 0;">
                            <i class="bi bi-clock"></i>
                            <strong>Pending Approval</strong>
                            <p style="margin: var(--spacing-xs) 0 0 0; font-size: var(--font-size-sm);">Waiting for admin approval. You'll be notified once reviewed.</p>
                        </div>
                    @elseif ($loan->isRejected())
                        <div class="alert alert-danger" style="padding: var(--spacing-md); border-radius: var(--radius-sm); margin: var(--spacing-md) 0 0 0;">
                            <i class="bi bi-x-circle"></i>
                            <strong>Rejected</strong>
                            <p style="margin: var(--spacing-xs) 0 0 0; font-size: var(--font-size-sm);">Contact admin for more information.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
