<style>
    .form-group {
        margin-bottom: var(--spacing-lg);
    }

    .form-group-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--spacing-lg);
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

    .form-control,
    .form-select {
        width: 100%;
        padding: var(--spacing-sm) var(--spacing-md);
        border: 1px solid var(--gray-300);
        border-radius: var(--radius-md);
        font-size: var(--font-size-sm);
        transition: all 0.2s ease;
        font-family: var(--font-family);
    }

    .form-control:focus,
    .form-select:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px var(--primary-light);
    }

    .form-control.is-invalid,
    .form-select.is-invalid {
        border-color: var(--danger);
    }

    .form-control.is-invalid:focus,
    .form-select.is-invalid:focus {
        box-shadow: 0 0 0 3px var(--danger-light);
    }

    .form-help {
        font-size: var(--font-size-xs);
        color: var(--gray-500);
        margin-top: var(--spacing-xs);
    }

    .error-message {
        color: var(--danger);
        font-size: var(--font-size-xs);
        margin-top: var(--spacing-xs);
    }

    .form-buttons {
        display: flex;
        gap: var(--spacing-md);
        justify-content: flex-end;
        margin-top: var(--spacing-xl);
    }

    @media (max-width: 768px) {
        .form-group-row {
            grid-template-columns: 1fr;
            gap: var(--spacing-md);
        }

        .form-buttons {
            flex-direction: column-reverse;
            gap: var(--spacing-sm);
        }

        .form-buttons .btn {
            width: 100%;
        }

        .form-control,
        .form-select {
            font-size: 16px;
            padding: var(--spacing-sm) var(--spacing-md);
        }

        .form-group {
            margin-bottom: var(--spacing-md);
        }

        .form-label {
            margin-bottom: var(--spacing-xs);
            font-size: var(--font-size-xs);
        }
    }

    @media (max-width: 480px) {
        .form-control,
        .form-select,
        textarea {
            font-size: 16px;
            padding: var(--spacing-sm) var(--spacing-md);
        }

        .form-group {
            margin-bottom: var(--spacing-md);
        }

        .form-help {
            font-size: var(--font-size-xs);
        }
    }
</style>

@if ($errors->any())
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle alert-icon"></i>
        <div>
            <strong>Kesalahan Validasi</strong>
            <ul style="margin: var(--spacing-md) 0 0 0; padding-left: var(--spacing-lg);">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<!-- First Row -->
<div class="form-group-row">
    <div class="form-group">
        <label for="code" class="form-label">Kode Barang <span class="required">*</span></label>
        <input 
            type="text" 
            class="form-control @error('code') is-invalid @enderror" 
            id="code" 
            name="code" 
            value="{{ old('code', $item->code ?? '') }}" 
            placeholder="mis., LT-001, MONITOR-05"
            maxlength="50"
            required
        >
        @error('code')
            <div class="error-message">{{ $message }}</div>
        @enderror
        <div class="form-help">Identifikasi unik untuk barang ini</div>
    </div>

    <div class="form-group">
        <label for="name" class="form-label">Nama Barang <span class="required">*</span></label>
        <input 
            type="text" 
            class="form-control @error('name') is-invalid @enderror" 
            id="name" 
            name="name" 
            value="{{ old('name', $item->name ?? '') }}" 
            placeholder="mis., Laptop Dell, Kursi Kantor"
            maxlength="200"
            required
        >
        @error('name')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>
</div>

<!-- Second Row -->
<div class="form-group-row">
    <div class="form-group">
        <label for="category_id" class="form-label">Kategori <span class="required">*</span></label>
        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
            <option value="">-- Pilih Kategori --</option>
            @foreach ($categories as $category)
                <option value="{{ $category->id }}" 
                    @if (old('category_id', $item->category_id ?? null) == $category->id) selected @endif>
                    {{ $category->name }}
                </option>
            @endforeach
        </select>
        @error('category_id')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="location_id" class="form-label">Lokasi <span class="required">*</span></label>
        <select class="form-select @error('location_id') is-invalid @enderror" id="location_id" name="location_id" required>
            <option value="">-- Pilih Lokasi --</option>
            @foreach ($locations as $location)
                <option value="{{ $location->id }}" 
                    @if (old('location_id', $item->location_id ?? null) == $location->id) selected @endif>
                    {{ $location->name }}
                </option>
            @endforeach
        </select>
        @error('location_id')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>
</div>

<!-- Third Row -->
<div class="form-group-row">
    <div class="form-group">
        <label for="total_stock" class="form-label">Total Stok <span class="required">*</span></label>
        <input 
            type="number" 
            class="form-control @error('total_stock') is-invalid @enderror" 
            id="total_stock" 
            name="total_stock" 
            value="{{ old('total_stock', $item->total_stock ?? 0) }}" 
            min="0"
            required
        >
        @error('total_stock')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="condition" class="form-label">Kondisi <span class="required">*</span></label>
        <select class="form-select @error('condition') is-invalid @enderror" id="condition" name="condition" required>
            <option value="">-- Pilih Kondisi --</option>
            <option value="Baik" @if (old('condition', $item->condition ?? null) == 'Baik') selected @endif>Baik</option>
            <option value="Rusak Ringan" @if (old('condition', $item->condition ?? null) == 'Rusak Ringan') selected @endif>Rusak Ringan</option>
            <option value="Rusak Berat" @if (old('condition', $item->condition ?? null) == 'Rusak Berat') selected @endif>Rusak Berat</option>
            <option value="Hilang" @if (old('condition', $item->condition ?? null) == 'Hilang') selected @endif>Hilang</option>
        </select>
        @error('condition')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>
</div>

<!-- Description -->
<div class="form-group">
    <label for="description" class="form-label">Deskripsi</label>
    <textarea 
        class="form-control @error('description') is-invalid @enderror" 
        id="description" 
        name="description" 
        rows="4"
        placeholder="Detail tambahan tentang barang ini..."
    >{{ old('description', $item->description ?? '') }}</textarea>
    @error('description')
        <div class="error-message">{{ $message }}</div>
    @enderror
</div>

<!-- Image Upload Section -->
<div class="form-group">
    <label for="image" class="form-label">Gambar Barang</label>
    
    <!-- Image Preview Container -->
    <div id="imagePreviewContainer" style="margin-bottom: var(--spacing-md);">
        @if (isset($item) && $item->hasImage())
            <div id="currentImagePreview" style="position: relative; width: 100%; border-radius: var(--radius-md); overflow: hidden; background: var(--gray-100); display: flex; align-items: center; justify-content: center;">
                <img src="{{ $item->getImageUrl() }}" alt="Gambar saat ini" style="max-width: 100%; max-height: 300px; object-fit: contain; padding: var(--spacing-md);">
                <button type="button" id="removeCurrentImageBtn" class="btn btn-sm btn-outline-danger" onclick="removeCurrentImage()" style="position: absolute; top: var(--spacing-md); right: var(--spacing-md);">
                    <i class="bi bi-trash"></i> Hapus
                </button>
            </div>
            <input type="hidden" id="removeCurrentImage" name="remove_current_image" value="">
            <div class="form-help" style="margin-top: var(--spacing-md);">Gambar saat ini akan diganti jika Anda mengunggah gambar baru.</div>
        @else
            <div id="noImageMessage" class="form-help" style="color: var(--gray-400); text-align: center; padding: var(--spacing-lg) var(--spacing-md); background: var(--gray-50); border-radius: var(--radius-md); border: 1px dashed var(--gray-300);">
                <i class="bi bi-image" style="font-size: 1.5rem; display: block; margin-bottom: var(--spacing-sm);"></i>
                Belum ada gambar yang diunggah
            </div>
        @endif
    </div>

    <!-- File Input -->
    <div style="position: relative; margin-bottom: var(--spacing-md);">
        <input 
            type="file" 
            class="form-control @error('image') is-invalid @enderror" 
            id="image" 
            name="image" 
            accept="image/jpeg,image/jpg,image/png,image/webp"
            onchange="previewImage(event)"
        >
        @error('image')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-help">
        <i class="bi bi-info-circle"></i> Format yang didukung: JPG, JPEG, PNG, WEBP • Ukuran file maksimal: 5 MB
    </div>
</div>

<!-- JavaScript for Image Preview -->
<script>
    function previewImage(event) {
        const file = event.target.files[0];
        const container = document.getElementById('imagePreviewContainer');
        const noImageMessage = document.getElementById('noImageMessage');
        
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                let previewDiv = document.getElementById('newImagePreview');
                
                if (!previewDiv) {
                    previewDiv = document.createElement('div');
                    previewDiv.id = 'newImagePreview';
                    previewDiv.style.cssText = 'position: relative; width: 100%; border-radius: var(--radius-md); overflow: hidden; background: var(--gray-100); display: flex; align-items: center; justify-content: center; margin-bottom: var(--spacing-md);';
                    container.insertBefore(previewDiv, container.firstChild);
                }
                
                previewDiv.innerHTML = '<img src="' + e.target.result + '" alt="Preview" style="max-width: 100%; max-height: 300px; object-fit: contain; padding: var(--spacing-md);"><button type="button" class="btn btn-sm btn-outline-danger" onclick="clearImagePreview()" style="position: absolute; top: var(--spacing-md); right: var(--spacing-md);"><i class="bi bi-x"></i> Clear</button>';
                
                if (noImageMessage) {
                    noImageMessage.style.display = 'none';
                }
            };
            reader.readAsDataURL(file);
        }
    }

    function clearImagePreview() {
        document.getElementById('image').value = '';
        const previewDiv = document.getElementById('newImagePreview');
        if (previewDiv) {
            previewDiv.remove();
        }
        const noImageMessage = document.getElementById('noImageMessage');
        if (noImageMessage) {
            noImageMessage.style.display = 'block';
        }
    }

    function removeCurrentImage() {
        document.getElementById('removeCurrentImage').value = '1';
        const currentImagePreview = document.getElementById('currentImagePreview');
        if (currentImagePreview) {
            currentImagePreview.style.display = 'none';
        }
    }
</script>

<!-- Buttons -->
<div class="form-buttons">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-circle"></i> {{ isset($item) ? 'Perbarui' : 'Buat' }} Barang
    </button>
    <a href="{{ route('admin.items.index') }}" class="btn btn-secondary">Batal</a>
</div>
