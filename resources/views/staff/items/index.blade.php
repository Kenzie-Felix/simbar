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
        margin-bottom: var(--spacing-xl);
    }

    .item-card {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        overflow: hidden;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        height: 100%;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
    }

    .item-card:hover {
        box-shadow: 0 8px 16px rgba(0, 0, 0, 0.12);
        transform: translateY(-4px);
        border-color: var(--primary);
    }

    .item-image-wrapper {
        position: relative;
        width: 100%;
        height: 220px;
        background: linear-gradient(135deg, var(--gray-50) 0%, var(--gray-100) 100%);
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .item-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.3s ease;
    }

    .item-card:hover .item-image {
        transform: scale(1.05);
    }

    .item-badge {
        position: absolute;
        top: var(--spacing-md);
        right: var(--spacing-md);
        z-index: 10;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .item-card-header {
        padding: var(--spacing-lg);
        border-bottom: 1px solid var(--gray-100);
        flex: 0 0 auto;
    }

    .item-code {
        font-size: var(--font-size-xs);
        color: var(--gray-500);
        margin-bottom: var(--spacing-xs);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: var(--font-weight-semibold);
    }

    .item-name {
        font-size: var(--font-size-base);
        font-weight: var(--font-weight-bold);
        color: var(--gray-900);
        margin-bottom: var(--spacing-sm);
        line-height: 1.3;
    }

    .item-meta {
        font-size: var(--font-size-xs);
        color: var(--gray-500);
        margin: 0;
        display: flex;
        gap: var(--spacing-sm);
    }

    .item-meta-item {
        display: flex;
        align-items: center;
        gap: 4px;
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
        background: linear-gradient(135deg, var(--gray-50) 0%, white 100%);
        border-radius: var(--radius-md);
        margin-bottom: var(--spacing-lg);
        border: 1px solid var(--gray-100);
    }

    .stock-label {
        font-size: var(--font-size-xs);
        color: var(--gray-500);
        margin-bottom: var(--spacing-xs);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: var(--font-weight-semibold);
    }

    .stock-value {
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
        color: var(--primary);
    }

    .stock-value.unavailable {
        color: var(--danger);
    }

    .item-badges-row {
        display: flex;
        gap: var(--spacing-sm);
        margin-bottom: var(--spacing-lg);
        flex-wrap: wrap;
    }

    .item-action {
        margin-top: auto;
    }

    .item-action .btn {
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: var(--spacing-sm);
    }

    .filters-card {
        margin-bottom: var(--spacing-lg);
    }

    .filters-card .card-body {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: var(--spacing-md);
        align-items: end;
    }

    .search-bar {
        position: relative;
    }

    .search-bar input {
        padding-left: 40px !important;
        width: 100%;
    }

    .search-bar::before {
        content: '';
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        background: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg>') no-repeat center;
        background-size: contain;
        color: var(--gray-400);
        pointer-events: none;
    }

    .results-info {
        font-size: var(--font-size-sm);
        color: var(--gray-600);
        margin-bottom: var(--spacing-lg);
        padding-bottom: var(--spacing-md);
        border-bottom: 1px solid var(--gray-100);
    }

    @media (max-width: 768px) {
        .page-header {
            flex-direction: column;
        }

        .items-grid {
            grid-template-columns: 1fr;
        }

        .filters-card .card-body {
            grid-template-columns: 1fr;
        }

        .item-card {
            display: grid;
            grid-template-columns: 180px 1fr;
            height: auto;
        }

        .item-image-wrapper {
            height: 180px;
        }

        .item-card-header,
        .item-body {
            padding: var(--spacing-md);
        }

        .item-card-header {
            border-bottom: none;
            border-right: 1px solid var(--gray-100);
        }
    }

    @media (max-width: 480px) {
        .item-card {
            grid-template-columns: 1fr;
        }

        .item-card-header {
            border-right: none;
            border-bottom: 1px solid var(--gray-100);
        }

        .item-image-wrapper {
            height: 160px;
        }
    }
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 0 var(--spacing-lg);">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1>
                <i class="bi bi-collection"></i>
                Inventory Catalog
            </h1>
            <p>Browse items available for borrowing</p>
        </div>
    </div>

    <!-- Search and Filter Card -->
    <div class="card filters-card">
        <div class="card-body">
            <form method="GET" action="{{ route('staff.items.index') }}" id="filterForm" style="display: contents;">
                <div class="search-bar">
                    <input 
                        type="text" 
                        class="form-control" 
                        name="search" 
                        placeholder="Search by name or code..." 
                        value="{{ request('search') }}"
                    >
                </div>

                <div>
                    <select class="form-select" name="category_id" onchange="document.getElementById('filterForm').submit()">
                        <option value="">-- All Categories --</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}" @if (request('category_id') == $category->id) selected @endif>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div style="display: flex; gap: var(--spacing-sm);">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">
                        <i class="bi bi-search"></i> Search
                    </button>

                    @if (request('search') || request('category_id'))
                        <a href="{{ route('staff.items.index') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Results Info -->
    @if ($items->count() > 0)
        <div class="results-info">
            Showing <strong>{{ $items->count() }}</strong> of <strong>{{ $items->total() }}</strong> items
            @if (request('search') || request('category_id'))
                <br>
                @if (request('search'))
                    <i class="bi bi-tag"></i> Search: <strong>{{ request('search') }}</strong>
                @endif
                @if (request('category_id'))
                    <i class="bi bi-folder"></i> Category: <strong>{{ $categories->find(request('category_id'))->name ?? 'Unknown' }}</strong>
                @endif
            @endif
        </div>
    @endif

    <!-- Items Grid -->
    @if ($items->count() > 0)
        <div class="items-grid">
            @foreach ($items as $item)
                <div class="item-card">
                    <!-- Image Section -->
                    <div class="item-image-wrapper">
                        <img src="{{ $item->getImageUrl() }}" alt="{{ $item->name }}" class="item-image">
                        <span class="badge badge-{{ $item->available_stock > 0 ? 'success' : 'danger' }} item-badge">
                            @if ($item->available_stock > 0)
                                <i class="bi bi-check-circle"></i> In Stock
                            @else
                                <i class="bi bi-x-circle"></i> Out
                            @endif
                        </span>
                    </div>
                    
                    <!-- Header Section -->
                    <div class="item-card-header">
                        <div class="item-code">{{ $item->code }}</div>
                        <div class="item-name">{{ Str::limit($item->name, 45) }}</div>
                        <p class="item-meta">
                            <span class="item-meta-item">
                                <i class="bi bi-tag"></i>
                                {{ $item->category->name }}
                            </span>
                        </p>
                    </div>

                    <!-- Body Section -->
                    <div class="item-body">
                        <!-- Stock Status -->
                        <div class="stock-status">
                            <div class="stock-label">Available</div>
                            <div class="stock-value {{ $item->available_stock > 0 ? '' : 'unavailable' }}">
                                {{ $item->available_stock }}
                            </div>
                        </div>

                        <!-- Badges -->
                        <div class="item-badges-row">
                            <span class="badge badge-{{ $item->getConditionBadgeColor() }}">
                                {{ $item->condition }}
                            </span>
                            @if ($item->total_stock - $item->available_stock > 0)
                                <span class="badge badge-secondary">
                                    {{ $item->total_stock - $item->available_stock }} in use
                                </span>
                            @endif
                        </div>

                        <!-- Action Button -->
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
            <div style="text-align: center; padding: var(--spacing-xl) var(--spacing-lg);">
                <div style="font-size: 3rem; color: var(--gray-400); margin-bottom: var(--spacing-lg);">
                    <i class="bi bi-inbox"></i>
                </div>
                <p style="font-size: var(--font-size-base); color: var(--gray-500); margin-bottom: var(--spacing-lg);">
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
