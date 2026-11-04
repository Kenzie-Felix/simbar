@extends('layouts.app')

@section('title', 'Manage Locations - SIMBAR')

@section('content')
<div class="container-fluid" style="max-width: 1400px; padding: var(--spacing-lg);">
    <!-- Header Section -->
    <div class="d-flex align-items-center justify-content-between mb-4" style="gap: var(--spacing-md);">
        <div>
            <h1 class="page-title">
                <i class="bi bi-geo"></i> Storage Locations
            </h1>
            <p class="page-subtitle">Manage item storage locations</p>
        </div>
        <a href="{{ route('admin.locations.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Add Location
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
            @if ($locations->count() > 0)
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
                            @foreach ($locations as $location)
                                <tr>
                                    <td>
                                        <strong>{{ $location->name }}</strong>
                                    </td>
                                    <td>
                                        <small style="color: var(--gray-500);">
                                            {{ $location->description ? Str::limit($location->description, 50) : '-' }}
                                        </small>
                                    </td>
                                    <td>
                                        <span class="badge badge-primary">{{ $location->items_count }}</span>
                                    </td>
                                    <td>
                                        <small style="color: var(--gray-500);">{{ $location->created_at->format('M d, Y') }}</small>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center" style="gap: 0.5rem;">
                                            <a href="{{ route('admin.locations.edit', $location) }}" 
                                               class="btn btn-sm btn-outline-primary" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.locations.destroy', $location) }}" 
                                                  style="display:inline;" onsubmit="return confirm('Delete this location?');">
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
                    {{ $locations->links() }}
                </div>
            @else
                <div class="empty-state text-center" style="padding: var(--spacing-xl) 0;">
                    <div class="empty-state-icon mb-3">
                        <i class="bi bi-inbox" style="font-size: 3rem; color: var(--gray-400);"></i>
                    </div>
                    <p class="empty-state-text">No locations found. Create one to get started.</p>
                    <a href="{{ route('admin.locations.create') }}" class="btn btn-primary mt-3">
                        <i class="bi bi-plus-circle"></i> Create First Location
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
