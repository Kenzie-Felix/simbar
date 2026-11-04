@extends('layouts.app')

@section('title', 'Reject Loan Request - SIMBAR')

@section('content')
<style>
    .page-header {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
        margin-bottom: var(--spacing-2xl);
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

    .form-container {
        display: grid;
        grid-template-columns: 1fr 300px;
        gap: var(--spacing-xl);
        max-width: 1200px;
    }

    .summary-card {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        margin-bottom: var(--spacing-lg);
        box-shadow: var(--shadow-sm);
    }

    .summary-card h5 {
        font-size: var(--font-size-base);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-900);
        margin-bottom: var(--spacing-md);
        padding-bottom: var(--spacing-md);
        border-bottom: 1px solid var(--gray-200);
    }

    .summary-item {
        margin-bottom: var(--spacing-md);
    }

    .summary-label {
        font-size: var(--font-size-xs);
        color: var(--gray-500);
        font-weight: var(--font-weight-medium);
        margin-bottom: var(--spacing-xs);
    }

    .summary-value {
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-900);
    }

    .form-card {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        padding: var(--spacing-xl);
        box-shadow: var(--shadow-sm);
    }

    .form-group {
        margin-bottom: var(--spacing-lg);
    }

    .form-label {
        display: block;
        margin-bottom: var(--spacing-sm);
        font-weight: var(--font-weight-medium);
        color: var(--gray-700);
        font-size: var(--font-size-sm);
    }

    .form-label .required {
        color: var(--danger);
    }

    .form-control {
        width: 100%;
        padding: var(--spacing-sm) var(--spacing-md);
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-md);
        font-size: var(--font-size-sm);
        transition: all 0.2s ease;
        font-family: var(--font-family);
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .form-help {
        font-size: var(--font-size-xs);
        color: var(--gray-500);
        margin-top: var(--spacing-xs);
    }

    .quick-reasons {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-sm);
        margin-bottom: var(--spacing-lg);
    }

    .quick-reason-btn {
        background: white;
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-md);
        padding: var(--spacing-sm) var(--spacing-md);
        text-align: left;
        font-size: var(--font-size-sm);
        color: var(--gray-700);
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .quick-reason-btn:hover {
        background: var(--gray-50);
        border-color: var(--primary);
        color: var(--primary);
    }

    .action-buttons {
        display: flex;
        gap: var(--spacing-md);
        margin-top: var(--spacing-xl);
    }

    .sidebar-card {
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        margin-bottom: var(--spacing-lg);
    }

    .sidebar-card h6 {
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-900);
        margin-bottom: var(--spacing-md);
    }

    .sidebar-card ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar-card li {
        font-size: var(--font-size-xs);
        color: var(--gray-600);
        padding: var(--spacing-sm) 0;
        border-bottom: 1px solid var(--gray-200);
    }

    .sidebar-card li:last-child {
        border-bottom: none;
    }

    .sidebar-card li strong {
        color: var(--gray-900);
    }

    @media (max-width: 1024px) {
        .form-container {
            grid-template-columns: 1fr;
        }

        .page-header {
            flex-direction: column;
            gap: var(--spacing-lg);
        }
    }
</style>

