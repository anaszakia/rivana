<!-- Language Switcher Component -->
<div class="relative" x-data="{ open: false }">
    <button 
        @click="open = !open" 
        @click.away="open = false"
        class="flex items-center space-x-2 px-4 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-cyan-500 hover:from-blue-600 hover:to-cyan-600 text-white transition-all duration-200 shadow-md hover:shadow-lg"
        title="Change Language"
    >
        <i class="fas fa-language text-lg"></i>
        <span class="text-sm font-medium">
            @if(app()->getLocale() === 'en')
                EN 🇬🇧
            @else
                ID 🇮🇩
            @endif
        </span>
        <i class="fas fa-chevron-down text-xs transition-transform duration-200" 
           :class="{ 'rotate-180': open }"></i>
    </button>
    
    <!-- Dropdown Menu -->
    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-200 py-2 z-50 overflow-hidden"
        style="display: none;"
    >
        <div class="px-4 py-2 border-b border-gray-100">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide">Select Language</p>
        </div>
        
        <!-- Indonesian -->
        <form action="{{ route('language.switch') }}" method="POST" class="block">
            @csrf
            <input type="hidden" name="locale" value="id">
            <button 
                type="submit" 
                class="w-full text-left px-4 py-3 text-sm hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 transition-all duration-150 flex items-center space-x-3 {{ app()->getLocale() === 'id' ? 'bg-gradient-to-r from-blue-100 to-cyan-100 text-blue-700 font-semibold' : 'text-gray-700' }}"
            >
                <span class="text-2xl">🇮🇩</span>
                <div class="flex-1">
                    <div class="font-medium">Bahasa Indonesia</div>
                    <div class="text-xs text-gray-500">Indonesian</div>
                </div>
                @if(app()->getLocale() === 'id')
                    <i class="fas fa-check-circle text-blue-600"></i>
                @endif
            </button>
        </form>
        
        <!-- English -->
        <form action="{{ route('language.switch') }}" method="POST" class="block">
            @csrf
            <input type="hidden" name="locale" value="en">
            <button 
                type="submit" 
                class="w-full text-left px-4 py-3 text-sm hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 transition-all duration-150 flex items-center space-x-3 {{ app()->getLocale() === 'en' ? 'bg-gradient-to-r from-blue-100 to-cyan-100 text-blue-700 font-semibold' : 'text-gray-700' }}"
            >
                <span class="text-2xl">🇬🇧</span>
                <div class="flex-1">
                    <div class="font-medium">English</div>
                    <div class="text-xs text-gray-500">English</div>
                </div>
                @if(app()->getLocale() === 'en')
                    <i class="fas fa-check-circle text-blue-600"></i>
                @endif
            </button>
        </form>
    </div>
</div>
