<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RIVANA - River DNA Analysis</title>
    <link rel="icon" type="image/jpeg" href="/images/logo2.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800;900&display=swap');
        body { font-family: 'Plus Jakarta Sans', sans-serif; }
        @keyframes float { 0%, 100% { transform: translateY(0) rotate(0deg); } 50% { transform: translateY(-30px) rotate(3deg); } }
        @keyframes wave { 0% { transform: translateX(0); } 100% { transform: translateX(-50%); } }
        @keyframes fadeUp { from { opacity: 0; transform: translateY(50px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes shimmer { 0% { background-position: -1000px 0; } 100% { background-position: 1000px 0; } }
        .animate-float { animation: float 10s ease-in-out infinite; }
        .animate-fadeUp { animation: fadeUp 1s ease-out forwards; }
        .wave-pattern { 
            background: repeating-linear-gradient(90deg, transparent, rgba(6, 182, 212, 0.03) 50%, transparent 100%);
            animation: wave 20s linear infinite;
            background-size: 200% 100%;
        }
        .shimmer {
            background: linear-gradient(90deg, transparent, rgba(6, 182, 212, 0.4), transparent);
            background-size: 1000px 100%;
            animation: shimmer 3s infinite;
        }
    </style>
</head>
<body class="bg-gradient-to-br from-slate-950 via-blue-950 to-cyan-950 min-h-screen overflow-x-hidden">
    
    <!-- Animated Water-themed Background -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <!-- Large gradient orbs -->
        <div class="absolute -top-48 -left-48 w-[500px] h-[500px] bg-blue-500/30 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute top-1/2 -right-32 w-96 h-96 bg-cyan-400/25 rounded-full blur-3xl animate-pulse" style="animation-delay: 2s;"></div>
        <div class="absolute -bottom-48 left-1/4 w-[400px] h-[400px] bg-teal-500/20 rounded-full blur-3xl animate-pulse" style="animation-delay: 4s;"></div>
        
        <!-- Wave pattern overlay -->
        <div class="absolute inset-0 wave-pattern opacity-50"></div>
        
        <!-- Subtle grid -->
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_1px_1px,rgba(6,182,212,0.15)_1px,transparent_0)] bg-[size:50px_50px] opacity-20"></div>
    </div>

    <!-- Main Content Container -->
    <div class="relative z-10 min-h-screen flex flex-col items-center justify-center px-4 sm:px-6 lg:px-8 py-20">
        <div class="max-w-7xl mx-auto w-full space-y-20">
            
            <!-- Hero Section -->
            <div class="text-center space-y-12 animate-fadeUp">
                <!-- Logo Section -->
                <div class="flex justify-center mb-8">
                    <div class="relative group animate-float">
                        <!-- Outer glow -->
                        <div class="absolute -inset-4 bg-gradient-to-r from-cyan-500 via-blue-500 to-cyan-500 rounded-full blur-2xl opacity-50 group-hover:opacity-75 transition duration-500"></div>
                        <!-- Ring pulse -->
                        <div class="absolute -inset-2 bg-cyan-400/30 rounded-full animate-pulse"></div>
                        <!-- Logo container -->
                        <div class="relative w-36 h-36 sm:w-44 sm:h-44 bg-gradient-to-br from-white to-cyan-100 rounded-full p-3 shadow-2xl ring-4 ring-cyan-400/50 ring-offset-4 ring-offset-slate-900">
                            <img src="/images/logo2.jpg" alt="Logo RIVANA" class="w-full h-full rounded-full object-cover shadow-inner">
                        </div>
                    </div>
                </div>
                
                <!-- Title -->
                <div class="space-y-6">
                    <div class="inline-block">
                        <h1 class="text-7xl sm:text-8xl lg:text-9xl font-black tracking-tighter">
                            <span class="block text-transparent bg-clip-text bg-gradient-to-r from-cyan-400 via-blue-500 to-cyan-600 drop-shadow-2xl">
                                RIVANA Auto Deploy
                            </span>
                        </h1>
                        <!-- Animated underline -->
                        <div class="h-2 mt-4 mx-auto bg-gradient-to-r from-transparent via-cyan-500 to-transparent rounded-full shimmer"></div>
                    </div>
                    
                    <div class="space-y-3">
                        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-bold text-white">
                            River DNA Analysis
                        </h2>
                        <p class="text-lg sm:text-xl lg:text-2xl text-cyan-200/90 max-w-4xl mx-auto leading-relaxed px-4">
                            Platform Analisis Hidrologi Berbasis Machine Learning & Google Earth Engine untuk Solusi Pengelolaan Sungai yang Cerdas dan Berkelanjutan
                        </p>
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="flex flex-col sm:flex-row gap-6 justify-center items-center pt-6">
                    <a href="{{ route('dashboard') }}" 
                       class="group relative px-12 py-5 bg-gradient-to-r from-cyan-500 via-blue-600 to-cyan-600 text-white font-bold text-xl rounded-2xl shadow-2xl hover:shadow-cyan-500/50 transition-all duration-300 overflow-hidden hover:scale-105">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-cyan-500 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        <div class="relative flex items-center gap-3">
                            <i class="fas fa-water text-2xl group-hover:scale-110 transition-transform"></i>
                            <span>Mulai Analisis</span>
                            <i class="fas fa-arrow-right group-hover:translate-x-2 transition-transform"></i>
                        </div>
                    </a>
                    
                    <a href="#features" 
                       class="group px-12 py-5 bg-white/10 backdrop-blur-xl text-white font-bold text-xl rounded-2xl border-2 border-cyan-400/50 hover:bg-white/20 hover:border-cyan-300 transition-all duration-300">
                        <div class="flex items-center gap-3">
                            <i class="fas fa-info-circle group-hover:rotate-12 transition-transform"></i>
                            <span>Pelajari Lebih Lanjut</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 animate-fadeUp" style="animation-delay: 0.2s;">
                <div class="group bg-white/5 backdrop-blur-xl rounded-3xl p-8 border border-cyan-400/20 hover:border-cyan-400/60 hover:bg-white/10 transition-all duration-300 hover:-translate-y-2">
                    <div class="flex items-center justify-center w-16 h-16 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl mb-5 mx-auto group-hover:scale-110 transition-transform shadow-lg shadow-cyan-500/50">
                        <i class="fas fa-brain text-3xl text-white"></i>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-black text-cyan-300 mb-2">12+</div>
                        <div class="text-sm font-medium text-cyan-200/70">ML Models</div>
                    </div>
                </div>

                <div class="group bg-white/5 backdrop-blur-xl rounded-3xl p-8 border border-blue-400/20 hover:border-blue-400/60 hover:bg-white/10 transition-all duration-300 hover:-translate-y-2">
                    <div class="flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl mb-5 mx-auto group-hover:scale-110 transition-transform shadow-lg shadow-blue-500/50">
                        <i class="fas fa-bolt text-3xl text-white"></i>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-black text-blue-300 mb-2">100%</div>
                        <div class="text-sm font-medium text-blue-200/70">Real-time</div>
                    </div>
                </div>

                <div class="group bg-white/5 backdrop-blur-xl rounded-3xl p-8 border border-teal-400/20 hover:border-teal-400/60 hover:bg-white/10 transition-all duration-300 hover:-translate-y-2">
                    <div class="flex items-center justify-center w-16 h-16 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-2xl mb-5 mx-auto group-hover:scale-110 transition-transform shadow-lg shadow-teal-500/50">
                        <i class="fas fa-chart-line text-3xl text-white"></i>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-black text-teal-300 mb-2">99.9%</div>
                        <div class="text-sm font-medium text-teal-200/70">Akurasi</div>
                    </div>
                </div>

                <div class="group bg-white/5 backdrop-blur-xl rounded-3xl p-8 border border-sky-400/20 hover:border-sky-400/60 hover:bg-white/10 transition-all duration-300 hover:-translate-y-2">
                    <div class="flex items-center justify-center w-16 h-16 bg-gradient-to-br from-sky-500 to-blue-600 rounded-2xl mb-5 mx-auto group-hover:scale-110 transition-transform shadow-lg shadow-sky-500/50">
                        <i class="fas fa-database text-3xl text-white"></i>
                    </div>
                    <div class="text-center">
                        <div class="text-4xl font-black text-sky-300 mb-2">∞</div>
                        <div class="text-sm font-medium text-sky-200/70">Big Data</div>
                    </div>
                </div>
            </div>

            <!-- Features Section -->
            <div id="features" class="space-y-12 animate-fadeUp" style="animation-delay: 0.4s;">
                <div class="text-center space-y-4">
                    <h2 class="text-4xl sm:text-5xl lg:text-6xl font-black text-transparent bg-clip-text bg-gradient-to-r from-cyan-300 to-blue-500">
                        Fitur Unggulan
                    </h2>
                    <p class="text-xl text-cyan-200/80 max-w-2xl mx-auto">
                        Teknologi terdepan untuk analisis hidrologi yang komprehensif
                    </p>
                </div>

                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Feature 1 -->
                    <div class="group bg-gradient-to-br from-white/5 to-white/10 backdrop-blur-2xl rounded-3xl p-8 border border-white/10 hover:border-cyan-400/50 transition-all duration-500 hover:-translate-y-3 hover:shadow-2xl hover:shadow-cyan-500/20">
                        <div class="w-20 h-20 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-xl shadow-cyan-500/50">
                            <i class="fas fa-water text-4xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">Analisis Hidrologi</h3>
                        <p class="text-cyan-200/80 leading-relaxed">
                            Analisis mendalam karakteristik sungai dengan machine learning terkini untuk prediksi yang akurat
                        </p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="group bg-gradient-to-br from-white/5 to-white/10 backdrop-blur-2xl rounded-3xl p-8 border border-white/10 hover:border-blue-400/50 transition-all duration-500 hover:-translate-y-3 hover:shadow-2xl hover:shadow-blue-500/20">
                        <div class="w-20 h-20 bg-gradient-to-br from-blue-500 to-cyan-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-xl shadow-blue-500/50">
                            <i class="fas fa-globe-asia text-4xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">Google Earth Engine</h3>
                        <p class="text-cyan-200/80 leading-relaxed">
                            Integrasi penuh dengan GEE untuk analisis geospasial dan citra satelit berkualitas tinggi
                        </p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="group bg-gradient-to-br from-white/5 to-white/10 backdrop-blur-2xl rounded-3xl p-8 border border-white/10 hover:border-teal-400/50 transition-all duration-500 hover:-translate-y-3 hover:shadow-2xl hover:shadow-teal-500/20">
                        <div class="w-20 h-20 bg-gradient-to-br from-teal-500 to-cyan-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-xl shadow-teal-500/50">
                            <i class="fas fa-chart-area text-4xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">Visualisasi Interaktif</h3>
                        <p class="text-cyan-200/80 leading-relaxed">
                            Dashboard interaktif dengan visualisasi data real-time untuk interpretasi yang mudah
                        </p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="group bg-gradient-to-br from-white/5 to-white/10 backdrop-blur-2xl rounded-3xl p-8 border border-white/10 hover:border-sky-400/50 transition-all duration-500 hover:-translate-y-3 hover:shadow-2xl hover:shadow-sky-500/20">
                        <div class="w-20 h-20 bg-gradient-to-br from-sky-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-xl shadow-sky-500/50">
                            <i class="fas fa-cloud-upload-alt text-4xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">Upload & Proses</h3>
                        <p class="text-cyan-200/80 leading-relaxed">
                            Upload data dengan mudah dan proses otomatis menggunakan algoritma canggih
                        </p>
                    </div>

                    <!-- Feature 5 -->
                    <div class="group bg-gradient-to-br from-white/5 to-white/10 backdrop-blur-2xl rounded-3xl p-8 border border-white/10 hover:border-indigo-400/50 transition-all duration-500 hover:-translate-y-3 hover:shadow-2xl hover:shadow-indigo-500/20">
                        <div class="w-20 h-20 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-xl shadow-indigo-500/50">
                            <i class="fas fa-file-export text-4xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">Export Report</h3>
                        <p class="text-cyan-200/80 leading-relaxed">
                            Export hasil analisis dalam berbagai format untuk dokumentasi profesional
                        </p>
                    </div>

                    <!-- Feature 6 -->
                    <div class="group bg-gradient-to-br from-white/5 to-white/10 backdrop-blur-2xl rounded-3xl p-8 border border-white/10 hover:border-emerald-400/50 transition-all duration-500 hover:-translate-y-3 hover:shadow-2xl hover:shadow-emerald-500/20">
                        <div class="w-20 h-20 bg-gradient-to-br from-emerald-500 to-cyan-600 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 group-hover:rotate-6 transition-all duration-300 shadow-xl shadow-emerald-500/50">
                            <i class="fas fa-shield-alt text-4xl text-white"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-white mb-4">Secure & Reliable</h3>
                        <p class="text-cyan-200/80 leading-relaxed">
                            Keamanan data terjamin dengan enkripsi dan sistem backup otomatis yang handal
                        </p>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="text-center pt-12 border-t border-cyan-400/20 animate-fadeUp" style="animation-delay: 0.6s;">
                <p class="text-cyan-200/60 text-base">
                    © {{ date('Y') }} <span class="font-semibold text-cyan-300">RIVANA</span> - River DNA Analysis. All rights reserved.
                </p>
            </div>

        </div>
    </div>

</body>
</html>
