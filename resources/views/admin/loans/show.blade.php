@extends('layouts.app')

@section('title', 'Loan Request Review - SIMBAR')

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

    .status-badge-success {
        background-color: var(--success-light);
        color: var(--success);
        padding: var(--spacing-xs) var(--spacing-md);
        border-radius: var(--radius-sm);
        font-size: var(--font-size-xs);
        font-weight: var(--font-weight-medium);
    }

    .status-badge-danger {
        background-color: var(--danger-light);
        color: var(--danger);
        padding: var(--spacing-xs) var(--spacing-md);
        border-radius: var(--radius-sm);
        font-size: var(--font-size-xs);
        font-weight: var(--font-weight-medium);
    }

    .sticky-action {
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
                <i class="bi bi-clipboard-check"></i>
                Loan Request #{{ $loan->id }}
            </h1>
            <p>Review and take action on this loan request</p>
        </div>
        <a href="{{ route('admin.loans.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <!-- Alerts -->
    @if (session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle alert-icon"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle alert-icon"></i>
            <div>
                <strong>Error:</strong>
                <ul style="margin: var(--spacing-sm) 0 0 var(--spacing-lg); padding-left: var(--spacing-lg);">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Content Grid -->
    <div class="content-grid">
        <!-- Main Content -->
        <div>
            <!-- Staff Information -->
            <div class="card">
                <div class="card-header">
                    <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                        <i class="bi bi-person"></i> Staff Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <div class="detail-field">
                            <span class="detail-label">Name</span>
                            <span class="detail-value">{{ $loan->user->name }}</span>
                        </div>
                        <div class="detail-field">
                            <span class="detail-label">Email</span>
                            <span class="detail-value" style="font-size: var(--font-size-sm);">{{ $loan->user->email }}</span>
                        </div>
                    </div>
                    @if ($loan->user->department)
                        <div class="detail-field">
                            <span class="detail-label">Department</span>
                            <span class="detail-value">{{ $loan->user->department }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Request Details -->
            <div class="card" style="margin-top: var(--spacing-lg);">
                <div class="card-header">
                    <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                        <i class="bi bi-info-circle"></i> Request Details
                    </h5>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <div class="detail-field">
                            <span class="detail-label">Requested Date</span>
                            <span class="detail-value">{{ $loan->requested_at->format('d M Y H:i') }}</span>
                        </div>
                        <div class="detail-field">
                            <span class="detail-label">Status</span>
                            <span class="badge badge-{{ $loan->status === 'Menunggu Persetujuan' ? 'warning' : ($loan->status === 'Disetujui' ? 'success' : ($loan->status === 'Ditolak' ? 'danger' : ($loan->status === 'Dipinjam' ? 'primary' : 'info'))) }}" style="display: inline-block;">
                                {{ $loan->status }}
                            </span>
                        </div>
                    </div>
                    <div class="detail-row">
                        <div class="detail-field">
                            <span class="detail-label">Expected Return Date</span>
                            <span class="detail-value">
                                @php
                                    $returnDate = $loan->getEarliestReturnDate();
                                @endphp
                                @if ($returnDate)
                                    {{ $returnDate->format('d M Y') }}
                                @else
                                    -
                                @endif
                            </span>
                        </div>
                        <div class="detail-field">
                            <span class="detail-label">Total Items</span>
                            <span class="detail-value">{{ $loan->getTotalItemsCount() }} items ({{ $loan->getTotalQuantity() }} units)</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Purpose -->
            <div class="card" style="margin-top: var(--spacing-lg);">
                <div class="card-header">
                    <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                        <i class="bi bi-chat-left-text"></i> Purpose
                    </h5>
                </div>
                <div class="card-body">
                    <p style="white-space: pre-wrap; color: var(--gray-700); margin: 0;">{{ $loan->purpose ?? 'No purpose specified' }}</p>
                </div>
            </div>

            <!-- Items Requested -->
            <div class="card" style="margin-top: var(--spacing-lg);">
                <div class="card-header">
                    <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                        <i class="bi bi-collection"></i> Items Requested
                    </h5>
                </div>
                <div style="overflow-x: auto;">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Category</th>
                                <th>Qty</th>
                                <th>Current Stock</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($loan->loanItems as $loanItem)
                                @php
                                    $status = $stockStatus[$loanItem->id];
                                @endphp
                                <tr>
                                    <td>
                                        <strong>{{ $loanItem->item->code }}</strong>
                                    </td>
                                    <td>{{ $loanItem->item->name }}</td>
                                    <td>{{ $loanItem->item->category->name }}</td>
                                    <td>
                                        <strong>{{ $loanItem->quantity }}</strong>
                                    </td>
                                    <td>{{ $status['current'] }} units</td>
                                    <td>
                                        @if ($status['sufficient'])
                                            <span class="status-badge-success">✓ Available</span>
                                        @else
                                            <span class="status-badge-danger">✗ Insufficient</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Approval History -->
            @if ($loan->approved_at)
                <div class="card" style="margin-top: var(--spacing-lg);">
                    <div class="card-header">
                        <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                            <i class="bi bi-clock-history"></i> Approval History
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="detail-field">
                            <span class="detail-label">Approved By</span>
                            <span class="detail-value">{{ $loan->approver?->name ?? 'System' }}</span>
                        </div>
                        <div class="detail-field">
                            <span class="detail-label">Approval Date</span>
                            <span class="detail-value">{{ $loan->approved_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                </div>
            @endif

            @if ($loan->rejection_reason)
                <div class="card" style="margin-top: var(--spacing-lg); border: 1px solid var(--danger); border-left: 4px solid var(--danger);">
                    <div class="card-header" style="background-color: var(--danger-light); border-bottom: 1px solid var(--danger);">
                        <h5 style="margin: 0; color: var(--danger); display: flex; align-items: center; gap: var(--spacing-md);">
                            <i class="bi bi-x-circle"></i> Rejection Reason
                        </h5>
                    </div>
                    <div class="card-body">
                        <p style="white-space: pre-wrap; color: var(--danger); margin: 0;">{{ $loan->rejection_reason }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar Actions -->
        <div>
            <!-- Stock Status Warning -->
            @php
                $hasInsufficientStock = collect($stockStatus)->contains(fn($status) => !$status['sufficient']);
            @endphp

            @if ($hasInsufficientStock)
                <div class="alert alert-danger" style="margin-bottom: var(--spacing-lg);">
                    <i class="bi bi-exclamation-triangle alert-icon"></i>
                    <div>
                        <strong>Warning!</strong>
                        <p style="margin: var(--spacing-xs) 0 0 0;">Some items have insufficient stock. Review the quantities before approving.</p>
                    </div>
                </div>
            @endif

            <!-- Action Card -->
            @if ($loan->isPending())
                <div class="card sticky-action">
                    <div class="card-header">
                        <h5 style="margin: 0;">Take Action</h5>
                    </div>
                    <div class="card-body">
                        <!-- Approve Button -->
                        <form action="{{ route('admin.loans.approve', $loan) }}" method="POST" style="margin-bottom: var(--spacing-md);">
                            @csrf
                            <button type="submit" class="btn btn-success" style="width: 100%;" @if ($hasInsufficientStock) disabled title="Cannot approve: insufficient stock" @endif>
                                <i class="bi bi-check2"></i> Approve Request
                            </button>
                        </form>

                        <!-- Reject Button -->
                        <a href="{{ route('admin.loans.rejectForm', $loan) }}" class="btn btn-danger" style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: var(--spacing-sm);">
                            <i class="bi bi-x-circle"></i> Reject Request
                        </a>

                        @if ($hasInsufficientStock)
                            <div class="alert alert-warning" style="margin-top: var(--spacing-md); margin-bottom: 0;">
                                <i class="bi bi-info-circle"></i>
                                <small>Some items don't have enough stock. Contact warehouse if needed.</small>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Information Card -->
                <div class="card" style="margin-top: var(--spacing-lg);">
                    <div class="card-header">
                        <h6 style="margin: 0;">What happens when you approve?</h6>
                    </div>
                    <div class="card-body">
                        <ul style="margin: 0; padding-left: var(--spacing-lg); font-size: var(--font-size-sm);">
                            <li style="margin-bottom: var(--spacing-xs);">✓ Stock will be decremented</li>
                            <li style="margin-bottom: var(--spacing-xs);">✓ Loan status will be "Borrowed"</li>
                            <li style="margin-bottom: var(--spacing-xs);">✓ Staff member will be notified</li>
                            <li>✓ A stock movement record will be created</li>
                        </ul>
                    </div>
                </div>
            @else
                <div class="card sticky-action">
                    <div class="card-header">
                        <h5 style="margin: 0;">Status</h5>
                    </div>
                    <div class="card-body">
                        <p style="color: var(--gray-500); margin-bottom: var(--spacing-md);">This request has already been processed.</p>
                        <span class="badge badge-{{ $loan->status === 'Disetujui' ? 'success' : ($loan->status === 'Ditolak' ? 'danger' : 'info') }}" style="display: inline-block; padding: var(--spacing-sm) var(--spacing-md); font-size: var(--font-size-base);">
                            {{ $loan->status }}
                        </span>

                        @if ($loan->isApproved() || $loan->isBorrowed())
                            <div style="margin-top: var(--spacing-lg);">
                                <p style="font-size: var(--font-size-sm); color: var(--gray-500); margin-bottom: var(--spacing-xs);">Approved on {{ $loan->approved_at?->format('d M Y H:i') }}</p>
                                <p style="font-size: var(--font-size-sm); color: var(--gray-500); margin: 0;">By {{ $loan->approver?->name ?? 'System' }}</p>
                            </div>
                        @elseif ($loan->isRejected())
                            <div style="margin-top: var(--spacing-lg);">
                                <p style="font-size: var(--font-size-sm); color: var(--gray-500); margin-bottom: var(--spacing-xs);">Rejected on {{ $loan->approved_at?->format('d M Y H:i') }}</p>
                                <p style="font-size: var(--font-size-sm); color: var(--danger); margin: 0;">Reason: {{ $loan->rejection_reason }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
