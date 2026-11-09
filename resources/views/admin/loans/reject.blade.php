@extends('layouts.app')

@section('title', 'Tolak Permintaan Peminjaman - SIMBAR')

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
                Tolak Permintaan Peminjaman
            </h1>
            <p>Berikan alasan untuk menolak permintaan peminjaman ini</p>
        </div>
        <a href="{{ route('admin.loans.show', $loan) }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Main Content -->
    <div class="form-container">
        <!-- Form Section -->
        <div>
            <!-- Request Summary -->
            <div class="summary-card">
                <h5>Permintaan Peminjaman #{{ $loan->id }}</h5>
                <div class="summary-item">
                    <div class="summary-label">Anggota Staf</div>
                    <div class="summary-value">{{ $loan->user->name }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Tanggal Diminta</div>
                    <div class="summary-value">{{ $loan->requested_at->format('d M Y H:i') }}</div>
                </div>
                <div class="summary-item">
                    <div class="summary-label">Barang yang Diminta</div>
                    <div class="summary-value">{{ $loan->getTotalItemsCount() }} barang ({{ $loan->getTotalQuantity() }} unit)</div>
                </div>
            </div>

            <!-- Rejection Form -->
            <div class="form-card">
                <h5 style="font-size: var(--font-size-base); font-weight: var(--font-weight-semibold); color: var(--gray-900); margin-bottom: var(--spacing-lg); padding-bottom: var(--spacing-lg); border-bottom: 1px solid var(--gray-200);">
                    <i class="bi bi-chat-left-text"></i> Alasan untuk Penolakan
                </h5>

                <form action="{{ route('admin.loans.reject', $loan) }}" method="POST">
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
                            placeholder="Jelaskan mengapa Anda menolak pinjaman ini..."
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
                            <button type="button" class="quick-reason-btn" onclick="setReason('Stok tidak cukup saat ini. Silakan kirim kembali ketika stok tersedia.')">
                                Stok Tidak Cukup
                            </button>
                            <button type="button" class="quick-reason-btn" onclick="setReason('Kondisi barang tidak sesuai untuk dipinjam.')">
                                Masalah Kondisi Barang
                            </button>
                            <button type="button" class="quick-reason-btn" onclick="setReason('Anggota staf memiliki barang kembalian yang harus diverifikasi terlebih dahulu.')">
                                Pengembalian Tertunda
                            </button>
                            <button type="button" class="quick-reason-btn" onclick="setReason('Barang yang diminta telah dipesan untuk tujuan lain.')">
                                Barang Dipesan
                            </button>
                            <button type="button" class="quick-reason-btn" onclick="setReason('Tujuan peminjaman tidak sesuai dengan kebijakan inventaris.')">
                                Pelanggaran Kebijakan
                            </button>
                        </div>
                    </div>

                    <!-- Alert Message -->
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle alert-icon"></i>
                        <div>
                            <strong>Catatan:</strong> Anggota staf akan diberitahu tentang penolakan dengan alasan yang diberikan. Stok tidak akan terpengaruh.
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Apakah Anda yakin ingin menolak permintaan peminjaman ini? Anggota staf akan diberitahu.')">
                            <i class="bi bi-x-circle"></i> Konfirmasi Penolakan
                        </button>
                        <a href="{{ route('admin.loans.show', $loan) }}" class="btn btn-secondary">
                            <i class="bi bi-arrow-left"></i> Batal
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Rejection Guidelines -->
            <div class="sidebar-card">
                <h6>Panduan Penolakan</h6>
                <ul>
                    <li><strong>Jelaskan:</strong> Jelaskan mengapa permintaan tidak dapat disetujui.</li>
                    <li><strong>Membantu:</strong> Sarankan tindakan (misalnya, "kirim kembali ketika stok tersedia").</li>
                    <li><strong>Profesional:</strong> Gunakan bahasa yang sopan.</li>
                    <li><strong>Beri Tahu Staf:</strong> Alasan akan terlihat oleh anggota staf.</li>
                    <li><strong>Tidak Ada Dampak Stok:</strong> Penolakan tidak mempengaruhi inventaris.</li>
                </ul>
            </div>

            <!-- Items in Request -->
            <div class="sidebar-card">
                <h6>Barang dalam Permintaan</h6>
                @foreach ($loan->loanItems as $item)
                    <div style="margin-bottom: var(--spacing-md); font-size: var(--font-size-xs); color: var(--gray-600);">
                        <strong style="color: var(--gray-900); display: block;">{{ $item->item->name }}</strong>
                        <span style="color: var(--gray-500);">{{ $item->item->code }}</span><br>
                        <span>Jml: <strong style="color: var(--gray-900);">{{ $item->quantity }}</strong> | Tersedia: <strong style="color: var(--primary);">{{ $item->item->available_stock }}</strong></span>
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
