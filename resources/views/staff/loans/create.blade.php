@extends('layouts.app')

@section('title', 'Request Loan - SIMBAR')

@section('content')
<style>
    .page-header {
        margin-bottom: var(--spacing-2xl);
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: var(--spacing-lg);
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
        margin: 0;
    }

    .form-section {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        margin-bottom: var(--spacing-lg);
    }

    .form-section-title {
        font-size: var(--font-size-lg);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-900);
        margin: 0 0 var(--spacing-lg) 0;
        display: flex;
        align-items: center;
        gap: var(--spacing-md);
    }

    .item-row {
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        margin-bottom: var(--spacing-lg);
    }

    .alert-stock {
        padding: var(--spacing-md);
        border-radius: var(--radius-sm);
        font-size: var(--font-size-sm);
        display: flex;
        align-items: center;
        gap: var(--spacing-md);
    }

    .alert-success {
        background-color: var(--success-light);
        color: var(--success);
        border: 1px solid var(--success);
    }

    .alert-warning {
        background-color: var(--warning-light);
        color: var(--warning);
        border: 1px solid var(--warning);
    }

    .summary-box {
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        margin-bottom: var(--spacing-lg);
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: var(--spacing-md);
    }

    .summary-row:last-child {
        margin-bottom: 0;
    }

    .summary-label {
        font-size: var(--font-size-sm);
        color: var(--gray-600);
    }

    .summary-value {
        font-size: var(--font-size-lg);
        font-weight: var(--font-weight-bold);
        color: var(--gray-900);
    }
</style>

<div style="max-width: 1200px; margin: 0 auto; padding: 0 var(--spacing-lg);">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <div class="page-title">
                <i class="bi bi-plus-circle"></i>
                Request Loan
            </div>
            <p class="page-subtitle">Submit a new loan request for available items</p>
        </div>
    </div>

    <form action="{{ route('staff.loans.store') }}" method="POST" id="loanForm">
        @csrf

        <!-- Error Messages -->
        @if ($errors->any())
            <div style="background-color: var(--danger-light); color: var(--danger); border: 1px solid var(--danger); border-left: 4px solid var(--danger); border-radius: var(--radius-lg); padding: var(--spacing-lg); margin-bottom: var(--spacing-lg);">
                <i class="bi bi-exclamation-triangle"></i> <strong>Validation Error</strong>
                <ul style="margin: var(--spacing-md) 0 0 var(--spacing-lg); padding-left: var(--spacing-lg);">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Items Section -->
        <div class="form-section">
            <h3 class="form-section-title">
                <i class="bi bi-collection"></i> Items to Borrow
            </h3>
            <div id="itemsContainer">
                <!-- Item rows will be added here -->
            </div>

            <button type="button" class="btn btn-outline-primary" id="addItemBtn">
                <i class="bi bi-plus"></i> Add Item
            </button>

            @if ($errors->has('items'))
                <div style="color: var(--danger); margin-top: var(--spacing-md);">
                    {{ $errors->first('items') }}
                </div>
            @endif
        </div>

        <!-- Loan Details Section -->
        <div class="form-section">
            <h3 class="form-section-title">
                <i class="bi bi-info-circle"></i> Loan Details
            </h3>

            <!-- Purpose -->
            <div style="margin-bottom: var(--spacing-lg);">
                <label for="purpose" class="form-label" style="font-weight: var(--font-weight-medium); color: var(--gray-900);">Purpose of Loan <span style="color: var(--danger);">*</span></label>
                <textarea 
                    class="form-control @error('purpose') is-invalid @enderror" 
                    id="purpose" 
                    name="purpose" 
                    rows="3" 
                    placeholder="Describe the purpose of this loan..."
                    maxlength="500"
                    style="border-color: {{ $errors->has('purpose') ? 'var(--danger)' : 'var(--gray-300)' }};"
                >{{ old('purpose') }}</textarea>
                <small style="color: var(--gray-500); display: block; margin-top: var(--spacing-xs);">Maximum 500 characters</small>
                @error('purpose')
                    <div style="color: var(--danger); margin-top: var(--spacing-xs);">{{ $message }}</div>
                @enderror
            </div>

            <!-- Planned Return Date -->
            <div>
                <label for="planned_return_date" class="form-label" style="font-weight: var(--font-weight-medium); color: var(--gray-900);">Expected Return Date <span style="color: var(--danger);">*</span></label>
                <input 
                    type="date" 
                    class="form-control @error('planned_return_date') is-invalid @enderror" 
                    id="planned_return_date" 
                    name="planned_return_date" 
                    value="{{ old('planned_return_date') }}"
                    min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                    style="border-color: {{ $errors->has('planned_return_date') ? 'var(--danger)' : 'var(--gray-300)' }};"
                >
                @error('planned_return_date')
                    <div style="color: var(--danger); margin-top: var(--spacing-xs);">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <!-- Summary Section -->
        <div class="summary-box">
            <div class="summary-row">
                <span class="summary-label">Total Items:</span>
                <span class="summary-value"><span id="totalItems">0</span> items</span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Total Quantity:</span>
                <span class="summary-value"><span id="totalQuantity">0</span> units</span>
            </div>
            <p style="color: var(--gray-500); font-size: var(--font-size-sm); margin: var(--spacing-md) 0 0 0;">
                Review your loan request details above before submitting
            </p>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; gap: var(--spacing-md); margin-bottom: var(--spacing-lg);">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-send"></i> Submit Loan Request
            </button>
            <a href="{{ route('staff.loans.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left"></i> Cancel
            </a>
        </div>
    </form>
