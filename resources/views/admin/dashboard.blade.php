@extends('layouts.app')

@section('title', 'RIVANA - Dashboard')

@section('content')
<div x-data="dashboardData()" class="space-y-6">
    <!-- Modern Welcome Banner -->
    <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-600 rounded-3xl shadow-2xl">
        <!-- Animated Background Pattern -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute w-96 h-96 -top-48 -right-48 bg-white rounded-full animate-pulse"></div>
            <div class="absolute w-64 h-64 -bottom-32 -left-32 bg-white rounded-full animate-pulse" style="animation-delay: 1s;"></div>
            <div class="absolute w-40 h-40 top-1/2 left-1/4 bg-white rounded-full animate-pulse" style="animation-delay: 2s;"></div>
        </div>
        
        <div class="relative z-10 p-8 md:p-10">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-12 h-12 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center">
                            <i class="fas fa-water text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl md:text-4xl font-extrabold text-white tracking-tight">
                                RIVANA Dashboard
                            </h1>
                            <p class="text-blue-100 text-sm mt-1">River DNA Analysis System</p>
                        </div>
                    </div>
                    
                    <p class="text-white/90 text-lg font-medium mb-4">
                        {{ __('messages.welcome_to_rivana') }}
                    </p>
                    
                    <div class="flex flex-wrap items-center gap-4 text-sm">
                        <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-xl">
                            <i class="fas fa-calendar-alt text-blue-200"></i>
                            <span class="text-white font-medium">{{ now()->format('d M Y') }}</span>
                        </div>
                        <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-xl">
                            <i class="fas fa-clock text-blue-200"></i>
                            <span class="text-white font-medium" x-text="currentTime"></span>
                        </div>
                        <div class="flex items-center gap-2 bg-green-500/20 backdrop-blur-sm px-4 py-2 rounded-xl border border-green-300/30">
                            <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                            <span class="text-white font-medium">System Online</span>
                        </div>
                    </div>
                </div>
                
                <div class="hidden md:flex items-center justify-center">
                    <div class="relative">
                        <div class="absolute inset-0 bg-white/20 rounded-3xl blur-xl"></div>
                        <div class="relative w-28 h-28 bg-white rounded-3xl shadow-2xl flex items-center justify-center transform hover:scale-105 transition-transform duration-300">
                            <img src="/images/logo2.jpg" alt="Logo" class="w-24 h-24 rounded-2xl object-cover">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modern Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Hidrologi Jobs -->
        <div class="group relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-cyan-500/10 to-blue-500/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-chart-area text-white text-xl"></i>
                    </div>
                    <div class="flex items-center gap-1 bg-cyan-50 px-3 py-1 rounded-full">
                        <i class="fas fa-arrow-up text-cyan-600 text-xs"></i>
                        <span class="text-xs font-bold text-cyan-600">+{{ $todayJobs }}</span>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-500 mb-1">{{ __('messages.total_analysis') }}</p>
                <p class="text-3xl font-extrabold text-gray-900 mb-2" x-data="{ count: 0 }" x-init="$nextTick(() => { let target = {{ $totalHidrologiJobs }}; let increment = target / 50; let timer = setInterval(() => { count += increment; if (count >= target) { count = target; clearInterval(timer); } }, 20); });" x-text="Math.floor(count).toLocaleString()">{{ number_format($totalHidrologiJobs) }}</p>
                <p class="text-xs text-gray-500">{{ __('messages.today') }}</p>
            </div>
        </div>

        <!-- Completed Jobs -->
        <div class="group relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-green-500/10 to-emerald-500/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                    <div class="flex items-center gap-1 bg-red-50 px-3 py-1 rounded-full">
                        <i class="fas fa-times text-red-600 text-xs"></i>
                        <span class="text-xs font-bold text-red-600">{{ number_format($failedJobs) }}</span>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-500 mb-1">{{ __('messages.successful') }}</p>
                <p class="text-3xl font-extrabold text-gray-900 mb-2" x-data="{ count: 0 }" x-init="$nextTick(() => { let target = {{ $completedJobs }}; let increment = target / 30; let timer = setInterval(() => { count += increment; if (count >= target) { count = target; clearInterval(timer); } }, 30); });" x-text="Math.floor(count).toLocaleString()">{{ number_format($completedJobs) }}</p>
                <p class="text-xs text-gray-500">{{ __('messages.failed') }}</p>
            </div>
        </div>

        <!-- Running Jobs -->
        <div class="group relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-amber-500/10 to-orange-500/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-amber-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-spinner fa-spin text-white text-xl"></i>
                    </div>
                    <div class="flex items-center gap-1 bg-amber-50 px-3 py-1 rounded-full">
                        <i class="fas fa-chart-line text-amber-600 text-xs"></i>
                        <span class="text-xs font-bold text-amber-600">{{ $thisWeekJobs }}</span>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-500 mb-1">{{ __('messages.running') }}</p>
                <p class="text-3xl font-extrabold text-gray-900 mb-2" x-data="{ count: 0 }" x-init="$nextTick(() => { let target = {{ $runningJobs }}; let increment = target / 25; let timer = setInterval(() => { count += increment; if (count >= target) { count = target; clearInterval(timer); } }, 40); });" x-text="Math.floor(count).toLocaleString()">{{ number_format($runningJobs) }}</p>
                <p class="text-xs text-gray-500">{{ __('messages.this_week') }}</p>
            </div>
        </div>

        <!-- Total Files Generated -->
        <div class="group relative overflow-hidden bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 border border-gray-100">
            <div class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-br from-purple-500/10 to-violet-500/10 rounded-full -mr-16 -mt-16 group-hover:scale-150 transition-transform duration-500"></div>
            <div class="relative p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-violet-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-file-alt text-white text-xl"></i>
                    </div>
                    <div class="flex items-center gap-1 bg-purple-50 px-3 py-1 rounded-full">
                        <i class="fas fa-calendar text-purple-600 text-xs"></i>
                        <span class="text-xs font-bold text-purple-600">{{ $thisMonthJobs }}</span>
                    </div>
                </div>
                <p class="text-sm font-medium text-gray-500 mb-1">{{ __('messages.total_files') }}</p>
                <p class="text-3xl font-extrabold text-gray-900 mb-2" x-data="{ count: 0 }" x-init="$nextTick(() => { let target = {{ $totalFiles }}; let increment = target / 40; let timer = setInterval(() => { count += increment; if (count >= target) { count = target; clearInterval(timer); } }, 25); });" x-text="Math.floor(count).toLocaleString()">{{ number_format($totalFiles) }}</p>
                <p class="text-xs text-gray-500">{{ __('messages.this_month') }}</p>
            </div>
        </div>
    </div>

    <!-- Modern Charts Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Hidrologi Jobs Growth Chart -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300">
            <div class="bg-gradient-to-r from-cyan-50 to-blue-50 px-6 py-5 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md">
                            <i class="fas fa-chart-line text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ __('messages.hydrology_growth') }}</h3>
                            <p class="text-xs text-gray-500">{{ __('messages.hydrology_growth_desc') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1.5 bg-white text-cyan-700 rounded-xl text-xs font-semibold shadow-sm border border-cyan-100">{{ __('messages.last_7_months') }}</span>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="relative h-72 bg-gradient-to-br from-cyan-50/30 to-transparent rounded-xl p-4">
                    <canvas id="hidrologiGrowthChart"></canvas>
                </div>
            </div>
        </div>

        <!-- Job Status Statistics -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300">
            <div class="bg-gradient-to-r from-green-50 to-emerald-50 px-6 py-5 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-md">
                            <i class="fas fa-chart-bar text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ __('messages.analysis_status') }}</h3>
                            <p class="text-xs text-gray-500">{{ __('messages.analysis_status_desc') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="px-3 py-1.5 bg-white text-green-700 rounded-xl text-xs font-semibold shadow-sm border border-green-100">{{ __('messages.last_7_days') }}</span>
                    </div>
                </div>
            </div>
            <div class="p-6">
                <div class="relative h-72 bg-gradient-to-br from-green-50/30 to-transparent rounded-xl p-4">
                    <canvas id="jobStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <!-- Modern Data Tables Section -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Recent Hidrologi Jobs -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300">
            <div class="bg-gradient-to-r from-cyan-50 to-blue-50 px-6 py-5 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center shadow-md">
                            <i class="fas fa-water text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ __('messages.latest_analysis') }}</h3>
                            <p class="text-xs text-gray-500">{{ __('messages.latest_analysis_desc') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('hidrologi.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-cyan-600 hover:bg-cyan-700 text-white text-sm font-semibold rounded-xl transition-all duration-300 shadow-md hover:shadow-lg hover:scale-105">
                        <span>{{ __('messages.view_all') }}</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-2">
                    @forelse($recentJobs as $job)
                    <div class="group flex items-center gap-4 p-4 bg-gray-50/50 hover:bg-cyan-50/50 rounded-2xl transition-all duration-300 cursor-pointer border border-transparent hover:border-cyan-200 hover:shadow-md">
                        @php
                            $statusColor = '';
                            $statusIcon = '';
                            $statusBg = '';
                            switch($job->status) {
                                case 'completed':
                                    $statusColor = 'from-green-500 to-emerald-600';
                                    $statusIcon = 'fa-check-circle';
                                    $statusBg = 'bg-green-500';
                                    break;
                                case 'failed':
                                    $statusColor = 'from-red-500 to-red-600';
                                    $statusIcon = 'fa-times-circle';
                                    $statusBg = 'bg-red-500';
                                    break;
                                case 'running':
                                case 'processing':
                                    $statusColor = 'from-amber-500 to-orange-600';
                                    $statusIcon = 'fa-spinner fa-spin';
                                    $statusBg = 'bg-amber-500';
                                    break;
                                default:
                                    $statusColor = 'from-gray-500 to-gray-600';
                                    $statusIcon = 'fa-clock';
                                    $statusBg = 'bg-gray-500';
                            }
                        @endphp
                        <div class="relative shrink-0">
                            <div class="w-12 h-12 bg-gradient-to-br {{ $statusColor }} rounded-2xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-300">
                                <i class="fas {{ $statusIcon }} text-white"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate mb-1">
                                {{ $job->location_name ?? 'Analisis #' . $job->job_id }}
                            </p>
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <i class="fas fa-user"></i>
                                <span>{{ $job->user->name ?? 'Unknown' }}</span>
                                <span>•</span>
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ $job->latitude }}, {{ $job->longitude }}</span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            @php
                                $statusBadgeClass = '';
                                $statusText = '';
                                switch($job->status) {
                                    case 'completed':
                                        $statusBadgeClass = 'bg-green-100 text-green-700 border-green-200';
                                        $statusText = 'Selesai';
                                        break;
                                    case 'failed':
                                        $statusBadgeClass = 'bg-red-100 text-red-700 border-red-200';
                                        $statusText = 'Gagal';
                                        break;
                                    case 'running':
                                    case 'processing':
                                        $statusBadgeClass = 'bg-amber-100 text-amber-700 border-amber-200';
                                        $statusText = 'Berjalan';
                                        break;
                                    default:
                                        $statusBadgeClass = 'bg-gray-100 text-gray-700 border-gray-200';
                                        $statusText = 'Pending';
                                }
                            @endphp
                            <span class="inline-flex items-center px-3 py-1.5 text-xs font-bold rounded-xl border {{ $statusBadgeClass }}">
                                {{ $statusText }}
                            </span>
                            <p class="text-xs text-gray-400 mt-1.5">{{ $job->created_at->diffForHumans() }}</p>
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
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300">
            <div class="bg-gradient-to-r from-purple-50 to-violet-50 px-6 py-5 border-b border-gray-100">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gradient-to-br from-purple-500 to-violet-600 rounded-xl flex items-center justify-center shadow-md">
                            <i class="fas fa-history text-white"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">{{ __('messages.recent_activity') }}</h3>
                            <p class="text-xs text-gray-500">{{ __('messages.recent_activity_desc') }}</p>
                        </div>
                    </div>
                    <a href="{{ route('audit.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white text-sm font-semibold rounded-xl transition-all duration-300 shadow-md hover:shadow-lg hover:scale-105">
                        <span>{{ __('messages.view_all') }}</span>
                        <i class="fas fa-arrow-right text-xs"></i>
                    </a>
                </div>
            </div>
            <div class="p-6">
                <div class="space-y-2">
                    @forelse($recentActivity as $activity)
                    <div class="group flex items-center gap-4 p-4 bg-gray-50/50 hover:bg-purple-50/50 rounded-2xl transition-all duration-300 border border-transparent hover:border-purple-200 hover:shadow-md">
                        @php
                            $iconClass = '';
                            $bgClass = '';
                            $textClass = '';
                            
                            switch($activity->action) {
                                case 'Login':
                                    $iconClass = 'fas fa-sign-in-alt';
                                    $bgClass = 'bg-green-500';
                                    $textClass = 'text-white';
                                    break;
                                case 'Logout':
                                    $iconClass = 'fas fa-sign-out-alt';
                                    $bgClass = 'bg-red-500';
                                    $textClass = 'text-white';
                                    break;
                                default:
                                    $iconClass = 'fas fa-cog';
                                    $bgClass = 'bg-blue-500';
                                    $textClass = 'text-white';
                            }
                        @endphp
                        <div class="shrink-0">
                            <div class="w-12 h-12 {{ $bgClass }} rounded-2xl flex items-center justify-center shadow-md group-hover:scale-110 transition-transform duration-300">
                                <i class="{{ $iconClass }} {{ $textClass }}"></i>
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-1">
                                <p class="text-sm font-bold text-gray-900">
                                    {{ $activity->user ? $activity->user->name : 'Unknown User' }}
                                </p>
                                <span class="px-3 py-1 bg-white border border-gray-200 text-gray-700 text-xs rounded-xl font-semibold">
                                    {{ $activity->action }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400">{{ $activity->created_at->diffForHumans() }}</p>
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
