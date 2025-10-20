<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RIVANA - Login</title>
    <link rel="icon" type="image/jpeg" href="/images/logo2.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        
        html, body {
            height: 100%;
            overflow-x: hidden;
            overflow-y: auto;
        }
        
        .login-container {
            min-height: 100vh;
        }
        
        .input-focus {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .input-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(59, 130, 246, 0.15);
        }
        
        .btn-primary {
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
            transition: left 0.5s;
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        @keyframes fadeInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        @keyframes fadeInRight {
            from {
                opacity: 0;
                transform: translateX(30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .fade-in-left {
            animation: fadeInLeft 0.8s ease-out;
        }
        
        .fade-in-right {
            animation: fadeInRight 0.8s ease-out;
        }
        
        .stagger-1 { animation-delay: 0.1s; }
        .stagger-2 { animation-delay: 0.2s; }
        .stagger-3 { animation-delay: 0.3s; }
        .stagger-4 { animation-delay: 0.4s; }
        .stagger-5 { animation-delay: 0.5s; }
        
        .logo-container {
            position: relative;
            display: inline-block;
        }
        
        .logo-glow {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 120%;
            height: 120%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.3), transparent);
            border-radius: 50%;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 0.5; transform: translate(-50%, -50%) scale(1); }
            50% { opacity: 0; transform: translate(-50%, -50%) scale(1.2); }
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
        }
        
        .gradient-bg::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }
        
        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.1);
        }
        
        .floating {
            animation: floating 3s ease-in-out infinite;
        }
        
        @keyframes floating {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        
        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(255, 255, 255, 0.5);
            border-radius: 50%;
            animation: particle-float 15s infinite;
        }
        
        @keyframes particle-float {
            0% {
                transform: translateY(100vh) translateX(0);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100vh) translateX(100px);
                opacity: 0;
            }
        }
        
        .particle:nth-child(1) { left: 10%; animation-delay: 0s; animation-duration: 12s; }
        .particle:nth-child(2) { left: 20%; animation-delay: 2s; animation-duration: 15s; }
        .particle:nth-child(3) { left: 30%; animation-delay: 4s; animation-duration: 18s; }
        .particle:nth-child(4) { left: 40%; animation-delay: 1s; animation-duration: 14s; }
        .particle:nth-child(5) { left: 50%; animation-delay: 3s; animation-duration: 16s; }
        .particle:nth-child(6) { left: 60%; animation-delay: 5s; animation-duration: 13s; }
        .particle:nth-child(7) { left: 70%; animation-delay: 2.5s; animation-duration: 17s; }
        .particle:nth-child(8) { left: 80%; animation-delay: 4.5s; animation-duration: 15s; }
        .particle:nth-child(9) { left: 90%; animation-delay: 1.5s; animation-duration: 19s; }
        .particle:nth-child(10) { left: 95%; animation-delay: 3.5s; animation-duration: 14s; }
    </style>
