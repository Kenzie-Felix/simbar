@extends('layouts.app')

@section('title', 'Laporan Peminjaman - SIMBAR')

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
                Laporan Peminjaman
            </h1>
            <p>Lihat dan analisis transaksi peminjaman</p>
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
                    <label for="date_from" class="form-label" style="font-size: var(--font-size-sm); color: var(--gray-700);">Dari Tanggal</label>
                    <input type="date" class="form-control" id="date_from" name="date_from" value="{{ request('date_from') }}">
                </div>
                <div>
                    <label for="date_to" class="form-label" style="font-size: var(--font-size-sm); color: var(--gray-700);">Sampai Tanggal</label>
                    <input type="date" class="form-control" id="date_to" name="date_to" value="{{ request('date_to') }}">
                </div>
                <div>
                    <label for="status" class="form-label" style="font-size: var(--font-size-sm); color: var(--gray-700);">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">-- Semua Status --</option>
                        @foreach ($statuses as $key => $label)
                            <option value="{{ $key }}" {{ request('status') === $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label for="user_id" class="form-label" style="font-size: var(--font-size-sm); color: var(--gray-700);">Anggota Staf</label>
                    <input 
                        type="text" 
                        id="staffSearch" 
                        class="form-control" 
                        placeholder="Cari berdasarkan nama atau email..."
                        style="font-size: var(--font-size-sm);"
                    >
                    <input type="hidden" id="user_id" name="user_id" value="{{ request('user_id') }}">
                    <div id="staffSearchResults" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-md); max-height: 200px; overflow-y: auto; z-index: 1000; display: none; box-shadow: var(--shadow-md);">
                    </div>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary" style="width: 100%;">
                        <i class="bi bi-search"></i> Terapkan Saringan
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Results -->
    <div class="table-card">
        <div style="padding: var(--spacing-lg); background-color: var(--gray-50); border-bottom: 1px solid var(--gray-200);">
            <h5 style="margin: 0;">Data Peminjaman ({{ $loans->total() }} total)</h5>
        </div>
        @if ($loans->count() > 0)
            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>ID Peminjaman</th>
                            <th>Anggota Staf</th>
                            <th>Status</th>
                            <th>Barang</th>
                            <th>Qty</th>
                            <th>Diminta</th>
                            <th>Disetujui</th>
                            <th>Aksi</th>
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
                                    <a href="{{ route('admin.loans.show', $loan) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-eye"></i> Lihat
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div style="padding: var(--spacing-2xl); text-align: center; color: var(--gray-500);">
                Tidak ada data peminjaman ditemukan
            </div>
        @endif
    </div>

    <!-- Pagination -->
    <div style="display: flex; justify-content: center; margin-top: var(--spacing-2xl);">
        {{ $loans->links() }}
    </div>
</div>
@endsection

<script>
// Staff search autocomplete
let searchTimeout;
const staffSearch = document.getElementById('staffSearch');
const userIdInput = document.getElementById('user_id');
const staffSearchResults = document.getElementById('staffSearchResults');

// Populate search input with selected staff name if already selected
@if (request('user_id'))
    fetch('{{ route("admin.reports.api.staff-search") }}?q=' + encodeURIComponent(userIdInput.value))
        .then(response => response.json())
        .then(data => {
            if (data.results && data.results.length > 0) {
                const selected = data.results.find(r => r.id == {{ request('user_id') }});
                if (selected) {
                    staffSearch.value = selected.name;
                }
            }
        });
@endif

staffSearch.addEventListener('input', function() {
    const query = this.value;
    
    if (!query.trim()) {
        staffSearchResults.style.display = 'none';
        userIdInput.value = '';
        return;
    }
    
    // Debounce the search
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        fetch('{{ route("admin.reports.api.staff-search") }}?q=' + encodeURIComponent(query))
            .then(response => response.json())
            .then(data => {
                if (data.success && data.results.length > 0) {
                    staffSearchResults.innerHTML = data.results.map(result => 
                        `<div style="padding: var(--spacing-sm) var(--spacing-md); border-bottom: 1px solid var(--gray-200); cursor: pointer; transition: background 0.2s ease;" 
                              onmouseover="this.style.background='var(--gray-50)'" 
                              onmouseout="this.style.background='white'" 
                              onclick="selectStaff('${result.id}', '${result.name}', '${result.email}')">
                            <div style="font-weight: var(--font-weight-medium);">${result.name}</div>
                            <div style="font-size: var(--font-size-xs); color: var(--gray-500);">${result.email}</div>
                        </div>`
                    ).join('');
                    staffSearchResults.style.display = 'block';
                } else {
                    staffSearchResults.innerHTML = '<div style="padding: var(--spacing-md); color: var(--gray-500); text-align: center;">Tidak ada anggota staf ditemukan</div>';
                    staffSearchResults.style.display = 'block';
                }
            })
            .catch(error => console.error('Search error:', error));
    }, 300); // 300ms debounce
});

function selectStaff(id, name, email) {
    staffSearch.value = name;
    userIdInput.value = id;
    staffSearchResults.style.display = 'none';
}

// Close dropdown when clicking outside
document.addEventListener('click', function(event) {
    if (event.target !== staffSearch && !staffSearchResults.contains(event.target)) {
        staffSearchResults.style.display = 'none';
    }
});

// Clear staff selection when input is cleared
staffSearch.addEventListener('blur', function() {
    setTimeout(() => {
        if (!staffSearch.value.trim()) {
            userIdInput.value = '';
        }
    }, 100);
});
</script>
