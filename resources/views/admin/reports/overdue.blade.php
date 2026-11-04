@extends('layouts.app')

@section('title', 'Overdue Loans Report - SIMBAR')

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

    .table-card tbody tr.overdue-severe {
        background-color: var(--danger-light);
    }

    .table-card tbody tr.overdue-severe:hover {
        background-color: var(--danger-light);
    }

    .table-card tbody tr.overdue-moderate {
        background-color: var(--warning-light);
    }

    .table-card tbody tr.overdue-moderate:hover {
        background-color: var(--warning-light);
    }

    .table-card tbody tr:hover {
        background-color: var(--gray-50);
    }

    .table-card tbody tr:last-child td {
        border-bottom: none;
    }

    .alert-danger {
        background-color: var(--danger-light);
        color: var(--danger);
        border: 1px solid var(--danger);
        border-left: 4px solid var(--danger);
    }
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 0 var(--spacing-lg);">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1>
                <i class="bi bi-exclamation-triangle"></i>
                Overdue Loans Report
            </h1>
            <p>Loans that have not been returned by their due date</p>
        </div>
        <div style="display: flex; gap: var(--spacing-md);">
            <a href="{{ route('admin.reports.export-overdue') }}" class="btn btn-success">
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
                    <label for="days_overdue" class="form-label" style="font-size: var(--font-size-sm); color: var(--gray-700);">Minimum Days Overdue</label>
                    <select class="form-select" id="days_overdue" name="days_overdue">
                        <option value="">-- Show All --</option>
                        <option value="1" {{ request('days_overdue') === '1' ? 'selected' : '' }}>1+ days</option>
                        <option value="7" {{ request('days_overdue') === '7' ? 'selected' : '' }}>1+ weeks</option>
                        <option value="14" {{ request('days_overdue') === '14' ? 'selected' : '' }}>2+ weeks</option>
                        <option value="30" {{ request('days_overdue') === '30' ? 'selected' : '' }}>1+ months</option>
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
                    <label for="search" class="form-label" style="font-size: var(--font-size-sm); color: var(--gray-700);">Search (Name/Email)</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Search staff..." value="{{ request('search') }}">
                </div>
                <div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="bi bi-search"></i> Apply
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Alert -->
    @if ($overdueLoans->count() > 0)
        <div class="alert alert-danger" style="padding: var(--spacing-lg); border-radius: var(--radius-lg); margin-bottom: var(--spacing-lg); display: flex; align-items: flex-start; gap: var(--spacing-md);">
            <i class="bi bi-exclamation-triangle-fill" style="font-size: 1.25rem; flex-shrink: 0;"></i>
            <div>
                <strong>{{ $overdueLoans->total() }} overdue loan(s) detected!</strong>
                <p style="margin: var(--spacing-xs) 0 0 0; font-size: var(--font-size-sm);">
                    Please follow up with staff members to collect items immediately.
                </p>
            </div>
        </div>
    @endif

    <!-- Results -->
    <div class="table-card">
        <div style="padding: var(--spacing-lg); background-color: var(--gray-50); border-bottom: 1px solid var(--gray-200);">
            <h5 style="margin: 0;">Overdue Loans ({{ $overdueLoans->total() }} total)</h5>
        </div>
        @if ($overdueLoans->count() > 0)
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Loan ID</th>
                            <th>Staff Name</th>
                            <th>Email</th>
                            <th>Items</th>
                            <th>Due Date</th>
                            <th>Days Overdue</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($overdueLoans as $loan)
                            @php
                                $minDueDate = $loan->loanItems()
                                    ->where('planned_return_at', '<', now())
                                    ->whereNull('returned_at')
                                    ->min('planned_return_at');
                                $daysOverdue = now()->diffInDays(\Carbon\Carbon::parse($minDueDate));
                            @endphp
                            <tr class="{{ $daysOverdue > 14 ? 'overdue-severe' : ($daysOverdue > 7 ? 'overdue-moderate' : '') }}">
                                <td style="font-weight: var(--font-weight-semibold); color: var(--primary);">#{{ $loan->id }}</td>
                                <td>{{ $loan->user->name }}</td>
                                <td style="font-size: var(--font-size-xs);">{{ $loan->user->email }}</td>
                                <td>
                                    @php
                                        $overdueItems = $loan->loanItems()
                                            ->where('planned_return_at', '<', now())
                                            ->whereNull('returned_at')
                                            ->get();
                                    @endphp
                                    <div style="font-size: var(--font-size-sm);">
                                        @foreach ($overdueItems as $item)
                                            <div>{{ $item->item->name }} ({{ $item->quantity }})</div>
                                        @endforeach
                                    </div>
                                </td>
                                <td>
                                    @if ($minDueDate)
                                        {{ \Carbon\Carbon::parse($minDueDate)->format('Y-m-d') }}
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $daysOverdue > 14 ? 'danger' : ($daysOverdue > 7 ? 'warning' : 'info') }}">
                                        {{ $daysOverdue }} days
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="padding: var(--spacing-2xl); text-align: center;">
                <i class="bi bi-check-circle" style="font-size: 2rem; color: var(--gray-300); margin-bottom: var(--spacing-md); display: block;"></i>
                <p style="color: var(--gray-500); margin: 0;">No overdue loans - Great!</p>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    <div style="display: flex; justify-content: center; margin-top: var(--spacing-2xl);">
        {{ $overdueLoans->links() }}
    </div>
</div>
@endsection
