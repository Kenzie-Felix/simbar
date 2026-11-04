<div class="sidebar">
    @if (auth()->user()->isAdmin())
        <!-- Admin Sidebar -->
        <div class="sidebar-section">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link @if (request()->routeIs('admin.dashboard')) active @endif">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </div>

        <!-- Inventory Management Section -->
        <div class="sidebar-section">
            <div class="sidebar-title">
                <i class="bi bi-collection"></i> Inventory
            </div>
            <a href="{{ route('admin.items.index') }}" class="sidebar-link @if (request()->routeIs('admin.items.*')) active @endif">
                <i class="bi bi-boxes"></i>
                <span>Items</span>
            </a>
            <a href="{{ route('admin.categories.index') }}" class="sidebar-link @if (request()->routeIs('admin.categories.*')) active @endif">
                <i class="bi bi-tags"></i>
                <span>Categories</span>
            </a>
            <a href="{{ route('admin.locations.index') }}" class="sidebar-link @if (request()->routeIs('admin.locations.*')) active @endif">
                <i class="bi bi-geo-alt"></i>
                <span>Locations</span>
            </a>
            <a href="{{ route('admin.stock.in') }}" class="sidebar-link @if (request()->routeIs('admin.stock.*')) active @endif">
                <i class="bi bi-arrow-left-right"></i>
                <span>Stock Management</span>
            </a>
        </div>

        <!-- Loan Management Section -->
        <div class="sidebar-section">
            <div class="sidebar-title">
                <i class="bi bi-clipboard-check"></i> Loans
            </div>
            <a href="{{ route('admin.loans.index', ['status' => 'Menunggu Persetujuan']) }}" class="sidebar-link @if (request()->routeIs('admin.loans.*') && request()->get('status') === 'Menunggu Persetujuan') active @endif">
                <i class="bi bi-hourglass-split"></i>
                <span>Pending Approvals</span>
            </a>
            <a href="{{ route('admin.loans.index') }}" class="sidebar-link @if (request()->routeIs('admin.loans.*') && !request()->get('status')) active @endif">
                <i class="bi bi-clock-history"></i>
                <span>All Loans</span>
            </a>
        </div>

        <!-- Return Management Section -->
        <div class="sidebar-section">
            <div class="sidebar-title">
                <i class="bi bi-box-arrow-down"></i> Returns
            </div>
            <a href="{{ route('admin.returns.index', ['status' => 'Menunggu Verifikasi']) }}" class="sidebar-link @if (request()->routeIs('admin.returns.*') && request()->get('status') === 'Menunggu Verifikasi') active @endif">
                <i class="bi bi-check-circle"></i>
                <span>Pending Verifications</span>
            </a>
            <a href="{{ route('admin.returns.index') }}" class="sidebar-link @if (request()->routeIs('admin.returns.*') && !request()->get('status')) active @endif">
                <i class="bi bi-list-check"></i>
                <span>All Returns</span>
            </a>
        </div>

        <!-- Reporting Section -->
        <div class="sidebar-section">
            <div class="sidebar-title">
                <i class="bi bi-graph-up"></i> Reports
            </div>
            <a href="{{ route('admin.reports.dashboard') }}" class="sidebar-link @if (request()->routeIs('admin.reports.dashboard')) active @endif">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
            <a href="{{ route('admin.reports.loans') }}" class="sidebar-link @if (request()->routeIs('admin.reports.loans')) active @endif">
                <i class="bi bi-file-earmark-text"></i>
                <span>Loan Reports</span>
            </a>
            <a href="{{ route('admin.reports.inventory') }}" class="sidebar-link @if (request()->routeIs('admin.reports.inventory')) active @endif">
                <i class="bi bi-archive"></i>
                <span>Inventory Reports</span>
            </a>
            <a href="{{ route('admin.reports.stock-movement') }}" class="sidebar-link @if (request()->routeIs('admin.reports.stock-movement')) active @endif">
                <i class="bi bi-arrow-left-right"></i>
                <span>Stock Movement</span>
            </a>
            <a href="{{ route('admin.reports.overdue') }}" class="sidebar-link @if (request()->routeIs('admin.reports.overdue')) active @endif">
                <i class="bi bi-exclamation-triangle"></i>
                <span>Overdue Loans</span>
            </a>
        </div>

    @else
        <!-- Staff Sidebar -->
        <div class="sidebar-section">
            <a href="{{ route('staff.dashboard') }}" class="sidebar-link @if (request()->routeIs('staff.dashboard')) active @endif">
                <i class="bi bi-speedometer2"></i>
                <span>Dashboard</span>
            </a>
        </div>

        <!-- Borrowing Section -->
        <div class="sidebar-section">
            <div class="sidebar-title">
                <i class="bi bi-hand-thumbs-up"></i> Borrowing
            </div>
            <a href="{{ route('staff.items.index') }}" class="sidebar-link @if (request()->routeIs('staff.items.*')) active @endif">
                <i class="bi bi-search"></i>
                <span>Browse Items</span>
            </a>
            <a href="{{ route('staff.loans.create') }}" class="sidebar-link @if (request()->routeIs('staff.loans.create')) active @endif">
                <i class="bi bi-plus-circle"></i>
                <span>Request Loan</span>
            </a>
            <a href="{{ route('staff.loans.index') }}" class="sidebar-link @if (request()->routeIs('staff.loans.*')) active @endif">
                <i class="bi bi-clock-history"></i>
                <span>My Loans</span>
            </a>
        </div>

        <!-- Returns Section -->
        <div class="sidebar-section">
            <div class="sidebar-title">
                <i class="bi bi-box-arrow-down"></i> Returns
            </div>
            <a href="{{ route('staff.returns.create') }}" class="sidebar-link @if (request()->routeIs('staff.returns.create')) active @endif">
                <i class="bi bi-box-arrow-down"></i>
                <span>Request Return</span>
            </a>
            <a href="{{ route('staff.returns.index') }}" class="sidebar-link @if (request()->routeIs('staff.returns.*')) active @endif">
                <i class="bi bi-list-check"></i>
                <span>My Returns</span>
            </a>
        </div>
    @endif
</div>
