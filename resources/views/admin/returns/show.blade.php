@extends('layouts.app')

@section('title', 'Verify Return Request - SIMBAR')

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
                Verify Return Request #{{ $return->id }}
            </h1>
            <p>Review and verify returned items</p>
        </div>
        <a href="{{ route('admin.returns.index') }}" class="btn btn-outline-primary">
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
                            <span class="detail-value">{{ $return->loan->user->name }}</span>
                        </div>
                        <div class="detail-field">
                            <span class="detail-label">Email</span>
                            <span class="detail-value" style="font-size: var(--font-size-sm);">{{ $return->loan->user->email }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Return Information -->
            <div class="card" style="margin-top: var(--spacing-lg);">
                <div class="card-header">
                    <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                        <i class="bi bi-info-circle"></i> Return Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <div class="detail-field">
                            <span class="detail-label">Loan Reference</span>
                            <span class="detail-value">#{{ $return->loan->id }}</span>
                        </div>
                        <div class="detail-field">
                            <span class="detail-label">Submitted Date</span>
                            <span class="detail-value">{{ $return->submitted_at->format('d M Y H:i') }}</span>
                        </div>
                    </div>
                    <div class="detail-field">
                        <span class="detail-label">Status</span>
                        <span class="badge badge-{{ $return->status === 'Menunggu Verifikasi' ? 'warning' : ($return->status === 'Terima' ? 'success' : 'danger') }}" style="display: inline-block;">
                            {{ $return->status }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Returned Items -->
            <div class="card" style="margin-top: var(--spacing-lg);">
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
                                <th>Borrowed Qty</th>
                                <th>Returned Qty</th>
                                <th>Condition</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($return->returnItems as $returnItem)
                                <tr>
                                    <td><strong>{{ $returnItem->loanItem->item->code }}</strong></td>
                                    <td>{{ $returnItem->loanItem->item->name }}</td>
                                    <td>{{ $returnItem->loanItem->quantity }}</td>
                                    <td><strong>{{ $returnItem->quantity }}</strong></td>
                                    <td>
                                        <span class="badge badge-{{ $returnItem->loanItem->item->getConditionBadgeColor() }}">
                                            {{ $returnItem->condition }}
                                        </span>
                                    </td>
                                    <td><small style="color: var(--gray-500);">{{ $returnItem->notes ?? '-' }}</small></td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions -->
        <div>
            @if ($return->isPending())
                <div class="card sticky-action">
                    <div class="card-header">
                        <h5 style="margin: 0;">Take Action</h5>
                    </div>
                    <div class="card-body">
                        <!-- Verify Form -->
                        <form action="{{ route('admin.returns.verify', $return) }}" method="POST" style="margin-bottom: var(--spacing-md);">
                            @csrf
                            <div style="margin-bottom: var(--spacing-md);">
                                <label style="display: block; margin-bottom: var(--spacing-sm); font-weight: var(--font-weight-medium); font-size: var(--font-size-sm);">Verification Notes (Optional)</label>
                                <textarea 
                                    class="form-control" 
                                    name="admin_notes" 
                                    rows="3" 
                                    placeholder="Add any verification notes..." 
                                    maxlength="500"
                                    style="border: 1px solid var(--gray-300); border-radius: var(--radius-md); padding: var(--spacing-sm); font-size: var(--font-size-sm); font-family: var(--font-family);"
                                ></textarea>
                            </div>
                            <button type="submit" class="btn btn-success" style="width: 100%; margin-bottom: var(--spacing-md);" onclick="return confirm('Are you sure you want to verify and accept this return? Stock will be restored.')">
                                <i class="bi bi-check2"></i> Accept Return
                            </button>
                        </form>

                        <!-- Reject Button -->
                        <a href="{{ route('admin.returns.rejectForm', $return) }}" class="btn btn-danger" style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: var(--spacing-sm);">
                            <i class="bi bi-x-circle"></i> Reject Return
                        </a>

                        <div class="alert alert-info" style="margin-top: var(--spacing-md); margin-bottom: 0;">
                            <i class="bi bi-info-circle"></i>
                            <small>
                                <strong>On Accept:</strong> Stock will be restored based on item condition. Damaged/Lost items won't be added to available stock.
                            </small>
                        </div>
                    </div>
                </div>
            @else
                <div class="card sticky-action">
                    <div class="card-header">
                        <h5 style="margin: 0;">Status</h5>
                    </div>
                    <div class="card-body">
                        <p style="color: var(--gray-500); margin-bottom: var(--spacing-md);">This return has already been processed.</p>
                        <span class="badge badge-{{ $return->status === 'Terima' ? 'success' : 'danger' }}" style="display: inline-block; padding: var(--spacing-sm) var(--spacing-md); font-size: var(--font-size-base);">
                            {{ $return->status }}
                        </span>

                        @if ($return->isAccepted())
                            <div style="margin-top: var(--spacing-lg);">
                                <p style="font-size: var(--font-size-sm); color: var(--gray-500); margin-bottom: var(--spacing-xs);">Verified by {{ $return->verifier?->name ?? 'System' }}</p>
                                <p style="font-size: var(--font-size-sm); color: var(--gray-500); margin: 0;">{{ $return->verified_at->format('d M Y H:i') }}</p>
                            </div>
                        @elseif ($return->isRejected())
                            <div style="margin-top: var(--spacing-lg);">
                                <p style="font-size: var(--font-size-sm); color: var(--danger); margin: 0;"><strong>Reason:</strong> {{ $return->rejection_reason }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Summary Card -->
            <div class="card" style="margin-top: var(--spacing-lg);">
                <div class="card-header">
                    <h6 style="margin: 0;">Return Summary</h6>
                </div>
                <div class="card-body">
                    <div style="margin-bottom: var(--spacing-lg);">
                        <p class="detail-label">Items Returned</p>
                        <h3 style="margin: 0; font-size: var(--font-size-2xl); color: var(--primary);">{{ $return->getReturnedItemsCount() }}</h3>
                    </div>

                    <div>
                        <p class="detail-label">Total Quantity</p>
                        <h3 style="margin: 0; font-size: var(--font-size-2xl); color: var(--success);">{{ $return->getTotalReturnedQuantity() }} units</h3>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
