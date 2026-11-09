@extends('layouts.app')

@section('title', 'Submit Return Request - SIMBAR')

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

    .summary-box {
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
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

    .content-grid {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: var(--spacing-lg);
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            align-items: flex-start;
            gap: var(--spacing-md);
            margin-bottom: var(--spacing-xl);
        }

        .page-title {
            font-size: var(--font-size-lg);
        }

        .page-subtitle {
            font-size: var(--font-size-xs);
        }

        .form-section {
            padding: var(--spacing-md);
            margin-bottom: var(--spacing-md);
        }

        .form-section-title {
            font-size: var(--font-size-base);
            margin-bottom: var(--spacing-md);
        }

        .item-row {
            padding: var(--spacing-md);
            margin-bottom: var(--spacing-md);
        }

        .item-row > div:first-child {
            grid-template-columns: 1fr !important;
            gap: var(--spacing-md) !important;
        }

        .summary-box {
            padding: var(--spacing-md);
            margin-bottom: var(--spacing-md);
        }

        .summary-row {
            margin-bottom: var(--spacing-sm);
        }

        .summary-label {
            font-size: var(--font-size-xs);
        }

        .summary-value {
            font-size: var(--font-size-base);
        }

        .form-control,
        .form-select {
            font-size: 16px;
            padding: var(--spacing-sm) var(--spacing-md);
        }

        .form-label {
            font-size: var(--font-size-xs);
        }

        table {
            font-size: var(--font-size-xs) !important;
        }

        table th,
        table td {
            padding: var(--spacing-sm) !important;
        }
    }

    @media (max-width: 480px) {
        .page-title {
            font-size: var(--font-size-base);
            gap: var(--spacing-sm);
        }

        .page-subtitle {
            font-size: var(--font-size-2xs);
        }

        .form-section {
            padding: var(--spacing-md);
        }

        .form-section-title {
            font-size: var(--font-size-sm);
        }

        .item-row {
            padding: var(--spacing-md);
        }

        .form-control,
        .form-select,
        textarea {
            font-size: 16px;
        }

        .btn {
            font-size: var(--font-size-xs);
            padding: var(--spacing-sm) var(--spacing-md);
        }

        .summary-box h5 {
            font-size: var(--font-size-sm);
        }

        table {
            font-size: var(--font-size-2xs) !important;
        }

        table th,
        table td {
            padding: var(--spacing-xs) !important;
        }
    }
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 0 var(--spacing-lg);">
    <!-- Page Header -->
    <div class="page-header">
        <div>
            <div class="page-title">
                <i class="bi bi-box-arrow-down"></i>
                Submit Return Request
            </div>
            <p class="page-subtitle">Return borrowed items to inventory</p>
        </div>
        <a href="{{ route('staff.returns.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back
        </a>
    </div>

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

    <form action="{{ route('staff.returns.store') }}" method="POST" id="returnForm">
        @csrf

        <div class="content-grid">
            <div>
                <!-- Loan Selection -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="bi bi-clipboard-check"></i> Select Loan to Return
                    </h3>

                <div>
                    <label for="loan_id" class="form-label" style="font-weight: var(--font-weight-medium); color: var(--gray-900);">Loan <span style="color: var(--danger);">*</span></label>
                    <select class="form-select @error('loan_id') is-invalid @enderror" id="loan_id" name="loan_id" required>
                        <option value="">-- Select a loan to return --</option>
                        @foreach ($loans as $loan)
                            <option value="{{ $loan->id }}" @if (old('loan_id') == $loan->id) selected @endif>
                                Loan #{{ $loan->id }} ({{ $loan->getTotalItemsCount() }} items, {{ $loan->getTotalQuantity() }} units) - Status: {{ $loan->status }}
                            </option>
                        @endforeach
                    </select>
                    @error('loan_id')
                        <div style="color: var(--danger); margin-top: var(--spacing-xs);">{{ $message }}</div>
                    @enderror
                </div>

                    <!-- Loan Items (Dynamically populated) -->
                    <div id="loanItemsContainer" style="display: none;">
                        <div style="overflow-x: auto;">
                            <table style="border-collapse: collapse; width: 100%; font-size: var(--font-size-sm);">
                                <thead>
                                    <tr style="background-color: var(--gray-50); border-bottom: 1px solid var(--gray-200);">
                                        <th style="padding: var(--spacing-md); text-align: left; font-weight: var(--font-weight-semibold);">Item</th>
                                        <th style="padding: var(--spacing-md); text-align: left; font-weight: var(--font-weight-semibold);">Borrowed Qty</th>
                                        <th style="padding: var(--spacing-md); text-align: left; font-weight: var(--font-weight-semibold);">Returned Qty</th>
                                        <th style="padding: var(--spacing-md); text-align: left; font-weight: var(--font-weight-semibold);">Remaining</th>
                                    </tr>
                                </thead>
                                <tbody id="loanItemsList">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Return Items Section -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="bi bi-collection"></i> Items to Return
                    </h3>
                    <div id="itemsContainer">
                        <!-- Return item rows will be added here -->
                    </div>

                    <button type="button" class="btn btn-outline-primary" id="addItemBtn">
                        <i class="bi bi-plus"></i> Add Item to Return
                    </button>

                    @if ($errors->has('items'))
                        <div style="color: var(--danger); margin-top: var(--spacing-md);">
                            {{ $errors->first('items') }}
                        </div>
                    @endif
                </div>

                <!-- Return Notes -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="bi bi-chat-left-text"></i> Return Notes
                    </h3>

                    <label for="return_notes" class="form-label" style="font-weight: var(--font-weight-medium); color: var(--gray-900);">Additional Notes (Optional)</label>
                    <textarea class="form-control" id="return_notes" name="return_notes" rows="3" placeholder="Any additional information about this return..." maxlength="1000" style="border-color: var(--gray-300);">{{ old('return_notes') }}</textarea>
                    <small style="color: var(--gray-500); display: block; margin-top: var(--spacing-xs);">Maximum 1000 characters</small>
                </div>

                <!-- Submit Button -->
                <div style="display: flex; gap: var(--spacing-md); margin-bottom: var(--spacing-lg);">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-send"></i> Submit Return Request
                    </button>
                    <a href="{{ route('staff.returns.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i> Cancel
                    </a>
                </div>
            </div>

            <!-- Summary Sidebar -->
            <div>
                <div class="summary-box">
                    <h5 style="margin: 0 0 var(--spacing-lg) 0; display: flex; align-items: center; gap: var(--spacing-md);">
                        <i class="bi bi-speedometer2"></i> Return Summary
                    </h5>

                    <div class="summary-row">
                        <span class="summary-label">Items to Return:</span>
                        <span class="summary-value"><span id="totalItems">0</span></span>
                    </div>

                    <div class="summary-row">
                        <span class="summary-label">Total Quantity:</span>
                        <span class="summary-value"><span id="totalQuantity">0</span> units</span>
                    </div>

                    <div style="padding-top: var(--spacing-lg); margin-top: var(--spacing-lg); border-top: 1px solid var(--gray-300);">
                        <div style="background-color: var(--info-light); color: var(--info); border: 1px solid var(--info); border-radius: var(--radius-sm); padding: var(--spacing-md);">
                            <i class="bi bi-info-circle"></i>
                            <strong style="display: block; margin-bottom: var(--spacing-xs);">Note:</strong>
                            <small>You can submit partial returns. Admin will verify items and update your inventory.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Item Row Template -->
