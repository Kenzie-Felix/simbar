@extends('layouts.app')

@section('title', 'Loan Approval - SIMBAR')

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

    /* Stats Grid - Modern Card Design */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-2xl);
    }

    .stat-card {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary), var(--primary-dark));
    }

    .stat-card.pending::before {
        background: linear-gradient(90deg, var(--warning), #ff9800);
    }

    .stat-card.approved::before {
        background: linear-gradient(90deg, var(--success), #28a745);
    }

    .stat-card.rejected::before {
        background: linear-gradient(90deg, var(--danger), #dc3545);
    }

    .stat-card.borrowed::before {
        background: linear-gradient(90deg, var(--info), #17a2b8);
    }

    .stat-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .stat-content {
        display: flex;
        align-items: center;
        gap: var(--spacing-md);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .stat-card.pending .stat-icon {
        background-color: #fff3cd;
        color: var(--warning);
    }

    .stat-card.approved .stat-icon {
        background-color: #d4edda;
        color: var(--success);
    }

    .stat-card.rejected .stat-icon {
        background-color: #f8d7da;
        color: var(--danger);
    }

    .stat-card.borrowed .stat-icon {
        background-color: #d1ecf1;
        color: var(--info);
    }

    .stat-info h3 {
        font-size: var(--font-size-xs);
        color: var(--gray-500);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin: 0 0 var(--spacing-xs) 0;
        font-weight: var(--font-weight-semibold);
    }

    .stat-value {
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
        color: var(--gray-900);
        line-height: 1;
    }

    /* Filter Section */
    .filter-section {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        box-shadow: var(--shadow-sm);
        margin-bottom: var(--spacing-2xl);
    }

    .filter-title {
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-700);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: var(--spacing-lg);
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
    }

    .filter-grid {
        display: grid;
        grid-template-columns: 1fr 1fr auto;
        gap: var(--spacing-md);
        align-items: end;
    }

    .form-group-compact {
        display: flex;
        flex-direction: column;
    }

    .filter-label {
        display: block;
        margin-bottom: var(--spacing-xs);
        font-weight: var(--font-weight-medium);
        font-size: var(--font-size-sm);
        color: var(--gray-700);
    }

    .form-control,
    .form-select {
        width: 100%;
        padding: var(--spacing-sm) var(--spacing-md);
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-md);
        font-size: var(--font-size-sm);
        transition: all 0.2s ease;
        font-family: inherit;
    }

    .form-control:focus,
    .form-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    }

    .filter-actions {
        display: flex;
        gap: var(--spacing-sm);
    }

    /* Loans Table */
    .table-container {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
        margin-bottom: var(--spacing-2xl);
    }

    .table {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
    }

    .table thead {
        background-color: var(--gray-50);
        border-bottom: 2px solid var(--gray-200);
    }

    .table th {
        padding: var(--spacing-md) var(--spacing-lg);
        text-align: left;
        font-size: var(--font-size-xs);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table td {
        padding: var(--spacing-lg);
        border-bottom: 1px solid var(--gray-200);
        font-size: var(--font-size-sm);
    }

    .table tbody tr {
        transition: background-color 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: var(--gray-50);
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    .checkbox-cell {
        width: 44px;
        text-align: center;
    }

    .request-id {
        font-weight: var(--font-weight-semibold);
        color: var(--primary);
    }

    .staff-info {
        display: flex;
        flex-direction: column;
        gap: var(--spacing-xs);
    }

    .staff-name {
        font-weight: var(--font-weight-medium);
        color: var(--gray-900);
    }

    .staff-email {
        font-size: var(--font-size-xs);
        color: var(--gray-500);
    }

    .item-badge {
        display: inline-block;
        background: var(--gray-100);
        color: var(--gray-700);
        padding: var(--spacing-xs) var(--spacing-sm);
        border-radius: var(--radius-sm);
        font-weight: var(--font-weight-medium);
        font-size: var(--font-size-xs);
    }

    .action-cell {
        text-align: center;
    }

    /* Bulk Actions */
    .bulk-actions-bar {
        background: linear-gradient(135deg, var(--primary-light), white);
        border: 1px solid var(--primary);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        margin-top: var(--spacing-2xl);
        display: flex;
        align-items: center;
        gap: var(--spacing-lg);
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.1);
    }

    .bulk-actions-bar strong {
        color: var(--primary);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: var(--spacing-3xl) var(--spacing-lg);
        background: var(--gray-50);
    }

    .empty-state-icon {
        font-size: 3.5rem;
        color: var(--gray-300);
        margin-bottom: var(--spacing-lg);
        opacity: 0.8;
    }

    .empty-state-text {
        font-size: var(--font-size-base);
        color: var(--gray-500);
        margin-bottom: var(--spacing-lg);
    }

    /* Status Badges - Modern Design */
    .badge {
        padding: var(--spacing-xs) var(--spacing-md);
        border-radius: var(--radius-sm);
        font-size: var(--font-size-xs);
        font-weight: var(--font-weight-semibold);
        display: inline-block;
    }

    .badge-warning {
        background-color: #fff3cd;
        color: #664d03;
        border: 1px solid #ffecb5;
    }

    .badge-success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }

    .badge-danger {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }

    .badge-info {
        background-color: #d1ecf1;
        color: #0c5460;
        border: 1px solid #bee5eb;
    }

    @media (max-width: 1024px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .filter-grid {
            grid-template-columns: 1fr;
        }

        .table {
            font-size: var(--font-size-xs);
        }

        .table td,
        .table th {
            padding: var(--spacing-md);
        }
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
        }

        .stats-grid {
            grid-template-columns: 1fr;
        }

        .table {
            overflow-x: auto;
        }

        /* Convert table to cards on mobile */
        .table thead {
            display: none;
        }

        .table tbody,
        .table tr,
        .table td {
            display: block;
            width: 100%;
        }

        .table tr {
            margin-bottom: var(--spacing-lg);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .table td {
            padding: var(--spacing-md) !important;
            padding-left: 45% !important;
            position: relative;
            border: none;
            border-bottom: 1px solid var(--gray-100);
            font-size: var(--font-size-sm);
        }

        .table td:last-child {
            border-bottom: none;
        }

        .table td:before {
            content: attr(data-label);
            position: absolute;
            left: var(--spacing-md);
            top: var(--spacing-md);
            font-weight: var(--font-weight-semibold);
            font-size: var(--font-size-xs);
            color: var(--gray-600);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .checkbox-cell {
            padding-top: var(--spacing-lg) !important;
        }

        .action-cell {
            text-align: left !important;
        }

        .bulk-actions-bar {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: var(--spacing-lg);">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1>
                <i class="bi bi-clipboard-check"></i>
                Loan Approval
            </h1>
            <p>Review and manage pending loan requests</p>
        </div>
        <a href="{{ route('admin.loans.exportPending') }}" class="btn btn-secondary" style="display: none;">
            <i class="bi bi-download"></i> Export CSV
        </a>
    </div>

    <!-- Stats Grid - Modern Design -->
    <div class="stats-grid">
        <div class="stat-card pending">
            <div class="stat-content">
                <div class="stat-icon">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="stat-info">
                    <h3>Pending</h3>
                    <div class="stat-value">{{ $stats['pending_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="stat-card approved">
            <div class="stat-content">
                <div class="stat-icon">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>Approved</h3>
                    <div class="stat-value">{{ $stats['approved_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="stat-card rejected">
            <div class="stat-content">
                <div class="stat-icon">
                    <i class="bi bi-x-circle"></i>
                </div>
                <div class="stat-info">
                    <h3>Rejected</h3>
                    <div class="stat-value">{{ $stats['rejected_count'] }}</div>
                </div>
            </div>
        </div>
        <div class="stat-card borrowed">
            <div class="stat-content">
                <div class="stat-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
                <div class="stat-info">
                    <h3>Currently Borrowed</h3>
                    <div class="stat-value">{{ $stats['borrowed_count'] }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if (session('bulk_results'))
        <div class="alert alert-success" style="margin-bottom: var(--spacing-lg);">
            <i class="bi bi-check-circle alert-icon"></i>
            <div>
                <strong>Bulk Approval Complete</strong>
                <p style="margin: var(--spacing-sm) 0 0 0;">
                    ✓ {{ session('bulk_results.approved') }} approved, ✗ {{ session('bulk_results.failed') }} failed
                </p>
                @if (session('bulk_results.errors'))
                    <ul style="margin: var(--spacing-sm) 0 0 var(--spacing-lg); padding-left: var(--spacing-lg); font-size: var(--font-size-xs);">
                        @foreach (array_slice(session('bulk_results.errors'), 0, 3) as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                        @if (count(session('bulk_results.errors')) > 3)
                            <li>... and {{ count(session('bulk_results.errors')) - 3 }} more</li>
                        @endif
                    </ul>
                @endif
            </div>
        </div>
    @endif

    @if (session('success'))
        <div class="alert alert-success" style="margin-bottom: var(--spacing-lg);">
            <i class="bi bi-check-circle alert-icon"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger" style="margin-bottom: var(--spacing-lg);">
            <i class="bi bi-exclamation-circle alert-icon"></i>
            <div>
                <strong>An Error Occurred</strong>
                <ul style="margin: var(--spacing-sm) 0 0 var(--spacing-lg); padding-left: var(--spacing-lg);">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="filter-title">
            <i class="bi bi-funnel"></i> Filter & Search
        </div>
        <form method="GET" action="{{ route('admin.loans.index') }}" class="filter-grid">
            <div class="form-group-compact">
                <label class="filter-label" for="search">Staff Name or Email</label>
                <input 
                    type="text" 
                    class="form-control" 
                    id="search"
                    name="search" 
                    placeholder="Search..." 
                    value="{{ request('search') }}"
                >
            </div>

            <div class="form-group-compact">
                <label class="filter-label" for="status">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">All Status</option>
                    @foreach ($statuses as $statusKey => $statusLabel)
                        <option value="{{ $statusKey }}" @if (request('status') == $statusKey) selected @endif>
                            {{ $statusLabel }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="filter-actions">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Filter
                </button>
                @if (request('search') || request('status'))
                    <a href="{{ route('admin.loans.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Loans Table -->
    @if ($loans->count() > 0)
        <div class="table-container">
            <div style="overflow-x: auto;">
                <table class="table" style="min-width: 1000px;">
                    <thead>
                        <tr>
                            <th class="checkbox-cell">
                                <input type="checkbox" class="form-check-input" id="selectAll" title="Select all loans">
                            </th>
                            <th>Request</th>
                            <th>Staff Member</th>
                            <th style="text-align: center;">Items</th>
                            <th style="text-align: center;">Qty</th>
                            <th>Purpose</th>
                            <th>Requested</th>
                            <th>Status</th>
                            <th style="text-align: center;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loans as $loan)
                            <tr>
                                <td class="checkbox-cell" data-label="">
                                    <input type="checkbox" class="form-check-input loan-checkbox" value="{{ $loan->id }}" title="Select loan">
                                </td>
                                <td data-label="Request ID">
                                    <span class="request-id">#{{ $loan->id }}</span>
                                </td>
                                <td data-label="Staff">
                                    <div class="staff-info">
                                        <span class="staff-name">{{ $loan->user->name }}</span>
                                        <span class="staff-email">{{ $loan->user->email }}</span>
                                    </div>
                                </td>
                                <td style="text-align: center;" data-label="Items">
                                    <span class="item-badge">{{ $loan->getTotalItemsCount() }} items{{ $loan->getTotalItemsCount() !== 1 ? 's' : '' }}</span>
                                </td>
                                <td style="text-align: center;" data-label="Qty">
                                    <span style="font-weight: var(--font-weight-semibold);">{{ $loan->getTotalQuantity() }} units</span>
                                </td>
                                <td data-label="Purpose" style="max-width: 200px;">
                                    <div style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $loan->purpose ?? 'No purpose specified' }}">
                                        <small style="color: var(--gray-600);">{{ $loan->purpose ?? 'No purpose specified' }}</small>
                                    </div>
                                </td>
                                <td data-label="Requested">
                                    <small style="color: var(--gray-600);">{{ $loan->requested_at->format('d M Y') }}<br><span style="font-size: 0.85em;">{{ $loan->requested_at->format('H:i') }}</span></small>
                                </td>
                                <td data-label="Status">
                                    <span class="badge badge-{{ match($loan->status) {
                                        'Pending Approval' => 'warning',
                                        'Approved' => 'info',
                                        'Borrowed' => 'primary',
                                        'Returned' => 'success',
                                        'Rejected' => 'danger',
                                        default => 'secondary'
                                    } }}">
                                        {{ $statuses[$loan->status] ?? $loan->status }}
                                    </span>
                                </td>
                                <td class="action-cell" data-label="Action">
                                    <a href="{{ route('admin.loans.show', $loan) }}" class="btn btn-sm btn-outline-primary" title="View loan request">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bulk Actions Bar -->
        @if ($loans->where('status', $statusPending)->count() > 0)
            <div class="bulk-actions-bar">
                <form method="POST" action="{{ route('admin.loans.bulkApprove') }}" id="bulkForm" style="width: 100%;">
                    @csrf
                    <div style="display: flex; align-items: center; gap: var(--spacing-lg); flex-wrap: wrap;">
                        <div style="flex: 1;">
                            <strong>Selected:</strong> <span id="selectedCount" style="font-size: var(--font-size-lg); color: var(--primary);">0</span> loans
                        </div>
                        <button type="submit" class="btn btn-success" id="bulkApproveBtn" disabled style="white-space: nowrap;">
                            <i class="bi bi-check2"></i> Approve Selected
                        </button>
                        <input type="hidden" id="loanIdsInput" name="loan_ids" value="">
                    </div>
                </form>
            </div>
        @endif

        <!-- Pagination -->
        <div style="display: flex; justify-content: center; margin-top: var(--spacing-2xl);">
            {{ $loans->links() }}
        </div>
    @else
        <div class="table-container">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-inbox"></i>
                </div>
                <p class="empty-state-text">
                    @if (request('status') || request('search'))
                        No loan requests match your filter.
                    @else
                        No loan requests to review.
                    @endif
                </p>
                @if (request('status') || request('search'))
                    <a href="{{ route('admin.loans.index') }}" class="btn btn-primary">
                        <i class="bi bi-arrow-clockwise"></i> Clear Filters
                    </a>
                @endif
            </div>
        </div>
    @endif
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const loanCheckboxes = document.querySelectorAll('.loan-checkbox');
    const bulkForm = document.getElementById('bulkForm');
    const bulkApproveBtn = document.getElementById('bulkApproveBtn');
    const loanIdsInput = document.getElementById('loanIdsInput');
    const selectedCount = document.getElementById('selectedCount');

    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            loanCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });
    }

    loanCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkActions();
        });
    });

    if (bulkForm) {
        bulkForm.addEventListener('submit', function(e) {
            const checkedCount = Array.from(loanCheckboxes).filter(cb => cb.checked).length;
            if (checkedCount === 0) {
                e.preventDefault();
                alert('Please select at least one loan request to approve.');
                return false;
            }
            if (!confirm(`Are you sure you want to approve ${checkedCount} loan requests? This will reduce inventory stock.`)) {
                e.preventDefault();
                return false;
            }
        });
    }

    function updateBulkActions() {
        const checkedLoans = Array.from(loanCheckboxes).filter(cb => cb.checked);
        selectedCount.textContent = checkedLoans.length;
        
        if (bulkApproveBtn) {
            bulkApproveBtn.disabled = checkedLoans.length === 0;
        }

        // ✅ FIXED: Send array of loan IDs as JSON string that Laravel can parse
        // Laravel's form data parser will convert the JSON array to PHP array
        loanIdsInput.value = JSON.stringify(checkedLoans.map(cb => parseInt(cb.value)));
    }
});
</script>
@endsection
