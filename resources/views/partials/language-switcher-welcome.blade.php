<!-- Google Translate Script Element -->
<div id="google_translate_element" style="display:none"></div>

<!-- Language Switcher Component for Navbar -->
<div class="relative" x-data="{ open: false }" @click.away="open = false">
    <button 
        @click="open = !open" 
        type="button"
        class="flex items-center gap-1.5 sm:gap-2 px-3 py-2 rounded-xl bg-white/10 hover:bg-white/15 text-white border border-white/15 backdrop-blur-md transition-all duration-200 text-xs sm:text-sm font-semibold"
        title="Ubah Bahasa / Change Language"
    >
        <i class="fas fa-language text-base text-teal-300"></i>
        <span class="hidden sm:inline" data-lang-label>ID 🇮🇩</span>
        <span class="sm:hidden" data-lang-flag>🇮🇩</span>
        <i class="fas fa-chevron-down text-[10px] text-gray-400 transition-transform duration-200" 
           :class="{ 'rotate-180': open }"></i>
    </button>
    
    <!-- Dropdown Menu -->
    <div 
        x-show="open"
        x-cloak
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 transform scale-95 -translate-y-2"
        x-transition:enter-end="opacity-100 transform scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 transform scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 transform scale-95 -translate-y-2"
        class="absolute right-0 mt-2 w-56 sm:w-64 bg-gray-900/95 backdrop-blur-xl rounded-2xl shadow-2xl border border-white/15 py-2 z-50 overflow-hidden"
        style="display: none;"
    >
        <div class="px-4 py-2 border-b border-white/10 bg-white/5">
            <p class="text-[11px] font-bold text-teal-300 uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-globe-americas"></i>
                Pilih Bahasa
            </p>
        </div>
        
        <!-- Indonesian -->
        <button 
            type="button" 
            onclick="window.changeLanguage('id')"
            @click="open = false"
            data-lang-opt="id"
            class="w-full text-left px-4 py-2.5 text-sm hover:bg-teal-500/10 text-gray-200 hover:text-white transition-all duration-150 flex items-center gap-3 group"
        >
            <span class="text-xl group-hover:scale-110 transition-transform">🇮🇩</span>
            <div class="flex-1">
                <div class="font-bold text-sm">Bahasa Indonesia</div>
                <div class="text-[11px] text-gray-400">Asli</div>
            </div>
            <i class="fas fa-check text-teal-400 text-xs" data-lang-check="id"></i>
        </button>
        
        <!-- English -->
        <button 
            type="button" 
            onclick="window.changeLanguage('en')"
            @click="open = false"
            data-lang-opt="en"
            class="w-full text-left px-4 py-2.5 text-sm hover:bg-teal-500/10 text-gray-200 hover:text-white transition-all duration-150 flex items-center gap-3 group"
        >
            <span class="text-xl group-hover:scale-110 transition-transform">🇬🇧</span>
            <div class="flex-1">
                <div class="font-bold text-sm">English</div>
                <div class="text-[11px] text-gray-400">Google Translate</div>
            </div>
            <i class="fas fa-check text-teal-400 text-xs hidden" data-lang-check="en"></i>
        </button>
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
