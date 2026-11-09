@extends('layouts.app')

@section('title', 'Welcome - SIMBAR')

@section('content')
<style>
    /* Hero Section - Modern Enterprise Landing */
    .welcome-container {
        min-height: calc(100vh - 70px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: var(--spacing-2xl) var(--spacing-lg);
        background: linear-gradient(135deg, #f9fafb 0%, #f0f9ff 100%);
    }

    .welcome-hero {
        max-width: 1000px;
        width: 100%;
    }

    .hero-content {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: var(--spacing-2xl);
        align-items: center;
        margin-bottom: var(--spacing-2xl);
    }

    .hero-text {
        padding-right: var(--spacing-xl);
    }

    .hero-icon-section {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .hero-icon-container {
        position: relative;
        width: 280px;
        height: 280px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .hero-icon-bg {
        position: absolute;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, var(--primary-light) 0%, #eff6ff 100%);
        border-radius: var(--radius-xl);
        box-shadow: var(--shadow-lg);
    }

    .hero-icon {
        position: relative;
        z-index: 2;
        font-size: 5rem;
        color: var(--primary);
    }

    .hero-title {
        font-size: 2.5rem;
        font-weight: var(--font-weight-bold);
        color: var(--gray-900);
        margin-bottom: var(--spacing-lg);
        letter-spacing: -0.5px;
    }

    .hero-title-highlight {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .hero-subtitle {
        font-size: 1.25rem;
        color: var(--gray-600);
        margin-bottom: var(--spacing-lg);
        line-height: 1.7;
    }

    .hero-description {
        font-size: var(--font-size-lg);
        color: var(--gray-600);
        margin-bottom: var(--spacing-2xl);
        line-height: 1.8;
    }

    .welcome-buttons {
        display: flex;
        gap: var(--spacing-md);
        margin-bottom: var(--spacing-2xl);
    }

    .btn-primary-enhanced {
        background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
        color: white;
        padding: 0.875rem 1.75rem;
        border-radius: var(--radius-md);
        text-decoration: none;
        font-weight: var(--font-weight-semibold);
        display: inline-flex;
        align-items: center;
        gap: var(--spacing-sm);
        transition: all 0.3s ease;
        box-shadow: var(--shadow-md);
        border: none;
        cursor: pointer;
    }

    .btn-primary-enhanced:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-lg);
        color: white;
        text-decoration: none;
    }

    .btn-secondary-enhanced {
        background: white;
        color: var(--primary);
        border: 2px solid var(--primary);
        padding: 0.75rem 1.75rem;
        border-radius: var(--radius-md);
        text-decoration: none;
        font-weight: var(--font-weight-semibold);
        display: inline-flex;
        align-items: center;
        gap: var(--spacing-sm);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-secondary-enhanced:hover {
        background: var(--primary-light);
        transform: translateY(-2px);
        color: var(--primary);
        text-decoration: none;
    }

    .highlights-section {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: var(--spacing-xl);
        margin-top: var(--spacing-2xl);
    }

    .highlight-card {
        background: white;
        border-radius: var(--radius-lg);
        padding: var(--spacing-xl);
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
        border: 1px solid var(--gray-100);
    }

    .highlight-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-4px);
    }

    .highlight-icon {
        font-size: 2.5rem;
        color: var(--secondary);
        margin-bottom: var(--spacing-md);
        display: block;
    }

    .highlight-title {
        font-size: var(--font-size-lg);
        font-weight: var(--font-weight-semibold);
        color: var(--gray-900);
        margin-bottom: var(--spacing-sm);
    }

    .highlight-description {
        font-size: var(--font-size-sm);
        color: var(--gray-600);
        line-height: 1.6;
    }

    /* Responsive Design */
    @media (max-width: 1024px) {
        .hero-content {
            grid-template-columns: 1fr;
            gap: var(--spacing-xl);
        }

        .hero-text {
            padding-right: 0;
        }

        .hero-title {
            font-size: 2rem;
        }

        .hero-subtitle {
            font-size: 1.125rem;
        }
    }

    @media (max-width: 768px) {
        .welcome-container {
            padding: var(--spacing-xl) var(--spacing-md);
            min-height: auto;
        }

        .hero-title {
            font-size: 1.75rem;
        }

        .hero-subtitle {
            font-size: 1rem;
        }

        .hero-description {
            font-size: var(--font-size-base);
        }

        .hero-icon-container {
            width: 200px;
            height: 200px;
        }

        .hero-icon {
            font-size: 3.5rem;
        }

        .welcome-buttons {
            flex-direction: column;
            gap: var(--spacing-sm);
        }

        .btn-primary-enhanced,
        .btn-secondary-enhanced {
            width: 100%;
            justify-content: center;
        }

        .highlights-section {
            grid-template-columns: 1fr;
            gap: var(--spacing-md);
        }
    }
</style>

<div class="welcome-container">
    <div class="welcome-hero">
        <!-- Hero Section with Content and Icon -->
        <div class="hero-content">
            <div class="hero-text">
                <h1 class="hero-title">
                    Welcome to <span class="hero-title-highlight">SIMBAR</span>
                </h1>
                <p class="hero-subtitle">
                    Enterprise-Grade Inventory Management
                </p>
                <p class="hero-description">
                    A modern, reliable system for managing office equipment and inventory. 
                    Streamline borrowing requests, track returns, and maintain complete visibility 
                    over your organization's assets with intelligent inventory management.
                </p>

                <!-- Action Buttons -->
                <div class="welcome-buttons">
                    @if (auth()->check())
                        <a href="{{ route('dashboard') }}" class="btn-primary-enhanced">
                            <i class="bi bi-speedometer2"></i> Go to Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="btn-primary-enhanced">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                        <a href="{{ route('register') }}" class="btn-secondary-enhanced">
                            <i class="bi bi-person-plus"></i> Register
                        </a>
                    @endif
                </div>
            </div>

            <!-- Hero Icon -->
            <div class="hero-icon-section">
                <div class="hero-icon-container">
                    <div class="hero-icon-bg"></div>
                    <i class="hero-icon bi bi-boxes"></i>
                </div>
            </div>
        </div>

        <!-- Features Highlight Section -->
        <div class="highlights-section">
            <div class="highlight-card">
                <i class="highlight-icon bi bi-shield-lock"></i>
                <h3 class="highlight-title">Secure & Protected</h3>
                <p class="highlight-description">
                    Role-based access control ensures only authorized personnel can manage inventory.
                </p>
            </div>
            <div class="highlight-card">
                <i class="highlight-icon bi bi-lightning-charge"></i>
                <h3 class="highlight-title">Fast & Responsive</h3>
                <p class="highlight-description">
                    Optimized interface for quick navigation and instant access to your inventory data.
                </p>
            </div>
            <div class="highlight-card">
                <i class="highlight-icon bi bi-graph-up"></i>
                <h3 class="highlight-title">Comprehensive Reports</h3>
                <p class="highlight-description">
                    Detailed analytics and reporting tools for informed inventory decisions.
                </p>
            </div>
        </div>
    </div>
</div>

@endsection
