@extends('layouts.app')

@section('title', 'RIVANA - Dashboard')

@push('styles')
<style>
    /* ── Design tokens (selaras dengan halaman create & index) ── */
    :root {
        --c-teal:     #0d9488;
        --c-teal-lt:  #ccfbf1;
        --c-teal-dk:  #0f766e;
        --c-ocean:    #0e7490;
        --c-sky:      #e0f2fe;
        --c-slate:    #1e293b;
        --c-muted:    #64748b;
        --c-surface:  #f8fafc;
        --c-border:   #e2e8f0;
        --c-white:    #ffffff;
        --radius-card: 1.25rem;
        --shadow-card: 0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.06);
    }

    body { background: var(--c-surface); }

    /* ── Hero / welcome banner ── */
    .dash-hero {
        position: relative;
        overflow: hidden;
        border-radius: var(--radius-card);
        background: linear-gradient(120deg, var(--c-teal-dk), var(--c-teal) 55%, var(--c-ocean));
        box-shadow: var(--shadow-card);
        padding: 1.75rem 2rem;
        color: #fff;
    }
    .dash-hero-pattern {
        position: absolute;
        inset: 0;
        background-image: radial-gradient(rgba(255,255,255,.22) 1px, transparent 1px);
        background-size: 18px 18px;
        opacity: .35;
        pointer-events: none;
    }
    .dash-hero-glow {
        position: absolute;
        width: 18rem; height: 18rem;
        right: -5rem; bottom: -7rem;
        background: radial-gradient(circle, rgba(255,255,255,.22), transparent 70%);
        filter: blur(6px);
        pointer-events: none;
    }
    .dash-hero-inner { position: relative; z-index: 1; }
    .dash-hero h1 {
        background: linear-gradient(90deg, #ffffff, #99f6e4);
        -webkit-background-clip: text;
        background-clip: text;
        color: transparent;
    }
    .dash-hero-logo {
        width: 3.5rem; height: 3.5rem;
        border-radius: 1rem;
        background: #fff;
        display: flex; align-items: center; justify-content: center;
        box-shadow: 0 6px 18px rgba(0,0,0,.18);
        flex-shrink: 0;
        overflow: hidden;
    }
    .dash-hero-logo img { width: 100%; height: 100%; object-fit: cover; }

    .hero-chip {
        display: inline-flex; align-items: center; gap: 0.5rem;
        background: rgba(255,255,255,.14);
        backdrop-filter: blur(6px);
        border: 1px solid rgba(255,255,255,.2);
        padding: 0.5rem 0.9rem;
        border-radius: 0.75rem;
        font-size: 0.8rem;
        font-weight: 600;
    }
    .hero-chip-live { background: rgba(16,185,129,.22); border-color: rgba(16,185,129,.4); }
    .live-dot {
        width: 0.5rem; height: 0.5rem;
        border-radius: 50%;
        background: #34d399;
        flex-shrink: 0;
        animation: live-pulse 1.8s ease-in-out infinite;
    }
    @keyframes live-pulse {
        0%, 100% { opacity: 1; box-shadow: 0 0 0 0 rgba(52,211,153,.5); }
        50% { opacity: .7; box-shadow: 0 0 0 4px rgba(52,211,153,0); }
    }

    /* ── Card dasar (sama dengan step-card di create & index) ── */
    .step-card {
        background: var(--c-white);
        border-radius: var(--radius-card);
        border: 1.5px solid var(--c-border);
        box-shadow: var(--shadow-card);
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .step-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(15,23,42,.10), 0 2px 8px rgba(15,23,42,.06);
    }
    .step-header {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        padding: 1.125rem 1.25rem;
        border-bottom: 1.5px solid var(--c-border);
        flex-wrap: wrap;
    }
    .step-badge {
        width: 2.25rem; height: 2.25rem;
        border-radius: 0.625rem;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 0.875rem;
        flex-shrink: 0;
        color: #fff;
        box-shadow: 0 6px 14px -4px rgba(0,0,0,.3);
    }

    /* ── Stat cards ── */
    .stat-card {
        position: relative;
        overflow: hidden;
        background: var(--c-white);
        border: 1.5px solid var(--c-border);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        padding: 1.25rem;
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0;
        height: 3px;
        background: var(--accent, var(--c-teal));
    }
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 30px rgba(15,23,42,.10), 0 2px 8px rgba(15,23,42,.06);
    }
    .stat-icon {
        width: 2.75rem; height: 2.75rem;
        border-radius: 0.75rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; font-size: 1.05rem; color: #fff;
        box-shadow: 0 8px 18px -6px rgba(15,23,42,.4);
    }
    .stat-chip {
        display: inline-flex; align-items: center; gap: 0.3rem;
        padding: 0.25rem 0.6rem; border-radius: 9999px;
        font-size: 0.68rem; font-weight: 800; white-space: nowrap;
    }
    .stat-value {
        font-size: 1.875rem; font-weight: 800; color: var(--c-slate); line-height: 1;
        font-variant-numeric: tabular-nums; letter-spacing: -0.02em;
    }
    .stat-label { font-size: 0.8rem; font-weight: 700; color: var(--c-slate); margin-top: 0.2rem; }
    .stat-caption { font-size: 0.7rem; color: var(--c-muted); margin-top: 0.15rem; }

    /* ── Tag pill (kanan atas header chart) ── */
    .tag-pill {
        padding: 0.375rem 0.75rem; border-radius: 0.625rem;
        font-size: 0.72rem; font-weight: 700;
        background: var(--c-surface); border: 1.5px solid var(--c-border); color: var(--c-muted);
    }

    /* ── Link "lihat semua" ── */
    .view-all-link {
        display: inline-flex; align-items: center; gap: 0.4rem;
        padding: 0.5rem 0.9rem;
        background: var(--c-teal);
        color: #fff;
        font-size: 0.78rem; font-weight: 700;
        border-radius: 0.625rem;
        text-decoration: none;
        transition: background .15s;
        flex-shrink: 0;
    }
    .view-all-link:hover { background: var(--c-teal-dk); color: #fff; }

    /* ── Baris list (recent jobs / recent activity) ── */
    .list-row {
        display: flex; align-items: center; gap: 1rem;
        padding: 0.875rem;
        border-radius: 0.875rem;
        border: 1.5px solid transparent;
        transition: background .15s, border-color .15s, transform .15s;
    }
    .list-row:hover { background: var(--c-surface); border-color: var(--c-border); transform: translateX(3px); }
    .list-icon {
        width: 2.5rem; height: 2.5rem;
        border-radius: 0.75rem;
        display: flex; align-items: center; justify-content: center;
        color: #fff;
        flex-shrink: 0;
        box-shadow: 0 6px 14px -5px rgba(15,23,42,.4);
    }
    .list-badge {
        display: inline-flex; align-items: center;
        padding: 0.3rem 0.7rem; border-radius: 9999px;
        font-size: 0.7rem; font-weight: 700;
        border: 1px solid transparent;
    }
    .empty-state-icon {
        width: 3.5rem; height: 3.5rem;
        border-radius: 1rem;
        background: var(--c-teal-lt);
        color: var(--c-teal);
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.4rem;
    }

    /* ── Entrance animation (halus, sekali jalan) ── */
    @keyframes dash-fade-up {
        from { opacity: 0; transform: translateY(12px); }
        to   { opacity: 1; transform: translateY(0); }
    }
    [x-data="dashboardData()"] > * {
        animation: dash-fade-up .5s ease both;
    }
    [x-data="dashboardData()"] > *:nth-child(2) { animation-delay: .06s; }
    [x-data="dashboardData()"] > *:nth-child(3) { animation-delay: .12s; }
    [x-data="dashboardData()"] > *:nth-child(4) { animation-delay: .18s; }
    @media (prefers-reduced-motion: reduce) {
        [x-data="dashboardData()"] > * { animation: none; }
        .step-card:hover, .stat-card:hover { transform: none; }
    }
</style>
@endpush

@section('content')
<div x-data="dashboardData()" class="space-y-5">

    {{-- ── Welcome banner ── --}}
    <div class="dash-hero">
        <div class="dash-hero-pattern"></div>
        <div class="dash-hero-glow"></div>
        <div class="dash-hero-inner flex flex-col md:flex-row md:items-center md:justify-between gap-5">
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-1">
                    <div class="dash-hero-logo">
                        <img src="/images/logo2.jpg" alt="Logo">
                    </div>
                    <div>
                        <h1 class="text-2xl md:text-3xl font-extrabold tracking-tight">RIVANA Dashboard</h1>
                        <p class="text-sm" style="color: rgba(255,255,255,.85);">River DNA Analysis System</p>
                    </div>
                </div>
                <p class="text-sm md:text-base font-medium mt-3" style="color: rgba(255,255,255,.92);">
                    {{ __('messages.welcome_to_rivana') }}
                </p>

                <div class="flex flex-wrap items-center gap-2.5 mt-4">
                    <span class="hero-chip">
                        <i class="fas fa-calendar-alt"></i>
                        {{ now()->format('d M Y') }}
                    </span>
                    <span class="hero-chip">
                        <i class="fas fa-clock"></i>
                        <span x-text="currentTime"></span>
                    </span>
                    <span class="hero-chip hero-chip-live">
                        <span class="live-dot"></span>
                        System Online
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Stat cards ── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Total Analisis --}}
        <div class="stat-card" style="--accent:#0d9488;">
            <div class="flex items-start justify-between mb-3">
                <div class="stat-icon" style="background: linear-gradient(135deg, #14b8a6, var(--c-teal-dk));"><i class="fas fa-chart-area"></i></div>
                <span class="stat-chip" style="background: var(--c-teal-lt); color: var(--c-teal-dk);">
                    <i class="fas fa-arrow-up" style="font-size:0.6rem"></i>+{{ $todayJobs }}
                </span>
            </div>
            <p class="stat-value"
               x-data="{ count: 0 }"
               x-init="$nextTick(() => { let target = {{ $totalHidrologiJobs }}; let increment = target / 50; let timer = setInterval(() => { count += increment; if (count >= target) { count = target; clearInterval(timer); } }, 20); });"
               x-text="Math.floor(count).toLocaleString()">{{ number_format($totalHidrologiJobs) }}</p>
            <p class="stat-label">{{ __('messages.total_analysis') }}</p>
            <p class="stat-caption">{{ __('messages.today') }}</p>
        </div>

        {{-- Successful --}}
        <div class="stat-card" style="--accent:#10b981;">
            <div class="flex items-start justify-between mb-3">
                <div class="stat-icon" style="background: linear-gradient(135deg, #34d399, #059669);"><i class="fas fa-check-circle"></i></div>
                <span class="stat-chip" style="background:#fee2e2; color:#b91c1c;">
                    <i class="fas fa-times" style="font-size:0.6rem"></i>{{ number_format($failedJobs) }}
                </span>
            </div>
            <p class="stat-value"
               x-data="{ count: 0 }"
               x-init="$nextTick(() => { let target = {{ $completedJobs }}; let increment = target / 30; let timer = setInterval(() => { count += increment; if (count >= target) { count = target; clearInterval(timer); } }, 30); });"
               x-text="Math.floor(count).toLocaleString()">{{ number_format($completedJobs) }}</p>
            <p class="stat-label">{{ __('messages.successful') }}</p>
            <p class="stat-caption">{{ __('messages.failed') }}</p>
        </div>

        {{-- Running --}}
        <div class="stat-card" style="--accent:#d97706;">
            <div class="flex items-start justify-between mb-3">
                <div class="stat-icon" style="background: linear-gradient(135deg, #fbbf24, #d97706);"><i class="fas fa-spinner fa-spin"></i></div>
                <span class="stat-chip" style="background:#fef3c7; color:#b45309;">
                    <i class="fas fa-chart-line" style="font-size:0.6rem"></i>{{ $thisWeekJobs }}
                </span>
            </div>
            <p class="stat-value"
               x-data="{ count: 0 }"
               x-init="$nextTick(() => { let target = {{ $runningJobs }}; let increment = target / 25; let timer = setInterval(() => { count += increment; if (count >= target) { count = target; clearInterval(timer); } }, 40); });"
               x-text="Math.floor(count).toLocaleString()">{{ number_format($runningJobs) }}</p>
            <p class="stat-label">{{ __('messages.running') }}</p>
            <p class="stat-caption">{{ __('messages.this_week') }}</p>
        </div>

        {{-- Total Files --}}
        <div class="stat-card" style="--accent:#7c3aed;">
            <div class="flex items-start justify-between mb-3">
                <div class="stat-icon" style="background: linear-gradient(135deg, #a78bfa, #7c3aed);"><i class="fas fa-file-alt"></i></div>
                <span class="stat-chip" style="background:#ede9fe; color:#6d28d9;">
                    <i class="fas fa-calendar" style="font-size:0.6rem"></i>{{ $thisMonthJobs }}
                </span>
            </div>
            <p class="stat-value"
               x-data="{ count: 0 }"
               x-init="$nextTick(() => { let target = {{ $totalFiles }}; let increment = target / 40; let timer = setInterval(() => { count += increment; if (count >= target) { count = target; clearInterval(timer); } }, 25); });"
               x-text="Math.floor(count).toLocaleString()">{{ number_format($totalFiles) }}</p>
            <p class="stat-label">{{ __('messages.total_files') }}</p>
            <p class="stat-caption">{{ __('messages.this_month') }}</p>
        </div>
    </div>

    {{-- ── Charts ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        <div class="step-card" style="overflow:hidden;">
            <div class="step-header">
                <div class="step-badge" style="background: linear-gradient(135deg, #14b8a6, var(--c-teal-dk));"><i class="fas fa-chart-line" style="font-size:0.8rem"></i></div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-extrabold text-gray-900">{{ __('messages.hydrology_growth') }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ __('messages.hydrology_growth_desc') }}</p>
                </div>
                <span class="tag-pill">{{ __('messages.last_7_months') }}</span>
            </div>
            <div class="p-4">
                <div class="relative" style="height: 18rem;">
                    <canvas id="hidrologiGrowthChart"></canvas>
                </div>
            </div>
        </div>

        <div class="step-card" style="overflow:hidden;">
            <div class="step-header">
                <div class="step-badge" style="background: linear-gradient(135deg, #34d399, #059669);"><i class="fas fa-chart-bar" style="font-size:0.8rem"></i></div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-extrabold text-gray-900">{{ __('messages.analysis_status') }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ __('messages.analysis_status_desc') }}</p>
                </div>
                <span class="tag-pill">{{ __('messages.last_7_days') }}</span>
            </div>
            <div class="p-4">
                <div class="relative" style="height: 18rem;">
                    <canvas id="jobStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Recent lists ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Latest Analysis --}}
        <div class="step-card" style="overflow:hidden;">
            <div class="step-header">
                <div class="step-badge" style="background: linear-gradient(135deg, #14b8a6, var(--c-teal-dk));"><i class="fas fa-water" style="font-size:0.8rem"></i></div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-extrabold text-gray-900">{{ __('messages.latest_analysis') }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ __('messages.latest_analysis_desc') }}</p>
                </div>
                <a href="{{ route('hidrologi.index') }}" class="view-all-link">
                    {{ __('messages.view_all') }}
                    <i class="fas fa-arrow-right" style="font-size:0.65rem"></i>
                </a>
            </div>
            <div class="p-3">
                @forelse($recentJobs as $job)
                    @php
                        switch ($job->status) {
                            case 'completed':
                                $jIcon = 'fa-check-circle'; $jBg = '#10b981';
                                $jBadgeBg = '#d1fae5'; $jBadgeText = '#047857'; $jBadgeLabel = 'Selesai';
                                break;
                            case 'failed':
                                $jIcon = 'fa-times-circle'; $jBg = '#ef4444';
                                $jBadgeBg = '#fee2e2'; $jBadgeText = '#b91c1c'; $jBadgeLabel = 'Gagal';
                                break;
                            case 'running':
                            case 'processing':
                                $jIcon = 'fa-spinner fa-spin'; $jBg = '#d97706';
                                $jBadgeBg = '#fef3c7'; $jBadgeText = '#b45309'; $jBadgeLabel = 'Berjalan';
                                break;
                            default:
                                $jIcon = 'fa-clock'; $jBg = '#64748b';
                                $jBadgeBg = '#f1f5f9'; $jBadgeText = '#475569'; $jBadgeLabel = 'Pending';
                        }
                    @endphp
                    <div class="list-row">
                        <div class="list-icon" style="background: {{ $jBg }};">
                            <i class="fas {{ $jIcon }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-gray-900 truncate mb-0.5">
                                {{ $job->location_name ?? 'Analisis #' . $job->job_id }}
                            </p>
                            <div class="flex items-center gap-1.5 text-xs text-gray-400 flex-wrap">
                                <i class="fas fa-user"></i>
                                <span>{{ $job->user->name ?? 'Unknown' }}</span>
                                <span>&middot;</span>
                                <i class="fas fa-map-marker-alt"></i>
                                <span>{{ $job->latitude }}, {{ $job->longitude }}</span>
                            </div>
                        </div>
                        <div class="text-right shrink-0">
                            <span class="list-badge" style="background: {{ $jBadgeBg }}; color: {{ $jBadgeText }};">
                                {{ $jBadgeLabel }}
                            </span>
                            <p class="text-xs text-gray-400 mt-1.5">{{ $job->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <div class="empty-state-icon"><i class="fas fa-water"></i></div>
                        <p class="text-gray-600 font-semibold text-sm">{{ __('messages.no_analysis_yet') }}</p>
                        <p class="text-gray-400 text-xs mt-1">{{ __('messages.new_analysis_will_appear') }}</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Recent Activity --}}
        <div class="step-card" style="overflow:hidden;">
            <div class="step-header">
                <div class="step-badge" style="background: linear-gradient(135deg, #a78bfa, #7c3aed);"><i class="fas fa-history" style="font-size:0.8rem"></i></div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-sm font-extrabold text-gray-900">{{ __('messages.recent_activity') }}</h3>
                    <p class="text-xs text-gray-400 mt-0.5">{{ __('messages.recent_activity_desc') }}</p>
                </div>
                <a href="{{ route('audit.index') }}" class="view-all-link" style="background:#7c3aed;">
                    {{ __('messages.view_all') }}
                    <i class="fas fa-arrow-right" style="font-size:0.65rem"></i>
                </a>
            </div>
            <div class="p-3">
                @forelse($recentActivity as $activity)
                    @php
                        switch ($activity->action) {
                            case 'Login':
                                $aIcon = 'fa-sign-in-alt'; $aBg = '#10b981';
                                break;
                            case 'Logout':
                                $aIcon = 'fa-sign-out-alt'; $aBg = '#ef4444';
                                break;
                            default:
                                $aIcon = 'fa-cog'; $aBg = 'var(--c-ocean)';
                        }
                    @endphp
                    <div class="list-row">
                        <div class="list-icon" style="background: {{ $aBg }};">
                            <i class="fas {{ $aIcon }}"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between gap-2 mb-0.5">
                                <p class="text-sm font-bold text-gray-900 truncate">
                                    {{ $activity->user ? $activity->user->name : 'Unknown User' }}
                                </p>
                                <span class="list-badge" style="background: var(--c-surface); color: var(--c-slate); border-color: var(--c-border);">
                                    {{ $activity->action }}
                                </span>
                            </div>
                            <p class="text-xs text-gray-400">{{ $activity->created_at->diffForHumans() }}</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-10">
                        <div class="empty-state-icon"><i class="fas fa-history"></i></div>
                        <p class="text-gray-600 font-semibold text-sm">{{ __('messages.no_activity_yet') }}</p>
                        <p class="text-gray-400 text-xs mt-1">{{ __('messages.activity_will_appear') }}</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
function dashboardData() {
    return {
        currentTime: new Date().toLocaleTimeString('id-ID'),

        init() {
            // Update jam setiap detik
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
                    borderColor: 'rgb(13, 148, 136)',
                    backgroundColor: 'rgba(13, 148, 136, 0.1)',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: 'rgb(13, 148, 136)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: 'rgb(13, 148, 136)',
                    pointHoverBorderColor: '#fff',
                    pointHoverBorderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: { usePointStyle: true, padding: 16, font: { size: 12, weight: '600' } }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: 'rgba(15, 23, 42, 0.92)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(13, 148, 136, 1)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: false,
                        titleFont: { size: 13, weight: '700' },
                        bodyFont: { size: 12 }
                    }
                },
                scales: {
                    x: { display: true, grid: { display: false }, ticks: { font: { size: 11, weight: '500' } } },
                    y: {
                        display: true, beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false },
                        ticks: { precision: 0, font: { size: 11, weight: '500' } }
                    }
                },
                interaction: { mode: 'nearest', axis: 'x', intersect: false },
                elements: { point: { hoverRadius: 7 } }
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
                        backgroundColor: 'rgba(16, 185, 129, 0.85)',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        borderSkipped: false,
                        hoverBackgroundColor: 'rgba(16, 185, 129, 0.95)'
                    },
                    {
                        label: 'Gagal',
                        data: @json($jobStatusData['failedCounts']),
                        backgroundColor: 'rgba(239, 68, 68, 0.85)',
                        borderColor: 'rgba(239, 68, 68, 1)',
                        borderWidth: 1.5,
                        borderRadius: 6,
                        borderSkipped: false,
                        hoverBackgroundColor: 'rgba(239, 68, 68, 0.95)'
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
                        labels: { usePointStyle: true, padding: 16, font: { size: 12, weight: '600' } }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15, 23, 42, 0.92)',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        borderColor: 'rgba(16, 185, 129, 1)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: true,
                        titleFont: { size: 13, weight: '700' },
                        bodyFont: { size: 12 }
                    }
                },
                scales: {
                    x: { display: true, grid: { display: false }, ticks: { font: { size: 11, weight: '500' } } },
                    y: {
                        display: true, beginAtZero: true,
                        grid: { color: 'rgba(0, 0, 0, 0.05)', drawBorder: false },
                        ticks: { precision: 0, font: { size: 11, weight: '500' } }
                    }
                },
                animation: { duration: 900, easing: 'easeOutQuart' },
                interaction: { intersect: false, mode: 'index' }
            }
        });
    }
});
</script>
@endsection