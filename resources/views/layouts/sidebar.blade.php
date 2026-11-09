<div class="sidebar" id="sidebar" role="navigation" aria-label="Main navigation">
    @if (auth()->user()->isAdmin())
        <!-- Admin Sidebar -->
        <div class="sidebar-section">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link @if (request()->routeIs('admin.dashboard')) active @endif" title="{{ __('messages.dashboard') }}" aria-current="@if (request()->routeIs('admin.dashboard')) page @endif">
                <i class="bi bi-speedometer2" aria-hidden="true"></i>
                <span>{{ __('messages.dashboard') }}</span>
            </a>
        </div>

        <!-- Inventory Management Section -->
        <div class="sidebar-section">
            <div class="sidebar-title" style="padding-left: var(--spacing-lg); padding-right: var(--spacing-lg);" aria-label="Inventory Management">
                <i class="bi bi-collection" style="margin-right: var(--spacing-sm);" aria-hidden="true"></i> {{ __('messages.inventory') }}
            </div>
            <a href="{{ route('admin.items.index') }}" class="sidebar-link @if (request()->routeIs('admin.items.*')) active @endif" title="{{ __('messages.items') }}" aria-current="@if (request()->routeIs('admin.items.*')) page @endif">
                <i class="bi bi-boxes" aria-hidden="true"></i>
                <span>{{ __('messages.items') }}</span>
            </a>
            <a href="{{ route('admin.categories.index') }}" class="sidebar-link @if (request()->routeIs('admin.categories.*')) active @endif" title="{{ __('messages.categories') }}" aria-current="@if (request()->routeIs('admin.categories.*')) page @endif">
                <i class="bi bi-tags" aria-hidden="true"></i>
                <span>{{ __('messages.categories') }}</span>
            </a>
            <a href="{{ route('admin.locations.index') }}" class="sidebar-link @if (request()->routeIs('admin.locations.*')) active @endif" title="{{ __('messages.locations') }}" aria-current="@if (request()->routeIs('admin.locations.*')) page @endif">
                <i class="bi bi-geo-alt" aria-hidden="true"></i>
                <span>{{ __('messages.locations') }}</span>
            </a>
            <a href="{{ route('admin.stock.in') }}" class="sidebar-link @if (request()->routeIs('admin.stock.*')) active @endif" title="Stock Management" aria-current="@if (request()->routeIs('admin.stock.*')) page @endif" style="display: none;">
                <i class="bi bi-arrow-left-right" aria-hidden="true"></i>
                <span>Stock Management</span>
            </a>
        </div>

        <!-- Loan Management Section -->
        <div class="sidebar-section">
            <div class="sidebar-title" style="padding-left: var(--spacing-lg); padding-right: var(--spacing-lg);" aria-label="Loan Management">
                <i class="bi bi-clipboard-check" style="margin-right: var(--spacing-sm);" aria-hidden="true"></i> {{ __('messages.loans') }}
            </div>
            <a href="{{ route('admin.loans.index', ['status' => 'Menunggu Persetujuan']) }}" class="sidebar-link @if (request()->routeIs('admin.loans.*') && request()->get('status') === 'Menunggu Persetujuan') active @endif" title="{{ __('messages.pending_approval_link') }}" aria-current="@if (request()->routeIs('admin.loans.*') && request()->get('status') === 'Menunggu Persetujuan') page @endif">
                <i class="bi bi-hourglass-split" aria-hidden="true"></i>
                <span>{{ __('messages.pending_approval_link') }}</span>
            </a>
            <a href="{{ route('admin.loans.index') }}" class="sidebar-link @if (request()->routeIs('admin.loans.*') && !request()->get('status')) active @endif" title="{{ __('messages.all_loans') }}" aria-current="@if (request()->routeIs('admin.loans.*') && !request()->get('status')) page @endif">
                <i class="bi bi-clock-history" aria-hidden="true"></i>
                <span>{{ __('messages.all_loans') }}</span>
            </a>
        </div>

        <!-- Return Management Section -->
        <div class="sidebar-section">
            <div class="sidebar-title" style="padding-left: var(--spacing-lg); padding-right: var(--spacing-lg);" aria-label="Return Management">
                <i class="bi bi-box-arrow-down" style="margin-right: var(--spacing-sm);" aria-hidden="true"></i> {{ __('messages.returns') }}
            </div>
            <a href="{{ route('admin.returns.index', ['status' => 'Menunggu Verifikasi']) }}" class="sidebar-link @if (request()->routeIs('admin.returns.*') && request()->get('status') === 'Menunggu Verifikasi') active @endif" title="{{ __('messages.pending_verification_link') }}" aria-current="@if (request()->routeIs('admin.returns.*') && request()->get('status') === 'Menunggu Verifikasi') page @endif">
                <i class="bi bi-check-circle" aria-hidden="true"></i>
                <span>{{ __('messages.pending_verification_link') }}</span>
            </a>
            <a href="{{ route('admin.returns.index') }}" class="sidebar-link @if (request()->routeIs('admin.returns.*') && !request()->get('status')) active @endif" title="{{ __('messages.all_loans') }}" aria-current="@if (request()->routeIs('admin.returns.*') && !request()->get('status')) page @endif">
                <i class="bi bi-list-check" aria-hidden="true"></i>
                <span>{{ __('messages.all_loans') }}</span>
            </a>
        </div>

        <!-- Reporting Section -->
        <div class="sidebar-section">
            <div class="sidebar-title" style="padding-left: var(--spacing-lg); padding-right: var(--spacing-lg);" aria-label="Reports">
                <i class="bi bi-graph-up" style="margin-right: var(--spacing-sm);" aria-hidden="true"></i> {{ __('messages.reports') }}
            </div>
            <a href="{{ route('admin.reports.dashboard') }}" class="sidebar-link @if (request()->routeIs('admin.reports.dashboard')) active @endif" title="Report Dashboard" aria-current="@if (request()->routeIs('admin.reports.dashboard')) page @endif">
                <i class="bi bi-speedometer2" aria-hidden="true"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.reports.loans') }}" class="sidebar-link @if (request()->routeIs('admin.reports.loans')) active @endif" title="Loan Reports" aria-current="@if (request()->routeIs('admin.reports.loans')) page @endif">
                <i class="bi bi-file-earmark-text" aria-hidden="true"></i>
                <span>Loan Reports</span>
            </a>
            <a href="{{ route('admin.reports.inventory') }}" class="sidebar-link @if (request()->routeIs('admin.reports.inventory')) active @endif" title="Inventory Reports" aria-current="@if (request()->routeIs('admin.reports.inventory')) page @endif">
                <i class="bi bi-archive" aria-hidden="true"></i>
                <span>Inventory Reports</span>
            </a>
            <a href="{{ route('admin.reports.stock-movement') }}" class="sidebar-link @if (request()->routeIs('admin.reports.stock-movement')) active @endif" title="Stock Movement" aria-current="@if (request()->routeIs('admin.reports.stock-movement')) page @endif" style="display: none;">
                <i class="bi bi-arrow-left-right" aria-hidden="true"></i>
                <span>Stock Movement</span>
            </a>
            <a href="{{ route('admin.reports.overdue') }}" class="sidebar-link @if (request()->routeIs('admin.reports.overdue')) active @endif" title="Overdue Loans" aria-current="@if (request()->routeIs('admin.reports.overdue')) page @endif">
                <i class="bi bi-exclamation-triangle" aria-hidden="true"></i>
                <span>Overdue Loans</span>
            </a>
        </div>

    @else
        <!-- Staff Sidebar -->
        <div class="sidebar-section">
            <a href="{{ route('staff.dashboard') }}" class="sidebar-link @if (request()->routeIs('staff.dashboard')) active @endif" title="{{ __('messages.dashboard') }}" aria-current="@if (request()->routeIs('staff.dashboard')) page @endif">
                <i class="bi bi-speedometer2" aria-hidden="true"></i>
                <span>{{ __('messages.dashboard') }}</span>
            </a>
        </div>

        <!-- Loans -->
        <div class="sidebar-section">
            <div class="sidebar-title" style="padding-left: var(--spacing-lg); padding-right: var(--spacing-lg);" aria-label="Loans">
                <i class="bi bi-hand-thumbs-up" style="margin-right: var(--spacing-sm);" aria-hidden="true"></i> {{ __('messages.loans') }}
            </div>
            <a href="{{ route('staff.items.index') }}" class="sidebar-link @if (request()->routeIs('staff.items.*')) active @endif" title="{{ __('messages.items') }}" aria-current="@if (request()->routeIs('staff.items.*')) page @endif">
                <i class="bi bi-search" aria-hidden="true"></i>
                <span>{{ __('messages.items') }}</span>
            </a>
            <a href="{{ route('staff.loans.create') }}" class="sidebar-link @if (request()->routeIs('staff.loans.create')) active @endif" title="{{ __('messages.loans') }}" aria-current="@if (request()->routeIs('staff.loans.create')) page @endif">
                <i class="bi bi-plus-circle" aria-hidden="true"></i>
                <span>{{ __('messages.loans') }}</span>
            </a>
            <a href="{{ route('staff.loans.index') }}" class="sidebar-link @if (request()->routeIs('staff.loans.*')) active @endif" title="{{ __('messages.loans') }}" aria-current="@if (request()->routeIs('staff.loans.*')) page @endif">
                <i class="bi bi-clock-history" aria-hidden="true"></i>
                <span>{{ __('messages.loans') }}</span>
            </a>
        </div>

        <!-- Returns Section -->
        <div class="sidebar-section">
            <div class="sidebar-title" style="padding-left: var(--spacing-lg); padding-right: var(--spacing-lg);" aria-label="Returns">
                <i class="bi bi-box-arrow-down" style="margin-right: var(--spacing-sm);" aria-hidden="true"></i> {{ __('messages.returns') }}
            </div>
            <a href="{{ route('staff.returns.create') }}" class="sidebar-link @if (request()->routeIs('staff.returns.create')) active @endif" title="{{ __('messages.returns') }}" aria-current="@if (request()->routeIs('staff.returns.create')) page @endif">
                <i class="bi bi-box-arrow-down" aria-hidden="true"></i>
                <span>{{ __('messages.returns') }}</span>
            </a>
            <a href="{{ route('staff.returns.index') }}" class="sidebar-link @if (request()->routeIs('staff.returns.*')) active @endif" title="{{ __('messages.returns') }}" aria-current="@if (request()->routeIs('staff.returns.*')) page @endif">
                <i class="bi bi-list-check" aria-hidden="true"></i>
                <span>{{ __('messages.returns') }}</span>
            </a>
        </div>
    @endif
</div>

<!-- Sidebar Backdrop Overlay for Mobile -->
<div class="sidebar-backdrop" id="sidebarBackdrop"></div>

<!-- Sidebar Toggle Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('sidebarBackdrop');
    const navbarToggle = document.querySelector('.navbar-toggle');

    // Toggle sidebar on mobile
    if (navbarToggle) {
        navbarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            sidebar.classList.toggle('active');
            backdrop.classList.toggle('active');
            navbarToggle.classList.toggle('active');
        });
    }

    // Close sidebar when clicking on backdrop
    if (backdrop) {
        backdrop.addEventListener('click', function() {
            sidebar.classList.remove('active');
            backdrop.classList.remove('active');
            if (navbarToggle) navbarToggle.classList.remove('active');
        });
    }

    // Close sidebar when clicking on a link
    const sidebarLinks = document.querySelectorAll('.sidebar-link');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 768) {
                sidebar.classList.remove('active');
                backdrop.classList.remove('active');
                if (navbarToggle) navbarToggle.classList.remove('active');
            }
        });
    });

    // Close sidebar on window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth > 768) {
            sidebar.classList.remove('active');
            backdrop.classList.remove('active');
            if (navbarToggle) navbarToggle.classList.remove('active');
        }
    });
});
</script>
