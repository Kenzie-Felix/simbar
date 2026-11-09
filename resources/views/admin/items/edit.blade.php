@extends('layouts.app')

@section('title', 'Edit Item - SIMBAR')

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

    .info-card p {
        font-size: var(--font-size-xs);
        color: var(--gray-600);
        margin: 0;
        line-height: 1.8;
    }

    .info-item {
        margin-bottom: var(--spacing-sm);
    }

    .info-item strong {
        color: var(--gray-900);
    }

    .stock-summary {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
    }

    .stock-summary h6 {
        font-size: var(--font-size-sm);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-900);
        margin-bottom: var(--spacing-md);
        padding-bottom: var(--spacing-md);
        border-bottom: 1px solid var(--gray-200);
    }

    .stock-summary p {
        font-size: var(--font-size-xs);
        color: var(--gray-600);
        margin: 0;
        line-height: 1.8;
    }

    .stock-summary strong {
        color: var(--gray-900);
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
            margin-bottom: var(--spacing-md);
        }

        .info-card h6 {
            font-size: var(--font-size-sm);
            margin-bottom: var(--spacing-sm);
        }

        .stock-summary {
            padding: var(--spacing-md);
        }

        .stock-summary h6 {
            font-size: var(--font-size-sm);
            margin-bottom: var(--spacing-sm);
            padding-bottom: var(--spacing-sm);
        }

        .stock-summary p {
            font-size: var(--font-size-xs);
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
            margin-bottom: var(--spacing-xs);
        }
    }
</style>

<div style="max-width: 1400px; margin: 0 auto;">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1>
                <i class="bi bi-pencil"></i>
                Edit Item
            </h1>
            <p>Update item details</p>
        </div>
    </div>

    <!-- Form Layout -->
    <div class="form-container">
        <!-- Form -->
        <div class="form-card">
            <form method="POST" action="{{ route('admin.items.update', $item) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                @include('admin.items.form', ['item' => $item, 'categories' => $categories, 'locations' => $locations])
            </form>
        </div>

        <!-- Info Sidebar -->
        <div>
            <!-- Item Info -->
            <div class="info-card">
                <h6>
                    <i class="bi bi-info-circle"></i> Item Info
                </h6>
                <div class="info-item">
                    <strong>Created:</strong><br>
                    <span style="color: var(--gray-500); font-size: var(--font-size-xs);">{{ $item->created_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="info-item">
                    <strong>Updated:</strong><br>
                    <span style="color: var(--gray-500); font-size: var(--font-size-xs);">{{ $item->updated_at->format('M d, Y H:i') }}</span>
                </div>
                <div class="info-item">
                    <strong>In Loans:</strong><br>
                    <span class="badge badge-warning">{{ $item->loanItems()->count() }}</span>
                </div>
            </div>

            <!-- Stock Summary -->
            <div class="stock-summary">
                <h6>Stock Summary</h6>
                <p>
                    <strong>Total Stock:</strong> {{ $item->total_stock }}<br>
                    <strong>Available:</strong> <span style="color: var(--primary); font-weight: var(--font-weight-bold);">{{ $item->available_stock }}</span><br>
                    <strong>In Loans:</strong> {{ $item->total_stock - $item->available_stock }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
