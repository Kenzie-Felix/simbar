@extends('layouts.app')

@section('title', $item->name . ' - SIMBAR')

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

    .content-grid {
        display: grid;
        grid-template-columns: 1fr 350px;
        gap: var(--spacing-lg);
    }

    .item-detail-image {
        width: 100%;
        max-height: 600px;
        object-fit: contain;
        background: var(--gray-50);
        border-radius: var(--radius-lg);
        border: 1px solid var(--gray-200);
        padding: var(--spacing-lg);
        margin-bottom: var(--spacing-lg);
        transition: transform 0.3s ease;
        cursor: zoom-in;
    }

    .item-detail-image:hover {
        transform: scale(1.02);
    }

    /* Modal for zoomed image */
    .image-modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.9);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: var(--spacing-lg);
    }

    .image-modal.active {
        display: flex;
    }

    .image-modal-content {
        position: relative;
        max-width: 90vw;
        max-height: 90vh;
        background: white;
        border-radius: var(--radius-lg);
        padding: var(--spacing-lg);
    }

    .image-modal-img {
        max-width: 100%;
        max-height: 85vh;
        object-fit: contain;
    }

    .image-modal-close {
        position: absolute;
        top: var(--spacing-md);
        right: var(--spacing-md);
        background: rgba(0, 0, 0, 0.5);
        color: white;
        border: none;
        border-radius: var(--radius-md);
        padding: var(--spacing-sm) var(--spacing-md);
        cursor: pointer;
        font-size: var(--font-size-lg);
        transition: all 0.2s ease;
    }

    .image-modal-close:hover {
        background: rgba(0, 0, 0, 0.8);
    }

    .detail-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--spacing-lg);
    }

    .detail-field {
        padding-bottom: var(--spacing-md);
    }

    .detail-label {
        font-size: var(--font-size-sm);
        color: var(--gray-500);
        margin-bottom: var(--spacing-xs);
        display: block;
        font-weight: var(--font-weight-medium);
    }

    .detail-value {
        font-size: var(--font-size-base);
        color: var(--gray-900);
        font-weight: var(--font-weight-semibold);
    }

    .stock-stat {
        text-align: center;
        padding: var(--spacing-lg);
        border-right: 1px solid var(--gray-200);
    }

    .stock-stat:last-child {
        border-right: none;
    }

    .stock-label {
        font-size: var(--font-size-sm);
        color: var(--gray-500);
        margin-bottom: var(--spacing-xs);
        display: block;
    }

    .stock-value {
        font-size: var(--font-size-2xl);
        font-weight: var(--font-weight-bold);
        margin-bottom: var(--spacing-md);
    }

    .stock-value.primary { color: var(--primary); }
    .stock-value.success { color: var(--success); }
    .stock-value.warning { color: var(--warning); }

    .alert-success {
        background-color: var(--success-light);
        color: var(--success);
        border: 1px solid var(--success);
        border-left: 4px solid var(--success);
    }

    .alert-danger {
        background-color: var(--danger-light);
        color: var(--danger);
        border: 1px solid var(--danger);
        border-left: 4px solid var(--danger);
    }

    @media (max-width: 1024px) {
        .content-grid {
            grid-template-columns: 1fr;
        }

        .stock-stat {
            border-right: none;
            border-bottom: 1px solid var(--gray-200);
        }

        .stock-stat:last-child {
            border-bottom: none;
        }
    }
</style>

