<style>
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

    .form-control.is-invalid {
        border-color: var(--danger);
    }

    .form-control.is-invalid:focus {
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
        .form-group {
            margin-bottom: var(--spacing-md);
        }

        .form-control {
            font-size: 16px;
            padding: var(--spacing-sm) var(--spacing-md);
        }

        .form-buttons {
            flex-direction: column-reverse;
            gap: var(--spacing-sm);
        }

        .form-buttons .btn {
            width: 100%;
        }

        .form-label {
            margin-bottom: var(--spacing-xs);
            font-size: var(--font-size-xs);
        }
    }

    @media (max-width: 480px) {
        .form-control,
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

        .error-message {
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

<div class="form-group">
    <label for="name" class="form-label">
        Nama Kategori <span class="required">*</span>
    </label>
    <input 
        type="text" 
        class="form-control @error('name') is-invalid @enderror" 
        id="name" 
        name="name" 
        value="{{ old('name', $category->name ?? '') }}" 
        placeholder="mis., Elektronik, Furnitur, Perlengkapan Kantor"
        maxlength="100"
        required
    >
    @error('name')
        <div class="error-message">{{ $message }}</div>
    @enderror
    <div class="form-help">Maksimal 100 karakter</div>
</div>

<div class="form-group">
    <label for="description" class="form-label">Deskripsi</label>
    <textarea 
        class="form-control @error('description') is-invalid @enderror" 
        id="description" 
        name="description" 
        rows="4"
        placeholder="Deskripsi opsional untuk kategori ini"
        maxlength="500"
    >{{ old('description', $category->description ?? '') }}</textarea>
    @error('description')
        <div class="error-message">{{ $message }}</div>
    @enderror
    <div class="form-help">Maksimal 500 karakter</div>
</div>

<div class="form-buttons">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-circle"></i> {{ isset($category) ? 'Perbarui' : 'Buat' }} Kategori
    </button>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">Batal</a>
</div>
