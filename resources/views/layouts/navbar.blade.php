<nav class="navbar">
    <div style="display: flex; align-items: center; width: 100%; padding: 0 var(--spacing-xl);">
        <!-- Brand -->
        <div class="navbar-brand">
            <i class="bi bi-boxes"></i>
            <span>SIMBAR</span>
        </div>

        <!-- User Menu (Right aligned) -->
        <div class="navbar-menu">
            <div class="navbar-user" onclick="toggleUserMenu()">
                <i class="bi bi-person-circle" style="font-size: 1.5rem;"></i>
                <span>{{ auth()->user()->name }}</span>
                <i class="bi bi-chevron-down" style="font-size: 0.8rem;"></i>
                
                <!-- Dropdown Menu -->
                <div id="userMenu" style="display: none; position: absolute; top: 70px; right: 20px; background: white; border: 1px solid var(--gray-200); border-radius: var(--radius-md); box-shadow: var(--shadow-lg); z-index: var(--z-dropdown); min-width: 200px;">
                    <div style="padding: var(--spacing-md); border-bottom: 1px solid var(--gray-200);">
                        <small style="color: var(--gray-500);">Role: <strong>{{ auth()->user()->role }}</strong></small>
                    </div>
                    <a href="{{ route('dashboard') }}" style="display: flex; align-items: center; gap: var(--spacing-md); padding: var(--spacing-md); color: var(--gray-700); text-decoration: none; transition: all 0.2s ease;">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="margin: 0;">
                        @csrf
                        <button type="submit" style="display: flex; align-items: center; gap: var(--spacing-md); padding: var(--spacing-md); color: var(--gray-700); background: none; border: none; cursor: pointer; width: 100%; text-align: left; transition: all 0.2s ease;">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</nav>

<script>
function toggleUserMenu() {
    const menu = document.getElementById('userMenu');
    menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
}

// Close menu when clicking outside
document.addEventListener('click', function(event) {
    const menu = document.getElementById('userMenu');
    const userDiv = event.target.closest('.navbar-user');
    if (!userDiv) {
        menu.style.display = 'none';
    }
});
</script>