</head>
<body>
    <div class="login-container flex min-h-screen flex-col lg:flex-row">
        <!-- Left Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-6 lg:p-12 py-12 lg:py-20 bg-gradient-to-br from-gray-50 via-blue-50 to-purple-50 relative">
            <!-- Decorative Elements -->
            <div class="absolute top-10 left-10 w-72 h-72 bg-purple-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse"></div>
            <div class="absolute bottom-10 right-10 w-72 h-72 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl opacity-20 animate-pulse" style="animation-delay: 2s;"></div>
            
            <div class="w-full max-w-md fade-in-left relative z-10">
                <!-- Logo & Brand -->
                <div class="text-center mb-10 fade-in-left stagger-1">
                    <div class="inline-flex items-center justify-center space-x-4 mb-6">
                        <div class="logo-container relative">
                            <div class="logo-glow"></div>
                            <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-blue-500 rounded-2xl blur-lg opacity-50"></div>
                            <img src="/images/logo2.jpg" alt="Logo RIVANA" class="w-20 h-20 rounded-2xl shadow-2xl border-3 border-white relative z-10 object-cover transform hover:scale-110 transition-transform duration-300">
                        </div>
                    </div>
                    <h1 class="text-4xl font-bold bg-gradient-to-r from-purple-600 via-blue-600 to-purple-600 bg-clip-text text-transparent mb-2">RIVANA</h1>
                    <p class="text-sm text-gray-600 font-medium tracking-wider">RIVER ANALYSIS & PLANNING SYSTEM</p>
                    <div class="mt-4 h-1 w-24 bg-gradient-to-r from-purple-500 via-blue-500 to-purple-500 rounded-full mx-auto"></div>
                </div>

                <!-- Glass Card -->
                <div class="glass-card rounded-3xl p-8 lg:p-10 fade-in-left stagger-2 shadow-2xl">
                    <!-- Title -->
                    <div class="mb-8">
                        <h2 class="text-3xl font-bold bg-gradient-to-r from-gray-800 to-gray-600 bg-clip-text text-transparent mb-3">Masuk ke Akun</h2>
                        <p class="text-gray-600">Silakan masukkan kredensial Anda untuk melanjutkan</p>
                    </div>

                    <!-- Login Form -->
                    <form class="space-y-6" method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <!-- Email Input -->
                        <div>
                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-envelope mr-2 text-purple-500"></i>
                                Alamat Email
                            </label>
                            <div class="relative group">
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email"
                                    class="input-focus w-full px-5 py-4 pl-12 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-200 focus:border-purple-500 text-base bg-white transition-all @error('email') border-red-400 @enderror"
                                    placeholder="nama@email.com"
                                    value="{{ old('email') }}"
                                    required autofocus
                                >
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-envelope text-gray-400 group-focus-within:text-purple-500 transition-colors"></i>
                                </div>
                            </div>
                            @error('email')
                                <span class="text-red-500 text-xs mt-2 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1.5"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Password Input -->
                        <div>
                            <label for="password" class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-lock mr-2 text-purple-500"></i>
                                Kata Sandi
                            </label>
                            <div class="relative group">
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password"
                                    class="input-focus w-full px-5 py-4 pl-12 pr-12 border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-4 focus:ring-purple-200 focus:border-purple-500 text-base bg-white transition-all @error('password') border-red-400 @enderror"
                                    placeholder="••••••••"
                                    required
                                >
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                    <i class="fas fa-lock text-gray-400 group-focus-within:text-purple-500 transition-colors"></i>
                                </div>
                                <button 
                                    type="button" 
                                    id="togglePassword" 
                                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-gray-400 hover:text-purple-500 focus:outline-none transition-colors"
                                >
                                    <i class="fas fa-eye" id="eyeIcon"></i>
                                </button>
                            </div>
                            @error('password')
                                <span class="text-red-500 text-xs mt-2 flex items-center">
                                    <i class="fas fa-exclamation-circle mr-1.5"></i>
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <input 
                                    id="remember" 
                                    name="remember" 
                                    type="checkbox" 
                                    class="h-5 w-5 text-purple-600 focus:ring-purple-500 border-gray-300 rounded cursor-pointer transition-all"
                                    {{ old('remember') ? 'checked' : '' }}
                                >
                                <label for="remember" class="ml-3 block text-sm text-gray-700 cursor-pointer select-none font-medium">
                                    Ingat Saya
                                </label>
                            </div>
                        </div>

                        <!-- Login Button -->
                        <button 
                            type="submit" 
                            class="btn-primary w-full flex justify-center items-center py-4 px-6 border border-transparent rounded-xl shadow-lg text-base font-bold text-white bg-gradient-to-r from-purple-600 via-blue-600 to-purple-600 hover:from-purple-700 hover:via-blue-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-purple-300 transition-all duration-300 transform hover:scale-[1.02] hover:shadow-2xl"
                        >
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Masuk ke Dashboard
                        </button>
                    </form>
                </div>
                
                <!-- Footer -->
                <div class="mt-8 text-center fade-in-left stagger-4">
                    <div class="flex items-center justify-center space-x-2 text-xs text-gray-600 bg-gradient-to-r from-purple-50 to-blue-50 rounded-2xl py-4 px-5 shadow-sm">
                        <i class="fas fa-shield-alt text-purple-500 text-sm"></i>
                        <span class="font-medium">Koneksi Aman dengan Enkripsi SSL 256-bit</span>
                    </div>
                    <p class="text-xs text-gray-400 mt-4">© 2025 RIVANA. All rights reserved.</p>
                </div>
            </div>
        </div>

        <!-- Right Side - Gradient & Illustration -->
        <div class="hidden lg:flex lg:w-1/2 relative gradient-bg">
            <!-- Animated Particles -->
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            <div class="particle"></div>
            
            <div class="absolute inset-0 flex flex-col items-center justify-center p-12 z-10 fade-in-right py-20">
                <!-- Illustration -->
                <div class="mb-8 relative floating">
                    <div class="absolute inset-0 bg-white opacity-20 rounded-full blur-3xl"></div>
                    <svg class="w-full max-w-lg drop-shadow-2xl relative z-10" viewBox="0 0 500 500" xmlns="http://www.w3.org/2000/svg">
                        <!-- Data Analysis Illustration -->
                        <!-- Monitor/Screen -->
                        <rect x="100" y="150" width="300" height="200" rx="10" fill="#ffffff" opacity="0.9"/>
                        <rect x="110" y="160" width="280" height="160" rx="5" fill="#e0f2fe"/>
                        
                        <!-- Chart Bars -->
                        <rect x="140" y="240" width="30" height="60" rx="3" fill="#3b82f6"/>
                        <rect x="185" y="220" width="30" height="80" rx="3" fill="#60a5fa"/>
                        <rect x="230" y="200" width="30" height="100" rx="3" fill="#2563eb"/>
                        <rect x="275" y="230" width="30" height="70" rx="3" fill="#3b82f6"/>
                        <rect x="320" y="210" width="30" height="90" rx="3" fill="#60a5fa"/>
                        
                        <!-- Graph Line -->
                        <path d="M 140 280 Q 185 240, 230 220 T 320 200" stroke="#1e40af" stroke-width="3" fill="none" opacity="0.6"/>
                        
                        <!-- Monitor Stand -->
                        <rect x="220" y="350" width="60" height="10" rx="5" fill="#ffffff" opacity="0.9"/>
                        <rect x="180" y="360" width="140" height="5" rx="3" fill="#ffffff" opacity="0.9"/>
                        
                        <!-- Floating Elements -->
                        <!-- Document Icon -->
                        <g transform="translate(50, 100)">
                            <rect width="50" height="60" rx="5" fill="#ffffff" opacity="0.9"/>
                            <line x1="10" y1="15" x2="40" y2="15" stroke="#3b82f6" stroke-width="2"/>
                            <line x1="10" y1="25" x2="40" y2="25" stroke="#60a5fa" stroke-width="2"/>
                            <line x1="10" y1="35" x2="30" y2="35" stroke="#60a5fa" stroke-width="2"/>
                        </g>
                        
                        <!-- Chart Icon -->
                        <g transform="translate(400, 250)">
                            <circle r="30" fill="#ffffff" opacity="0.9"/>
                            <rect x="-15" y="0" width="8" height="15" rx="2" fill="#3b82f6"/>
                            <rect x="-3" y="-5" width="8" height="20" rx="2" fill="#60a5fa"/>
                            <rect x="9" y="-10" width="8" height="25" rx="2" fill="#2563eb"/>
                        </g>
                        
                        <!-- Data Icon -->
                        <g transform="translate(420, 120)">
                            <circle r="25" fill="#ffffff" opacity="0.9"/>
                            <circle r="8" cx="-5" cy="-5" fill="#3b82f6"/>
                            <circle r="8" cx="8" cy="-5" fill="#60a5fa"/>
                            <circle r="8" cx="-5" cy="8" fill="#60a5fa"/>
                            <circle r="8" cx="8" cy="8" fill="#2563eb"/>
                        </g>
                        
                        <!-- Decorative Dots -->
                        <circle cx="80" cy="250" r="4" fill="#ffffff" opacity="0.6">
                            <animate attributeName="opacity" values="0.3;0.8;0.3" dur="2s" repeatCount="indefinite"/>
                        </circle>
                        <circle cx="450" cy="180" r="4" fill="#ffffff" opacity="0.6">
                            <animate attributeName="opacity" values="0.3;0.8;0.3" dur="2.5s" repeatCount="indefinite"/>
                        </circle>
                        <circle cx="70" cy="380" r="4" fill="#ffffff" opacity="0.6">
                            <animate attributeName="opacity" values="0.3;0.8;0.3" dur="3s" repeatCount="indefinite"/>
                        </circle>
                    </svg>
                </div>
                
                <!-- Text Content -->
                <div class="text-center text-white relative z-10 max-w-2xl mx-auto">
                    <div class="mb-8">
                        <h2 class="text-4xl lg:text-5xl font-bold mb-5 drop-shadow-lg leading-tight">
                            Sistem Analisis <br/>
                            <span class="text-transparent bg-clip-text bg-gradient-to-r from-yellow-200 to-pink-200">Hidrologi Terpadu</span>
                        </h2>
                        <p class="text-lg lg:text-xl text-white text-opacity-90 mb-5 max-w-xl mx-auto drop-shadow-md leading-relaxed">
                            Platform analisis hidrologi berbasis Machine Learning dengan 12 model prediksi canggih
                        </p>
                        <div class="flex items-center justify-center space-x-3 text-sm flex-wrap gap-2">
                            <div class="flex items-center space-x-2 bg-white bg-opacity-20 backdrop-blur-sm rounded-full px-4 py-2">
                                <i class="fas fa-brain"></i>
                                <span>12 ML Models</span>
                            </div>
                            <div class="flex items-center space-x-2 bg-white bg-opacity-20 backdrop-blur-sm rounded-full px-4 py-2">
                                <i class="fas fa-satellite"></i>
                                <span>Google Earth Engine</span>
                            </div>
                            <div class="flex items-center space-x-2 bg-white bg-opacity-20 backdrop-blur-sm rounded-full px-4 py-2">
                                <i class="fas fa-chart-area"></i>
                                <span>Real-time Analysis</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-3 gap-4 lg:gap-6 mt-8 mb-8">
                        <div class="text-center bg-white bg-opacity-15 backdrop-blur-md rounded-2xl p-6 transform hover:scale-105 hover:bg-opacity-20 transition-all duration-300 shadow-xl">
                            <div class="text-4xl font-bold drop-shadow-lg mb-2">250+</div>
                            <div class="text-sm text-white text-opacity-90 font-medium">Total Analisis</div>
                            <i class="fas fa-chart-line text-2xl mt-3 opacity-70"></i>
                        </div>
                        <div class="text-center bg-white bg-opacity-15 backdrop-blur-md rounded-2xl p-6 transform hover:scale-105 hover:bg-opacity-20 transition-all duration-300 shadow-xl">
                            <div class="text-4xl font-bold drop-shadow-lg mb-2">99.2%</div>
                            <div class="text-sm text-white text-opacity-90 font-medium">Akurasi Model</div>
                            <i class="fas fa-bullseye text-2xl mt-3 opacity-70"></i>
                        </div>
                        <div class="text-center bg-white bg-opacity-15 backdrop-blur-md rounded-2xl p-6 transform hover:scale-105 hover:bg-opacity-20 transition-all duration-300 shadow-xl">
                            <div class="text-4xl font-bold drop-shadow-lg mb-2">24/7</div>
                            <div class="text-sm text-white text-opacity-90 font-medium">Monitoring</div>
                            <i class="fas fa-clock text-2xl mt-3 opacity-70"></i>
                        </div>
                    </div>
                    
                    <!-- Features Grid -->
                    <div class="grid grid-cols-2 gap-4 max-w-2xl mx-auto">
                        <div class="flex items-center space-x-4 text-left bg-white bg-opacity-15 backdrop-blur-md rounded-xl p-5 transform hover:scale-105 hover:bg-opacity-20 transition-all duration-300">
                            <div class="w-14 h-14 bg-gradient-to-br from-yellow-400 to-orange-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                <i class="fas fa-water text-white text-xl"></i>
                            </div>
                            <div>
                                <div class="font-bold text-base mb-1">Water Balance</div>
                                <div class="text-xs text-white text-opacity-80">Analisis keseimbangan air</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 text-left bg-white bg-opacity-15 backdrop-blur-md rounded-xl p-5 transform hover:scale-105 hover:bg-opacity-20 transition-all duration-300">
                            <div class="w-14 h-14 bg-gradient-to-br from-blue-400 to-cyan-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                <i class="fas fa-cloud-rain text-white text-xl"></i>
                            </div>
                            <div>
                                <div class="font-bold text-base mb-1">Flood Prediction</div>
                                <div class="text-xs text-white text-opacity-80">Prediksi risiko banjir</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 text-left bg-white bg-opacity-15 backdrop-blur-md rounded-xl p-5 transform hover:scale-105 hover:bg-opacity-20 transition-all duration-300">
                            <div class="w-14 h-14 bg-gradient-to-br from-green-400 to-emerald-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                <i class="fas fa-leaf text-white text-xl"></i>
                            </div>
                            <div>
                                <div class="font-bold text-base mb-1">Ecosystem Health</div>
                                <div class="text-xs text-white text-opacity-80">Kesehatan ekosistem</div>
                            </div>
                        </div>
                        <div class="flex items-center space-x-4 text-left bg-white bg-opacity-15 backdrop-blur-md rounded-xl p-5 transform hover:scale-105 hover:bg-opacity-20 transition-all duration-300">
                            <div class="w-14 h-14 bg-gradient-to-br from-purple-400 to-pink-500 rounded-xl flex items-center justify-center flex-shrink-0 shadow-lg">
                                <i class="fas fa-chart-pie text-white text-xl"></i>
                            </div>
                            <div>
                                <div class="font-bold text-base mb-1">Economic Analysis</div>
                                <div class="text-xs text-white text-opacity-80">Analisis ekonomi air</div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Technology Stack -->
                    <div class="mt-8 pt-6 border-t border-white border-opacity-20">
                        <p class="text-sm text-white text-opacity-70 mb-4">Powered by Rivana</p>
                        <div class="flex items-center justify-center space-x-6 flex-wrap gap-3">
                            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg px-4 py-2 text-sm font-medium">
                                <i class="fab fa-python mr-2"></i>Python ML
                            </div>
                            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg px-4 py-2 text-sm font-medium">
                                <i class="fas fa-satellite-dish mr-2"></i>GEE API
                            </div>
                            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg px-4 py-2 text-sm font-medium">
                                <i class="fas fa-database mr-2"></i>Big Data
                            </div>
                            <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg px-4 py-2 text-sm font-medium">
                                <i class="fas fa-server mr-2"></i>Laravel
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password visibility toggle
        const togglePassword = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eyeIcon');

        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            if (type === 'password') {
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            } else {
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            }
        });

        // Add smooth focus animations
        const inputs = document.querySelectorAll('input[type="email"], input[type="password"]');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.style.transform = 'scale(1.01)';
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.style.transform = 'scale(1)';
            });
        });

        // Form validation animation
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Memproses...';
            submitBtn.disabled = true;
        });
    </script>
</body>
</html>