@extends('layouts.app')

@section('title', 'Loan Reports - SIMBAR')

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

    .table-card {
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        overflow: hidden;
    }

    .table-card table {
        border-collapse: collapse;
        width: 100%;
    }

    .table-card thead th {
        padding: var(--spacing-md) var(--spacing-lg);
        text-align: left;
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-700);
        background-color: var(--gray-50);
        border-bottom: 1px solid var(--gray-200);
    }

    .table-card tbody td {
        padding: var(--spacing-md) var(--spacing-lg);
        border-bottom: 1px solid var(--gray-200);
        font-size: var(--font-size-sm);
        color: var(--gray-600);
    }

    .table-card tbody tr:hover {
        background-color: var(--gray-50);
    }

    .table-card tbody tr:last-child td {
        border-bottom: none;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
        }
    }
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 0 var(--spacing-lg);">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1>
                <i class="bi bi-file-earmark-text"></i>
                Loan Reports
            </h1>
            <p>View and analyze loan transactions</p>
        </div>
        <div style="display: flex; gap: var(--spacing-md);">
            <a href="{{ route('admin.reports.export-loans', request()->query()) }}" class="btn btn-success">
                <i class="bi bi-download"></i> Export to CSV
            </a>
            <a href="{{ route('admin.reports.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card" style="margin-bottom: var(--spacing-lg);">
        <div class="card-header">
            <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                <i class="bi bi-funnel"></i> Filters
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-md); align-items: end;">
                <div>
                    <label for="date_from" class="form-label" style="font-size: var(--font-size-sm); color: var(--gray-700);">From Date</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div>
                    <label for="date_to" class="form-label" style="font-size: var(--font-size-sm); color: var(--gray-700);">To Date</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div>
                    <label for="status" class="form-label" style="font-size: var(--font-size-sm); color: var(--gray-700);">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">-- All Statuses --</option>
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="user_id" class="form-label" style="font-size: var(--font-size-sm); color: var(--gray-700);">Staff Member</label>
                    <select class="form-select" id="user_id" name="user_id">
                        <option value="">-- All Staff --</option>
                        @foreach (\App\Models\User::where('role', 'Staff')->where('is_active', true)->get() as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="bi bi-search"></i> Apply Filters
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Results -->
    <div class="table-card">
        <div style="padding: var(--spacing-lg); background-color: var(--gray-50); border-bottom: 1px solid var(--gray-200);">
            <h5 style="margin: 0;">Loan Records ({{ $loans->total() }} total)</h5>
        </div>
        @if ($loans->count() > 0)
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Loan ID</th>
                            <th>Staff Member</th>
                            <th>Status</th>
                            <th>Items</th>
                            <th>Qty</th>
                            <th>Requested</th>
                            <th>Approved</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($loans as $loan)
                            <tr>
                                <td style="font-weight: var(--font-weight-semibold); color: var(--primary);">#{{ $loan->id }}</td>
                                <td>
                                    <div style="font-weight: var(--font-weight-medium);">{{ $loan->user->name }}</div>
                                    <div style="font-size: var(--font-size-xs); color: var(--gray-500);">{{ $loan->user->email }}</div>
                                </td>
                                <td>
                                    <span class="badge badge-{{ match($loan->status) {
                                        'Menunggu Persetujuan' => 'warning',
                                        'Disetujui' => 'info',
                                        'Dipinjam' => 'primary',
                                        'Dikembalikan' => 'success',
                                        'Ditolak' => 'danger',
                                        default => 'secondary'
                                    } }}">
                                        {{ $loan->status }}
                                    </span>
                                </td>
                                <td>{{ $loan->getTotalItemsCount() }}</td>
                                <td>{{ $loan->getTotalQuantity() }}</td>
                                <td>{{ $loan->requested_at->format('Y-m-d H:i') }}</td>
                                <td>
                                    @if ($loan->approved_at)
                                        {{ $loan->approved_at->format('Y-m-d H:i') }}
                                    @else
                                        <span style="color: var(--gray-400);">-</span>
                                    @endif
                                </td>
                                <td>
                                    <a href="{{ route('staff.loans.show', $loan) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="padding: var(--spacing-2xl); text-align: center; color: var(--gray-500);">
                No loan records found
            </div>
        @endif
    </div>

    <!-- Pagination -->
    <div style="display: flex; justify-content: center; margin-top: var(--spacing-2xl);">
        {{ $loans->links() }}
    </div>
</div>
@endsection
