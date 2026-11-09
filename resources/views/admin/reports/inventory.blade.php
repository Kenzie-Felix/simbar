@extends('layouts.app')

@section('title', 'Laporan Inventaris - SIMBAR')

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
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 0 var(--spacing-lg);">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1>
                <i class="bi bi-archive"></i>
                Laporan Inventaris
            </h1>
            <p>Status stok dan inventaris saat ini</p>
        </div>
        <div style="display: flex; gap: var(--spacing-sm);">
            <a href="{{ route('admin.reports.dashboard') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Filter Card -->
    <div class="card" style="margin-bottom: var(--spacing-lg);">
        <div class="card-body">
            <form method="GET" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-md); align-items: end;">
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: var(--font-size-sm);">Kategori</label>
                    <select class="form-select" name="category_id">
                        <option value="">-- Semua Kategori --</option>
                        @foreach (\App\Models\Category::all() as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: var(--font-size-sm);">Lokasi</label>
                    <select class="form-select" name="location_id">
                        <option value="">-- Semua Lokasi --</option>
                        @foreach (\App\Models\Location::all() as $location)
                            <option value="{{ $location->id }}" {{ request('location_id') == $location->id ? 'selected' : '' }}>
                                {{ $location->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 0.5rem; font-weight: 500; font-size: var(--font-size-sm);">Level Stok</label>
                    <select class="form-select" name="stock_level">
                        <option value="">-- Semua Level --</option>
                        <option value="good" {{ request('stock_level') === 'good' ? 'selected' : '' }}>Baik (> 5)</option>
                        <option value="low" {{ request('stock_level') === 'low' ? 'selected' : '' }}>Rendah (1-5)</option>
                        <option value="out" {{ request('stock_level') === 'out' ? 'selected' : '' }}>Habis (0)</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-funnel"></i> Saringan
                </button>
            </form>
        </div>
    </div>

    <!-- Results -->
    @if ($items->count() > 0)
        <div class="card">
            <div class="card-body p-0">
                <div style="overflow-x: auto;">
                    <table class="items-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Nama</th>
                                <th>Kategori</th>
                                <th>Lokasi</th>
                                <th style="text-align: center;">Total Stok</th>
                                <th style="text-align: center;">Tersedia</th>
                                <th>Kondisi</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td>
                                        <strong>{{ $item->code }}</strong>
                                    </td>
                                    <td>{{ $item->name }}</td>
                                    <td>
                                        <span class="badge badge-secondary">{{ $item->category->name }}</span>
                                    </td>
                                    <td>
                                        <small style="color: var(--gray-500);">{{ $item->location->name }}</small>
                                    </td>
                                    <td style="text-align: center;">
                                        {{ $item->total_stock }}
                                    </td>
                                    <td style="text-align: center;">
                                        <span style="font-weight: var(--font-weight-bold); color: {{ $item->available_stock > 0 ? 'var(--success)' : 'var(--danger)' }};">
                                            {{ $item->available_stock }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge badge-{{ $item->getConditionBadgeColor() }}">
                                            {{ $item->condition }}
                                        </span>
                                    </td>
                                    <td>
                                        @if ($item->available_stock === 0)
                                            <span class="badge badge-danger">Habis Stok</span>
                                        @elseif ($item->available_stock <= 5)
                                            <span class="badge badge-warning">Stok Rendah</span>
                                        @else
                                            <span class="badge badge-success">Baik</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Pagination -->
        @if ($items->hasPages())
            <div style="display: flex; justify-content: center; margin-top: var(--spacing-xl);">
                {{ $items->links() }}
            </div>
        @endif
    @else
        <div class="card">
            <div class="empty-state" style="text-align: center; padding: var(--spacing-xl) var(--spacing-lg);">
                <div class="empty-state-icon" style="margin-bottom: var(--spacing-lg);">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: var(--gray-400);"></i>
                </div>
                <p class="empty-state-text" style="font-size: var(--font-size-base); color: var(--gray-500);">
                    Tidak ada item inventaris yang ditemukan
                </p>
            </div>
        </div>
    @endif
</div>

@endsection
