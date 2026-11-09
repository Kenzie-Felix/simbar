@extends('layouts.app')

@section('title', 'Manage Items - SIMBAR')

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

    .items-table {
        border-collapse: collapse;
        width: 100%;
    }

    .items-table thead th {
        padding: var(--spacing-md) var(--spacing-lg);
        text-align: left;
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-700);
        background-color: var(--gray-50);
        border-bottom: 1px solid var(--gray-200);
    }

    .items-table tbody td {
        padding: var(--spacing-md) var(--spacing-lg);
        border-bottom: 1px solid var(--gray-200);
        font-size: var(--font-size-sm);
    }

    .item-thumbnail {
        width: 40px;
        height: 40px;
        border-radius: var(--radius-md);
        object-fit: cover;
        background: var(--gray-100);
        border: 1px solid var(--gray-200);
    }

    .items-table tbody tr:hover {
        background-color: var(--gray-50);
    }

    .items-table tbody tr:last-child td {
        border-bottom: none;
    }

    .action-buttons {
        display: flex;
        gap: var(--spacing-xs);
    }

    .action-buttons form {
        margin: 0;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
            gap: var(--spacing-lg);
        }

        .page-header-content {
            width: 100%;
        }

        .items-table thead th,
        .items-table tbody td {
            padding: var(--spacing-sm) var(--spacing-md);
            font-size: var(--font-size-xs);
        }

        .action-buttons {
            flex-wrap: wrap;
        }

        /* Convert table to cards on mobile */
        .items-table thead {
            display: none;
        }

        .items-table tbody,
        .items-table tr,
        .items-table td {
            display: block;
            width: 100%;
        }

        .items-table tr {
            margin-bottom: var(--spacing-lg);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .items-table td {
            padding: var(--spacing-md) !important;
            padding-left: 35% !important;
            position: relative;
            border: none;
            border-bottom: 1px solid var(--gray-100);
            font-size: var(--font-size-sm);
        }

        .items-table td:last-child {
            border-bottom: none;
            text-align: left !important;
        }

        .items-table td:before {
            content: attr(data-label);
            position: absolute;
            left: var(--spacing-md);
            top: var(--spacing-md);
            font-weight: var(--font-weight-semibold);
            font-size: var(--font-size-xs);
            color: var(--gray-600);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .item-thumbnail {
            display: none;
        }

        .action-buttons {
            display: flex;
            gap: var(--spacing-xs);
        }

        .action-buttons form {
            margin: 0;
        }
    }
</style>

<div style="max-width: 1400px; margin: 0 auto;">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1>
                <i class="bi bi-boxes"></i>
                Items
            </h1>
            <p>Manage inventory items</p>
        </div>
        <a href="{{ route('admin.items.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Item
        </a>
    </div>

    <!-- Alerts -->
    @if (session('success'))
        <div class="alert alert-success">
            <i class="bi bi-check-circle alert-icon"></i>
            <div>{{ session('success') }}</div>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger">
            <i class="bi bi-exclamation-circle alert-icon"></i>
            <div>{{ session('error') }}</div>
        </div>
    @endif

    <!-- Items Table Card -->
    <div class="card">
        <div class="card-body">
            @if ($items->count() > 0)
                <div style="overflow-x: auto;">
                    <table class="items-table" style="min-width: 1000px;">
                        <thead>
                            <tr>
                                <th style="width: 60px;"></th>
                                <th data-label="">Code</th>
                                <th data-label="">Name</th>
                                <th data-label="">Category</th>
                                <th data-label="">Location</th>
                                <th style="text-align: center;" data-label="">Total Stock</th>
                                <th style="text-align: center;" data-label="">Available</th>
                                <th data-label="">Condition</th>
                                <th style="text-align: center;" data-label="">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($items as $item)
                                <tr>
                                    <td data-label="">
                                        <img src="{{ $item->getImageUrl() }}" alt="{{ $item->name }}" class="item-thumbnail">
                                    </td>
                                    <td data-label="Code">
                                        <small style="color: var(--gray-500);">{{ $item->code }}</small>
                                    </td>
                                    <td data-label="Name">
                                        <strong>{{ $item->name }}</strong>
                                    </td>
                                    <td data-label="Category">
                                        <span class="badge badge-secondary">{{ $item->category->name }}</span>
                                    </td>
                                    <td data-label="Location">
                                        <small style="color: var(--gray-500);">{{ $item->location->name }}</small>
                                    </td>
                                    <td style="text-align: center;" data-label="Total">
                                        <span style="font-weight: var(--font-weight-semibold);">{{ $item->total_stock }}</span>
                                    </td>
                                    <td style="text-align: center;" data-label="Available">
                                        <span style="font-weight: var(--font-weight-bold); color: var(--primary);">{{ $item->available_stock }}</span>
                                    </td>
                                    <td data-label="Condition">
                                        <span class="badge badge-{{ $item->getConditionBadgeColor() }}">
                                            {{ $item->condition }}
                                        </span>
                                    </td>
                                    <td style="text-align: center;" data-label="Action">
                                        <div class="action-buttons">
                                            <a href="{{ route('admin.items.show', $item) }}" 
                                               class="btn btn-sm btn-outline-primary" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.items.edit', $item) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.items.destroy', $item) }}" 
                                                  onsubmit="return confirm('Are you sure you want to delete this item?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if ($items->hasPages())
                    <div style="display: flex; justify-content: center; margin-top: var(--spacing-xl);">
                        {{ $items->links() }}
                    </div>
                @endif
            @else
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="bi bi-inbox"></i>
                    </div>
                    <div class="empty-state-text">No items found</div>
                    <p style="color: var(--gray-500); margin-bottom: var(--spacing-lg);">Create an item to start managing your inventory.</p>
                    <a href="{{ route('admin.items.create') }}" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Create First Item
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
