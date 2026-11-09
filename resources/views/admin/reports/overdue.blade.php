@extends('layouts.app')

@section('title', 'Laporan Peminjaman Terlambat - SIMBAR')

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
                Laporan Peminjaman Terlambat
            </h1>
            <p>Daftar barang yang belum dikembalikan melampaui tanggal jatuh tempo</p>
        </div>
        <div style="display: flex; gap: var(--spacing-md);">
            <a href="{{ route('admin.reports.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="card" style="margin-bottom: var(--spacing-lg);">
        <div class="card-header">
            <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                <i class="bi bi-funnel"></i> Filter
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-md); align-items: end;">
                <div>
                    <label for="days_overdue" class="form-label" style="font-size: var(--font-size-sm); color: var(--gray-700);">Minimum Lama Keterlambatan</label>
                    <select class="form-select" id="days_overdue" name="days_overdue">
                        <option value="">-- Tampilkan Semua --</option>
                        <option value="1" {{ request('days_overdue') === '1' ? 'selected' : '' }}>1+ hari</option>
                        <option value="7" {{ request('days_overdue') === '7' ? 'selected' : '' }}>1+ minggu</option>
                        <option value="14" {{ request('days_overdue') === '14' ? 'selected' : '' }}>2+ minggu</option>
                        <option value="30" {{ request('days_overdue') === '30' ? 'selected' : '' }}>1+ bulan</option>
                    </select>
                </div>
                <div>
                    <label for="user_id" class="form-label" style="font-size: var(--font-size-sm); color: var(--gray-700);">Anggota Staf</label>
                    <select class="form-select" id="user_id" name="user_id">
                        <option value="">-- Semua Staf --</option>
                        @foreach (\App\Models\User::where('role', 'Staff')->where('is_active', true)->get() as $user)
                            <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                {{ $user->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="search" class="form-label" style="font-size: var(--font-size-sm); color: var(--gray-700);">Cari (Nama/Email)</label>
                    <input type="text" class="form-control" id="search" name="search" placeholder="Cari staf..." value="{{ request('search') }}">
                </div>
                <div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="bi bi-search"></i> Terapkan
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
                <strong>{{ $overdueLoans->total() }} peminjaman terlambat terdeteksi!</strong>
                <p style="margin: var(--spacing-xs) 0 0 0; font-size: var(--font-size-sm);">
                    Silakan hubungi anggota staf untuk segera mengambil kembali barang.
                </p>
            </div>
        </div>
    @endif

    <!-- Results -->
    <div class="table-card">
        <div style="padding: var(--spacing-lg); background-color: var(--gray-50); border-bottom: 1px solid var(--gray-200);">
            <h5 style="margin: 0;">Peminjaman Terlambat ({{ $overdueLoans->total() }} total)</h5>
        </div>
        @if ($overdueLoans->count() > 0)
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID Peminjaman</th>
                            <th>Nama Staf</th>
                            <th>Email</th>
                            <th>Barang</th>
                            <th>Tanggal Jatuh Tempo</th>
                            <th>Lama Keterlambatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($overdueLoans as $loan)
                            @php
                                $minDueDate = $loan->loanItems()
                                    ->where('planned_return_at', '<', now())
                                    ->whereNull('returned_at')
                                    ->min('planned_return_at');
                                // Calculate days overdue correctly (from past to now = positive number)
                                $daysOverdue = max(0, (int) \Carbon\Carbon::parse($minDueDate)->diffInDays(now()));
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
                                        {{ \Carbon\Carbon::parse($minDueDate)->format('d F Y') }}
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-{{ $daysOverdue > 14 ? 'danger' : ($daysOverdue > 7 ? 'warning' : 'info') }}">
                                        {{ $daysOverdue }} hari
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
                <p style="color: var(--gray-500); margin: 0;">Tidak ada peminjaman terlambat - Sempurna!</p>
            </div>
        @endif
    </div>

    <!-- Pagination -->
    <div style="display: flex; justify-content: center; margin-top: var(--spacing-2xl);">
        {{ $overdueLoans->links() }}
    </div>
</div>
@endsection
