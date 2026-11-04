@extends('layouts.app')

@section('title', 'Browse Items - SIMBAR')

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

    .items-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: var(--spacing-lg);
    }

    .item-card {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        overflow: hidden;
        transition: all 0.2s ease;
        display: flex;
        flex-direction: column;
    }

    .item-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-2px);
    }

    .item-card-header {
        padding: var(--spacing-lg);
        border-bottom: 1px solid var(--gray-200);
    }

    .item-code {
        font-size: var(--font-size-sm);
        color: var(--gray-500);
        margin-bottom: var(--spacing-xs);
    }

    .item-name {
        font-size: var(--font-size-base);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-900);
    }

    .item-meta {
        font-size: var(--font-size-sm);
        color: var(--gray-600);
        margin-top: var(--spacing-sm);
    }

    .item-body {
        padding: var(--spacing-lg);
        flex: 1;
        display: flex;
        flex-direction: column;
    }

    .stock-status {
        text-align: center;
        padding: var(--spacing-md);
        background: var(--gray-50);
        border-radius: var(--radius-md);
        margin-bottom: var(--spacing-lg);
    }

    .stock-label {
        font-size: var(--font-size-xs);
        color: var(--gray-500);
        margin-bottom: var(--spacing-xs);
    }

    .stock-value {
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
        color: var(--success);
    }

    .item-action {
        margin-top: auto;
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
        }

        .items-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 0 var(--spacing-lg);">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1>
                <i class="bi bi-search"></i>
                Browse Items
            </h1>
            <p>Search and view items available for borrowing</p>
        </div>
    </div>

    <!-- Search and Filter Card -->
    <div class="card" style="margin-bottom: var(--spacing-lg);">
        <div class="card-body">
            <form method="GET" action="{{ route('staff.items.index') }}" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-md); align-items: end;">
                <div>
                    <input 
                        type="text" 
                        class="form-control" 
                        name="search" 
                        placeholder="Search by name or code..." 
                        value="{{ request('search') }}"
                    >
                </div>

                <div>
                    <select class="form-select" name="category_id">
                        <option value="">-- All Categories --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @if (request('category_id') == $category->id) selected @endif>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Search
                </button>

                @if (request('search') || request('category_id'))
                    <a href="{{ route('staff.items.index') }}" class="btn btn-outline-secondary">
                        <i class="bi bi-arrow-clockwise"></i> Clear
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Items Grid -->
    @if ($items->count() > 0)
        <div class="items-grid">
            @foreach ($items as $item)
                <div class="item-card" style="border-left: 4px solid {{ $item->available_stock > 0 ? 'var(--success)' : 'var(--danger)' }};">
                    <div class="item-card-header">
                        <div class="item-code">{{ $item->code }}</div>
                        <div class="item-name">{{ $item->name }}</div>
                        <div class="item-meta">
                            {{ $item->category->name }} • {{ $item->location->name }}
                        </div>
                    </div>

                    <div class="item-body">
                        <div class="stock-status">
                            <div class="stock-label">Available Stock</div>
                            <div class="stock-value">{{ $item->available_stock }}</div>
                        </div>

                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--spacing-lg);">
                            <span class="badge badge-{{ $item->getConditionBadgeColor() }}">
                                {{ $item->condition }}
                            </span>
                            @if ($item->available_stock > 0)
                                <span class="badge badge-success">In Stock</span>
                            @else
                                <span class="badge badge-danger">Out of Stock</span>
                            @endif
                        </div>

                        <a href="{{ route('staff.items.show', $item) }}" class="btn btn-primary item-action">
                            <i class="bi bi-eye"></i> View Details
                        </a>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if ($items->hasPages())
            <div style="display: flex; justify-content: center; margin-top: var(--spacing-xl);">
                {{ $items->links() }}
            </div>
        @endif
    @else
        <div class="card">
            <div class="empty-state" style="text-align: center; padding: var(--spacing-xl) var(--spacing-lg);">
                <div class="empty-state-icon" style="margin-bottom: var(--spacing-lg);">
                    <i class="bi bi-inbox" style="font-size: 3rem; color: var(--gray-400);"></i>
                </div>
                <p class="empty-state-text" style="font-size: var(--font-size-base); color: var(--gray-500); margin-bottom: var(--spacing-lg);">
                    No items found matching your search.
                </p>
                <a href="{{ route('staff.items.index') }}" class="btn btn-primary">
                    <i class="bi bi-arrow-clockwise"></i> View All Items
                </a>
            </div>
        </div>
    @endif
</div>

@endsection
