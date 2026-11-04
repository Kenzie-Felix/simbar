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
        }

        .form-buttons .btn {
            width: 100%;
        }
    }
</style>

@if ($errors->any())
    <div class="alert alert-danger">
        <i class="bi bi-exclamation-circle alert-icon"></i>
        <div>
            <strong>Validation Error</strong>
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
        <label for="code" class="form-label">Item Code <span class="required">*</span></label>
        <input 
            type="text" 
            class="form-control @error('code') is-invalid @enderror" 
            id="code" 
            name="code" 
            value="{{ old('code', $item->code ?? '') }}" 
            placeholder="e.g., LT-001, MONITOR-05"
            maxlength="50"
            required
        >
        @error('code')
            <div class="error-message">{{ $message }}</div>
        @enderror
        <div class="form-help">Unique identifier for this item</div>
    </div>

    <div class="form-group">
        <label for="name" class="form-label">Item Name <span class="required">*</span></label>
        <input 
            type="text" 
            class="form-control @error('name') is-invalid @enderror" 
            id="name" 
            name="name" 
            value="{{ old('name', $item->name ?? '') }}" 
            placeholder="e.g., Dell Laptop, Office Chair"
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
        <label for="category_id" class="form-label">Category <span class="required">*</span></label>
        <select class="form-select @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
            <option value="">-- Select Category --</option>
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
        <label for="location_id" class="form-label">Location <span class="required">*</span></label>
        <select class="form-select @error('location_id') is-invalid @enderror" id="location_id" name="location_id" required>
            <option value="">-- Select Location --</option>
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
        <label for="total_stock" class="form-label">Total Stock <span class="required">*</span></label>
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
        <label for="condition" class="form-label">Condition <span class="required">*</span></label>
        <select class="form-select @error('condition') is-invalid @enderror" id="condition" name="condition" required>
            <option value="">-- Select Condition --</option>
            <option value="Baik" @if (old('condition', $item->condition ?? null) == 'Baik') selected @endif>Baik (Good)</option>
            <option value="Rusak Ringan" @if (old('condition', $item->condition ?? null) == 'Rusak Ringan') selected @endif>Rusak Ringan (Slightly Damaged)</option>
            <option value="Rusak Berat" @if (old('condition', $item->condition ?? null) == 'Rusak Berat') selected @endif>Rusak Berat (Severely Damaged)</option>
            <option value="Hilang" @if (old('condition', $item->condition ?? null) == 'Hilang') selected @endif>Hilang (Lost)</option>
        </select>
        @error('condition')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>
</div>

<!-- Description -->
<div class="form-group">
    <label for="description" class="form-label">Description</label>
    <textarea 
        class="form-control @error('description') is-invalid @enderror" 
        id="description" 
        name="description" 
        rows="4"
        placeholder="Additional details about this item..."
    >{{ old('description', $item->description ?? '') }}</textarea>
    @error('description')
        <div class="error-message">{{ $message }}</div>
    @enderror
</div>

<!-- Buttons -->
<div class="form-buttons">
    <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-circle"></i> {{ isset($item) ? 'Update' : 'Create' }} Item
    </button>
    <a href="{{ route('admin.items.index') }}" class="btn btn-secondary">Cancel</a>
</div>