</div>

<!-- Item Row Template -->
<template id="itemRowTemplate">
    <div class="item-row">
        <div style="display: grid; grid-template-columns: 1fr 200px 120px; gap: var(--spacing-md); margin-bottom: var(--spacing-md);">
            <div>
                <label class="form-label" style="font-size: var(--font-size-sm); font-weight: var(--font-weight-medium);">Item <span style="color: var(--danger);">*</span></label>
                <select class="form-select item-select" name="items[INDEX][item_id]" required>
                    <option value="">-- Select Item --</option>
                    @foreach ($items as $item)
                        <option value="{{ $item->id }}" data-stock="{{ $item->available_stock }}">
                            {{ $item->code }} - {{ $item->name }} ({{ $item->available_stock }} available)
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size: var(--font-size-sm); font-weight: var(--font-weight-medium);">Quantity <span style="color: var(--danger);">*</span></label>
                <input 
                    type="number" 
                    class="form-control quantity-input" 
                    name="items[INDEX][quantity]" 
                    value="1" 
                    min="1" 
                    max="999"
                    required
                >
            </div>

            <div style="display: flex; align-items: flex-end;">
                <button type="button" class="btn btn-outline-danger remove-item-btn" style="width: 100%; white-space: nowrap;">
                    <i class="bi bi-trash"></i> Remove
                </button>
            </div>
        </div>

        <div class="alert-stock" style="display: none; margin-bottom: 0;">
            <!-- Stock availability message will go here -->
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = 0;
    const template = document.getElementById('itemRowTemplate');
    const container = document.getElementById('itemsContainer');
    const addBtn = document.getElementById('addItemBtn');

    // ✅ Attach event listener to Add Item button
    addBtn.addEventListener('click', function(e) {
        e.preventDefault();
        addItemRow();
    });

    function addItemRow() {
        // Clone template properly
        const clone = template.content.cloneNode(true);
        
        // Create wrapper and get the item-row div
        const wrapper = document.createElement('div');
        wrapper.appendChild(clone);
        const itemRow = wrapper.querySelector('.item-row');
        
        // Replace INDEX placeholders with actual index
        const selects = itemRow.querySelectorAll('select');
        const inputs = itemRow.querySelectorAll('input[type="number"]');
        
        selects.forEach(select => {
            select.name = select.name.replace('INDEX', itemIndex);
        });
        
        inputs.forEach(input => {
            input.name = input.name.replace('INDEX', itemIndex);
        });
        
        // Append to container
        container.appendChild(itemRow);

        // Get references to the new row's elements
        const itemSelect = itemRow.querySelector('.item-select');
        const quantityInput = itemRow.querySelector('.quantity-input');
        const removeBtn = itemRow.querySelector('.remove-item-btn');

        // Add event listeners
        itemSelect.addEventListener('change', function() {
            checkAvailability(itemSelect, quantityInput);
            updateSummary();
        });

        quantityInput.addEventListener('change', function() {
            checkAvailability(itemSelect, quantityInput);
            updateSummary();
        });

        quantityInput.addEventListener('input', function() {
            checkAvailability(itemSelect, quantityInput);
        });

        removeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            itemRow.remove();
            updateSummary();
        });

        itemIndex++;
        updateSummary();
    }

    function checkAvailability(itemSelect, quantityInput) {
        const itemRow = itemSelect.closest('.item-row');
        const alertBox = itemRow.querySelector('.alert-stock');
        
        if (!itemSelect.value) {
            alertBox.style.display = 'none';
            return;
        }

        const selectedOption = itemSelect.options[itemSelect.selectedIndex];
        const availableStock = parseInt(selectedOption.dataset.stock) || 0;
        const requestedQuantity = parseInt(quantityInput.value) || 1;

        if (requestedQuantity > availableStock) {
            alertBox.classList.remove('alert-success');
            alertBox.classList.add('alert-warning');
            alertBox.innerHTML = `<i class="bi bi-exclamation-triangle"></i><span>Only ${availableStock} units available</span>`;
            alertBox.style.display = 'flex';
            quantityInput.classList.add('is-invalid');
        } else {
            alertBox.classList.remove('alert-warning');
            alertBox.classList.add('alert-success');
            alertBox.innerHTML = `<i class="bi bi-check-circle"></i><span>${availableStock} units available</span>`;
            alertBox.style.display = 'flex';
            quantityInput.classList.remove('is-invalid');
        }
    }

    function updateSummary() {
        let totalItems = 0;
        let totalQuantity = 0;

        document.querySelectorAll('.item-row').forEach(row => {
            const itemSelect = row.querySelector('.item-select');
            const quantityInput = row.querySelector('.quantity-input');
            
            if (itemSelect.value) {
                totalItems++;
                totalQuantity += parseInt(quantityInput.value) || 0;
            }
        });

        document.getElementById('totalItems').textContent = totalItems;
        document.getElementById('totalQuantity').textContent = totalQuantity;
    }

    // ✅ Remove automatic initial row - let users click "Add Item"
});
</script>
@endsection