<div style="max-width: 1400px; margin: 0 auto; padding: 0 var(--spacing-lg);">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <h1>
                <i class="bi bi-collection"></i>
                {{ $item->name }}
            </h1>
            <p>Code: <strong>{{ $item->code }}</strong></p>
        </div>
        <a href="{{ route('staff.items.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-arrow-left"></i> Back to Items
        </a>
    </div>

    <!-- Content Grid -->
    <div class="content-grid">
        <!-- Main Content -->
        <div>
            <!-- Item Image -->
            <div>
                <img src="{{ $item->getImageUrl() }}" alt="{{ $item->name }}" class="item-detail-image" onclick="zoomImage(this)" title="Click to zoom">
                <!-- Image Zoom Modal -->
                <div class="image-modal" id="imageModal">
                    <div class="image-modal-content">
                        <button class="image-modal-close" onclick="closeImageZoom()">&times;</button>
                        <img src="{{ $item->getImageUrl() }}" alt="{{ $item->name }}" class="image-modal-img">
                    </div>
                </div>
            </div>

            <!-- Item Information Card -->
            <div class="card" style="margin-bottom: var(--spacing-lg);">
                <div class="card-header">
                    <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                        <i class="bi bi-info-circle"></i> Item Information
                    </h5>
                </div>
                <div class="card-body">
                    <div class="detail-row">
                        <div class="detail-field">
                            <span class="detail-label">Category</span>
                            <span class="badge badge-secondary" style="display: inline-block;">{{ $item->category->name }}</span>
                        </div>
                        <div class="detail-field">
                            <span class="detail-label">Location</span>
                            <span class="detail-value">{{ $item->location->name }}</span>
                        </div>
                    </div>

                    @if ($item->description)
                        <div class="detail-field" style="padding-top: var(--spacing-md); border-top: 1px solid var(--gray-200);">
                            <span class="detail-label">Description</span>
                            <span class="detail-value" style="font-weight: var(--font-weight-normal);">{{ $item->description }}</span>
                        </div>
                    @endif

                    <div class="detail-field" style="padding-top: var(--spacing-md); border-top: 1px solid var(--gray-200);">
                        <span class="detail-label">Condition</span>
                        <span class="badge badge-{{ $item->getConditionBadgeColor() }}" style="display: inline-block;">
                            {{ $item->condition }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Stock Availability Card -->
            <div class="card" style="margin-bottom: var(--spacing-lg);">
                <div class="card-header">
                    <h5 style="margin: 0; display: flex; align-items: center; gap: var(--spacing-md);">
                        <i class="bi bi-pie-chart"></i> Stock Availability
                    </h5>
                </div>
                <div style="display: flex;">
                    <div class="stock-stat">
                        <span class="stock-label">Total Stock</span>
                        <div class="stock-value primary">{{ $item->total_stock }}</div>
                    </div>
                    <div class="stock-stat">
                        <span class="stock-label">Available</span>
                        <div class="stock-value success">{{ $item->available_stock }}</div>
                    </div>
                    <div class="stock-stat">
                        <span class="stock-label">In Use</span>
                        <div class="stock-value warning">{{ $item->total_stock - $item->available_stock }}</div>
                    </div>
                </div>
            </div>

            <!-- Action Card -->
            @if ($item->isAvailable())
                <div class="alert alert-success" style="padding: var(--spacing-lg); border-radius: var(--radius-lg); margin-bottom: var(--spacing-lg);">
                    <h6 style="margin: 0 0 var(--spacing-md) 0; display: flex; align-items: center; gap: var(--spacing-md);">
                        <i class="bi bi-check-circle"></i> Ready to Borrow
                    </h6>
                    <!-- <p style="margin: 0 0 var(--spacing-md) 0; font-size: var(--font-size-sm);">This item is available for borrowing.</p>
                    <a href="#" class="btn btn-primary" style="display: inline-flex; align-items: center; gap: var(--spacing-sm);">
                        <i class="bi bi-plus-circle"></i> Request to Borrow -->
                    </a>
                    <p style="font-size: var(--font-size-xs); color: var(--gray-500); margin: var(--spacing-md) 0 0 0;">
                        Note: Loan request feature coming soon
                    </p>
                </div>
            @else
                <div class="alert alert-danger" style="padding: var(--spacing-lg); border-radius: var(--radius-lg); margin-bottom: var(--spacing-lg);">
                    <h6 style="margin: 0 0 var(--spacing-md) 0; display: flex; align-items: center; gap: var(--spacing-md);">
                        <i class="bi bi-x-circle"></i> Not Available
                    </h6>
                    <p style="margin: 0; font-size: var(--font-size-sm);">
                        This item is currently not available for borrowing.
                    </p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div>
            <!-- Quick Info Card -->
            <div class="card" style="margin-bottom: var(--spacing-lg); background-color: var(--gray-50);">
                <div class="card-body">
                    <h6 style="margin: 0 0 var(--spacing-md) 0; display: flex; align-items: center; gap: var(--spacing-md);">
                        <i class="bi bi-info-circle"></i> Details
                    </h6>
                    <ul style="list-style: none; padding: 0; margin: 0; font-size: var(--font-size-sm);">
                        <li style="margin-bottom: var(--spacing-md); padding-bottom: var(--spacing-md); border-bottom: 1px solid var(--gray-200);">
                            <strong style="color: var(--gray-700);">Code:</strong>
                            <div style="color: var(--gray-600);">{{ $item->code }}</div>
                        </li>
                        <li style="margin-bottom: var(--spacing-md); padding-bottom: var(--spacing-md); border-bottom: 1px solid var(--gray-200);">
                            <strong style="color: var(--gray-700);">Category:</strong>
                            <div style="color: var(--gray-600);">{{ $item->category->name }}</div>
                        </li>
                        <li style="margin-bottom: var(--spacing-md); padding-bottom: var(--spacing-md); border-bottom: 1px solid var(--gray-200);">
                            <strong style="color: var(--gray-700);">Location:</strong>
                            <div style="color: var(--gray-600);">{{ $item->location->name }}</div>
                        </li>
                        <li style="margin-bottom: var(--spacing-md); padding-bottom: var(--spacing-md); border-bottom: 1px solid var(--gray-200);">
                            <strong style="color: var(--gray-700);">Condition:</strong>
                            <div>
                                <span class="badge badge-{{ $item->getConditionBadgeColor() }}" style="display: inline-block; margin-top: var(--spacing-xs);">
                                    {{ $item->condition }}
                                </span>
                            </div>
                        </li>
                        <li>
                            <strong style="color: var(--gray-700);">Status:</strong>
                            <div>
                                @if ($item->available_stock > 0)
                                    <span class="badge badge-success" style="display: inline-block; margin-top: var(--spacing-xs);">Available</span>
                                @else
                                    <span class="badge badge-danger" style="display: inline-block; margin-top: var(--spacing-xs);">Out of Stock</span>
                                @endif
                            </div>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- Tips Card -->
            <div class="card">
                <div class="card-body">
                    <h6 style="margin: 0 0 var(--spacing-md) 0; display: flex; align-items: center; gap: var(--spacing-md);">
                        <i class="bi bi-lightbulb"></i> How to Borrow
                    </h6>
                    <ol style="margin: 0; padding-left: var(--spacing-lg); font-size: var(--font-size-sm); color: var(--gray-700);">
                        <li style="margin-bottom: var(--spacing-xs);">Review item details above</li>
                        <li style="margin-bottom: var(--spacing-xs);">Click "Request to Borrow"</li>
                        <li style="margin-bottom: var(--spacing-xs);">Specify quantity and return date</li>
                        <li style="margin-bottom: var(--spacing-xs);">Submit your request</li>
                        <li style="margin-bottom: var(--spacing-xs);">Admin will review and approve</li>
                        <li>Item will be ready for pickup</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function zoomImage(img) {
        const modal = document.getElementById('imageModal');
        modal.classList.add('active');
    }

    function closeImageZoom() {
        const modal = document.getElementById('imageModal');
        modal.classList.remove('active');
    }

    // Close modal when clicking outside image
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('imageModal');
        if (modal) {
            modal.addEventListener('click', function(e) {
                if (e.target === modal) {
                    closeImageZoom();
                }
            });
            
            // Close with Escape key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    closeImageZoom();
                }
            });
        }
    });
</script>

@endsection
