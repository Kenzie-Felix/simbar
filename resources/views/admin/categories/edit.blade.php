@extends('layouts.app')

@section('title', 'Edit Category - SIMBAR')

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
        max-width: 900px;
    }

    .form-card {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        padding: var(--spacing-xl);
        box-shadow: var(--shadow-sm);
    }

    .info-card {
        background: var(--gray-50);
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        height: fit-content;
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

    .info-card p {
        font-size: var(--font-size-sm);
        color: var(--gray-600);
        margin: 0;
        line-height: 1.8;
    }

    .info-item {
        margin-bottom: var(--spacing-sm);
    }

    .info-item strong {
        color: var(--gray-700);
    }

    .danger-card {
        background: var(--danger-light);
        border: 1px solid var(--danger);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
        margin-top: var(--spacing-lg);
    }

    .danger-card h6 {
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-semibold);
        color: var(--danger);
        margin-bottom: var(--spacing-md);
        display: flex;
        align-items: center;
        gap: var(--spacing-sm);
    }

    .danger-card p {
        font-size: var(--font-size-sm);
        color: var(--danger);
        margin: 0 0 var(--spacing-md) 0;
    }

    @media (max-width: 900px) {
        .form-container {
            grid-template-columns: 1fr;
        }

        .page-header {
            flex-direction: column;
        }

        .form-card {
            padding: var(--spacing-lg);
        }
    }

    @media (max-width: 768px) {
        .form-container {
            grid-template-columns: 1fr;
            gap: var(--spacing-lg);
        }

        .form-card {
            padding: var(--spacing-md);
            border-radius: var(--radius-md);
        }

        .page-header {
            flex-direction: column;
            gap: var(--spacing-md);
        }

        .page-header-content h1 {
            font-size: var(--font-size-lg);
        }

        .info-card {
            padding: var(--spacing-md);
            height: auto;
            margin-bottom: var(--spacing-md);
        }

        .info-card h6 {
            font-size: var(--font-size-sm);
            margin-bottom: var(--spacing-sm);
        }

        .danger-card {
            padding: var(--spacing-md);
            margin-top: var(--spacing-md);
        }

        .danger-card h6 {
            font-size: var(--font-size-sm);
            margin-bottom: var(--spacing-sm);
        }

        .danger-card p {
            font-size: var(--font-size-xs);
            margin-bottom: var(--spacing-sm);
        }

        .info-item {
            font-size: var(--font-size-xs);
            margin-bottom: var(--spacing-xs);
        }
    }

    @media (max-width: 480px) {
        .page-header-content h1 {
            font-size: var(--font-size-base);
            gap: var(--spacing-sm);
        }

        .page-header-content p {
            font-size: var(--font-size-xs);
        }

        .form-card {
            padding: var(--spacing-md);
        }

        .info-card h6 {
            font-size: var(--font-size-xs);
        }

        .info-item {
            font-size: var(--font-size-xs);
        }

        .danger-card h6 {
            font-size: var(--font-size-xs);
        }

        .btn-danger {
            width: 100%;
        }
    }
</style>

<div style="max-width: 1400px; margin: 0 auto;">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1>
                <i class="bi bi-pencil"></i>
                {{ __('messages.edit_category') }}
            </h1>
            <p>{{ __('messages.edit_category') }}</p>
        </div>
    </div>

    <!-- Form Layout -->
    <div class="form-container">
        <!-- Form -->
        <div class="form-card">
            <form method="POST" action="{{ route('admin.categories.update', $category) }}">
                @csrf
                @method('PUT')
                @include('admin.categories.form', ['category' => $category])
            </form>
        </div>

        <!-- Info Sidebar -->
        <div>
            <!-- Info Card -->
            <div class="info-card">
                <h6>
                    <i class="bi bi-info-circle"></i> {{ __('messages.item_details') }}
                </h6>
                <div class="info-item">
                    <strong>{{ __('messages.created') }}:</strong><br>
                    <span style="color: var(--gray-500); font-size: var(--font-size-xs);">{{ $category->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="info-item">
                    <strong>{{ __('messages.last_updated') }}:</strong><br>
                    <span style="color: var(--gray-500); font-size: var(--font-size-xs);">{{ $category->updated_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="info-item">
                    <strong>Items Count:</strong><br>
                    <span class="badge badge-info">{{ $category->items()->count() }}</span>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="danger-card">
                <h6>
                    <i class="bi bi-exclamation-triangle"></i> {{ __('messages.policy_violation') }}
                </h6>
                @if ($category->items()->count() > 0)
                    <p>Cannot delete this category because it has {{ $category->items()->count() }} item(s).</p>
                @else
                    <p>Delete this category permanently. This action cannot be undone.</p>
                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Are you sure? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm" style="width: 100%;">
                            <i class="bi bi-trash"></i> Delete Category
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
