@extends('layouts.app')

@section('title', 'Welcome - SIMBAR')

@section('content')
<style>
    .welcome-container {
        max-width: 900px;
        margin: 0 auto;
        padding: var(--spacing-2xl) var(--spacing-lg);
    }

    .welcome-card {
        background: white;
        border: 1px solid var(--gray-200);
        border-radius: var(--radius-lg);
        padding: var(--spacing-2xl) var(--spacing-lg);
        text-align: center;
        box-shadow: var(--shadow-sm);
    }

    .welcome-title {
        font-size: var(--font-size-3xl);
        font-weight: var(--font-weight-bold);
        color: var(--gray-900);
        margin-bottom: var(--spacing-md);
    }

    .welcome-subtitle {
        font-size: var(--font-size-lg);
        color: var(--gray-500);
        margin-bottom: var(--spacing-lg);
    }

    .welcome-description {
        font-size: var(--font-size-base);
        color: var(--gray-600);
        margin-bottom: var(--spacing-2xl);
        line-height: 1.6;
    }

    .welcome-buttons {
        display: flex;
        gap: var(--spacing-md);
        justify-content: center;
        margin-bottom: var(--spacing-2xl);
        flex-wrap: wrap;
    }

    .features-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: var(--spacing-lg);
        margin-top: var(--spacing-2xl);
        padding-top: var(--spacing-2xl);
        border-top: 1px solid var(--gray-200);
    }

    .feature-card {
        text-align: center;
    }

    .feature-icon {
        font-size: 2rem;
        color: var(--primary);
        margin-bottom: var(--spacing-md);
    }

    .feature-title {
        font-size: var(--font-size-base);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-900);
        margin-bottom: var(--spacing-sm);
    }

    .feature-description {
        font-size: var(--font-size-sm);
        color: var(--gray-600);
    }

    @media (max-width: 768px) {
        .welcome-container {
            padding: var(--spacing-xl) var(--spacing-md);
        }

        .welcome-card {
            padding: var(--spacing-xl) var(--spacing-md);
        }

        .welcome-buttons {
            flex-direction: column;
        }

        .welcome-buttons a {
            width: 100%;
        }
    }
</style>

<div class="welcome-container">
    <div class="welcome-card">
        <!-- Logo/Icon -->
        <div style="font-size: 3rem; margin-bottom: var(--spacing-lg); color: var(--primary);">
            <i class="bi bi-boxes"></i>
        </div>

        <!-- Title and Subtitle -->
        <h1 class="welcome-title">SIMBAR</h1>
        <p class="welcome-subtitle">Sistem Manajemen Inventaris Barang Kantor</p>
        
        <!-- Description -->
        <p class="welcome-description">
            A comprehensive office inventory management system designed to streamline borrowing requests, 
            returns, and inventory tracking with secure role-based access control.
        </p>

        <!-- Action Buttons -->
        <div class="welcome-buttons">
            @if (auth()->check())
                <a href="{{ route('dashboard') }}" class="btn btn-primary" style="padding: var(--spacing-md) var(--spacing-xl);">
                    <i class="bi bi-speedometer2"></i> Go to Dashboard
                </a>
            @else
                <a href="{{ route('login') }}" class="btn btn-primary" style="padding: var(--spacing-md) var(--spacing-xl);">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </a>
                <a href="{{ route('register') }}" class="btn btn-outline-primary" style="padding: var(--spacing-md) var(--spacing-xl);">
                    <i class="bi bi-person-plus"></i> Register
                </a>
            @endif
        </div>

        <!-- Features Grid -->
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="bi bi-lock-fill"></i>
                </div>
                <h6 class="feature-title">Secure</h6>
                <p class="feature-description">
                    Role-based access control with encrypted passwords
                </p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="bi bi-speedometer2"></i>
                </div>
                <h6 class="feature-title">Fast</h6>
                <p class="feature-description">
                    Optimized database queries and caching
                </p>
            </div>
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="bi bi-graph-up"></i>
                </div>
                <h6 class="feature-title">Scalable</h6>
                <p class="feature-description">
                    Handles large inventories efficiently
                </p>
            </div>
        </div>
    </div>
</div>

@endsection
