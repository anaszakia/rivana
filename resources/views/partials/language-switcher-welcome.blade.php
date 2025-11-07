<!-- Language Switcher for Welcome Page - Fixed Top Right -->
<div class="fixed top-6 right-6 z-50" x-data="{ open: false }">
    <button 
        @click="open = !open" 
        @click.away="open = false"
        class="flex items-center space-x-3 px-5 py-3 rounded-2xl bg-white/90 backdrop-blur-md border-2 border-blue-200 hover:border-blue-400 text-gray-800 transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105"
        title="Change Language"
    >
        <i class="fas fa-language text-2xl text-blue-600"></i>
        <div class="flex flex-col items-start">
            <span class="text-xs text-gray-500 font-medium uppercase">Language</span>
            <span class="text-sm font-bold">
                @if(app()->getLocale() === 'en')
                    English 🇬🇧
                @else
                    Indonesia 🇮🇩
                @endif
            </span>
        </div>
        <i class="fas fa-chevron-down text-sm text-gray-500 transition-transform duration-200" 
           :class="{ 'rotate-180': open }"></i>
    </button>
    
    <!-- Dropdown Menu -->
    <div 
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95 -translate-y-2"
        x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 transform scale-95 -translate-y-2"
        class="absolute right-0 mt-3 w-72 bg-white/95 backdrop-blur-xl rounded-2xl shadow-2xl border-2 border-gray-100 py-3 overflow-hidden"
        style="display: none;"
    >
        <div class="px-5 py-3 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-cyan-50">
            <p class="text-xs font-bold text-gray-600 uppercase tracking-wider flex items-center">
                <i class="fas fa-globe-americas mr-2 text-blue-600"></i>
                Choose Your Language
            </p>
        </div>
        
        <!-- Indonesian -->
        <form action="{{ route('language.switch') }}" method="POST" class="block">
            @csrf
            <input type="hidden" name="locale" value="id">
            <button 
                type="submit" 
                class="w-full text-left px-5 py-4 text-sm hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 transition-all duration-200 flex items-center space-x-4 group {{ app()->getLocale() === 'id' ? 'bg-gradient-to-r from-blue-100 to-cyan-100' : '' }}"
            >
                <span class="text-3xl group-hover:scale-110 transition-transform duration-200">🇮🇩</span>
                <div class="flex-1">
                    <div class="font-bold text-gray-800 group-hover:text-blue-600 transition-colors {{ app()->getLocale() === 'id' ? 'text-blue-700' : '' }}">
                        Bahasa Indonesia
                    </div>
                    <div class="text-xs text-gray-500 mt-0.5">Indonesia • Indonesian</div>
                </div>
                @if(app()->getLocale() === 'id')
                    <div class="flex items-center space-x-1">
                        <i class="fas fa-check-circle text-blue-600 text-lg animate-pulse"></i>
                        <span class="text-xs font-semibold text-blue-600">Active</span>
                    </div>
                @else
                    <i class="fas fa-arrow-right text-gray-300 group-hover:text-blue-400 group-hover:translate-x-1 transition-all duration-200"></i>
                @endif
            </button>
        </form>
        
        <!-- English -->
        <form action="{{ route('language.switch') }}" method="POST" class="block">
            @csrf
            <input type="hidden" name="locale" value="en">
            <button 
                type="submit" 
                class="w-full text-left px-5 py-4 text-sm hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 transition-all duration-200 flex items-center space-x-4 group {{ app()->getLocale() === 'en' ? 'bg-gradient-to-r from-blue-100 to-cyan-100' : '' }}"
            >
                <span class="text-3xl group-hover:scale-110 transition-transform duration-200">🇬🇧</span>
                <div class="flex-1">
                    <div class="font-bold text-gray-800 group-hover:text-blue-600 transition-colors {{ app()->getLocale() === 'en' ? 'text-blue-700' : '' }}">
                        English
                    </div>
                    <div class="text-xs text-gray-500 mt-0.5">United Kingdom • English</div>
                </div>
                @if(app()->getLocale() === 'en')
                    <div class="flex items-center space-x-1">
                        <i class="fas fa-check-circle text-blue-600 text-lg animate-pulse"></i>
                        <span class="text-xs font-semibold text-blue-600">Active</span>
                    </div>
                @else
                    <i class="fas fa-arrow-right text-gray-300 group-hover:text-blue-400 group-hover:translate-x-1 transition-all duration-200"></i>
                @endif
            </button>
        </form>
        
        <!-- Footer Info -->
        <div class="px-5 py-3 border-t border-gray-100 bg-gray-50">
            <p class="text-xs text-gray-500 flex items-center">
                <i class="fas fa-info-circle mr-2 text-blue-500"></i>
                Language will be saved to your session
            </p>
        </div>
    </div>
</div>

<style>
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.7;
    }
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}
</style>
