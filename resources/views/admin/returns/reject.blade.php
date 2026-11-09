@extends('layouts.app')

@section('title', 'Tolak Permintaan Pengembalian - SIMBAR')

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
    }

    .sidebar-card h6 {
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-900);
        margin-bottom: var(--spacing-md);
    }

    .item-entry {
        margin-bottom: var(--spacing-md);
        font-size: var(--font-size-xs);
        color: var(--gray-600);
        padding-bottom: var(--spacing-md);
        border-bottom: 1px solid var(--gray-200);
    }

    .item-entry:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .item-entry strong {
        color: var(--gray-900);
        display: block;
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
                Tolak Permintaan Pengembalian
            </h1>
            <p>Berikan alasan untuk menolak pengembalian ini</p>
        </div>
        <a href="{{ route('admin.returns.show', $return) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Main Content -->
    <div class="form-container">
        <!-- Form Section -->
        <div>
            <!-- Return Summary -->
            <div class="summary-card">
                <h5>Permintaan Pengembalian #{{ $return->id }}</h5>
                <div class="summary-item">
                    <div class="summary-label">Anggota Staf</div>
                    <div class="summary-value">{{ $return->loan->user->name }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Tanggal Dikirim</div>
                    <div class="summary-value">{{ $return->submitted_at->format('d M Y H:i') }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Barang Dikembalikan</div>
                    <div class="summary-value">{{ $return->getReturnedItemsCount() }} barang ({{ $return->getTotalReturnedQuantity() }} unit)</div>
                </div>
            </div>

            <!-- Rejection Form -->
            <div class="form-card">
                <h5 style="font-size: var(--font-size-base); font-weight: var(--font-weight-semibold); color: var(--gray-900); margin-bottom: var(--spacing-lg); padding-bottom: var(--spacing-lg); border-bottom: 1px solid var(--gray-200);">
                    <i class="bi bi-chat-left-text"></i> Alasan untuk Penolakan
                </h5>

                <form action="{{ route('admin.returns.reject', $return) }}" method="POST">
                    @csrf

                    <!-- Rejection Reason -->
                    <div class="form-group">
                        <label for="rejection_reason" class="form-label">
                            Alasan untuk Penolakan <span class="required">*</span>
                        </label>
                        <textarea 
                            class="form-control @error('rejection_reason') is-invalid @enderror" 
                            id="rejection_reason" 
                            name="rejection_reason" 
                            rows="5" 
                            placeholder="Jelaskan mengapa pengembalian ini ditolak..."
                            maxlength="500"
                            required
                        >{{ old('rejection_reason') }}</textarea>
                        <div class="form-help">Maksimal 500 karakter</div>
                        @error('rejection_reason')
                            <div style="color: var(--danger); font-size: var(--font-size-xs); margin-top: var(--spacing-xs);">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Quick Reasons -->
                    <div class="form-group">
                        <label class="form-label">Alasan Cepat:</label>
                        <div class="quick-reasons">
                            <button type="button" class="quick-reason-btn" onclick="setReason('Barang tidak sesuai dengan inventaris yang dipinjam.')">
                                Ketidaksesuaian Barang
                            </button>
                            <button type="button" class="quick-reason-btn" onclick="setReason('Jumlah yang dikembalikan tidak sesuai dengan jumlah yang diminta.')">
                                Ketidaksesuaian Jumlah
                            </button>
                            <button type="button" class="quick-reason-btn" onclick="setReason('Barang dalam kondisi yang tidak dapat diterima.')">
                                Masalah Kondisi
                            </button>
                            <button type="button" class="quick-reason-btn" onclick="setReason('Permintaan pengembalian tidak sesuai dengan kebijakan inventaris.')">
                                Pelanggaran Kebijakan
                            </button>
                        </div>
                    </div>

                    <!-- Alert Message -->
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle alert-icon"></i>
                        <div>
                            <strong>Catatan:</strong> Anggota staf akan diberitahu tentang penolakan dengan alasan yang diberikan. Stok tidak akan dipulihkan.
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menolak permintaan pengembalian ini?')">
                            <i class="bi bi-x-circle"></i> Konfirmasi Penolakan
                        </button>
                        <a href="{{ route('admin.returns.show', $return) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Items Preview -->
            <div class="sidebar-card">
                <h6>Barang yang Dikembalikan</h6>
                @foreach ($return->returnItems as $item)
                    <div class="item-entry">
                        <span style="color: var(--gray-500);">{{ $item->loanItem->item->code }}</span>
                        <strong>{{ $item->loanItem->item->name }}</strong>
                        <span>Jml: <strong>{{ $item->quantity }}</strong> | Kondisi: <span class="badge badge-{{ $item->loanItem->item->getConditionBadgeColor() }}">{{ $item->condition }}</span></span>
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
