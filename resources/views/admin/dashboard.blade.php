@extends('layouts.app')

@section('title', 'RIVANA')

@section('content')
<div x-data="dashboardData()" class="space-y-8">
    <!-- Welcome Section with Enhanced Design -->
    <div class="mb-8">
        <div class="relative overflow-hidden bg-gradient-to-br from-cyan-600 via-blue-700 to-indigo-900 rounded-2xl p-8 text-white shadow-2xl">
            <!-- Water Wave Pattern Background -->
            <div class="absolute inset-0 opacity-10">
                <svg class="absolute bottom-0 left-0 w-full" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
                    <path fill="#ffffff" fill-opacity="1" d="M0,96L48,112C96,128,192,160,288,160C384,160,480,128,576,122.7C672,117,768,139,864,138.7C960,139,1056,117,1152,106.7C1248,96,1344,96,1392,96L1440,96L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
                </svg>
                <div class="absolute -top-10 -right-10 w-40 h-40 bg-white rounded-full"></div>
                <div class="absolute top-20 right-20 w-20 h-20 bg-white rounded-full"></div>
                <div class="absolute bottom-10 left-10 w-32 h-32 bg-white rounded-full"></div>
            </div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="mb-4 md:mb-0">
                    <h1 class="text-3xl md:text-4xl font-bold mb-2 animate-fade-in flex items-center">
                        <i class="fas fa-water mr-3 text-cyan-300"></i>
                        {{ __('messages.river_dna_analysis') }} - {{ __('messages.hidrologi') }} {{ __('messages.analysis_status') }}
                    </h1>
                    <p class="text-cyan-100 text-lg animate-fade-in-delay">
                        {{ __('messages.welcome_to_rivana') }}
                    </p>
                    <p class="text-cyan-200 text-sm mt-2">
                        {{ __('messages.manage_monitor_hydrology') }}
                    </p>
                    <div class="mt-4 flex items-center text-cyan-200">
                        <i class="fas fa-calendar-alt mr-2"></i>
                        <span>{{ now()->format('l, d F Y') }}</span>
                        <i class="fas fa-clock ml-4 mr-2"></i>
                        <span x-text="currentTime"></span>
                    </div>
                </div>
                <div class="hidden md:block">
                    <div class="w-24 h-24 rounded-full flex items-center justify-center backdrop-blur-sm bg-white bg-opacity-90 shadow-2xl">
                        <img src="/images/logo2.jpg" alt="Logo" class="w-20 h-20 rounded-full object-cover">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Stats Overview with Animations -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Hidrologi Jobs -->
        <div class="group bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 hover:border-cyan-300">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">{{ __('messages.total_analysis') }}</p>
                        <div class="ml-2 w-2 h-2 bg-cyan-400 rounded-full animate-pulse"></div>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 mb-2 group-hover:text-cyan-600 transition-colors" x-data="{ count: 0 }" x-init="$nextTick(() => { let target = {{ $totalHidrologiJobs }}; let increment = target / 50; let timer = setInterval(() => { count += increment; if (count >= target) { count = target; clearInterval(timer); } }, 20); });" x-text="Math.floor(count).toLocaleString()">{{ number_format($totalHidrologiJobs) }}</p>
                    <div class="flex items-center">
                        <div class="flex items-center bg-cyan-100 rounded-full px-2 py-1">
                            <i class="fas fa-arrow-up text-cyan-600 text-xs mr-1"></i>
                            <span class="text-sm text-cyan-600 font-semibold">+{{ $todayJobs }}</span>
                        </div>
                        <span class="text-sm text-gray-500 ml-2">{{ __('messages.today') }}</span>
                    </div>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                    <i class="fas fa-chart-area text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Completed Jobs -->
        <div class="group bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 hover:border-green-300">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">{{ __('messages.successful') }}</p>
                        <div class="ml-2 w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 mb-2 group-hover:text-green-600 transition-colors" x-data="{ count: 0 }" x-init="$nextTick(() => { let target = {{ $completedJobs }}; let increment = target / 30; let timer = setInterval(() => { count += increment; if (count >= target) { count = target; clearInterval(timer); } }, 30); });" x-text="Math.floor(count).toLocaleString()">{{ number_format($completedJobs) }}</p>
                    <div class="flex items-center">
                        <div class="flex items-center bg-red-100 rounded-full px-2 py-1">
                            <i class="fas fa-times text-red-600 text-xs mr-1"></i>
                            <span class="text-sm text-red-600 font-semibold">{{ number_format($failedJobs) }}</span>
                        </div>
                        <span class="text-sm text-gray-500 ml-2">{{ __('messages.failed') }}</span>
                    </div>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                    <i class="fas fa-check-circle text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Running Jobs -->
        <div class="group bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 hover:border-amber-300">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">{{ __('messages.running') }}</p>
                        <div class="ml-2 w-2 h-2 bg-amber-400 rounded-full animate-pulse"></div>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 mb-2 group-hover:text-amber-600 transition-colors" x-data="{ count: 0 }" x-init="$nextTick(() => { let target = {{ $runningJobs }}; let increment = target / 25; let timer = setInterval(() => { count += increment; if (count >= target) { count = target; clearInterval(timer); } }, 40); });" x-text="Math.floor(count).toLocaleString()">{{ number_format($runningJobs) }}</p>
                    <div class="flex items-center">
                        <div class="flex items-center bg-amber-100 rounded-full px-2 py-1">
                            <i class="fas fa-chart-line text-amber-600 text-xs mr-1"></i>
                            <span class="text-sm text-amber-600 font-semibold">{{ $thisWeekJobs }}</span>
                        </div>
                        <span class="text-sm text-gray-500 ml-2">{{ __('messages.this_week') }}</span>
                    </div>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                    <i class="fas fa-spinner fa-spin text-white text-xl"></i>
                </div>
            </div>
        </div>

        <!-- Total Files Generated -->
        <div class="group bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 hover:border-purple-300">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <div class="flex items-center mb-2">
                        <p class="text-sm font-semibold text-gray-600 uppercase tracking-wide">{{ __('messages.total_files') }}</p>
                        <div class="ml-2 w-2 h-2 bg-purple-400 rounded-full animate-pulse"></div>
                    </div>
                    <p class="text-3xl font-bold text-gray-900 mb-2 group-hover:text-purple-600 transition-colors" x-data="{ count: 0 }" x-init="$nextTick(() => { let target = {{ $totalFiles }}; let increment = target / 40; let timer = setInterval(() => { count += increment; if (count >= target) { count = target; clearInterval(timer); } }, 25); });" x-text="Math.floor(count).toLocaleString()">{{ number_format($totalFiles) }}</p>
                    <div class="flex items-center">
                        <div class="flex items-center bg-purple-100 rounded-full px-2 py-1">
                            <i class="fas fa-calendar text-purple-600 text-xs mr-1"></i>
                            <span class="text-sm text-purple-600 font-semibold">{{ $thisMonthJobs }}</span>
                        </div>
                        <span class="text-sm text-gray-500 ml-2">{{ __('messages.this_month') }}</span>
                    </div>
                </div>
                <div class="w-16 h-16 bg-gradient-to-br from-purple-500 to-violet-600 rounded-2xl flex items-center justify-center group-hover:scale-110 transition-transform duration-300 shadow-lg">
                    <i class="fas fa-file-alt text-white text-xl"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
        <!-- Hidrologi Jobs Growth Chart -->
        <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-chart-line text-cyan-600 mr-3"></i>
                        {{ __('messages.hydrology_growth') }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">{{ __('messages.hydrology_growth_desc') }}</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 bg-cyan-100 text-cyan-600 rounded-full text-xs font-semibold">{{ __('messages.last_7_months') }}</span>
                    <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors" title="Refresh Chart">
                        <i class="fas fa-sync-alt text-gray-400 hover:text-gray-600"></i>
                    </button>
                </div>
            </div>
            <div class="relative h-72 bg-gradient-to-br from-cyan-50 to-transparent rounded-xl p-4">
                <canvas id="hidrologiGrowthChart"></canvas>
            </div>
        </div>

        <!-- Job Status Statistics -->
        <div class="bg-white p-6 rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-chart-bar text-green-600 mr-3"></i>
                        {{ __('messages.analysis_status') }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">{{ __('messages.analysis_status_desc') }}</p>
                </div>
                <div class="flex items-center space-x-2">
                    <span class="px-3 py-1 bg-green-100 text-green-600 rounded-full text-xs font-semibold">{{ __('messages.last_7_days') }}</span>
                    <button class="p-2 hover:bg-gray-100 rounded-lg transition-colors" title="Refresh Chart">
                        <i class="fas fa-sync-alt text-gray-400 hover:text-gray-600"></i>
                    </button>
                </div>
            </div>
            <div class="relative h-72 bg-gradient-to-br from-green-50 to-transparent rounded-xl p-4">
                <canvas id="jobStatusChart"></canvas>
            </div>
        </div>
    </div>
    <!-- Enhanced Data Tables Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Recent Hidrologi Jobs -->
        <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 bg-gradient-to-r from-cyan-50 to-transparent">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-water text-cyan-600 mr-3"></i>
                            {{ __('messages.latest_analysis') }}
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">{{ __('messages.latest_analysis_desc') }}</p>
                    </div>
                    <a href="{{ route('hidrologi.index') }}" class="inline-flex items-center px-4 py-2 bg-cyan-600 text-white text-sm font-semibold rounded-lg hover:bg-cyan-700 transition-colors shadow-md hover:shadow-lg">
                        <span>{{ __('messages.view_all') }}</span>
                        <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-3">
                    @forelse($recentJobs as $job)
                    <div class="group flex items-center space-x-4 p-4 hover:bg-gradient-to-r hover:from-cyan-50 hover:to-transparent rounded-xl transition-all duration-300 cursor-pointer border border-transparent hover:border-cyan-100">
                        <div class="relative">
                            @php
                                $statusColor = '';
                                $statusIcon = '';
                                switch($job->status) {
                                    case 'completed':
                                        $statusColor = 'from-green-500 to-emerald-600';
                                        $statusIcon = 'fa-check-circle';
                                        break;
                                    case 'failed':
                                        $statusColor = 'from-red-500 to-red-600';
                                        $statusIcon = 'fa-times-circle';
                                        break;
                                    case 'running':
                                    case 'processing':
                                        $statusColor = 'from-amber-500 to-orange-600';
                                        $statusIcon = 'fa-spinner fa-spin';
                                        break;
                                    default:
                                        $statusColor = 'from-gray-500 to-gray-600';
                                        $statusIcon = 'fa-clock';
                                }
                            @endphp
                            <div class="w-12 h-12 bg-gradient-to-br {{ $statusColor }} rounded-xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-300">
                                <i class="fas {{ $statusIcon }} text-white text-lg"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 truncate group-hover:text-cyan-600 transition-colors">
                                {{ $job->location_name ?? 'Analisis #' . $job->job_id }}
                            </p>
                            <p class="text-sm text-gray-500 truncate">
                                {{ $job->user->name ?? 'Unknown' }} • {{ $job->latitude }}, {{ $job->longitude }}
                            </p>
                        </div>
                        <div class="text-right">
                            @php
                                $statusBadgeClass = '';
                                $statusText = '';
                                switch($job->status) {
                                    case 'completed':
                                        $statusBadgeClass = 'bg-green-100 text-green-800';
                                        $statusText = 'Selesai';
                                        break;
                                    case 'failed':
                                        $statusBadgeClass = 'bg-red-100 text-red-800';
                                        $statusText = 'Gagal';
                                        break;
                                    case 'running':
                                    case 'processing':
                                        $statusBadgeClass = 'bg-amber-100 text-amber-800';
                                        $statusText = 'Berjalan';
                                        break;
                                    default:
                                        $statusBadgeClass = 'bg-gray-100 text-gray-800';
                                        $statusText = 'Pending';
                                }
                            @endphp
                            <span class="inline-flex items-center px-3 py-1 text-xs font-semibold rounded-full {{ $statusBadgeClass }}">
                                {{ $statusText }}
                            </span>
                            <p class="text-xs text-gray-500 mt-1">{{ $job->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-12">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-water text-gray-400 text-2xl"></i>
                        </div>
                        <p class="text-gray-500 font-medium">{{ __('messages.no_analysis_yet') }}</p>
                        <p class="text-gray-400 text-sm mt-1">{{ __('messages.new_analysis_will_appear') }}</p>
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
                            {{ __('messages.recent_activity') }}
                        </h3>
                        <p class="text-sm text-gray-500 mt-1">{{ __('messages.recent_activity_desc') }}</p>
                    </div>
                    <a href="{{ route('audit.index') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white text-sm font-semibold rounded-lg hover:bg-purple-700 transition-colors shadow-md hover:shadow-lg">
                        <span>{{ __('messages.view_all') }}</span>
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
                        <p class="text-gray-500 font-medium">{{ __('messages.no_activity_yet') }}</p>
                        <p class="text-gray-400 text-sm mt-1">{{ __('messages.activity_will_appear') }}</p>
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

@keyframes wave {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
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

.group:hover .group-hover\:text-cyan-600 {
    color: #0891b2;
}

.group:hover .group-hover\:text-green-600 {
    color: #059669;
}

.group:hover .group-hover\:text-amber-600 {
    color: #d97706;
}

.group:hover .group-hover\:text-purple-600 {
    color: #7c3aed;
}

/* Water ripple effect */
@keyframes ripple {
    0% {
        box-shadow: 0 0 0 0 rgba(6, 182, 212, 0.7),
                    0 0 0 10px rgba(6, 182, 212, 0.7),
                    0 0 0 20px rgba(6, 182, 212, 0.7);
    }
    100% {
        box-shadow: 0 0 0 10px rgba(6, 182, 212, 0.7),
                    0 0 0 20px rgba(6, 182, 212, 0.7),
                    0 0 0 30px rgba(6, 182, 212, 0);
    }
}

/* Custom scrollbar with water theme */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: linear-gradient(to bottom, #ecfeff, #cffafe);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(to bottom, #06b6d4, #0891b2);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(to bottom, #0891b2, #0e7490);
}

/* Glassmorphism effect for cards */
.glass-card {
    background: rgba(255, 255, 255, 0.9);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
}

/* Floating animation for water icons */
@keyframes float {
    0%, 100% {
        transform: translateY(0px);
    }
    50% {
        transform: translateY(-15px);
    }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

/* Gradient text effect */
.gradient-text {
    background: linear-gradient(135deg, #06b6d4 0%, #3b82f6 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
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
    // Hidrologi Jobs Growth Chart
    const hidrologiGrowthCtx = document.getElementById('hidrologiGrowthChart');
    if (hidrologiGrowthCtx) {
        new Chart(hidrologiGrowthCtx, {
            type: 'line',
            data: {
                labels: @json($hidrologiGrowthData['months']),
                datasets: [{
                    label: 'Analisis Hidrologi',
                    data: @json($hidrologiGrowthData['jobCounts']),
                    borderColor: 'rgb(6, 182, 212)',
                    backgroundColor: 'rgba(6, 182, 212, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgb(6, 182, 212)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 3,
                    pointRadius: 6,
                    pointHoverRadius: 8,
                    pointHoverBackgroundColor: 'rgb(6, 182, 212)',
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
                        borderColor: 'rgba(6, 182, 212, 1)',
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

    // Job Status Chart
    const jobStatusCtx = document.getElementById('jobStatusChart');
    if (jobStatusCtx) {
        new Chart(jobStatusCtx, {
            type: 'bar',
            data: {
                labels: @json($jobStatusData['days']),
                datasets: [
                    {
                        label: 'Berhasil',
                        data: @json($jobStatusData['completedCounts']),
                        backgroundColor: 'rgba(34, 197, 94, 0.8)',
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                        hoverBackgroundColor: 'rgba(34, 197, 94, 0.9)',
                        hoverBorderColor: 'rgba(34, 197, 94, 1)',
                        hoverBorderWidth: 3
                    },
                    {
                        label: 'Gagal',
                        data: @json($jobStatusData['failedCounts']),
                        backgroundColor: 'rgba(239, 68, 68, 0.8)',
                        borderColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 2,
                        borderRadius: 8,
                        borderSkipped: false,
                        hoverBackgroundColor: 'rgba(239, 68, 68, 0.9)',
                        hoverBorderColor: 'rgba(239, 68, 68, 1)',
                        hoverBorderWidth: 3
                    }
                ]
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
                        borderColor: 'rgba(34, 197, 94, 1)',
                        borderWidth: 2,
                        cornerRadius: 10,
                        displayColors: true,
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