<div style="max-width: 1400px; margin: 0 auto;">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1>
                <i class="bi bi-x-circle"></i>
                Reject Loan Request
            </h1>
            <p>Provide a reason for rejecting this loan request</p>
        </div>
        <a href="{{ route('admin.loans.show', $loan) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

    <!-- Main Content -->
    <div class="form-container">
        <!-- Form Section -->
        <div>
            <!-- Request Summary -->
            <div class="summary-card">
                <h5>Loan Request #{{ $loan->id }}</h5>
                <div class="summary-item">
                    <div class="summary-label">Staff Member</div>
                    <div class="summary-value">{{ $loan->user->name }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Requested Date</div>
                    <div class="summary-value">{{ $loan->requested_at->format('d M Y H:i') }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Items Requested</div>
                    <div class="summary-value">{{ $loan->getTotalItemsCount() }} items ({{ $loan->getTotalQuantity() }} units)</div>
                </div>
            </div>

            <!-- Rejection Form -->
            <div class="form-card">
                <h5 style="font-size: var(--font-size-base); font-weight: var(--font-weight-semibold); color: var(--gray-900); margin-bottom: var(--spacing-lg); padding-bottom: var(--spacing-lg); border-bottom: 1px solid var(--gray-200);">
                    <i class="bi bi-chat-left-text"></i> Rejection Reason
                </h5>

                <form action="{{ route('admin.loans.reject', $loan) }}" method="POST">
                    @csrf

                    <!-- Rejection Reason -->
                    <div class="form-group">
                        <label for="rejection_reason" class="form-label">
                            Reason for Rejection <span class="required">*</span>
                        </label>
                        <textarea 
                            class="form-control @error('rejection_reason') is-invalid @enderror" 
                            id="rejection_reason" 
                            name="rejection_reason" 
                            rows="5" 
                            placeholder="Explain why this loan request is being rejected..."
                            maxlength="500"
                            required
                        >{{ old('rejection_reason') }}</textarea>
                        <div class="form-help">Maximum 500 characters</div>
                        @error('rejection_reason')
                            <div style="color: var(--danger); font-size: var(--font-size-xs); margin-top: var(--spacing-xs);">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Quick Reasons -->
                    <div class="form-group">
                        <label class="form-label">Quick Reasons:</label>
                        <div class="quick-reasons">
                            <button type="button" class="quick-reason-btn" onclick="setReason('Insufficient stock available at this time. Please resubmit when stock is available.')">
                                Insufficient Stock
                            </button>
                            <button type="button" class="quick-reason-btn" onclick="setReason('Item condition is not suitable for borrowing.')">
                                Item Condition Issue
                            </button>
                            <button type="button" class="quick-reason-btn" onclick="setReason('Staff member has outstanding return items that must be verified first.')">
                                Pending Returns
                            </button>
                            <button type="button" class="quick-reason-btn" onclick="setReason('Requested items are reserved for other purposes.')">
                                Items Reserved
                            </button>
                            <button type="button" class="quick-reason-btn" onclick="setReason('Purpose of loan does not align with inventory policies.')">
                                Policy Violation
                            </button>
                        </div>
                    </div>

                    <!-- Alert Message -->
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle alert-icon"></i>
                        <div>
                            <strong>Note:</strong> The staff member will be notified of the rejection with the reason provided. No stock will be affected.
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to reject this loan request? The staff member will be notified.')">
                            <i class="bi bi-x-circle"></i> Confirm Rejection
                        </button>
                        <a href="{{ route('admin.loans.show', $loan) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Rejection Guidelines -->
            <div class="sidebar-card">
                <h6>Rejection Guidelines</h6>
                <ul>
                    <li><strong>Be Clear:</strong> Explain why the request cannot be approved.</li>
                    <li><strong>Be Helpful:</strong> Suggest actions (e.g., "resubmit when stock available").</li>
                    <li><strong>Be Professional:</strong> Use courteous language.</li>
                    <li><strong>Notify Staff:</strong> The reason will be visible to the staff member.</li>
                    <li><strong>No Stock Impact:</strong> Rejecting doesn't affect inventory.</li>
                </ul>
            </div>

            <!-- Items in Request -->
            <div class="sidebar-card">
                <h6>Items in Request</h6>
                @foreach ($loan->loanItems as $item)
                    <div style="margin-bottom: var(--spacing-md); font-size: var(--font-size-xs); color: var(--gray-600);">
                        <strong style="color: var(--gray-900); display: block;">{{ $item->item->name }}</strong>
                        <span style="color: var(--gray-500);">{{ $item->item->code }}</span><br>
                        <span>Qty: <strong style="color: var(--gray-900);">{{ $item->quantity }}</strong> | Available: <strong style="color: var(--primary);">{{ $item->item->available_stock }}</strong></span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
function setReason(reason) {
    document.getElementById('rejection_reason').value = reason;
    document.getElementById('rejection_reason').focus();
}
</script>
@endsection
