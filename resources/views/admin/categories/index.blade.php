@extends('layouts.app')

@section('title', 'Manage Categories - SIMBAR')

@section('content')
<style>
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

    .table {
        width: 100%;
        margin: 0;
        border-collapse: collapse;
    }

    .table thead {
        background-color: var(--gray-50);
        border-bottom: 2px solid var(--gray-200);
    }

    .table th {
        padding: var(--spacing-md) var(--spacing-lg);
        text-align: left;
        font-size: var(--font-size-xs);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-600);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .table td {
        padding: var(--spacing-lg);
        border-bottom: 1px solid var(--gray-200);
        font-size: var(--font-size-sm);
        color: var(--gray-700);
    }

    .table tbody tr {
        transition: background-color 0.2s ease;
    }

    .table tbody tr:hover {
        background-color: var(--gray-50);
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    .card {
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
    }

    .card-body {
        padding: var(--spacing-lg);
    }

    .badge {
        display: inline-block;
        padding: var(--spacing-xs) var(--spacing-md);
        border-radius: var(--radius-sm);
        font-size: var(--font-size-xs);
        font-weight: var(--font-weight-semibold);
    }

    .badge-primary {
        background-color: #cfe2ff;
        color: #084298;
        border: 1px solid #b6d4fe;
    }

    @media (max-width: 768px) {
        .page-title {
            font-size: var(--font-size-lg);
        }

        .card {
            border-radius: var(--radius-md);
        }

        .card-body {
            padding: var(--spacing-md);
        }

        /* Convert table to cards on mobile */
        .table thead {
            display: none;
        }

        .table tbody,
        .table tr,
        .table td {
            display: block;
            width: 100%;
        }

        .table tr {
            margin-bottom: var(--spacing-lg);
            border: 1px solid var(--gray-200);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .table td {
            padding: var(--spacing-md) !important;
            padding-left: 35% !important;
            position: relative;
            border: none;
            border-bottom: 1px solid var(--gray-100);
            font-size: var(--font-size-sm);
        }

        .table td:last-child {
            border-bottom: none;
        }

        .table td:before {
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

        .d-flex {
            flex-wrap: wrap;
            gap: var(--spacing-sm);
        }

        .btn-sm {
            padding: var(--spacing-xs) var(--spacing-sm);
            font-size: var(--font-size-xs);
        }
    }

    @media (max-width: 480px) {
        .page-title {
            font-size: var(--font-size-base);
            gap: var(--spacing-sm);
        }

        .page-subtitle {
            font-size: var(--font-size-xs);
        }

        .card-body {
            padding: var(--spacing-md);
        }

        .table td {
            font-size: var(--font-size-xs);
        }

        .table td:before {
            font-size: var(--font-size-2xs);
        }
    }
</style>

<div class="container-fluid" style="max-width: 1400px; padding: var(--spacing-lg);">
    <!-- Header Section -->
    <div class="d-flex align-items-center justify-content-between mb-4" style="gap: var(--spacing-md);">
        <div>
            <h1 class="page-title">
                <i class="bi bi-tags"></i> Categories
            </h1>
            <p class="page-subtitle">Manage item categories</p>
        </div>
        <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Category
        </a>
    </div>

    <!-- Alert Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Main Card -->
    <div class="card">
        <div class="card-body">
            @if ($categories->count() > 0)
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th data-label="Name">Name</th>
                                <th data-label="Description">Description</th>
                                <th data-label="Items">Items</th>
                                <th data-label="Created">Created</th>
                                <th style="width: 150px;" data-label="Action">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td data-label="Name">
                                        <strong>{{ $category->name }}</strong>
                                    </td>
                                    <td data-label="Description">
                                        <small style="color: var(--gray-500);">
                                            {{ $category->description ? Str::limit($category->description, 50) : '-' }}
                                        </small>
                                    </td>
                                    <td data-label="Items">
                                        <span class="badge badge-primary">{{ $category->items_count }}</span>
                                    </td>
                                    <td data-label="Created">
                                        <small style="color: var(--gray-500);">{{ $category->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td data-label="Action">
                                        <div class="d-flex align-items-center" style="gap: 0.5rem;">
                                            <a href="{{ route('admin.categories.edit', $category) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" 
                                                  style="display:inline;" onsubmit="return confirm('Delete this category?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-primary" title="Delete" style="--bs-btn-border-color: #dc3545; color: #dc3545;">
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
                <div class="d-flex justify-content-center mt-4">
                    {{ $categories->links() }}
                </div>
            @else
                <div class="empty-state text-center" style="padding: var(--spacing-xl) 0;">
                    <div class="empty-state-icon mb-3">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: var(--gray-400);"></i>
                    </div>
                    <p class="empty-state-text">No categories found. Create one to get started.</p>
                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-plus-circle"></i> Create First Category
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
