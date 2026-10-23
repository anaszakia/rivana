<!-- Google Translate Script Element -->
<div id="google_translate_element" style="display:none"></div>

<!-- Language Switcher Component -->
<div class="relative" x-data="{ open: false }" @click.away="open = false">
    <button 
        @click="open = !open" 
        type="button"
        class="flex items-center space-x-1.5 sm:space-x-2 px-2.5 sm:px-4 py-1.5 sm:py-2 rounded-lg bg-gradient-to-r from-teal-500 to-cyan-500 hover:from-teal-600 hover:to-cyan-600 text-white transition-all duration-200 shadow-md hover:shadow-lg"
        title="Ubah Bahasa / Change Language"
    >
        <i class="fas fa-language text-sm sm:text-lg"></i>
        <span class="text-xs sm:text-sm font-medium" data-lang-short>ID 🇮🇩</span>
        <i class="fas fa-chevron-down text-[10px] sm:text-xs transition-transform duration-200" 
           :class="{ 'rotate-180': open }"></i>
    </button>
    
    <!-- Dropdown Menu -->
    <div 
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95"
        x-transition:enter-end="opacity-100 transform scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100"
        x-transition:leave-end="opacity-0 transform scale-95"
        class="absolute right-0 mt-2 w-48 sm:w-56 bg-white rounded-xl shadow-xl border border-gray-200 py-2 z-[60] overflow-hidden"
    >
        <div class="px-3 sm:px-4 py-2 border-b border-gray-100">
            <p class="text-[10px] sm:text-xs font-semibold text-gray-500 uppercase tracking-wide">Pilih Bahasa</p>
        </div>
        
        <!-- Indonesian -->
        <button 
            type="button"
            onclick="window.changeLanguage('id')"
            @click="open = false"
            data-lang-opt="id"
            class="w-full text-left px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm hover:bg-gradient-to-r hover:from-teal-50 hover:to-cyan-50 transition-all duration-150 flex items-center space-x-2 sm:space-x-3 text-gray-700 group"
        >
            <span class="text-xl sm:text-2xl">🇮🇩</span>
            <div class="flex-1">
                <div class="font-medium">Bahasa Indonesia</div>
                <div class="text-[10px] sm:text-xs text-gray-500">Asli</div>
            </div>
            <i class="fas fa-check text-teal-600 text-xs" data-lang-check="id"></i>
        </button>
        
        <!-- English -->
        <button 
            type="button"
            onclick="window.changeLanguage('en')"
            @click="open = false"
            data-lang-opt="en"
            class="w-full text-left px-3 sm:px-4 py-2 sm:py-3 text-xs sm:text-sm hover:bg-gradient-to-r hover:from-teal-50 hover:to-cyan-50 transition-all duration-150 flex items-center space-x-2 sm:space-x-3 text-gray-700 group"
        >
            <span class="text-xl sm:text-2xl">🇬🇧</span>
            <div class="flex-1">
                <div class="font-medium">English</div>
                <div class="text-[10px] sm:text-xs text-gray-500">Google Translate</div>
            </div>
            <i class="fas fa-check text-teal-600 text-xs hidden" data-lang-check="en"></i>
        </button>
    </div>
</div>

<style>
/* Sembunyikan frame bawaan Google Translate agar layout tetap rapi */
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