<template id="itemRowTemplate">
    <div class="item-row">
        <div style="display: grid; grid-template-columns: 1fr 150px 150px 100px; gap: var(--spacing-md); margin-bottom: var(--spacing-md);">
            <div>
                <label class="form-label" style="font-size: var(--font-size-sm); font-weight: var(--font-weight-medium);">Item <span style="color: var(--danger);">*</span></label>
                <select class="form-select item-select" name="items[INDEX][loan_item_id]" required>
                    <option value="">-- Select Item --</option>
                </select>
            </div>

            <div>
                <label class="form-label" style="font-size: var(--font-size-sm); font-weight: var(--font-weight-medium);">Qty <span style="color: var(--danger);">*</span></label>
                <input type="number" class="form-control quantity-input" name="items[INDEX][quantity]" value="1" min="1" max="999" required>
            </div>

            <div>
                <label class="form-label" style="font-size: var(--font-size-sm); font-weight: var(--font-weight-medium);">Condition <span style="color: var(--danger);">*</span></label>
                <select class="form-select condition-select" name="items[INDEX][condition]" required>
                    <option value="">-- Select --</option>
                    <option value="Baik">Good</option>
                    <option value="Rusak Ringan">Minor Damage</option>
                    <option value="Rusak Berat">Damaged</option>
                    <option value="Hilang">Lost</option>
                </select>
            </div>

            <div style="display: flex; align-items: flex-end;">
                <button type="button" class="btn btn-outline-danger remove-item-btn" style="width: 100%; white-space: nowrap;">
                    <i class="bi bi-trash"></i> Remove
                </button>
            </div>
        </div>

        <div>
            <label class="form-label" style="font-size: var(--font-size-sm); font-weight: var(--font-weight-medium);">Notes (Optional)</label>
            <input type="text" class="form-control" name="items[INDEX][notes]" placeholder="Item condition notes..." maxlength="500">
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let itemIndex = 0;
    let loanItems = [];

    document.getElementById('loan_id').addEventListener('change', function() {
        const loanId = this.value;
        if (loanId) {
            loadLoanDetails(loanId);
        } else {
            document.getElementById('loanItemsContainer').style.display = 'none';
            document.getElementById('itemsContainer').innerHTML = '';
        }
    });

    document.getElementById('addItemBtn').addEventListener('click', function() {
        if (loanItems.length === 0) {
            alert('Please select a loan first.');
            return;
        }
        addItemRow();
    });

    function loadLoanDetails(loanId) {
        fetch(`{{ route('staff.returns.loan-details', ['loan' => ':loan']) }}`.replace(':loan', loanId))
            .then(response => response.json())
            .then(data => {
                loanItems = data.items;
                displayLoanItems(data.items);
                document.getElementById('loanItemsContainer').style.display = 'block';
                document.getElementById('itemsContainer').innerHTML = '';
                updateSummary();
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading loan details');
            });
    }

    function displayLoanItems(items) {
        const tbody = document.getElementById('loanItemsList');
        tbody.innerHTML = '';
        items.forEach(item => {
            const row = document.createElement('tr');
            row.style.borderBottom = '1px solid var(--gray-200)';
            row.innerHTML = `
                <td style="padding: var(--spacing-md);">${item.item_code} - ${item.item_name}</td>
                <td style="padding: var(--spacing-md);">${item.quantity}</td>
                <td style="padding: var(--spacing-md);">${item.returned_quantity}</td>
                <td style="padding: var(--spacing-md); font-weight: var(--font-weight-bold);">${item.remaining}</td>
            `;
            tbody.appendChild(row);
        });
    }

    function addItemRow() {
        const template = document.getElementById('itemRowTemplate');
        const container = document.getElementById('itemsContainer');
        const clone = template.content.cloneNode(true);
        
        // ✅ FIXED: Use proper DOM-based approach instead of string manipulation
        const wrapper = document.createElement('div');
        wrapper.appendChild(clone);
        const itemRow = wrapper.querySelector('.item-row');
        
        // Replace INDEX placeholders with actual index
        const selects = itemRow.querySelectorAll('select');
        const inputs = itemRow.querySelectorAll('input[type="number"], input[type="text"]');
        
        selects.forEach(select => {
            select.name = select.name.replace('INDEX', itemIndex);
        });
        
        inputs.forEach(input => {
            input.name = input.name.replace('INDEX', itemIndex);
        });
        
        // Append to container
        container.appendChild(itemRow);

        const itemSelect = itemRow.querySelector('.item-select');
        loanItems.forEach(item => {
            const option = document.createElement('option');
            option.value = item.id;
            option.textContent = `${item.item_code} - ${item.item_name} (Remaining: ${item.remaining})`;
            itemSelect.appendChild(option);
        });

        const quantityInput = itemRow.querySelector('.quantity-input');
        const removeBtn = itemRow.querySelector('.remove-item-btn');

        itemSelect.addEventListener('change', updateSummary);
        quantityInput.addEventListener('change', updateSummary);
        removeBtn.addEventListener('click', function(e) {
            e.preventDefault();
            itemRow.remove();
            updateSummary();
        });

        itemIndex++;
        updateSummary();
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
});
</script>
@endsection
