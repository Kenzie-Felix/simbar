@extends('layouts.app')

@section('title', 'Loan Request Details - SIMBAR')

@section('content')
<style>
    .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: 3rem;
        gap: 1.5rem;
    }

    .page-header-content h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #111827;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .page-header-content p {
        font-size: 0.875rem;
        color: #6b7280;
        margin: 0;
    }

    .content-grid {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: 1.5rem;
    }

    .detail-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
    }

    .detail-field {
        padding-bottom: 1rem;
    }

    .detail-label {
        font-size: 0.875rem;
        color: #6b7280;
        margin-bottom: 0.25rem;
        display: block;
        font-weight: 500;
    }

    .detail-value {
        font-size: 1rem;
        color: #111827;
        font-weight: 600;
    }

    .items-table {
        border-collapse: collapse;
        width: 100%;
    }

    .items-table thead th {
        padding: 1rem;
        text-align: left;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        background-color: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }

    .items-table tbody td {
        padding: 1rem;
        border-bottom: 1px solid #e5e7eb;
        font-size: 0.875rem;
        color: #374151;
    }

    .items-table tbody tr:hover {
        background-color: #f9fafb;
    }

    .items-table tbody tr:last-child td {
        border-bottom: none;
    }

    .sticky-action {
        position: sticky;
        top: 100px;
    }

    .card {
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        overflow: hidden;
    }

    .card-header {
        background: #f9fafb;
        padding: 1.5rem;
        border-bottom: 1px solid #e5e7eb;
    }

    .card-body {
        padding: 1.5rem;
    }

    .badge {
        display: inline-block;
        padding: 0.25rem 0.75rem;
        border-radius: 0.25rem;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-info {
        background: #d1e7f7;
        color: #1e3a8a;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-primary {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-success {
        background: #dcfce7;
        color: #166534;
    }

    .alert {
        padding: 1rem;
        border-radius: 0.375rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .alert-success {
        background-color: #dcfce7;
        color: #166534;
        border: 1px solid #86efac;
        border-left: 4px solid #10b981;
    }

    .alert-info {
        background-color: #d1e7f7;
        color: #1e3a8a;
        border: 1px solid #93c5fd;
        border-left: 4px solid #0ea5e9;
    }

    .alert-warning {
        background-color: #fef3c7;
        color: #92400e;
        border: 1px solid #fde047;
        border-left: 4px solid #f59e0b;
    }

    .alert-danger {
        background-color: #fee2e2;
        color: #991b1b;
        border: 1px solid #fca5a5;
        border-left: 4px solid #ef4444;
    }

    .btn {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 0.375rem;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
        border: 1px solid;
    }

    .btn-outline-secondary {
        background: white;
        color: #374151;
        border-color: #e5e7eb;
    }

    .btn-outline-secondary:hover {
        background: #f9fafb;
        border-color: #1e40af;
        color: #1e40af;
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

<div style="max-width: 1400px; margin: 0 auto; padding: 0 1.5rem;">
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
        <div class="alert alert-success">
            <i class="bi bi-check-circle"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <div class="content-grid">
        <!-- Request Info -->
        <div>
            <!-- Status Card -->
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <div style="display: flex; justify-content: space-between; align-items: center;">
                        <h5 style="margin: 0; display: flex; align-items: center; gap: 1rem; font-size: 1.125rem;">
                            <i class="bi bi-info-circle"></i> Request Information
                        </h5>
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
                    </div>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <div class="detail-field">
                            <span class="detail-label">Request Date</span>
                            <span class="detail-value">{{ $loan->requested_at ? $loan->requested_at->format('d M Y H:i') : '—' }}</span>
                        </div>
                        <div class="detail-field">
                            <span class="detail-label">Requested By</span>
                            <span class="detail-value">{{ $loan->user->name }}</span>
                        </div>
                    </div>

                    @if ($loan->approved_at)
                        <div class="detail-row" style="padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                            <div class="detail-field">
                                <span class="detail-label">Approval Date</span>
                                <span class="detail-value">{{ $loan->approved_at->format('d M Y H:i') }}</span>
                            </div>
                            <div class="detail-field">
                                <span class="detail-label">Approved By</span>
                                <span class="detail-value">{{ $loan->approver?->name ?? '-' }}</span>
                            </div>
                        </div>
                    @elseif ($loan->rejection_reason)
                        <div style="padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                            <span class="detail-label">Rejection Reason</span>
                            <p style="color: #ef4444; margin: 0.25rem 0 0 0; white-space: pre-wrap;">{{ $loan->rejection_reason }}</p>
                        </div>
                    @endif

                    @if ($loan->purpose)
                        <div style="padding-top: 1rem; border-top: 1px solid #e5e7eb;">
                            <span class="detail-label">Purpose of Loan</span>
                            <p style="color: #374151; margin: 0.25rem 0 0 0; white-space: pre-wrap;">{{ $loan->purpose }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Items Card -->
            <div class="card" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h5 style="margin: 0; display: flex; align-items: center; gap: 1rem; font-size: 1.125rem;">
                        <i class="bi bi-collection"></i> Items ({{ $loan->getTotalItemsCount() }} item, {{ $loan->getTotalQuantity() }} units)
                    </h5>
                </div>
                <div style="overflow-x: auto;">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Quantity</th>
                                <th>Return Date</th>
                                <th>Category</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($loan->loanItems as $loanItem)
                                <tr>
                                    <td style="font-weight: 600;">{{ $loanItem->item->code }}</td>
                                    <td>{{ $loanItem->item->name }}</td>
                                    <td style="font-weight: 600;">{{ $loanItem->quantity }} units</td>
                                    <td>
                                        @if ($loanItem->planned_return_at)
                                            {{ $loanItem->planned_return_at->format('d M Y') }}
                                            @if ($loanItem->planned_return_at->isPast() && !$loanItem->returned_at)
                                                <span class="badge badge-danger" style="margin-left: 0.5rem;">OVERDUE</span>
                                            @endif
                                        @else
                                            <span style="color: #d1d5db;">—</span>
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
            <div class="card sticky-action" style="margin-bottom: 1.5rem;">
                <div class="card-header">
                    <h5 style="margin: 0; display: flex; align-items: center; gap: 1rem; font-size: 1.125rem;">
                        <i class="bi bi-speedometer2"></i> Summary
                    </h5>
                </div>
                <div class="card-body">
                    <div style="margin-bottom: 1.5rem;">
                        <p style="color: #6b7280; margin: 0 0 0.25rem 0; font-size: 0.875rem;">Total Items</p>
                        <h3 style="margin: 0; color: #111827;">{{ $loan->getTotalItemsCount() }}</h3>
                    </div>

                    <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid #e5e7eb;">
                        <p style="color: #6b7280; margin: 0 0 0.25rem 0; font-size: 0.875rem;">Total Quantity</p>
                        <h3 style="margin: 0; color: #111827;">{{ $loan->getTotalQuantity() }} units</h3>
                    </div>

                    <div style="margin-bottom: 1.5rem; padding-bottom: 1.5rem; border-bottom: 1px solid #e5e7eb;">
                        <p style="color: #6b7280; margin: 0 0 0.25rem 0; font-size: 0.875rem;">Status</p>
                        <span class="badge badge-{{ $statusColors[$loan->status] ?? 'secondary' }}">
                            {{ $statuses[$loan->status] ?? $loan->status }}
                        </span>
                    </div>

                    @if ($loan->isOverdue())
                        <div class="alert alert-danger" style="padding: 1rem; border-radius: 0.375rem; margin-bottom: 0; display: flex; flex-direction: column;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <i class="bi bi-exclamation-triangle"></i>
                                <strong>Overdue</strong>
                            </div>
                            <p style="margin: 0.25rem 0 0 0; font-size: 0.875rem;">This loan has passed the return date. Please submit a return request immediately.</p>
                        </div>
                    @elseif ($loan->isBorrowed())
                        <div class="alert alert-info" style="padding: 1rem; border-radius: 0.375rem; margin-bottom: 0; display: flex; flex-direction: column;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <i class="bi bi-info-circle"></i>
                                <strong>Days Until Return</strong>
                            </div>
                            <p style="margin: 0.25rem 0 0 0; font-size: 0.875rem;">{{ $loan->daysUntilDue() }} days until return date</p>
                        </div>
                    @endif

                    @if ($loan->isPending())
                        <div class="alert alert-warning" style="padding: 1rem; border-radius: 0.375rem; margin: 1rem 0 0 0; display: flex; flex-direction: column;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <i class="bi bi-clock"></i>
                                <strong>Pending Approval</strong>
                            </div>
                            <p style="margin: 0.25rem 0 0 0; font-size: 0.875rem;">Awaiting admin approval. You will be notified after review.</p>
                        </div>
                    @elseif ($loan->isRejected())
                        <div class="alert alert-danger" style="padding: 1rem; border-radius: 0.375rem; margin: 1rem 0 0 0; display: flex; flex-direction: column;">
                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                <i class="bi bi-x-circle"></i>
                                <strong>Rejected</strong>
                            </div>
                            <p style="margin: 0.25rem 0 0 0; font-size: 0.875rem;">Contact admin for more information.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
