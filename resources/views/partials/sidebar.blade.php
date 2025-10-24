<!-- Sidebar Overlay for Mobile & Tablet -->
<div id="sidebar-overlay" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden hidden transition-opacity duration-300"></div>

<!-- Mobile/Tablet Header Bar (Fixed Top) -->
<div class="lg:hidden fixed top-0 left-0 right-0 h-16 bg-white border-b border-gray-200 shadow-md z-30 flex items-center justify-between px-4">
    <button id="sidebar-toggle" class="p-2.5 text-gray-600 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-all duration-200">
        <i class="fas fa-bars text-xl"></i>
    </button>
    <div class="flex items-center space-x-3">
        <img src="/images/logo2.jpg" alt="Logo" class="w-9 h-9 rounded-lg object-cover border border-gray-200 shadow-sm">
        <span class="text-lg font-bold text-gray-800">RIVANA</span>
    </div>
    <div class="w-10"></div> <!-- Spacer for centering -->
</div>

<!-- Sidebar -->
<div id="sidebar" class="fixed inset-y-0 left-0 z-50 w-72 sm:w-80 md:w-72 bg-white shadow-2xl transform -translate-x-full transition-all duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 lg:w-64 flex flex-col border-r border-gray-200 overflow-hidden lg:mt-0 mt-16">
    <!-- Sidebar Header (Desktop Only) -->
    <div class="hidden lg:flex items-center justify-between h-16 px-4 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
        <div class="flex items-center space-x-3">
            <img src="/images/logo2.jpg" alt="Logo" class="w-8 h-8 rounded-lg object-cover border border-gray-200 shadow-md">
            <span class="text-lg font-bold text-gray-800 sidebar-text">RIVANA</span>
        </div>
        <div class="flex items-center">
            <!-- Desktop Toggle Button -->
            <button id="desktop-toggle" class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-lg transition-all duration-200">
                <i class="fas fa-chevron-left text-sm transition-transform duration-200" id="toggle-icon"></i>
            </button>
        </div>
    </div>
    
    <!-- Mobile/Tablet Header (Inside Sidebar) -->
    <div class="lg:hidden flex items-center justify-between h-16 px-4 sm:px-6 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-cyan-50">
        <div class="flex items-center space-x-3">
            <img src="/images/logo2.jpg" alt="Logo" class="w-10 h-10 sm:w-12 sm:h-12 rounded-lg object-cover border border-gray-200 shadow-md">
            <span class="text-lg sm:text-xl font-bold text-gray-800">RIVANA</span>
        </div>
        <button id="sidebar-close" class="p-2 sm:p-2.5 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition-all duration-200">
            <i class="fas fa-times text-lg sm:text-xl"></i>
        </button>
    </div>
    
    <!-- Navigation -->
    <nav class="flex-1 px-3 sm:px-5 lg:px-4 py-4 sm:py-6 overflow-y-auto">
        <ul class="space-y-2 sm:space-y-3">
            <!-- Dashboard - Available to all authenticated users -->
            <li>
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center px-3 sm:px-4 py-3 sm:py-3.5 text-sm sm:text-base font-medium text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group shadow-sm hover:shadow-md">
                    <i class="fas fa-home w-5 h-5 sm:w-6 sm:h-6 mr-3 text-gray-500 group-hover:text-blue-500"></i>
                    <span class="sidebar-text">Dashboard</span>
                </a>
            </li>
            
            <!-- Management Section - Always show since no authentication -->
            {{-- @if(true)
                <li class="relative">
                    <button onclick="toggleDropdown()" class="flex items-center justify-between w-full px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group">
                        <div class="flex items-center">
                            <i class="fas fa-users-cog w-5 h-5 mr-3 text-gray-500 group-hover:text-blue-500"></i>
                            <span class="sidebar-text">Management</span>
                        </div>
                        <i class="fas fa-chevron-down text-xs text-gray-400 group-hover:text-blue-500 transition-all duration-200 sidebar-arrow" id="dropdown-arrow"></i>
                    </button>
                    
                    <!-- Dropdown Content -->
                    <div id="dropdown-content" class="hidden mt-1 ml-8 space-y-1 dropdown-content">
                        @can('view users')
                            <a href="{{ route('users.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-md hover:bg-gray-100 hover:text-blue-600 transition-all duration-200">
                                <i class="fas fa-users w-4 h-4 mr-3"></i>
                                <span class="sidebar-text">Users</span>
                            </a>
                        @endcan
                        
                        @can('view audit logs')
                            <a href="{{ route('audit.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-md hover:bg-gray-100 hover:text-blue-600 transition-all duration-200">
                                <i class="fas fa-clipboard-check w-4 h-4 mr-3"></i>
                                <span class="sidebar-text">Audit Log</span>
                            </a>
                        @endcan
                        
                        @can('view roles')
                            <a href="{{ route('roles.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-md hover:bg-gray-100 hover:text-blue-600 transition-all duration-200">
                                <i class="fas fa-user-shield w-4 h-4 mr-3"></i>
                                <span class="sidebar-text">Roles</span>
                            </a>
                        @endcan
                        
                        @can('view permissions')
                            <a href="{{ route('permissions.index') }}" class="flex items-center px-3 py-2 text-sm text-gray-600 rounded-md hover:bg-gray-100 hover:text-blue-600 transition-all duration-200">
                                <i class="fas fa-key w-4 h-4 mr-3"></i>
                                <span class="sidebar-text">Permissions</span>
                            </a>
                        @endcan
                    </div>
                </li>
            @endif --}}
            
            <!-- Hidrologi - Available to users with view hidrologi permission -->
            @can('view hidrologi')
                <li>
                    <a href="{{ route('hidrologi.index') }}" class="flex items-center px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group {{ request()->routeIs('hidrologi.*') ? 'bg-blue-50 text-blue-600' : '' }}">
                        <i class="fas fa-water w-5 h-5 mr-3 text-gray-500 group-hover:text-blue-500 {{ request()->routeIs('hidrologi.*') ? 'text-blue-500' : '' }}"></i>
                        <span class="sidebar-text">Hidrologi</span>
                    </a>
                </li>
            @endcan
            
            <!-- Reports - Only for super admin or users with specific permissions -->
            {{-- @hasrole('super_admin')
                <li>
                    <a href="#" class="flex items-center px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group">
                        <i class="fas fa-chart-line w-5 h-5 mr-3 text-gray-500 group-hover:text-blue-500"></i>
                        <span class="sidebar-text">Reports</span>
                    </a>
                </li>
            @endhasrole --}}
            
            <!-- Profile - Available to all users with edit profile permission -->
            {{-- @can('edit profile')
                <li>
                    <a href="{{ route('profile.edit') }}" class="flex items-center px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group">
                        <i class="fas fa-user-edit w-5 h-5 mr-3 text-gray-500 group-hover:text-blue-500"></i>
                        <span class="sidebar-text">My Profile</span>
                    </a>
                </li>
            @endcan --}}
            
            <!-- Settings - Super Admin only -->
            {{-- @hasrole('super_admin')
                <li>
                    <a href="#" class="flex items-center px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group">
                        <i class="fas fa-cog w-5 h-5 mr-3 text-gray-500 group-hover:text-blue-500"></i>
                        <span class="sidebar-text">Settings</span>
                    </a>
                </li>
            @endhasrole --}}
            
            <!-- Help - Available to all users -->
            {{-- <li>
                <a href="#" class="flex items-center px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-blue-50 hover:text-blue-600 transition-all duration-200 group">
                    <i class="fas fa-question-circle w-5 h-5 mr-3 text-gray-500 group-hover:text-blue-500"></i>
                    <span class="sidebar-text">Help</span>
                </a>
            </li> --}}
        </ul>
    </nav>
    
    <!-- Back to Welcome Button -->
    <div class="border-t border-gray-200 p-4 sm:p-5 lg:p-4 mt-auto bg-gray-50">
        <a href="{{ route('welcome') }}" class="flex items-center justify-center px-4 sm:px-5 py-3 sm:py-4 text-sm sm:text-base font-semibold text-white bg-gradient-to-r from-purple-600 to-blue-600 rounded-xl hover:from-purple-700 hover:to-blue-700 transition-all duration-200 shadow-lg hover:shadow-xl hover:scale-105 group">
            <i class="fas fa-home w-5 h-5 sm:w-6 sm:h-6 mr-2 sm:mr-3 group-hover:scale-110 transition-transform duration-200"></i>
            <span class="sidebar-text">Kembali ke Beranda</span>
        </a>
    </div>
