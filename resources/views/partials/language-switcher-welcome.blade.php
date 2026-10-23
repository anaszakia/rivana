<!-- Google Translate Script Element -->
<div id="google_translate_element" style="display:none"></div>

<!-- Language Switcher for Welcome Page - Fixed Top Right -->
<div class="fixed top-4 right-4 sm:top-6 sm:right-6 z-50" x-data="{ open: false }">
    <button 
        @click="open = !open" 
        @click.away="open = false"
        type="button"
        class="flex items-center space-x-2 sm:space-x-3 px-3 py-2 sm:px-5 sm:py-3 rounded-xl sm:rounded-2xl bg-white/90 backdrop-blur-md border-2 border-blue-200 hover:border-blue-400 text-gray-800 transition-all duration-300 shadow-xl hover:shadow-2xl hover:scale-105"
        title="Ubah Bahasa / Change Language"
    >
        <i class="fas fa-language text-lg sm:text-2xl text-blue-600"></i>
        <div class="hidden sm:flex flex-col items-start">
            <span class="text-xs text-gray-500 font-medium uppercase">Bahasa</span>
            <span class="text-sm font-bold" data-lang-label>Indonesia 🇮🇩</span>
        </div>
        <!-- Mobile version - show flag only -->
        <span class="sm:hidden text-xl" data-lang-flag>🇮🇩</span>
        <i class="fas fa-chevron-down text-xs sm:text-sm text-gray-500 transition-transform duration-200" 
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
        class="absolute right-0 mt-3 w-64 sm:w-72 bg-white/95 backdrop-blur-xl rounded-xl sm:rounded-2xl shadow-2xl border-2 border-gray-100 py-2 sm:py-3 overflow-hidden"
        style="display: none;"
    >
        <div class="px-4 sm:px-5 py-2 sm:py-3 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-cyan-50">
            <p class="text-xs font-bold text-gray-600 uppercase tracking-wider flex items-center">
                <i class="fas fa-globe-americas mr-2 text-blue-600"></i>
                Pilih Bahasa
            </p>
        </div>
        
        <!-- Indonesian -->
        <button 
            type="button" 
            onclick="window.changeLanguage('id')"
            @click="open = false"
            data-lang-opt="id"
            class="w-full text-left px-4 sm:px-5 py-3 sm:py-4 text-sm hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 transition-all duration-200 flex items-center space-x-3 sm:space-x-4 group"
        >
            <span class="text-2xl sm:text-3xl group-hover:scale-110 transition-transform duration-200">🇮🇩</span>
            <div class="flex-1">
                <div class="font-bold text-sm sm:text-base text-gray-800 group-hover:text-blue-600 transition-colors">
                    Bahasa Indonesia
                </div>
                <div class="text-xs text-gray-500 mt-0.5">Indonesia • Asli</div>
            </div>
            <i class="fas fa-check-circle text-blue-600 text-base sm:text-lg" data-lang-check="id"></i>
        </button>
        
        <!-- English -->
        <button 
            type="button" 
            onclick="window.changeLanguage('en')"
            @click="open = false"
            data-lang-opt="en"
            class="w-full text-left px-4 sm:px-5 py-3 sm:py-4 text-sm hover:bg-gradient-to-r hover:from-blue-50 hover:to-cyan-50 transition-all duration-200 flex items-center space-x-3 sm:space-x-4 group"
        >
            <span class="text-2xl sm:text-3xl group-hover:scale-110 transition-transform duration-200">🇬🇧</span>
            <div class="flex-1">
                <div class="font-bold text-sm sm:text-base text-gray-800 group-hover:text-blue-600 transition-colors">
                    English
                </div>
                <div class="text-xs text-gray-500 mt-0.5">United Kingdom • English</div>
            </div>
            <i class="fas fa-check-circle text-blue-600 text-base sm:text-lg hidden" data-lang-check="en"></i>
        </button>
        
        <!-- Footer Info -->
        <div class="px-4 sm:px-5 py-2 sm:py-3 border-t border-gray-100 bg-gray-50">
            <p class="text-xs text-gray-500 flex items-center">
                <i class="fab fa-google mr-2 text-blue-500"></i>
                <span>Google Full-Page Translate</span>
            </p>
        </div>
    </div>
</div>

<style>
/* Sembunyikan frame bawaan Google Translate agar tampilan tetap bersih */
.goog-te-banner-frame.skiptranslate,
.goog-te-banner-frame,
iframe.goog-te-banner-frame {
    display: none !important;
    visibility: hidden !important;
    height: 0 !important;
}
body {
    top: 0px !important;
    position: static !important;
}
.goog-logo-link,
.goog-te-gadget span {
    display: none !important;
}
.goog-tooltip {
    display: none !important;
}
.goog-tooltip:hover {
    display: none !important;
}
.goog-text-highlight {
    background-color: transparent !important;
    box-shadow: none !important;
}
</style>
