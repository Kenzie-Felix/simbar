@extends('layouts.app')

@section('title', 'Create Item - SIMBAR')

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
        font-size: var(--font-size-xs);
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

    .info-divider {
        height: 1px;
        background: var(--gray-200);
        margin: var(--spacing-lg) 0;
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

        .info-divider {
            margin: var(--spacing-md) 0;
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
            margin-bottom: var(--spacing-sm);
        }

        .info-card li {
            font-size: var(--font-size-2xs);
            padding: var(--spacing-xs) 0;
        }
    }
</style>

<div style="max-width: 1400px; margin: 0 auto;">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1>
                <i class="bi bi-plus-circle"></i>
                Create New Item
            </h1>
            <p>Add a new item to inventory</p>
        </div>
    </div>

    <!-- Form Layout -->
    <div class="form-container">
        <!-- Form -->
        <div class="form-card">
            <form method="POST" action="{{ route('admin.items.store') }}" enctype="multipart/form-data">
                @csrf
                @include('admin.items.form', ['categories' => $categories, 'locations' => $locations])
            </form>
        </div>

        <!-- Info Sidebar -->
        <div>
            <!-- Required Fields -->
            <div class="info-card">
                <h6>
                    <i class="bi bi-info-circle"></i> Required Fields
                </h6>
                <ul>
                    <li>Item code (must be unique)</li>
                    <li>Item name</li>
                    <li>Category selection</li>
                    <li>Location selection</li>
                    <li>Total stock (initial quantity)</li>
                    <li>Item condition</li>
                </ul>
            </div>

            <div class="info-divider"></div>

            <!-- Tips -->
            <div class="info-card" style="margin-top: 0;">
                <h6>
                    <i class="bi bi-lightbulb"></i> Tips
                </h6>
                <ul>
                    <li>Use codes like: LT-001, MON-05</li>
                    <li>Ensure stock quantity is accurate</li>
                    <li>You can add more stock later via Stock In</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection
