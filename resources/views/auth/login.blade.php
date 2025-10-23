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
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', sans-serif;
        }
        
        html, body {
            height: 100vh;
            overflow: hidden;
        }
        
        .login-container {
            height: 100vh;
            display: flex;
        }
        
        .input-focus {
            transition: all 0.2s ease;
        }
        
        .input-focus:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .btn-primary {
            transition: all 0.2s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(59, 130, 246, 0.4);
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <!-- Left Side - Login Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gray-50">
            <div class="w-full max-w-md">
                <!-- Logo & Brand -->
                <div class="text-center mb-8">
                    <img src="/images/logo2.jpg" alt="Logo RIVANA" class="w-16 h-16 rounded-lg shadow-md mx-auto mb-4 object-cover">
                    <h1 class="text-2xl font-bold text-gray-800 mb-1">RIVANA</h1>
                    <p class="text-sm text-gray-500">River Analysis & Planning System</p>
                </div>

                <!-- Login Card -->
                <div class="bg-white rounded-lg shadow-md p-8">
                    <h2 class="text-xl font-semibold text-gray-800 mb-2">Masuk ke Akun</h2>
                    <p class="text-sm text-gray-500 mb-6">Silakan masukkan kredensial Anda</p>

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        
                        <!-- Email Input -->
                        <div class="mb-4">
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                                Email
                            </label>
                            <div class="relative">
                                <input 
                                    type="email" 
                                    id="email" 
                                    name="email"
                                    class="input-focus w-full px-4 py-2.5 pl-10 border border-gray-300 rounded-lg focus:outline-none @error('email') border-red-400 @enderror"
                                    placeholder="nama@email.com"
                                    value="{{ old('email') }}"
                                    required autofocus
                                >
                                <i class="fas fa-envelope absolute left-3 top-3.5 text-gray-400 text-sm"></i>
                            </div>
                            @error('email')
                                <span class="text-red-500 text-xs mt-1 block">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Password Input -->
                        <div class="mb-4">
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Password
                            </label>
                            <div class="relative">
                                <input 
                                    type="password" 
                                    id="password" 
                                    name="password"
                                    class="input-focus w-full px-4 py-2.5 pl-10 pr-10 border border-gray-300 rounded-lg focus:outline-none @error('password') border-red-400 @enderror"
                                    placeholder="••••••••"
                                    required
                                >
                                <i class="fas fa-lock absolute left-3 top-3.5 text-gray-400 text-sm"></i>
                                <button 
                                    type="button" 
                                    id="togglePassword" 
                                    class="absolute right-3 top-3.5 text-gray-400 hover:text-gray-600 focus:outline-none"
                                >
                                    <i class="fas fa-eye text-sm" id="eyeIcon"></i>
                                </button>
                            </div>
                            @error('password')
                                <span class="text-red-500 text-xs mt-1 block">
                                    {{ $message }}
                                </span>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-6">
                            <label class="flex items-center">
                                <input 
                                    id="remember" 
                                    name="remember" 
                                    type="checkbox" 
                                    class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                    {{ old('remember') ? 'checked' : '' }}
                                >
                                <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                            </label>
                        </div>

                        <!-- Login Button -->
                        <button 
                            type="submit" 
                            class="btn-primary w-full py-2.5 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                        >
                            Masuk
                        </button>
                    </form>
                </div>
                
                <!-- Footer -->
                <p class="text-xs text-gray-400 text-center mt-6">© 2025 RIVANA. All rights reserved.</p>
            </div>
        </div>

        <!-- Right Side - Info Panel -->
        <div class="hidden lg:flex lg:w-1/2 gradient-bg items-center justify-center p-12">
            <div class="text-center text-white max-w-lg">
                <div class="mb-8">
                    <i class="fas fa-water text-6xl mb-6 opacity-90"></i>
                    <h2 class="text-3xl font-bold mb-4">Sistem Analisis Hidrologi</h2>
                    <p class="text-lg opacity-90 mb-6">
                        Platform analisis hidrologi berbasis Machine Learning
                    </p>
                </div>
                
                <!-- Features -->
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4">
                        <i class="fas fa-brain text-2xl mb-2"></i>
                        <div class="font-medium">12 ML Models</div>
                    </div>
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4">
                        <i class="fas fa-satellite text-2xl mb-2"></i>
                        <div class="font-medium">Google Earth Engine</div>
                    </div>
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4">
                        <i class="fas fa-chart-line text-2xl mb-2"></i>
                        <div class="font-medium">Real-time Analysis</div>
                    </div>
                    <div class="bg-white bg-opacity-10 backdrop-blur-sm rounded-lg p-4">
                        <i class="fas fa-database text-2xl mb-2"></i>
                        <div class="font-medium">Big Data</div>
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

        if (togglePassword) {
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
        }
    </script>
</body>
</html>