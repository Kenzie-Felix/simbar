@extends('layouts.app')

@section('title', 'Stock Adjustment - SIMBAR')

@section('content')
<style>
    .page-header {
        margin-bottom: var(--spacing-2xl);
    }

    .page-title {
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
        color: var(--gray-900);
        margin-bottom: var(--spacing-sm);
        display: flex;
        align-items: center;
        gap: var(--spacing-md);
    }

    .page-subtitle {
        font-size: var(--font-size-sm);
        color: var(--gray-500);
    }

    .form-grid {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: var(--spacing-lg);
    }

    @media (max-width: 1024px) {
        .form-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 0 var(--spacing-lg);">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-title">
            <i class="bi bi-arrow-repeat"></i>
            Stock Adjustment
        </div>
        <p class="page-subtitle">Adjust stock for damage, loss, or corrections</p>
    </div>

    <!-- Alerts -->
    @if (session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle alert-icon"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle alert-icon"></i>
            <div>
                <strong>Error:</strong>
                <ul style="margin: var(--spacing-sm) 0 0 var(--spacing-lg); padding-left: var(--spacing-lg);">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endif

    <!-- Form Grid -->
    <div class="form-grid">
        <!-- Form -->
        <div>
            <form method="POST" action="{{ route('admin.stock.adjustment.store') }}">
                @csrf

                <!-- Item Selection -->
                <div class="card">
                    <div class="card-header">
                        <h5 style="margin: 0;">Select Item</h5>
                    </div>
                    <div class="card-body">
                        <div style="margin-bottom: var(--spacing-lg);">
                            <label style="display: block; margin-bottom: var(--spacing-sm); font-weight: var(--font-weight-medium); font-size: var(--font-size-sm);">Item <span class="text-danger">*</span></label>
                            <select class="form-select @error('item_id') is-invalid @enderror" id="item_id" name="item_id" required>
                                <option value="">-- Select Item --</option>
                                @foreach ($items as $item)
                                    <option value="{{ $item->id }}" @if (old('item_id') == $item->id) selected @endif>
                                        {{ $item->code }} - {{ $item->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('item_id')
                                <div style="color: var(--danger); font-size: var(--font-size-sm); margin-top: var(--spacing-xs);">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Adjustment Details -->
                <div class="card" style="margin-top: var(--spacing-lg);">
                    <div class="card-header">
                        <h5 style="margin: 0;">Adjustment Details</h5>
                    </div>
                    <div class="card-body">
                        <!-- Quantity -->
                        <div style="margin-bottom: var(--spacing-lg);">
                            <label style="display: block; margin-bottom: var(--spacing-sm); font-weight: var(--font-weight-medium); font-size: var(--font-size-sm);">Quantity Adjustment <span class="text-danger">*</span></label>
                            <input 
                                type="number" 
                                class="form-control @error('quantity') is-invalid @enderror" 
                                id="quantity" 
                                name="quantity" 
                                value="{{ old('quantity') }}" 
                                required
                                placeholder="Positive: increase | Negative: decrease"
                            >
                            <small style="color: var(--gray-500); display: block; margin-top: var(--spacing-xs);">
                                Use negative numbers to decrease stock (e.g., -5)
                            </small>
                            @error('quantity')
                                <div style="color: var(--danger); font-size: var(--font-size-sm); margin-top: var(--spacing-xs);">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Reason -->
                        <div style="margin-bottom: var(--spacing-lg);">
                            <label style="display: block; margin-bottom: var(--spacing-sm); font-weight: var(--font-weight-medium); font-size: var(--font-size-sm);">Reason <span class="text-danger">*</span></label>
                            <select class="form-select @error('reason') is-invalid @enderror" id="reason" name="reason" required>
                                <option value="">-- Select Reason --</option>
                                <option value="Damage" @if (old('reason') == 'Damage') selected @endif>Damaged in warehouse</option>
                                <option value="Loss" @if (old('reason') == 'Loss') selected @endif>Lost/Missing</option>
                                <option value="Expiry" @if (old('reason') == 'Expiry') selected @endif>Expired/Unusable</option>
                                <option value="Correction" @if (old('reason') == 'Correction') selected @endif>Inventory correction</option>
                                <option value="Other" @if (old('reason') == 'Other') selected @endif>Other</option>
                            </select>
                            @error('reason')
                                <div style="color: var(--danger); font-size: var(--font-size-sm); margin-top: var(--spacing-xs);">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Notes -->
                        <div>
                            <label style="display: block; margin-bottom: var(--spacing-sm); font-weight: var(--font-weight-medium); font-size: var(--font-size-sm);">Additional Notes</label>
                            <textarea 
                                class="form-control @error('notes') is-invalid @enderror" 
                                id="notes" 
                                name="notes" 
                                rows="3"
                                placeholder="Describe the adjustment..."
                                maxlength="500"
                                style="border: 1px solid var(--gray-300); border-radius: var(--radius-md); padding: var(--spacing-sm); font-size: var(--font-size-sm); font-family: var(--font-family);"
                            >{{ old('notes') }}</textarea>
                            @error('notes')
                                <div style="color: var(--danger); font-size: var(--font-size-sm); margin-top: var(--spacing-xs);">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div style="display: flex; gap: var(--spacing-md); margin-top: var(--spacing-lg);">
                    <button type="submit" class="btn btn-warning" style="flex: 1;">
                        <i class="bi bi-exclamation-triangle"></i> Record Adjustment
                    </button>
                    <a href="{{ route('admin.items.index') }}" class="btn btn-outline-secondary">
                        Cancel
                    </a>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Information Card -->
            <div class="card">
                <div class="card-header">
                    <h6 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                        <i class="bi bi-info-circle"></i> About Adjustments
                    </h6>
                </div>
                <div class="card-body">
                    <ul style="margin: 0; padding-left: var(--spacing-lg); font-size: var(--font-size-sm);">
                        <li style="margin-bottom: var(--spacing-xs);">For non-standard stock changes</li>
                        <li style="margin-bottom: var(--spacing-xs);">Include reason for audit trail</li>
                        <li style="margin-bottom: var(--spacing-xs);">Positive: add stock</li>
                        <li style="margin-bottom: var(--spacing-xs);">Negative: remove stock</li>
                        <li>Cannot result in negative stock</li>
                    </ul>
                </div>
            </div>

            <!-- Other Actions -->
            <div class="card" style="margin-top: var(--spacing-lg);">
                <div class="card-header">
                    <h6 style="margin: 0;">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.stock.in') }}" class="btn btn-sm btn-outline-primary" style="width: 100%; margin-bottom: var(--spacing-md); display: inline-flex; align-items: center; justify-content: center; gap: var(--spacing-sm);">
                        <i class="bi bi-arrow-down-square"></i> Stock In
                    </a>
                    <a href="{{ route('admin.stock.history') }}" class="btn btn-sm btn-outline-secondary" style="width: 100%; display: inline-flex; align-items: center; justify-content: center; gap: var(--spacing-sm);">
                        <i class="bi bi-clock-history"></i> View History
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
