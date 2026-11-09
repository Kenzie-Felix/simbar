@extends('layouts.app')

@section('title', $item->name . ' - SIMBAR')

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
        grid-template-columns: 1fr;
        gap: var(--spacing-lg);
    }

    .item-detail-image {
        width: 100%;
        max-height: 600px;
        object-fit: contain;
        background: var(--gray-50);
        border-radius: var(--radius-lg);
        border: 1px solid var(--gray-200);
        padding: var(--spacing-lg);
        margin-bottom: var(--spacing-lg);
        transition: transform 0.3s ease;
        cursor: zoom-in;
    }

    .item-detail-image:hover {
        transform: scale(1.02);
    }

    /* Modal for zoomed image */
    .image-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.9);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: var(--spacing-lg);
    }

    .image-modal.active {
        display: flex;
    }

    .image-modal-content {
        position: relative;
        max-width: 90vw;
        max-height: 90vh;
        background: white;
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
    }

    .image-modal-img {
        max-width: 100%;
        max-height: 85vh;
        object-fit: contain;
    }

    .image-modal-close {
        position: absolute;
        top: var(--spacing-md);
        right: var(--spacing-md);
        background: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        border-radius: var(--radius-md);
        padding: var(--spacing-sm) var(--spacing-md);
        cursor: pointer;
        font-size: var(--font-size-lg);
        transition: all 0.2s ease;
    }

    .image-modal-close:hover {
        background: rgba(0, 0, 0, 0.8);
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

    .movements-table {
        border-collapse: collapse;
        width: 100%;
    }

    .movements-table thead th {
        padding: var(--spacing-md) var(--spacing-lg);
        text-align: left;
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-700);
        background-color: var(--gray-50);
        border-bottom: 1px solid var(--gray-200);
    }

    .movements-table tbody td {
        padding: var(--spacing-md) var(--spacing-lg);
        border-bottom: 1px solid var(--gray-200);
        font-size: var(--font-size-sm);
    }

    .movements-table tbody tr:hover {
        background-color: var(--gray-50);
    }

    .movements-table tbody tr:last-child td {
        border-bottom: none;
    }

    .stat-box {
        text-align: center;
        padding: var(--spacing-lg);
        border-radius: var(--radius-lg);
        background-color: var(--gray-50);
    }

    .stat-box-label {
        font-size: var(--font-size-sm);
        color: var(--gray-500);
        margin-bottom: var(--spacing-sm);
    }

    .stat-box-value {
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
        color: var(--gray-900);
    }

    .sticky-sidebar {
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

        .sticky-sidebar {
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
                <i class="bi bi-collection"></i>
                {{ $item->name }}
            </h1>
            <p>Code: <strong>{{ $item->code }}</strong></p>
        </div>
        <div style="display: flex; gap: var(--spacing-sm);">
            <a href="{{ route('admin.items.edit', $item) }}" class="btn btn-primary">
                <i class="bi bi-pencil"></i> Sunting
            </a>
            <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <!-- Content Grid -->
    <div class="content-grid">
        <!-- Main Content -->
        <div>
            <!-- Item Image -->
            <div>
                <img src="{{ $item->getImageUrl() }}" alt="{{ $item->name }}" class="item-detail-image" onclick="zoomImage(this)" title="Klik untuk memperbesar">
                <!-- Image Zoom Modal -->
                <div class="image-modal" id="imageModal">
                    <div class="image-modal-content">
                        <button class="image-modal-close" onclick="closeImageZoom()">&times;</button>
                        <img src="{{ $item->getImageUrl() }}" alt="{{ $item->name }}" class="image-modal-img">
                    </div>
                </div>
            </div>

            <!-- Item Details -->
            <div class="card">
                <div class="card-header">
                    <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                        <i class="bi bi-info-circle"></i> Detail Barang
                    </h5>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <div class="detail-field">
                            <span class="detail-label">Kategori</span>
                            <span class="badge badge-secondary">{{ $item->category->name }}</span>
                        </div>
                        <div class="detail-field">
                            <span class="detail-label">Lokasi</span>
                            <span class="detail-value">{{ $item->location->name }}</span>
                        </div>
                    </div>
                    @if ($item->description)
                        <div class="detail-field">
                            <span class="detail-label">Deskripsi</span>
                            <span class="detail-value" style="font-size: var(--font-size-sm);">{{ $item->description }}</span>
                        </div>
                    @endif
                    <div class="detail-row">
                        <div class="detail-field">
                            <span class="detail-label">Dibuat</span>
                            <span class="detail-value" style="font-size: var(--font-size-sm);">{{ $item->created_at ? $item->created_at->format('d M Y H:i') : 'Belum tersedia' }}</span>
                        </div>
                        <div class="detail-field">
                            <span class="detail-label">Terakhir Diperbarui</span>
                            <span class="detail-value" style="font-size: var(--font-size-sm);">{{ $item->updated_at ? $item->updated_at->format('d M Y H:i') : 'Belum tersedia' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Stock Summary -->
            <div class="card" style="margin-top: var(--spacing-lg);">
                <div class="card-header">
                    <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                        <i class="bi bi-graph-up"></i> Ringkasan Stok
                    </h5>
                </div>
                <div class="card-body">
                    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--spacing-lg);">
                        <div class="stat-box">
                            <div class="stat-box-label">Total Stok</div>
                            <div class="stat-box-value" style="color: var(--info);">{{ $item->total_stock }}</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-box-label">Tersedia</div>
                            <div class="stat-box-value" style="color: var(--success);">{{ $item->available_stock }}</div>
                        </div>
                        <div class="stat-box">
                            <div class="stat-box-label">Sedang Digunakan</div>
                            <div class="stat-box-value" style="color: var(--warning);">{{ $item->total_stock - $item->available_stock }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Condition -->
            <div class="card" style="margin-top: var(--spacing-lg);">
                <div class="card-header">
                    <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                        <i class="bi bi-info-circle"></i> Kondisi Barang
                    </h5>
                </div>
                <div class="card-body">
                    <span class="badge badge-{{ $item->getConditionBadgeColor() }}" style="padding: var(--spacing-sm) var(--spacing-md); font-size: var(--font-size-base);">
                        {{ $item->condition }}
                    </span>
                </div>
            </div>

            <!-- Condition History -->
            @if ($item->conditionLogs->count() > 0)
                <div class="card" style="margin-top: var(--spacing-lg);">
                    <div class="card-header">
                        <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                            <i class="bi bi-clock-history"></i> Riwayat Kondisi
                        </h5>
                    </div>
                    <div class="card-body">
                        <div style="position: relative; padding-left: 30px;">
                            @foreach ($item->conditionLogs as $log)
                                <div style="padding-bottom: 15px; border-left: 2px solid var(--gray-200); padding-left: 15px;">
                                    <small style="color: var(--gray-500);">{{ $log->created_at ? $log->created_at->format('M d, Y H:i') : '-' }}</small>
                                    <p style="small; margin: var(--spacing-xs) 0 0 0; font-weight: var(--font-weight-medium);">
                                        {{ $log->changedBy ? $log->changedBy->name : 'System' }} changed condition
                                    </p>
                                    <p style="font-size: var(--font-size-sm); color: var(--gray-600); margin: 0;">
                                        {{ $log->getConditionChangeLabel() }}
                                        @if ($log->reason)
                                            <br>Reason: {{ $log->reason }}
                                        @endif
                                    </p>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Stock Movements -->
            @if ($item->stockMovements->count() > 0)
                <div class="card" style="margin-top: var(--spacing-lg);">
                    <div class="card-header">
                        <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                            <i class="bi bi-arrow-left-right"></i> Pergerakan Stok
                        </h5>
                    </div>
                    <div style="overflow-x: auto;">
                        <table class="movements-table">
                            <thead>
                                <tr>
                                    <th>Tanggal</th>
                                    <th>Tipe</th>
                                    <th>Jumlah</th>
                                    <th>Oleh</th>
                                    <th>Catatan</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($item->stockMovements->sortByDesc('created_at')->take(10) as $movement)
                                    <tr>
                                        <td>{{ $movement->created_at ? $movement->created_at->format('M d, Y H:i') : '-' }}</td>
                                        <td>
                                            <span class="badge badge-{{ $movement->type === 'In' ? 'success' : ($movement->type === 'Out' ? 'danger' : 'secondary') }}">
                                                {{ $movement->type }}
                                            </span>
                                        </td>
                                        <td>{{ $movement->quantity }}</td>
                                        <td><small>{{ $movement->createdBy ? $movement->createdBy->name : 'System' }}</small></td>
                                        <td><small style="color: var(--gray-500);">{{ $movement->notes ? Str::limit($movement->notes, 30) : '-' }}</small></td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
    function zoomImage(img) {
        const modal = document.getElementById('imageModal');
        modal.classList.add('active');
    }

    function closeImageZoom() {
        const modal = document.getElementById('imageModal');
        modal.classList.remove('active');
    }

    // Close modal when clicking outside image
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('imageModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeImageZoom();
                }
            });
            
            // Close with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeImageZoom();
                }
            });
        }
    });
</script>

@endsection
