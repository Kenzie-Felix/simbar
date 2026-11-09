<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SIMBAR - Inventory Management System">
    <meta name="theme-color" content="#1e3a8a">
    <title>@yield('title', 'SIMBAR')</title>
    
    <!-- Bootstrap 5 CSS from CDN (for form controls and components) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons from CDN -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <!-- Vite CSS (includes design-system.css) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    @yield('styles')

    <style>
        /* Accessibility: Focus states */
        *:focus {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        *:focus:not(:focus-visible) {
            outline: none;
        }

        /* High contrast mode support */
        @media (prefers-contrast: more) {
            .btn, .badge, a {
                border: 1px solid currentColor;
            }
        }

        /* Reduced motion support */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
                scroll-behavior: auto !important;
            }
        }
    </style>
</head>
<body>
    <!-- <a href="#main-content" class="skip-link" style="position: absolute; top: -40px; left: 0; background: var(--primary); color: white; padding: var(--spacing-md); z-index: 100000; text-decoration: none; border-radius: 0 0 var(--radius-md) 0;">Skip to main content</a> -->
    
    @if (auth()->check())
        @include('layouts.navbar')
        @include('layouts.sidebar')
        <div class="main-content" id="main-content" role="main">
            @yield('content')
        </div>
    @else
        <div id="main-content" role="main">
            @yield('content')
        </div>
    @endif

    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @yield('scripts')

    <script>
        // Skip link functionality
        document.querySelector('.skip-link').addEventListener('click', function(e) {
            e.preventDefault();
            const mainContent = document.getElementById('main-content');
            mainContent.focus();
            mainContent.scrollIntoView({ behavior: 'smooth' });
        });
    </script>
</body>
</html>
