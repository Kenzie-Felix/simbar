@extends('layouts.app')

@section('title', 'Stock In - SIMBAR')

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
        grid-template-columns: 1fr 280px;
        gap: var(--spacing-xl);
        max-width: 1100px;
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

    .info-card {
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        margin-bottom: var(--spacing-lg);
    }

    .info-card h6 {
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-900);
        margin-bottom: var(--spacing-md);
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
    }

    .info-card ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .info-card li {
        font-size: var(--font-size-xs);
        color: var(--gray-600);
        padding: var(--spacing-sm) 0;
        border-bottom: 1px solid var(--gray-200);
    }

    .info-card li:last-child {
        border-bottom: none;
    }

    .action-card {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
    }

    .action-card h6 {
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-900);
        margin-bottom: var(--spacing-md);
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
    }

    .action-card .btn {
        width: 100%;
        margin-bottom: var(--spacing-sm);
    }

    .action-card .btn:last-child {
        margin-bottom: 0;
    }

    @media (max-width: 900px) {
        .form-container {
            grid-template-columns: 1fr;
        }

        .page-header {
            flex-direction: column;
        }

        .form-buttons {
            flex-direction: column-reverse;
        }

        .form-buttons .btn {
            width: 100%;
        }
    }
</style>

<div style="max-width: 1400px; margin: 0 auto;">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1>
                <i class="bi bi-arrow-down-square"></i>
                Stock In
            </h1>
            <p>Record incoming stock or transfers</p>
        </div>
    </div>

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle alert-icon"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    <!-- Form Layout -->
    <div class="form-container">
        <!-- Form -->
        <div class="form-card">
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

            <form method="POST" action="{{ route('admin.stock.in.store') }}">
                @csrf

                <!-- Item Selection -->
                <div class="form-group">
                    <label for="item_id" class="form-label">Select Item <span class="required">*</span></label>
                    <select class="form-select @error('item_id') is-invalid @enderror" id="item_id" name="item_id" required>
                        <option value="">-- Select Item --</option>
                        @foreach ($items as $item)
                            <option value="{{ $item->id }}" @if (old('item_id') == $item->id) selected @endif>
                                {{ $item->code }} - {{ $item->name }} (Available: {{ $item->available_stock }})
                            </option>
                        @endforeach
                    </select>
                    @error('item_id')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Quantity -->
                <div class="form-group">
                    <label for="quantity" class="form-label">Quantity <span class="required">*</span></label>
                    <input 
                        type="number" 
                        class="form-control @error('quantity') is-invalid @enderror" 
                        id="quantity" 
                        name="quantity" 
                        value="{{ old('quantity') }}" 
                        min="1"
                        required
                        placeholder="Enter quantity"
                    >
                    @error('quantity')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Reference Type -->
                <div class="form-group">
                    <label for="reference_type" class="form-label">Reference Type <span class="required">*</span></label>
                    <select class="form-select @error('reference_type') is-invalid @enderror" id="reference_type" name="reference_type" required>
                        <option value="">-- Select Type --</option>
                        <option value="Purchase" @if (old('reference_type') == 'Purchase') selected @endif>Purchase</option>
                        <option value="Transfer" @if (old('reference_type') == 'Transfer') selected @endif>Transfer from another location</option>
                        <option value="Return" @if (old('reference_type') == 'Return') selected @endif>Return from loan</option>
                        <option value="Other" @if (old('reference_type') == 'Other') selected @endif>Other</option>
                    </select>
                    @error('reference_type')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Notes -->
                <div class="form-group">
                    <label for="notes" class="form-label">Notes</label>
                    <textarea 
                        class="form-control @error('notes') is-invalid @enderror" 
                        id="notes" 
                        name="notes" 
                        rows="3"
                        placeholder="Optional notes..."
                        maxlength="500"
                    >{{ old('notes') }}</textarea>
                    @error('notes')
                        <div class="error-message">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Buttons -->
                <div class="form-buttons">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Record Stock In
                    </button>
                    <a href="{{ route('admin.items.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Information -->
            <div class="info-card">
                <h6>
                    <i class="bi bi-info-circle"></i> Stock In Info
                </h6>
                <ul>
                    <li>Use Stock In to increase inventory</li>
                    <li>Reference Type helps track the source</li>
                    <li>Stock increases immediately</li>
                    <li>A transaction record is created automatically</li>
                    <li>Both total and available stock are updated</li>
                </ul>
            </div>

            <!-- Other Actions -->
            <div class="action-card">
                <h6>
                    <i class="bi bi-arrow-left-right"></i> Other Actions
                </h6>
                <a href="{{ route('admin.stock.adjustment') }}" class="btn btn-outline-primary btn-sm">
                    <i class="bi bi-arrow-repeat"></i> Stock Adjustment
                </a>
                <a href="{{ route('admin.stock.history') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-clock-history"></i> View History
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
