@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div x-data="dashboardData()" class="space-y-8">
    <!-- Welcome Section with Enhanced Design -->
    <div class="mb-8">
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-purple-800 rounded-2xl p-8 text-white shadow-2xl">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-white rounded-full"></div>
                <div class="absolute top-20 -right-5 w-20 h-20 bg-white rounded-full"></div>
                <div class="absolute -bottom-5 -left-5 w-32 h-32 bg-white rounded-full"></div>
            </div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="mb-4 md:mb-0">
                    <h1 class="text-3xl md:text-4xl font-bold mb-2 animate-fade-in">
                        Selamat Datang, {{ auth()->user()->name }}! 👋
                    </h1>
                    <p class="text-blue-100 text-lg animate-fade-in-delay">
                        Kelola sistem dengan mudah melalui dashboard yang powerful
                    </p>
                    <div class="mt-4 flex items-center text-blue-200">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        <span>{{ now()->format('l, d F Y') }}</span>
                        <i class="fas fa-clock ml-4 mr-2"></i>
                        <span x-text="currentTime"></span>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="w-24 h-24 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-chart-line text-4xl text-white"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Stats Overview with Animations -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Users -->
        <div class="group bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 hover:border-blue-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Users</p>
                        <div class="ml-2 w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 mb-2 group-hover:text-blue-600 transition-colors" x-data="{ count: 0 }" x-init="$nextTick(() => { let target = {{ $totalUsers }}; let increment = target / 50; let timer = setInterval(() => { count += increment; if (count >= target) { count = target; clearInterval(timer); } }, 20); });" x-text="Math.floor(count).toLocaleString()">{{ number_format($totalUsers) }}</p>
                    <div class="flex items-center">
                        <div class="flex items-center bg-green-100 rounded-full px-2 py-1">
                            <i class="fas fa-arrow-up text-green-600 text-xs mr-1"></i>
                            <span class="text-sm text-green-600 font-semibold">+{{ $todayRegistrations }}</span>
                        </div>
                        <span class="text-sm text-gray-500 ml-2">hari ini</span>
                    </div>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                    <i class="fas fa-users text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Admins -->
        <div class="group bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 hover:border-green-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Admin</p>
                        <div class="ml-2 w-2 h-2 bg-blue-400 rounded-full animate-pulse"></div>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 mb-2 group-hover:text-green-600 transition-colors" x-data="{ count: 0 }" x-init="$nextTick(() => { let target = {{ $totalAdmins }}; let increment = target / 30; let timer = setInterval(() => { count += increment; if (count >= target) { count = target; clearInterval(timer); } }, 30); });" x-text="Math.floor(count).toLocaleString()">{{ number_format($totalAdmins) }}</p>
                    <div class="flex items-center">
                        <div class="flex items-center bg-blue-100 rounded-full px-2 py-1">
                            <i class="fas fa-users text-blue-600 text-xs mr-1"></i>
                            <span class="text-sm text-blue-600 font-semibold">{{ number_format($totalRegularUsers) }}</span>
                        </div>
                        <span class="text-sm text-gray-500 ml-2">regular users</span>
                    </div>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                    <i class="fas fa-user-shield text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Today Logins -->
        <div class="group bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 hover:border-purple-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Login Hari Ini</p>
                        <div class="ml-2 w-2 h-2 bg-purple-400 rounded-full animate-pulse"></div>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 mb-2 group-hover:text-purple-600 transition-colors" x-data="{ count: 0 }" x-init="$nextTick(() => { let target = {{ $todayLogins }}; let increment = target / 25; let timer = setInterval(() => { count += increment; if (count >= target) { count = target; clearInterval(timer); } }, 40); });" x-text="Math.floor(count).toLocaleString()">{{ number_format($todayLogins) }}</p>
                    <div class="flex items-center">
                        <div class="flex items-center bg-purple-100 rounded-full px-2 py-1">
                            <i class="fas fa-chart-line text-purple-600 text-xs mr-1"></i>
                            <span class="text-sm text-purple-600 font-semibold">{{ $thisWeekRegistrations }}</span>
                        </div>
                        <span class="text-sm text-gray-500 ml-2">minggu ini</span>
                    </div>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                    <i class="fas fa-sign-in-alt text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Audit Logs -->
        <div class="group bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 hover:border-orange-200">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">Total Audit Log</p>
                        <div class="ml-2 w-2 h-2 bg-orange-400 rounded-full animate-pulse"></div>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 mb-2 group-hover:text-orange-600 transition-colors" x-data="{ count: 0 }" x-init="$nextTick(() => { let target = {{ $totalAuditLogs }}; let increment = target / 40; let timer = setInterval(() => { count += increment; if (count >= target) { count = target; clearInterval(timer); } }, 25); });" x-text="Math.floor(count).toLocaleString()">{{ number_format($totalAuditLogs) }}</p>
                    <div class="flex items-center">
                        <div class="flex items-center bg-orange-100 rounded-full px-2 py-1">
                            <i class="fas fa-calendar text-orange-600 text-xs mr-1"></i>
                            <span class="text-sm text-orange-600 font-semibold">{{ $thisMonthRegistrations }}</span>
                        </div>
                        <span class="text-sm text-gray-500 ml-2">bulan ini</span>
                    </div>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-orange-500 to-orange-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                    <i class="fas fa-clipboard-list text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- User Growth Chart -->
        <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-chart-line text-blue-600 mr-3"></i>
                        Pertumbuhan User
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">Tren registrasi pengguna baru</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 bg-blue-100 text-blue-600 rounded-full text-xs font-semibold">7 Bulan Terakhir</span>
                    <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors" title="Refresh Chart">
                        <i class="fas fa-sync-alt text-gray-400 hover:text-gray-600"></i>
                    </button>
                </div>
            </div>
            <div class="relative h-72 bg-gradient-to-br from-blue-50 to-transparent rounded-xl p-4">
                <canvas id="userGrowthChart"></canvas>
            </div>
        </div>

        <!-- Login Statistics -->
        <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-chart-bar text-purple-600 mr-3"></i>
                        Statistik Login
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">Aktivitas login harian pengguna</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 bg-purple-100 text-purple-600 rounded-full text-xs font-semibold">7 Hari Terakhir</span>
                    <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors" title="Refresh Chart">
                        <i class="fas fa-sync-alt text-gray-400 hover:text-gray-600"></i>
                    </button>
                </div>
            </div>
            <div class="relative h-72 bg-gradient-to-br from-purple-50 to-transparent rounded-xl p-4">
                <canvas id="loginStatsChart"></canvas>
            </div>
        </div>
    </div>
    <!-- Enhanced Data Tables Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Users -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-blue-50 to-transparent">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-user-plus text-blue-600 mr-3"></i>
                            User Terbaru
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Pengguna yang baru bergabung</p>
                    </div>
                    <a href="{{ route('users.index') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-semibold rounded-lg hover:bg-blue-700 transition-colors shadow-md hover:shadow-lg">
                        <span>Lihat Semua</span>
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @forelse($recentUsers as $user)
                    <div class="group flex items-center space-x-4 p-4 hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent rounded-xl transition-all duration-300 cursor-pointer border border-transparent hover:border-blue-100">
                        <div class="relative">
                            <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-300">
                                <span class="text-white font-bold text-lg">{{ strtoupper(substr($user->name, 0, 1)) }}</span>
                            </div>
                            <div class="absolute -top-1 -right-1 w-4 h-4 bg-green-400 rounded-full border-2 border-white"></div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate group-hover:text-blue-600 transition-colors">{{ $user->name }}</p>
                            <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                        </div>
                        <div class="text-right">
                            @php
                                $userRoles = $user->roles;
                                $roleClass = $userRoles->contains('name', 'super_admin') ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800';
                                $roleName = $userRoles->first() ? ucwords(str_replace('_', ' ', $userRoles->first()->name)) : 'No Role';
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full {{ $roleClass }}">
                                @if($userRoles->contains('name', 'super_admin'))
                                    <i class="fas fa-crown mr-1"></i>
                                @endif
                                {{ $roleName }}
                            </span>
                            <p class="text-xs text-gray-500 mt-1">{{ $user->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-users text-gray-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-500 font-medium">Belum ada user terbaru</p>
                        <p class="text-gray-400 text-sm mt-1">User baru akan muncul di sini</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-purple-50 to-transparent">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-history text-purple-600 mr-3"></i>
                            Aktivitas Terbaru
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">Log aktivitas sistem terkini</p>
                    </div>
                    <a href="{{ route('audit.index') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700 transition-colors shadow-md hover:shadow-lg">
                        <span>Lihat Semua</span>
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @forelse($recentActivity as $activity)
                    <div class="group flex items-start space-x-4 p-4 hover:bg-gradient-to-r hover:from-purple-50 hover:to-transparent rounded-xl transition-all duration-300 border border-transparent hover:border-purple-100">
                        <div class="flex-shrink-0">
                            @php
                                $iconClass = '';
                                $bgClass = '';
                                $textClass = '';
                                
                                switch($activity->action) {
                                    case 'Login':
                                        $iconClass = 'fas fa-sign-in-alt';
                                        $bgClass = 'bg-green-100';
                                        $textClass = 'text-green-600';
                                        break;
                                    case 'Logout':
                                        $iconClass = 'fas fa-sign-out-alt';
                                        $bgClass = 'bg-red-100';
                                        $textClass = 'text-red-600';
                                        break;
                                    default:
                                        $iconClass = 'fas fa-cog';
                                        $bgClass = 'bg-blue-100';
                                        $textClass = 'text-blue-600';
                                }
                            @endphp
                            <div class="w-10 h-10 {{ $bgClass }} rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-sm">
                                <i class="{{ $iconClass }} {{ $textClass }}"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <p class="text-sm font-semibold text-gray-900 group-hover:text-purple-600 transition-colors">
                                    {{ $activity->user ? $activity->user->name : 'Unknown User' }}
                                </p>
                                <span class="px-2 py-1 bg-gray-100 text-gray-600 text-xs rounded-full font-medium">
                                    {{ $activity->action }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500 mt-1">{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-history text-gray-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-500 font-medium">Belum ada aktivitas</p>
                        <p class="text-gray-400 text-sm mt-1">Aktivitas akan muncul di sini</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
@keyframes fade-in {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

@keyframes fade-in-delay {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.animate-fade-in {
    animation: fade-in 0.8s ease-out;
}

.animate-fade-in-delay {
    animation: fade-in-delay 0.8s ease-out 0.3s both;
}

.group:hover .group-hover\:scale-110 {
    transform: scale(1.1);
}

.group:hover .group-hover\:text-blue-600 {
    color: #2563eb;
}

.group:hover .group-hover\:text-green-600 {
    color: #059669;
}

.group:hover .group-hover\:text-purple-600 {
    color: #7c3aed;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 3px;
}

::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>

<script>
function dashboardData() {
    return {
        currentTime: new Date().toLocaleTimeString('id-ID'),
        
        init() {
            // Update time every second
            setInterval(() => {
                this.currentTime = new Date().toLocaleTimeString('id-ID');
            }, 1000);
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // User Growth Chart
    const userGrowthCtx = document.getElementById('userGrowthChart');
    if (userGrowthCtx) {
        new Chart(userGrowthCtx, {
            type: 'line',
            data: {
                labels: @json($userGrowthData['months']),
                datasets: [{
                    label: 'User Baru',
                    data: @json($userGrowthData['userCounts']),
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgb(59, 130, 246)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: 'rgb(59, 130, 246)',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        cornerRadius: 10,
                        displayColors: false,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    y: {
                        display: true,
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0,
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    }
                },
                interaction: {
                    mode: 'nearest',
                    axis: 'x',
                    intersect: false
                },
                elements: {
                    point: {
                        hoverRadius: 8
                    }
                }
            }
        });
    }

    // Login Statistics Chart
    const loginStatsCtx = document.getElementById('loginStatsChart');
    if (loginStatsCtx) {
        new Chart(loginStatsCtx, {
            type: 'bar',
            data: {
                labels: @json($loginStats['days']),
                datasets: [{
                    label: 'Login',
                    data: @json($loginStats['loginCounts']),
                    backgroundColor: 'rgba(168, 85, 247, 0.8)',
                    borderColor: 'rgba(168, 85, 247, 1)',
                    borderWidth: 2,
                    borderRadius: 8,
                    borderSkipped: false,
                    hoverBackgroundColor: 'rgba(168, 85, 247, 0.9)',
                    hoverBorderColor: 'rgba(168, 85, 247, 1)',
                    hoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 14,
                                weight: 'bold'
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.9)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        borderColor: 'rgba(168, 85, 247, 1)',
                        borderWidth: 2,
                        cornerRadius: 10,
                        displayColors: false,
                        titleFont: {
                            size: 14,
                            weight: 'bold'
                        },
                        bodyFont: {
                            size: 13
                        }
                    }
                },
                scales: {
                    x: {
                        display: true,
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    },
                    y: {
                        display: true,
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)',
                            drawBorder: false
                        },
                        ticks: {
                            precision: 0,
                            font: {
                                size: 12,
                                weight: '500'
                            }
                        }
                    }
                },
                animation: {
                    duration: 2000,
                    easing: 'easeInOutQuart'
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }
});
</script>
@endsection
