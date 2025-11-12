<!-- Modern Navigation Header -->
<header class="bg-white border-b border-gray-200 sticky top-0 z-50 shadow-sm" x-data="{ mobileMenuOpen: false }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <!-- Logo and Brand -->
            <div class="flex items-center gap-8">
                <div class="flex items-center gap-3">
                    <img src="/images/logo2.jpg" alt="Logo" class="w-10 h-10 rounded-lg object-cover">
                    <span class="text-xl font-bold text-gray-800">RIVANA</span>
                </div>
                
                <!-- Desktop Navigation Menu -->
                <nav class="hidden lg:flex items-center gap-1">
                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" 
                       class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        Dashboard
                    </a>
                    
                    <!-- Hidrologi -->
                    <a href="{{ route('hidrologi.index') }}" 
                       class="px-4 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('hidrologi.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                        Hidrologi
                    </a>
                </nav>
            </div>
            
            <!-- Right Side Content -->
            <div class="flex items-center gap-3">
                <!-- Language Switcher -->
                <div class="hidden sm:block">
                    @include('partials.language-switcher')
                </div>
                
                <!-- Mobile menu button -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" 
                        class="lg:hidden p-2 text-gray-600 hover:text-gray-900 hover:bg-gray-50 rounded-lg transition-colors">
                    <i class="fas fa-bars text-lg"></i>
                </button>
            </div>
        </div>
        
        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" 
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             x-cloak
             class="lg:hidden border-t border-gray-200"
             @click.away="mobileMenuOpen = false">
            <div class="px-4 py-3 space-y-1">
                <!-- Dashboard Mobile -->
                <a href="{{ route('dashboard') }}" 
                   class="block px-4 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('dashboard') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    Dashboard
                </a>
                
                <!-- Hidrologi Mobile -->
                <a href="{{ route('hidrologi.index') }}" 
                   class="block px-4 py-2 text-sm font-semibold rounded-lg transition-colors {{ request()->routeIs('hidrologi.*') ? 'bg-blue-50 text-blue-600' : 'text-gray-600 hover:text-gray-900 hover:bg-gray-50' }}">
                    Hidrologi
                </a>
                
                <!-- Language Switcher Mobile -->
                <div class="pt-3 border-t border-gray-200 sm:hidden">
                    @include('partials.language-switcher')
                </div>
            </div>
        </div>
    </div>
    
    <!-- Back to Welcome Floating Button -->
    <div class="fixed bottom-6 right-6 z-50 group">
        <a href="{{ route('welcome') }}" 
           class="flex items-center justify-center w-12 h-12 bg-blue-600 hover:bg-blue-700 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-200">
            <i class="fas fa-home text-sm"></i>
        </a>
        <div class="absolute bottom-full mb-2 right-0 bg-gray-800 text-white text-xs py-1.5 px-3 rounded-lg opacity-0 group-hover:opacity-100 transition-opacity duration-200 whitespace-nowrap">
            Kembali ke Beranda
        </div>
    </div>
</header>