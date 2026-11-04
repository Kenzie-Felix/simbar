@extends('layouts.app')

@section('title', 'Loan Approvals - SIMBAR')

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

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: var(--spacing-lg);
        margin-bottom: var(--spacing-xl);
    }

    .stat-card {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        box-shadow: var(--shadow-sm);
        transition: all 0.2s ease;
    }

    .stat-card:hover {
        box-shadow: var(--shadow-md);
    }

    .stat-label {
        font-size: var(--font-size-sm);
        color: var(--gray-500);
        font-weight: var(--font-weight-medium);
        margin-bottom: var(--spacing-sm);
    }

    .stat-value {
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
        color: var(--gray-900);
    }

    .filter-card {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        box-shadow: var(--shadow-sm);
        margin-bottom: var(--spacing-xl);
    }

    .filter-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: var(--spacing-lg);
        align-items: end;
    }

    .filter-label {
        display: block;
        margin-bottom: var(--spacing-sm);
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
    }

    .form-control:focus,
    .form-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .table-card {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .table {
        margin-bottom: 0;
    }

    .table thead {
        background-color: var(--gray-50);
        border-bottom: 1px solid var(--gray-200);
    }

    .table th {
        padding: var(--spacing-md) var(--spacing-lg);
        text-align: left;
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-700);
    }

    .table td {
        padding: var(--spacing-md) var(--spacing-lg);
        border-bottom: 1px solid var(--gray-200);
        font-size: var(--font-size-sm);
    }

    .table tbody tr:hover {
        background-color: var(--gray-50);
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    .bulk-actions {
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        margin-top: var(--spacing-xl);
        display: flex;
        align-items: center;
        gap: var(--spacing-lg);
    }

    .empty-state {
        text-align: center;
        padding: var(--spacing-2xl) var(--spacing-lg);
    }

    .empty-state-icon {
        font-size: 3rem;
        color: var(--gray-300);
        margin-bottom: var(--spacing-lg);
    }

    .empty-state-text {
        font-size: var(--font-size-base);
        color: var(--gray-500);
        margin-bottom: var(--spacing-lg);
    }

    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }

        .filter-grid {
            grid-template-columns: 1fr;
        }

        .bulk-actions {
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
                <i class="bi bi-clipboard-check"></i> Loan Approvals
            </h1>
            <p>Review and manage loan requests</p>
        </div>
        <a href="{{ route('admin.loans.exportPending') }}" class="btn btn-primary">
            <i class="bi bi-download"></i> Export Pending
        </a>
    </div>

    <!-- Stats Grid -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-label">Pending</div>
            <div class="stat-value" style="color: var(--warning);">{{ $stats['pending_count'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Approved</div>
            <div class="stat-value" style="color: var(--success);">{{ $stats['approved_count'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Rejected</div>
            <div class="stat-value" style="color: var(--danger);">{{ $stats['rejected_count'] }}</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Borrowed</div>
            <div class="stat-value" style="color: var(--info);">{{ $stats['borrowed_count'] }}</div>
        </div>
    </div>

    <!-- Alerts -->
    @if (session('bulk_results'))
        <div class="alert alert-success">
            <i class="bi bi-info-circle alert-icon"></i>
            <div>
                <strong>Bulk Approval Results:</strong><br>
                Approved: {{ session('bulk_results.approved') }}, Failed: {{ session('bulk_results.failed') }}
                @if (session('bulk_results.errors'))
                    <ul style="margin: var(--spacing-md) 0 0 0; padding-left: var(--spacing-lg);">
                        @foreach (session('bulk_results.errors') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                @endif
            </div>
        </div>
    @endif

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
                <ul style="margin: var(--spacing-md) 0 0 0; padding-left: var(--spacing-lg);">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Filter Card -->
    <div class="filter-card">
        <form method="GET" action="{{ route('admin.loans.index') }}" class="filter-grid">
            <div>
                <label class="filter-label">Search</label>
                <input 
                    type="text" 
                    class="form-control" 
                    name="search" 
                    placeholder="By staff name or email..." 
                    value="{{ request('search') }}"
                >
            </div>

            <div>
                <label class="filter-label">Status</label>
                <select class="form-select" name="status">
                    <option value="">-- All Status --</option>
                    @foreach ($statuses as $statusKey => $statusLabel)
                        <option value="{{ $statusKey }}" @if (request('status') == $statusKey) selected @endif>
                            {{ $statusLabel }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: var(--spacing-md);">
                <button type="submit" class="btn btn-primary" style="flex: 1;">
                    <i class="bi bi-funnel"></i> Filter
                </button>

                @if (request('search') || request('status'))
                    <a href="{{ route('admin.loans.index') }}" class="btn btn-secondary" style="flex: 1;">
                        <i class="bi bi-arrow-clockwise"></i> Clear
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Loans Table -->
    @if ($loans->count() > 0)
        <div class="table-card">
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">
                                <input type="checkbox" class="form-check-input" id="selectAll" title="Select all">
                            </th>
                            <th>Request ID</th>
                            <th>Staff Member</th>
                            <th style="text-align: center;">Items</th>
                            <th style="text-align: center;">Qty</th>
                            <th>Purpose</th>
                            <th>Requested</th>
                            <th>Status</th>
                            <th style="width: 100px;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loans as $loan)
                            <tr>
                                <td>
                                    <input type="checkbox" class="form-check-input loan-checkbox" value="{{ $loan->id }}" title="Select loan">
                                </td>
                                <td><strong>#{{ $loan->id }}</strong></td>
                                <td>
                                    <strong>{{ $loan->user->name }}</strong><br>
                                    <small style="color: var(--gray-500);">{{ $loan->user->email }}</small>
                                </td>
                                <td style="text-align: center;">{{ $loan->getTotalItemsCount() }}</td>
                                <td style="text-align: center;">{{ $loan->getTotalQuantity() }} units</td>
                                <td style="max-width: 200px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="{{ $loan->purpose ?? '-' }}">
                                    <small style="color: var(--gray-500);">{{ $loan->purpose ?? '-' }}</small>
                                </td>
                                <td>
                                    <small style="color: var(--gray-500);">{{ $loan->requested_at->format('d M Y H:i') }}</small>
                                </td>
                                <td>
                                    <span class="badge badge-{{ $loan->status === 'Menunggu Persetujuan' ? 'warning' : ($loan->status === 'Disetujui' ? 'success' : ($loan->status === 'Ditolak' ? 'danger' : 'info')) }}">
                                        {{ $statuses[$loan->status] ?? $loan->status }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.loans.show', $loan) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Review
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Bulk Actions -->
        @if ($loans->where('status', $statusPending)->count() > 0)
            <div class="bulk-actions">
                <form method="POST" action="{{ route('admin.loans.bulkApprove') }}" id="bulkForm" style="width: 100%;">
                    @csrf
                    <div style="display: flex; align-items: center; gap: var(--spacing-lg); flex-wrap: wrap;">
                        <span style="color: var(--gray-600); font-weight: var(--font-weight-medium);">
                            Selected: <strong id="selectedCount">0</strong> loans
                        </span>
                        <button type="submit" class="btn btn-success" id="bulkApproveBtn" disabled>
                            <i class="bi bi-check2"></i> Bulk Approve Selected
                        </button>
                        <input type="hidden" id="loanIdsInput" name="loan_ids" value="">
                    </div>
                </form>
            </div>
        @endif

        <!-- Pagination -->
        <div style="display: flex; justify-content: center; margin-top: var(--spacing-xl);">
            {{ $loans->links() }}
        </div>
    @else
        <div class="table-card">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="bi bi-inbox"></i>
                </div>
                <p class="empty-state-text">
                    @if (request('status') || request('search'))
                        No loan requests match your filters.
                    @else
                        No loan requests available.
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
            if (!confirm(`Are you sure you want to approve ${checkedCount} loan request(s)? This will decrement the inventory stock.`)) {
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
