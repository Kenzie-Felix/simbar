<nav class="navbar" role="navigation" aria-label="Main navbar">
    <div style="display: flex; align-items: center; width: 100%; padding: 0 var(--spacing-lg);">
        <!-- Hamburger Menu Toggle (Mobile) -->
        <button class="navbar-toggle" id="navbarToggle" title="Toggle menu" aria-label="Toggle navigation menu" aria-expanded="false" aria-controls="sidebar">
            <i class="bi bi-list" aria-hidden="true"></i>
        </button>

        <!-- Brand with Logo -->
        <div class="navbar-brand" style="font-size: 1.5rem; font-weight: var(--font-weight-bold); letter-spacing: -0.5px; margin-left: var(--spacing-md);">
            <a href="{{ route('dashboard') }}" style="text-decoration: none; display: flex; align-items: center; gap: var(--spacing-sm);">
                <img src="{{ asset('logo-simbar.svg') }}" alt="SIMBAR Logo" style="width: 32px; height: 32px; display: block;" />
                <span style="background: linear-gradient(135deg, var(--primary) 0%, #1e40af 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">SIMBAR</span>
            </a>
        </div>

        <!-- User Menu (Right aligned) -->
        <div class="navbar-menu" style="margin-left: auto;">
            <div class="navbar-user" onclick="toggleUserMenu()" role="button" tabindex="0" style="cursor: pointer; padding: var(--spacing-sm) var(--spacing-md); border-radius: var(--radius-md); transition: all 0.3s ease;" aria-label="User menu" aria-expanded="false" aria-haspopup="true">
                <i class="bi bi-person-circle" style="font-size: 1.5rem; color: var(--primary);" aria-hidden="true"></i>
                <span style="font-weight: var(--font-weight-medium); color: var(--gray-800);">{{ auth()->user()->name }}</span>
                <i class="bi bi-chevron-down" style="font-size: 0.75rem; color: var(--gray-600); transition: transform 0.3s ease;" aria-hidden="true"></i>
                
                <!-- Dropdown Menu - Modern -->
                <div id="userMenu" style="display: none; position: absolute; top: 70px; right: 20px; background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-lg); box-shadow: var(--shadow-lg); z-index: var(--z-dropdown); min-width: 220px; overflow: hidden;" role="menu">
                    <div style="padding: var(--spacing-md) var(--spacing-lg); border-bottom: 1px solid var(--gray-100); background: linear-gradient(135deg, var(--primary-light) 0%, white 100%);">
                        <small style="color: var(--gray-500); font-weight: var(--font-weight-medium); text-transform: uppercase; letter-spacing: 0.5px;">Role</small>
                        <div style="color: var(--primary); font-weight: var(--font-weight-semibold); margin-top: 2px;">{{ auth()->user()->role === 'Admin' ? 'Administrator' : 'Staff Member' }}</div>
                    </div>
                    <a href="{{ route('dashboard') }}" style="display: flex; align-items: center; gap: var(--spacing-md); padding: var(--spacing-md) var(--spacing-lg); color: var(--gray-700); text-decoration: none; transition: all 0.2s ease; border-bottom: 1px solid var(--gray-100);" role="menuitem">
                        <i class="bi bi-speedometer2" style="color: var(--primary); font-size: 1.1rem;" aria-hidden="true"></i> 
                        <span>Dashboard</span>
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" style="display: flex; align-items: center; gap: var(--spacing-md); padding: var(--spacing-md) var(--spacing-lg); color: var(--gray-700); background: none; border: none; cursor: pointer; width: 100%; text-align: left; transition: all 0.2s ease; font-weight: var(--font-weight-medium);" role="menuitem">
                            <i class="bi bi-box-arrow-right" style="color: var(--danger); font-size: 1.1rem;" aria-hidden="true"></i> 
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
.navbar-user:hover {
    background-color: var(--primary-light);
}

.navbar-user:hover i:last-child {
    transform: rotate(180deg);
}

#userMenu a:hover,
#userMenu button:hover {
    background-color: var(--primary-light);
    color: var(--primary);
    padding-left: calc(var(--spacing-lg) + var(--spacing-sm));
}

@media (min-width: 769px) {
    .navbar-toggle {
        display: none !important;
    }
}
</style>

<script>
function toggleUserMenu() {
    const menu = document.getElementById('userMenu');
    const userDiv = event.target.closest('.navbar-user');
    const chevron = userDiv.querySelector('i:last-child');
    const isOpen = menu.style.display !== 'none';
    
    menu.style.display = isOpen ? 'none' : 'block';
    userDiv.setAttribute('aria-expanded', !isOpen);
    
    if (!isOpen) {
        chevron.style.transform = 'rotate(180deg)';
        // Focus first menu item
        setTimeout(() => {
            const firstMenuItem = menu.querySelector('[role="menuitem"]');
            if (firstMenuItem) firstMenuItem.focus();
        }, 0);
    } else {
        chevron.style.transform = 'rotate(0deg)';
    }
}

// Keyboard navigation for user menu
document.addEventListener('keydown', function(event) {
    const userDiv = event.target.closest('.navbar-user');
    if (!userDiv) return;

    if (event.key === 'Enter' || event.key === ' ') {
        event.preventDefault();
        toggleUserMenu();
    } else if (event.key === 'Escape') {
        const menu = document.getElementById('userMenu');
        menu.style.display = 'none';
        userDiv.setAttribute('aria-expanded', 'false');
        userDiv.focus();
    }
});

// Close menu when clicking outside
document.addEventListener('click', function(event) {
    const menu = document.getElementById('userMenu');
    const userDiv = event.target.closest('.navbar-user');
    if (!userDiv && menu && menu.style.display !== 'none') {
        menu.style.display = 'none';
        const navUser = document.querySelector('.navbar-user');
        if (navUser) navUser.setAttribute('aria-expanded', 'false');
    }
});

// Mobile hamburger menu
document.addEventListener('DOMContentLoaded', function() {
    const navbarToggle = document.getElementById('navbarToggle');
    if (navbarToggle) {
        navbarToggle.addEventListener('click', function(e) {
            e.preventDefault();
            const sidebar = document.getElementById('sidebar');
            const backdrop = document.getElementById('sidebarBackdrop');
            if (sidebar && backdrop) {
                const isOpen = sidebar.classList.toggle('active');
                backdrop.classList.toggle('active');
                navbarToggle.classList.toggle('active');
                navbarToggle.setAttribute('aria-expanded', isOpen);
            }
        });

        // Keyboard support for hamburger
        navbarToggle.addEventListener('keydown', function(e) {
            if (e.key === 'Enter' || e.key === ' ') {
                e.preventDefault();
                this.click();
            }
        });
    }
});
</script>
