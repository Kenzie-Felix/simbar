@extends('layouts.app')

@section('title', 'Manage Categories - SIMBAR')

@section('content')
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
                                <th>Name</th>
                                <th>Description</th>
                                <th>Items</th>
                                <th>Created</th>
                                <th style="width: 150px;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($categories as $category)
                                <tr>
                                    <td>
                                        <strong>{{ $category->name }}</strong>
                                    </td>
                                    <td>
                                        <small style="color: var(--gray-500);">
                                            {{ $category->description ? Str::limit($category->description, 50) : '-' }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ $category->items_count }}</span>
                                    </td>
                                    <td>
                                        <small style="color: var(--gray-500);">{{ $category->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
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