</div>

<style>
.sidebar-collapsed {
    width: 4rem !important;
}
.sidebar-collapsed .sidebar-text {
    display: none;
}
.sidebar-collapsed .sidebar-arrow {
    display: none;
}
.sidebar-collapsed .dropdown-content {
    display: none !important;
}
.sidebar-collapsed #user-dropdown-content {
    display: none !important;
}
</style>

<script>
// Toggle dropdown function
function toggleDropdown() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar.classList.contains('sidebar-collapsed')) {
        return; // Don't open dropdown when sidebar is collapsed
    }
    
    const dropdown = document.getElementById('dropdown-content');
    const arrow = document.getElementById('dropdown-arrow');
    
    dropdown.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
}

// Toggle user dropdown function
function toggleUserDropdown() {
    const sidebar = document.getElementById('sidebar');
    if (sidebar.classList.contains('sidebar-collapsed')) {
        return; // Don't open dropdown when sidebar is collapsed
    }
    
    const dropdown = document.getElementById('user-dropdown-content');
    const arrow = document.getElementById('user-dropdown-arrow');
    
    dropdown.classList.toggle('hidden');
    arrow.classList.toggle('rotate-180');
}

// Sidebar toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebar-toggle');
    const sidebarClose = document.getElementById('sidebar-close');
    const sidebarOverlay = document.getElementById('sidebar-overlay');
    const desktopToggle = document.getElementById('desktop-toggle');
    const toggleIcon = document.getElementById('toggle-icon');
    
    // Mobile toggle sidebar
    sidebarToggle?.addEventListener('click', function(e) {
        e.stopPropagation();
        sidebar.classList.remove('-translate-x-full');
        sidebarOverlay.classList.remove('hidden');
    });
    
    // Desktop toggle sidebar (collapse/expand) - LANGSUNG KLIK
    desktopToggle?.addEventListener('click', function(e) {
        e.stopPropagation();
        
        if (sidebar.classList.contains('sidebar-collapsed')) {
            // Expand sidebar
            sidebar.classList.remove('sidebar-collapsed');
            toggleIcon.classList.remove('rotate-180');
        } else {
            // Collapse sidebar
            sidebar.classList.add('sidebar-collapsed');
            toggleIcon.classList.add('rotate-180');
            
            // Close any open dropdowns when collapsing
            document.getElementById('dropdown-content')?.classList.add('hidden');
            document.getElementById('user-dropdown-content')?.classList.add('hidden');
            document.getElementById('dropdown-arrow')?.classList.remove('rotate-180');
            document.getElementById('user-dropdown-arrow')?.classList.remove('rotate-180');
        }
    });
    
    // Close sidebar function for mobile
    function closeSidebar() {
        sidebar.classList.add('-translate-x-full');
        sidebarOverlay.classList.add('hidden');
    }
    
    sidebarClose?.addEventListener('click', function(e) {
        e.stopPropagation();
        closeSidebar();
    });
    
    sidebarOverlay?.addEventListener('click', closeSidebar);
    
    // Close sidebar when clicking outside on mobile
    document.addEventListener('click', function(event) {
        const isLargeScreen = window.innerWidth >= 1024;
        
        if (!isLargeScreen && !sidebar.contains(event.target) && 
            event.target !== sidebarToggle && !sidebarToggle?.contains(event.target)) {
            closeSidebar();
        }
    });
    
    // Close dropdowns when clicking outside
    document.addEventListener('click', function(event) {
        const dropdown = document.getElementById('dropdown-content');
        const userDropdown = document.getElementById('user-dropdown-content');
        
        if (!event.target.closest('.relative') && dropdown && !dropdown.classList.contains('hidden')) {
            dropdown.classList.add('hidden');
            document.getElementById('dropdown-arrow')?.classList.remove('rotate-180');
        }
        
        if (!event.target.closest('.border-t') && userDropdown && !userDropdown.classList.contains('hidden')) {
            userDropdown.classList.add('hidden');
            document.getElementById('user-dropdown-arrow')?.classList.remove('rotate-180');
        }
    });
    
    // Handle window resize
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 1024) {
            // Desktop mode
            sidebarOverlay.classList.add('hidden');
        } else {
            // Mobile mode - reset collapsed state
            if (sidebar.classList.contains('sidebar-collapsed')) {
                sidebar.classList.remove('sidebar-collapsed');
                toggleIcon.classList.remove('rotate-180');
            }
        }
    });
});
</script>