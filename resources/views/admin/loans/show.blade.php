@extends('layouts.app')

@section('title', 'Tinjau Permintaan Peminjaman - SIMBAR')

@section('content')
<style>
    .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: var(--spacing-2xl);
        gap: var(--spacing-lg);
    }

    .page-header h1 {
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
        color: var(--gray-900);
        margin: 0 0 var(--spacing-sm) 0;
        display: flex;
        align-items: center;
        gap: var(--spacing-md);
    }

    .page-subtitle {
        font-size: var(--font-size-sm);
        color: var(--gray-500);
        margin: 0;
    }

    .content-wrapper {
        display: grid;
        grid-template-columns: 1fr 380px;
        gap: var(--spacing-lg);
    }

    /* Card Styling */
    .info-card {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        margin-bottom: var(--spacing-lg);
    }

    .card-header {
        background: linear-gradient(135deg, var(--gray-50), white);
        border-bottom: 1px solid var(--gray-200);
        padding: var(--spacing-lg);
        display: flex;
        align-items: center;
        gap: var(--spacing-md);
    }

    .card-header h5 {
        margin: 0;
        font-size: var(--font-size-base);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-900);
        display: flex;
        align-items: center;
        gap: var(--spacing-md);
    }

    .card-header i {
        font-size: 1.25rem;
        color: var(--primary);
    }

    .card-body {
        padding: var(--spacing-lg);
    }

    /* Field Styling */
    .detail-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: var(--spacing-lg);
    }

    .detail-grid.full {
        grid-template-columns: 1fr;
    }

    .detail-field {
        display: flex;
        flex-direction: column;
    }

    .detail-label {
        font-size: var(--font-size-xs);
        color: var(--gray-500);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: var(--spacing-sm);
        font-weight: var(--font-weight-semibold);
    }

    .detail-value {
        font-size: var(--font-size-base);
        color: var(--gray-900);
        font-weight: var(--font-weight-medium);
    }

    .detail-value.text-muted {
        color: var(--gray-600);
        font-weight: normal;
    }

    /* Items Table */
    .items-table {
        width: 100%;
        border-collapse: collapse;
    }

    .items-table thead {
        background-color: var(--gray-50);
    }

    .items-table th {
        padding: var(--spacing-md) var(--spacing-lg);
        text-align: left;
        font-size: var(--font-size-xs);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border-bottom: 2px solid var(--gray-200);
    }

    .items-table td {
        padding: var(--spacing-md) var(--spacing-lg);
        border-bottom: 1px solid var(--gray-200);
        font-size: var(--font-size-sm);
        color: var(--gray-700);
    }

    .items-table tbody tr:hover {
        background-color: var(--gray-50);
    }

    .items-table tbody tr:last-child td {
        border-bottom: none;
    }

    .item-code {
        font-weight: var(--font-weight-semibold);
        color: var(--primary);
        font-family: monospace;
    }

    .qty-badge {
        display: inline-block;
        background: var(--primary-light);
        color: var(--primary);
        padding: var(--spacing-xs) var(--spacing-sm);
        border-radius: var(--radius-sm);
        font-weight: var(--font-weight-semibold);
        font-size: var(--font-size-sm);
    }

    .status-badge {
        display: inline-block;
        padding: var(--spacing-xs) var(--spacing-md);
        border-radius: var(--radius-sm);
        font-size: var(--font-size-xs);
        font-weight: var(--font-weight-semibold);
    }

    .status-available {
        background-color: #d4edda;
        color: #155724;
    }

    .status-insufficient {
        background-color: #f8d7da;
        color: #721c24;
    }

    .sticky-sidebar {
        position: relative;
    }

    .action-card {
        position: sticky;
        top: 80px;
        margin-bottom: var(--spacing-lg);
    }

    .action-card {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        margin-bottom: var(--spacing-lg);
    }

    .action-card.warning {
        border-color: #ffc107;
        border-left: 4px solid #ffc107;
    }

    .action-card.danger {
        border-color: var(--danger);
        border-left: 4px solid var(--danger);
    }

    .action-card-header {
        background: #fff3cd;
        padding: var(--spacing-lg);
        border-bottom: 1px solid #ffc107;
    }

    .action-card-header h5 {
        margin: 0;
        color: #856404;
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
        font-size: var(--font-size-base);
        font-weight: var(--font-weight-semibold);
    }

    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-md);
    }

    .action-buttons .btn {
        width: 100%;
    }

    .info-note {
        background: var(--gray-50);
        border-left: 4px solid var(--primary);
        padding: var(--spacing-md);
        border-radius: var(--radius-sm);
        font-size: var(--font-size-sm);
        color: var(--gray-700);
        line-height: 1.6;
    }

    .info-note ul {
        margin: var(--spacing-md) 0 0 0;
        padding-left: var(--spacing-lg);
    }

    .info-note li {
        margin-bottom: var(--spacing-xs);
    }

    .rejection-note {
        background: #f8d7da;
        border-left: 4px solid var(--danger);
        padding: var(--spacing-md);
        border-radius: var(--radius-sm);
        color: #721c24;
        white-space: pre-wrap;
        line-height: 1.5;
    }

    @media (max-width: 1024px) {
        .content-wrapper {
            grid-template-columns: 1fr;
        }

        .detail-grid {
            grid-template-columns: 1fr;
        }

        .sticky-sidebar {
            position: relative;
        }

        .action-card {
            position: relative;
            top: auto;
        }
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
        }

        .items-table {
            font-size: var(--font-size-xs);
        }

        .items-table th,
        .items-table td {
            padding: var(--spacing-sm);
        }
    }
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: var(--spacing-lg);">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <h1>
                <i class="bi bi-clipboard-check"></i>
                {{ __('messages.loan_request') }}#{{ $loan->id }}
            </h1>
            <p class="page-subtitle">{{ __('messages.review_loan_request') }}</p>
        </div>
        <a href="{{ route('admin.loans.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> {{ __('messages.back') }}
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
                <ul style="margin: var(--spacing-md) 0 0 var(--spacing-lg); padding-left: var(--spacing-lg);">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <!-- Main Content -->
        <div>
            <!-- Staff Information -->
            <div class="info-card">
                <div class="card-header">
                    <h5><i class="bi bi-person-circle"></i> {{ __('messages.staff_member') }}</h5>
                </div>
                <div class="card-body">
                    <div class="detail-grid">
                        <div class="detail-field">
                            <span class="detail-label">Name</span>
                            <span class="detail-value">{{ $loan->user->name }}</span>
                        </div>
                        <div class="detail-field">
                            <span class="detail-label">Email</span>
                            <span class="detail-value text-muted">{{ $loan->user->email }}</span>
                        </div>
                    </div>
                    @if ($loan->user->department)
                        <div class="detail-field" style="margin-top: var(--spacing-lg);">
                            <span class="detail-label">Department</span>
                            <span class="detail-value">{{ $loan->user->department }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Request Details -->
            <div class="info-card">
                <div class="card-header">
                    <h5><i class="bi bi-info-circle"></i> {{ __('messages.items_requested') }}</h5>
                </div>
                <div class="card-body">
                    <div class="detail-grid">
                        <div class="detail-field">
                            <span class="detail-label">{{ __('messages.date_requested') }}</span>
                            <span class="detail-value">{{ $loan->requested_at->format('d M Y H:i') }}</span>
                        </div>
                        <div class="detail-field">
                            <span class="detail-label">Status</span>
                            <span class="badge badge-{{ match($loan->status) {
                                'Pending Approval' => 'warning',
                                'Approved' => 'info',
                                'Borrowed' => 'primary',
                                'Returned' => 'success',
                                'Rejected' => 'danger',
                                default => 'secondary'
                            } }}" style="display: inline-block;">
                                {{ $loan->status }}
                            </span>
                        </div>
                    </div>
                    <div class="detail-grid" style="margin-top: var(--spacing-lg);">
                        <div class="detail-field">
                            <span class="detail-label">Expected Return Date</span>
                            <span class="detail-value">
                                @php
                                    $returnDate = $loan->getEarliestReturnDate();
                                @endphp
                                @if ($returnDate)
                                    {{ $returnDate->format('d M Y') }}
                                @else
                                    <span class="text-muted">Not set</span>
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
            <div class="info-card">
                <div class="card-header">
                    <h5><i class="bi bi-chat-left-text"></i> Purpose of Loan</h5>
                </div>
                <div class="card-body">
                    <p style="white-space: pre-wrap; color: var(--gray-700); margin: 0; line-height: 1.6;">{{ $loan->purpose ?? 'No purpose specified' }}</p>
                </div>
            </div>

            <!-- Items Requested -->
            <div class="info-card">
                <div class="card-header">
                    <h5><i class="bi bi-collection"></i> {{ __('messages.items_requested') }}</h5>
                </div>
                <div class="card-body" style="padding: 0;">
                    <div style="overflow-x: auto;">
                        <table class="items-table">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.item_code') }}</th>
                                    <th>{{ __('messages.item_name') }}</th>
                                    <th>{{ __('messages.category') }}</th>
                                    <th style="text-align: center;">{{ __('messages.qty') }}</th>
                                    <th style="text-align: center;">Current Stock</th>
                                    <th style="text-align: center;">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($loan->loanItems as $loanItem)
                                    @php
                                        $status = $stockStatus[$loanItem->id];
                                    @endphp
                                    <tr>
                                        <td>
                                            <span class="item-code">{{ $loanItem->item->code }}</span>
                                        </td>
                                        <td>{{ $loanItem->item->name }}</td>
                                        <td>{{ $loanItem->item->category->name }}</td>
                                        <td style="text-align: center;">
                                            <span class="qty-badge">{{ $loanItem->quantity }}</span>
                                        </td>
                                        <td style="text-align: center;">
                                            {{ $status['current'] }} units
                                        </td>
                                        <td style="text-align: center;">
                                            @if ($status['sufficient'])
                                                <span class="status-badge status-available">✓ {{ __('messages.available') }}</span>
                                            @else
                                                <span class="status-badge status-insufficient">✗ {{ __('messages.stock_not_available') }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Approval History -->
            @if ($loan->approved_at)
                <div class="info-card">
                    <div class="card-header">
                        <h5><i class="bi bi-check-circle"></i> {{ __('messages.approval') ?? 'Approval History' }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="detail-grid full">
                            <div class="detail-field">
                                <span class="detail-label">Approved By</span>
                                <span class="detail-value">{{ $loan->approver?->name ?? 'System' }}</span>
                            </div>
                            <div class="detail-field" style="margin-top: var(--spacing-lg);">
                                <span class="detail-label">Approval Date & Time</span>
                                <span class="detail-value">{{ $loan->approved_at->format('d M Y H:i') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($loan->rejection_reason)
                <div class="info-card" style="border: 1px solid var(--danger); border-left: 4px solid var(--danger);">
                    <div class="card-header" style="background-color: #f8d7da; border-bottom: 1px solid var(--danger);">
                        <h5 style="color: var(--danger);"><i class="bi bi-x-circle"></i> Rejection Reason</h5>
                    </div>
                    <div class="card-body">
                        <p class="rejection-note">{{ $loan->rejection_reason }}</p>
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="sticky-sidebar">
            <!-- Stock Warning -->
            @php
                $hasInsufficientStock = collect($stockStatus)->contains(fn($status) => !$status['sufficient']);
            @endphp

            @if ($hasInsufficientStock && $loan->isPending())
                <div class="action-card warning">
                    <div class="action-card-header">
                        <h5><i class="bi bi-exclamation-triangle"></i> {{ __('messages.stock_not_available') }}</h5>
                    </div>
                    <div class="card-body">
                        <p style="margin: 0; font-size: var(--font-size-sm); color: #856404; line-height: 1.5;">
                            {{ __('messages.item_condition_not_suitable') }}
                        </p>
                    </div>
                </div>
            @endif

            <!-- Action Card - HIDDEN -->
            @if ($loan->isPending())
                <div class="action-card" style="display: none;">
                    <div class="card-header">
                        <h5 style="color: var(--gray-900);">Take Action</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('admin.loans.approve', $loan) }}" method="POST" style="margin-bottom: var(--spacing-md);">
                            @csrf
                            <button type="submit" class="btn btn-success" style="width: 100%;" @if ($hasInsufficientStock) disabled title="{{ __('messages.stock_not_available') }}" @endif>
                                <i class="bi bi-check2"></i> {{ __('messages.approved') }}
                            </button>
                        </form>

                        <a href="{{ route('admin.loans.rejectForm', $loan) }}" class="btn btn-danger" style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: var(--spacing-sm);">
                            <i class="bi bi-x-circle"></i> {{ __('messages.rejected') }}
                        </a>
                    </div>
                </div>

                <!-- Info Note -->
                <div class="info-note">
                    <strong style="color: var(--gray-900);">When you approve:</strong>
                    <ul>
                        <li>Stock will be decremented automatically</li>
                        <li>Loan status changes to "Borrowed"</li>
                        <li>Staff member will be notified</li>
                        <li>Audit trail will be created</li>
                    </ul>
                </div>
            @else
                <div class="action-card">
                    <div class="card-header">
                        <h5 style="color: var(--gray-900);">Status</h5>
                    </div>
                    <div class="card-body">
                        <p style="color: var(--gray-500); margin-bottom: var(--spacing-md);">This request has already been processed.</p>
                        <span class="badge badge-{{ match($loan->status) {
                            'Pending Approval' => 'warning',
                            'Approved' => 'info',
                            'Borrowed' => 'primary',
                            'Returned' => 'success',
                            'Rejected' => 'danger',
                            default => 'secondary'
                        } }}" style="display: inline-block; padding: var(--spacing-sm) var(--spacing-md); font-size: var(--font-size-base);">
                            {{ $loan->status }}
                        </span>

                        @if ($loan->isApproved() || $loan->isBorrowed())
                            <div style="margin-top: var(--spacing-lg); padding-top: var(--spacing-lg); border-top: 1px solid var(--gray-200);">
                                <p style="font-size: var(--font-size-sm); color: var(--gray-600); margin: 0;">Approved on {{ $loan->approved_at?->format('d M Y H:i') }}</p>
                                <p style="font-size: var(--font-size-sm); color: var(--gray-600); margin: var(--spacing-xs) 0 0 0;">by {{ $loan->approver?->name ?? 'System' }}</p>
                            </div>
                        @elseif ($loan->isRejected())
                            <div style="margin-top: var(--spacing-lg); padding-top: var(--spacing-lg); border-top: 1px solid var(--gray-200);">
                                <p style="font-size: var(--font-size-sm); color: var(--gray-600); margin: 0;">Rejected</p>
                                <p style="font-size: var(--font-size-sm); color: var(--danger); margin: var(--spacing-xs) 0 0 0; font-weight: var(--font-weight-medium);">Reason provided in details above</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
