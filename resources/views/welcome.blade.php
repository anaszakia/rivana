<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RIVANA - River DNA Analysis & Hydrological Intelligence</title>
    <link rel="icon" type="image/jpeg" href="/images/logo2.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.3/dist/cdn.min.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --brand-teal: #0d9488;
            --brand-cyan: #06b6d4;
            --brand-blue: #0284c7;
        }
        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            background: #030712;
            color: #f3f4f6;
            overflow-x: hidden;
        }

        /* Ambient Glow & Mesh Background */
        .ambient-mesh {
            background-image: 
                radial-gradient(at 15% 15%, rgba(13, 148, 136, 0.28) 0px, transparent 55%),
                radial-gradient(at 85% 10%, rgba(2, 132, 199, 0.25) 0px, transparent 50%),
                radial-gradient(at 50% 50%, rgba(6, 182, 212, 0.15) 0px, transparent 65%),
                radial-gradient(at 20% 85%, rgba(14, 116, 144, 0.22) 0px, transparent 55%),
                radial-gradient(at 80% 80%, rgba(16, 185, 129, 0.18) 0px, transparent 50%);
        }

        .grid-pattern {
            background-size: 40px 40px;
            background-image: 
                linear-gradient(to right, rgba(255, 255, 255, 0.03) 1px, transparent 1px),
                linear-gradient(to bottom, rgba(255, 255, 255, 0.03) 1px, transparent 1px);
            mask-image: radial-gradient(ellipse at center, rgba(0, 0, 0, 1) 0%, rgba(0, 0, 0, 0) 75%);
            -webkit-mask-image: radial-gradient(ellipse at center, rgba(0, 0, 0, 1) 0%, rgba(0, 0, 0, 0) 75%);
        }

        /* Glassmorphism Classes */
        .glass-card {
            background: rgba(17, 24, 39, 0.65);
            backdrop-filter: blur(16px);
            -webkit-backdrop-filter: blur(16px);
            border: 1px solid rgba(255, 255, 255, 0.08);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .glass-card:hover {
            background: rgba(17, 24, 39, 0.85);
            border-color: rgba(45, 212, 191, 0.35);
            transform: translateY(-4px);
            box-shadow: 0 20px 40px -15px rgba(13, 148, 136, 0.25);
        }

        .glass-nav {
            background: rgba(3, 7, 18, 0.7);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.06);
        }

        /* Animated Gradients */
        .gradient-text {
            background: linear-gradient(135deg, #ffffff 0%, #a5f3fc 45%, #2dd4bf 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .gradient-border-btn {
            position: relative;
            background: linear-gradient(135deg, #0d9488, #0284c7);
            padding: 1px;
            border-radius: 1rem;
            display: inline-block;
        }

        @keyframes pulseGlow {
            0%, 100% { opacity: 0.4; transform: scale(1); }
            50% { opacity: 0.7; transform: scale(1.05); }
        }
        .animate-pulse-glow {
            animation: pulseGlow 6s ease-in-out infinite;
        }

        /* Hide Google Translate Bar */
        .goog-te-banner-frame,
        .goog-te-banner-frame.skiptranslate,
        iframe.goog-te-banner-frame,
        iframe[id=":1.container"],
        iframe[id=":2.container"],
        .VIpgJd-ZVi9C-OR9Mb-Oxf5Fd,
        .VIpgJd-ZVi9C-bLu3Nc {
            display: none !important;
            visibility: hidden !important;
            height: 0 !important;
            width: 0 !important;
            border: none !important;
        }
        html, body {
            top: 0px !important;
            position: static !important;
        }
        .goog-tooltip, .goog-tooltip:hover, #google_translate_element {
            display: none !important;
        }
    </style>
</head>
<body class="min-h-screen relative selection:bg-teal-500 selection:text-white">

    <!-- Ambient Mesh Background Layers -->
    <div class="fixed inset-0 ambient-mesh pointer-events-none z-0"></div>
    <div class="fixed inset-0 grid-pattern pointer-events-none z-0"></div>

    <!-- Glowing Orbs in Background -->
    <div class="fixed -top-40 -left-40 w-96 h-96 bg-teal-500/20 rounded-full blur-[120px] pointer-events-none animate-pulse-glow"></div>
    <div class="fixed top-1/3 -right-40 w-[30rem] h-[30rem] bg-cyan-500/15 rounded-full blur-[140px] pointer-events-none animate-pulse-glow" style="animation-delay: 2s;"></div>
    <div class="fixed -bottom-40 left-1/3 w-[28rem] h-[28rem] bg-blue-600/15 rounded-full blur-[130px] pointer-events-none animate-pulse-glow" style="animation-delay: 4s;"></div>

    <!-- Navigation Header -->
    <nav class="sticky top-0 z-40 glass-nav">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('welcome') }}" class="flex items-center gap-3 group">
                <div class="w-11 h-11 rounded-xl overflow-hidden ring-2 ring-teal-500/40 group-hover:ring-teal-400 transition-all duration-300 shadow-lg shadow-teal-500/20">
                    <img src="/images/logo2.jpg" alt="RIVANA Logo" class="w-full h-full object-cover">
                </div>
                <div>
                    <span class="text-xl font-extrabold tracking-tight text-white group-hover:text-teal-300 transition-colors">RIVANA</span>
                    <span class="hidden sm:inline-block text-[10px] font-semibold tracking-wider text-teal-400 uppercase bg-teal-950/80 px-2 py-0.5 rounded-full border border-teal-500/30 ml-2">Open Platform</span>
                </div>
            </a>

            <!-- Navigation Links -->
            <div class="flex items-center gap-3 sm:gap-4">
                <a href="#features" class="hidden md:inline-flex text-sm font-medium text-gray-300 hover:text-white transition-colors">
                    Fitur
                </a>
                <a href="#workflow" class="hidden md:inline-flex text-sm font-medium text-gray-300 hover:text-white transition-colors">
                    Cara Kerja
                </a>
                <a href="{{ route('hidrologi.index') }}" class="hidden sm:inline-flex text-sm font-medium text-teal-400 hover:text-teal-300 transition-colors">
                    Daftar Analisis
                </a>

                <!-- Language Switcher Component inside Navbar -->
                @include('partials.language-switcher-welcome')

                <!-- CTA Button to Dashboard -->
                <a href="{{ route('dashboard') }}" 
                   class="inline-flex items-center gap-2 px-3.5 sm:px-4 py-2 rounded-xl bg-gradient-to-r from-teal-500 to-cyan-500 hover:from-teal-400 hover:to-cyan-400 text-gray-950 font-bold text-xs sm:text-sm shadow-lg shadow-teal-500/25 hover:shadow-teal-500/40 transition-all duration-200 hover:scale-[1.02]">
                    <i class="fas fa-chart-line"></i>
                    <span>Dashboard</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <header class="relative z-10 pt-16 sm:pt-24 pb-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto text-center">
        <!-- Top Pill Badge -->
        <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-teal-500/10 border border-teal-500/30 backdrop-blur-md mb-8 hover:border-teal-400/50 transition-colors">
            <span class="flex h-2 w-2 rounded-full bg-teal-400 animate-ping"></span>
            <span class="text-xs sm:text-sm font-semibold text-teal-300">
                Sistem Cerdas Analisis DNA & Karakteristik Sungai
            </span>
            <i class="fas fa-sparkles text-xs text-teal-300 ml-1"></i>
        </div>

        <!-- Hero Headline -->
        <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight text-white max-w-5xl mx-auto leading-[1.15] mb-6">
            Eksplorasi Karakteristik DAS & Hidrologi dengan <span class="gradient-text">Kecerdasan Buatan</span>
        </h1>

        <p class="text-base sm:text-xl text-gray-300 max-w-3xl mx-auto font-normal leading-relaxed mb-10">
            Platform komputasi geospasial terintegrasi satelit resolusi tinggi untuk memetakan erosi USLE, indeks kebasahan topografi (TWI), debit limpasan, serta mitigasi konservasi DAS tanpa login.
        </p>

        <!-- CTA Action Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 sm:gap-5 mb-16">
            <a href="{{ route('hidrologi.create') }}" 
               class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 rounded-2xl bg-gradient-to-r from-teal-500 via-teal-400 to-cyan-400 text-gray-950 font-extrabold text-base shadow-xl shadow-teal-500/30 hover:shadow-teal-500/50 hover:scale-[1.03] transition-all duration-300">
                <i class="fas fa-plus-circle text-lg"></i>
                <span>Buat Analisis Baru</span>
                <i class="fas fa-arrow-right text-xs"></i>
            </a>

            <a href="{{ route('dashboard') }}" 
               class="w-full sm:w-auto inline-flex items-center justify-center gap-3 px-8 py-4 rounded-2xl glass-card text-white font-semibold text-base hover:text-teal-300 hover:border-teal-500/40 transition-all duration-300">
                <i class="fas fa-th-large text-gray-400"></i>
                <span>Lihat Dashboard</span>
            </a>
        </div>

        <!-- Highlight Key Metrics Bar -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-5xl mx-auto">
            <div class="glass-card rounded-2xl p-5 text-center">
                <div class="text-3xl lg:text-4xl font-extrabold text-teal-400 mb-1">12+</div>
                <div class="text-xs font-medium text-gray-400 uppercase tracking-wider">Model ML & Erosi</div>
            </div>
            <div class="glass-card rounded-2xl p-5 text-center">
                <div class="text-3xl lg:text-4xl font-extrabold text-cyan-400 mb-1">Cloud</div>
                <div class="text-xs font-medium text-gray-400 uppercase tracking-wider">GEE Satelit Pipeline</div>
            </div>
            <div class="glass-card rounded-2xl p-5 text-center">
                <div class="text-3xl lg:text-4xl font-extrabold text-sky-400 mb-1">Open</div>
                <div class="text-xs font-medium text-gray-400 uppercase tracking-wider">Akses Tanpa Akun</div>
            </div>
            <div class="glass-card rounded-2xl p-5 text-center">
                <div class="text-3xl lg:text-4xl font-extrabold text-emerald-400 mb-1">Real-time</div>
                <div class="text-xs font-medium text-gray-400 uppercase tracking-wider">Monitoring & Output</div>
            </div>
        </div>
    </header>

    <!-- Workflow Section -->
    <section id="workflow" class="relative z-10 py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto border-t border-white/5">
        <div class="text-center max-w-2xl mx-auto mb-16">
            <span class="text-xs font-bold uppercase tracking-widest text-teal-400 bg-teal-950/60 border border-teal-500/30 px-3 py-1 rounded-full">Alur Kerja</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white mt-4 mb-3">Tiga Langkah Cepat Analisis</h2>
            <p class="text-gray-400 text-sm sm:text-base">Mulai perhitungan hidrologi akurat tanpa instalasi software rumit.</p>
        </div>

        <div class="grid md:grid-cols-3 gap-8 relative">
            <!-- Step 1 -->
            <div class="glass-card rounded-3xl p-8 relative">
                <div class="w-12 h-12 rounded-2xl bg-teal-500/20 border border-teal-500/40 flex items-center justify-center text-teal-400 font-extrabold text-lg mb-6">
                    01
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Tentukan Titik & Tanggal</h3>
                <p class="text-gray-400 text-sm leading-relaxed">
                    Pilih koordinat latitude/longitude sungai atau outlet DAS di peta interaktif dan tentukan rentang observasi.
                </p>
            </div>

            <!-- Step 2 -->
            <div class="glass-card rounded-3xl p-8 relative">
                <div class="w-12 h-12 rounded-2xl bg-cyan-500/20 border border-cyan-500/40 flex items-center justify-center text-cyan-400 font-extrabold text-lg mb-6">
                    02
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Pemrosesan GEE Cloud</h3>
                <p class="text-gray-400 text-sm leading-relaxed">
                    Sistem secara otomatis mengekstraksi data satelit Copernicus, CHIRPS, MODIS, dan topografi DEM HydroSHEDS.
                </p>
            </div>

            <!-- Step 3 -->
            <div class="glass-card rounded-3xl p-8 relative">
                <div class="w-12 h-12 rounded-2xl bg-sky-500/20 border border-sky-500/40 flex items-center justify-center text-sky-400 font-extrabold text-lg mb-6">
                    03
                </div>
                <h3 class="text-xl font-bold text-white mb-2">Dapatkan Hasil & Rekomendasi</h3>
                <p class="text-gray-400 text-sm leading-relaxed">
                    Lihat peta deliniasi DAS resolusi tinggi, estimasi laju erosi, zona resiko banjir TWI, serta perhitungan estimasi biaya mitigasi.
                </p>
            </div>
        </div>
    </section>

    <!-- Features Grid Section -->
    <section id="features" class="relative z-10 py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-bold uppercase tracking-widest text-teal-400 bg-teal-950/60 border border-teal-500/30 px-3 py-1 rounded-full">Fitur Lengkap</span>
            <h2 class="text-3xl sm:text-5xl font-extrabold text-white mt-4 mb-4">Kemampuan Geospasial Modern</h2>
            <p class="text-gray-400 text-base sm:text-lg">Dukungan komputasi menyeluruh untuk kebutuhan riset, dinas lingkungan, dan perencana infrastruktur air.</p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            <!-- Card 1 -->
            <div class="glass-card rounded-3xl p-8 flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-teal-500/10 border border-teal-500/30 flex items-center justify-center text-teal-400 text-2xl mb-6">
                        <i class="fas fa-water"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Model Erosi USLE & TWI</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Perhitungan mendalam laju erosi tanah tahunan (Universal Soil Loss Equation) dipadukan dengan Topological Wetness Index untuk zona rawan genangan.
                    </p>
                </div>
            </div>

            <!-- Card 2 -->
            <div class="glass-card rounded-3xl p-8 flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400 text-2xl mb-6">
                        <i class="fas fa-satellite"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Integrasi Google Earth Engine</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Akses repositori data citra satelit multispektral global secara instan tanpa perlu mengunduh file raster berukuran gigabyte ke komputer lokal.
                    </p>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="glass-card rounded-3xl p-8 flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-blue-500/10 border border-blue-500/30 flex items-center justify-center text-blue-400 text-2xl mb-6">
                        <i class="fas fa-map-marked-alt"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Peta Interaktif Layer Satelit</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Visualisasi batas kontur DAS interaktif dengan pilihan layer Esri Satellite, OpenStreetMap, serta overlay kurva aliran sungai dan titik mitigasi.
                    </p>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="glass-card rounded-3xl p-8 flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 flex items-center justify-center text-emerald-400 text-2xl mb-6">
                        <i class="fas fa-bolt"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Antrean Komputasi Otomatis</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Pipeline asynchronous background job yang tangguh dengan pelacak status real-time, persentase progres, dan detail log komputasi per tahapan.
                    </p>
                </div>
            </div>

            <!-- Card 5 -->
            <div class="glass-card rounded-3xl p-8 flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-purple-500/10 border border-purple-500/30 flex items-center justify-center text-purple-400 text-2xl mb-6">
                        <i class="fas fa-calculator"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Estimasi Biaya Standar PUPR</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Estimasi Rencana Anggaran Biaya (RAB) infrastruktur penahan erosi dan normalisasi sungai berdasarkan acuan harga konstruksi PUPR & benchmark LPSE.
                    </p>
                </div>
            </div>

            <!-- Card 6 -->
            <div class="glass-card rounded-3xl p-8 flex flex-col justify-between">
                <div>
                    <div class="w-14 h-14 rounded-2xl bg-sky-500/10 border border-sky-500/30 flex items-center justify-center text-sky-400 text-2xl mb-6">
                        <i class="fas fa-file-download"></i>
                    </div>
                    <h3 class="text-xl font-bold text-white mb-3">Ekspor Data Komprehensif</h3>
                    <p class="text-gray-400 text-sm leading-relaxed">
                        Unduh paket hasil analisis dalam berbagai format: grafik visual PNG, tabel kalkulasi CSV/Excel, serta ringkasan geospasial JSON & GeoJSON.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- Bottom Banner CTA -->
    <section class="relative z-10 py-16 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="rounded-3xl p-8 sm:p-14 text-center relative overflow-hidden bg-gradient-to-r from-teal-900/60 via-slate-900/80 to-cyan-950/60 border border-teal-500/30 shadow-2xl backdrop-blur-xl">
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-teal-500/20 rounded-full blur-3xl pointer-events-none"></div>
            
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white mb-4 relative z-10">
                Siap Melakukan Analisis Hidrologi Sekarang?
            </h2>
            <p class="text-gray-300 text-base sm:text-lg max-w-2xl mx-auto mb-8 relative z-10">
                Langsung masukkan titik koordinat lokasi sungai Anda dan biarkan algoritma RIVANA menghitung semuanya secara otomatis.
            </p>
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 relative z-10">
                <a href="{{ route('hidrologi.create') }}" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl bg-gradient-to-r from-teal-400 to-cyan-400 text-gray-950 font-bold text-sm hover:scale-105 transition-all duration-200 shadow-lg shadow-teal-500/30">
                    <i class="fas fa-plus"></i>
                    <span>Mulai Analisis Gratis</span>
                </a>
                <a href="{{ route('hidrologi.index') }}" 
                   class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl bg-white/10 hover:bg-white/15 text-white font-medium text-sm border border-white/10 transition-all duration-200">
                    <span>Lihat Daftar Pekerjaan</span>
                    <i class="fas fa-arrow-right text-xs"></i>
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="relative z-10 border-t border-white/10 py-10 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
            <div class="flex items-center gap-3">
                <img src="/images/logo2.jpg" alt="RIVANA" class="w-7 h-7 rounded-lg object-cover">
                <span class="text-sm font-bold text-white tracking-tight">RIVANA</span>
                <span class="text-xs text-gray-400">River DNA Analysis Platform</span>
            </div>
            <p class="text-xs text-gray-400">
                © {{ date('Y') }} RIVANA. Dibuat untuk riset konservasi sungai & hidrologi terbuka.
            </p>
        </div>
    </footer>

    <!-- Google Full Page Translate -->
    <script src="/js/google-translator.js"></script>
    <script src="https://translate.google.com/translate_a/element.js?cb=googleTranslateElementInit"></script>
</body>
</html>