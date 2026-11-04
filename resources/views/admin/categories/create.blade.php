@extends('layouts.app')

@section('title', 'Create Category - SIMBAR')

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

    .info-card ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .info-card li {
        font-size: var(--font-size-sm);
        color: var(--gray-600);
        padding: var(--spacing-sm) 0;
        border-bottom: 1px solid var(--gray-200);
    }

    .info-card li:last-child {
        border-bottom: none;
    }

    .info-card li:before {
        content: "✓ ";
        color: var(--success);
        font-weight: var(--font-weight-bold);
        margin-right: var(--spacing-sm);
    }

    @media (max-width: 900px) {
        .form-container {
            grid-template-columns: 1fr;
        }

        .page-header {
            flex-direction: column;
        }
    }
</style>

<div style="max-width: 1400px; margin: 0 auto;">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1>
                <i class="bi bi-plus-circle"></i>
                Create New Category
            </h1>
            <p>Add a new category to organize your items</p>
        </div>
    </div>

    <!-- Form Layout -->
    <div class="form-container">
        <!-- Form -->
        <div class="form-card">
            <form method="POST" action="{{ route('admin.categories.store') }}">
                @csrf
                @include('admin.categories.form')
            </form>
        </div>

        <!-- Info Card -->
        <div class="info-card">
            <h6>
                <i class="bi bi-info-circle"></i> Tips
            </h6>
            <ul>
                <li>Category names must be unique</li>
                <li>Use clear and descriptive names</li>
                <li>Examples: Electronics, Furniture, Office Supplies</li>
                <li>You can edit or delete categories later</li>
                <li>Cannot delete categories with items</li>
            </ul>
        </div>
    </div>
</div>
@endsection
