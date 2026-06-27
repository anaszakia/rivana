@extends('layouts.app')

@section('title', __('messages.job_detail') . ' - ' . $job->job_id)

@push('styles')
<style>
    /* ── Design tokens (selaras dengan halaman index) ── */
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

    /* ── Komponen dasar (sama persis dengan index) ── */
    .step-card {
        background: var(--c-white);
        border-radius: var(--radius-card);
        border: 1.5px solid var(--c-border);
        box-shadow: var(--shadow-card);
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
        background: var(--c-teal); color: #fff;
    }
    .step-body { padding: 1.25rem; }

    /* ── Tombol utama ── */
    .btn-submit {
        display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
        padding: 0.625rem 1.125rem;
        background: var(--c-teal); color: #fff;
        font-weight: 800; font-size: 0.82rem;
        border-radius: 0.625rem; border: none; cursor: pointer;
        transition: all 0.15s;
        box-shadow: 0 4px 14px rgba(13,148,136,.3);
        text-decoration: none;
    }
    .btn-submit:hover { background: var(--c-teal-dk); transform: translateY(-1px); color: #fff; }

    .btn-warn {
        display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
        padding: 0.625rem 1.125rem;
        background: #d97706; color: #fff;
        font-weight: 700; font-size: 0.82rem;
        border-radius: 0.625rem; border: none; cursor: pointer;
        transition: background .15s; text-decoration: none;
    }
    .btn-warn:hover { background: #b45309; color: #fff; }

    .btn-danger {
        display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
        padding: 0.625rem 1.125rem;
        background: #dc2626; color: #fff;
        font-weight: 700; font-size: 0.82rem;
        border-radius: 0.625rem; border: none; cursor: pointer;
        transition: background .15s;
    }
    .btn-danger:hover { background: #b91c1c; }

    /* ── Tombol ikon ── */
    .icon-btn {
        width: 2.25rem; height: 2.25rem;
        display: flex; align-items: center; justify-content: center;
        border-radius: 0.625rem; border: 1.5px solid var(--c-border);
        background: var(--c-white); color: var(--c-muted);
        transition: all .15s; cursor: pointer; flex-shrink: 0;
        text-decoration: none;
    }
    .icon-btn:hover { background: var(--c-surface); border-color: #94a3b8; color: var(--c-slate); }
    .icon-btn-view:hover { border-color: var(--c-teal); color: var(--c-teal-dk); background: #f0fdfa; }

    /* ── Status pill ── */
    .status-pill {
        display: inline-flex; align-items: center; gap: 0.375rem;
        padding: 0.35rem 0.875rem; border-radius: 9999px;
        font-size: 0.75rem; font-weight: 700;
    }

    /* ── Progress bar ── */
    .progress-track { width: 100%; height: 0.5rem; background: var(--c-border); border-radius: 9999px; overflow: hidden; }
    .progress-fill  { height: 100%; border-radius: 9999px; background: var(--c-teal); transition: width .4s; }

    /* ── Info row (label : value) ── */
    .info-row {
        display: flex; justify-content: space-between; align-items: center;
        padding: 0.625rem 0;
        border-bottom: 1px solid var(--c-border);
        font-size: 0.82rem;
        gap: 1rem;
    }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: var(--c-muted); font-weight: 600; flex-shrink: 0; }
    .info-value { color: var(--c-slate); font-weight: 700; text-align: right; }

    /* ── Kartu ringkasan (summary) ── */
    .summary-section {
        border-radius: 0.75rem;
        border: 1.5px solid var(--c-border);
        margin-bottom: 1rem;
        overflow: hidden;
    }
    .summary-section-header {
        display: flex; align-items: center; gap: 0.625rem;
        padding: 0.75rem 1rem;
        background: var(--c-surface);
        border-bottom: 1.5px solid var(--c-border);
        font-size: 0.8rem; font-weight: 800; color: var(--c-slate);
    }
    .summary-section-body { padding: 1rem; }

    /* ── Rekomendasi priority pills ── */
    .prio-high   { background: #fef2f2; border-left: 3px solid #ef4444; }
    .prio-medium { background: #fffbeb; border-left: 3px solid #f59e0b; }
    .prio-low    { background: #eff6ff; border-left: 3px solid #3b82f6; }

    /* ── Tabel ringkas ── */
    .mini-table { width: 100%; font-size: 0.78rem; border-collapse: collapse; }
    .mini-table th { background: var(--c-surface); color: var(--c-muted); font-weight: 800; text-transform: uppercase; letter-spacing: .04em; padding: 0.5rem 0.75rem; text-align: left; border-bottom: 1.5px solid var(--c-border); }
    .mini-table td { padding: 0.5rem 0.75rem; border-bottom: 1px solid var(--c-border); color: var(--c-slate); }
    .mini-table tbody tr:last-child td { border-bottom: none; }
    .mini-table tbody tr:hover td { background: var(--c-surface); }

    /* ── Stat kecil di sidebar ── */
    .sidebar-stat {
        display: flex; justify-content: space-between; align-items: center;
        padding: 0.75rem 0;
        border-bottom: 1px solid var(--c-border);
        font-size: 0.82rem;
    }
    .sidebar-stat:last-child { border-bottom: none; }

    /* ── Timeline dot ── */
    .tl-dot { width: 0.625rem; height: 0.625rem; border-radius: 9999px; flex-shrink: 0; margin-top: 0.3rem; }

    /* ── File filter buttons ── */
    .filter-btn {
        padding: 0.375rem 0.875rem; border-radius: 0.5rem;
        font-size: 0.75rem; font-weight: 700;
        border: 1.5px solid var(--c-border);
        background: var(--c-white); color: var(--c-muted);
        cursor: pointer; transition: all .15s;
    }
    .filter-btn.active, .filter-btn:hover {
        background: var(--c-teal); border-color: var(--c-teal); color: #fff;
    }

    /* ── Map container ── */
    #riverMapFrame { transition: opacity 0.5s ease; }
    #mapLoadingOverlay { transition: opacity 0.5s ease; }

    /* ── Alert boxes ── */
    .alert-warning {
        background: #fffbeb; border: 1.5px solid #f59e0b;
        border-radius: 0.75rem; padding: 0.875rem 1rem;
        display: flex; gap: 0.75rem; align-items: flex-start;
    }
    .alert-danger {
        background: #fef2f2; border: 1.5px solid #ef4444;
        border-radius: 0.75rem; padding: 0.875rem 1rem;
        display: flex; gap: 0.75rem; align-items: flex-start;
    }

    /* ── Scrollable list inside accordion ── */
    .scroll-list { max-height: 16rem; overflow-y: auto; padding-right: 0.25rem; }
    .scroll-list::-webkit-scrollbar { width: 4px; }
    .scroll-list::-webkit-scrollbar-track { background: var(--c-surface); border-radius: 9999px; }
    .scroll-list::-webkit-scrollbar-thumb { background: var(--c-teal-lt); border-radius: 9999px; }

    /* ── Collapsible section ── */
    .collapsible-chevron { transition: transform 0.2s; }
    .collapsible-chevron.open { transform: rotate(180deg); }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 sm:px-5 lg:px-6 py-6 max-w-6xl">

    {{-- ══════════════════════════════════════════
         HEADER
    ══════════════════════════════════════════ --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-xs text-gray-400 font-medium mb-0.5">
                <a href="{{ route('hidrologi.index') }}" class="hover:text-teal-600 transition-colors">{{ __('messages.hydrology') }}</a>
                <span class="mx-1">›</span>
                {{ __('messages.job_detail') }}
            </p>
            <h1 class="text-xl font-extrabold text-gray-900 leading-tight">{{ __('messages.job_detail') }}</h1>
            <p class="text-xs text-gray-400 mt-1 font-mono">{{ $job->job_id }}</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            @if(in_array($job->status, ['pending', 'submitted', 'processing']))
                <button onclick="cancelJob({{ $job->id }})" class="btn-warn">
                    <i class="fas fa-stop-circle" style="font-size:0.75rem"></i>
                    {{ __('messages.cancel_job') }}
                </button>
            @endif
            <button onclick="deleteJob({{ $job->id }})" class="btn-danger">
                <i class="fas fa-trash" style="font-size:0.75rem"></i>
                {{ __('messages.delete') }}
            </button>
            <a href="{{ route('hidrologi.index') }}" class="icon-btn" title="{{ __('messages.back') }}">
                <i class="fas fa-arrow-left" style="font-size:0.8rem"></i>
            </a>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         STATUS + PROGRESS
    ══════════════════════════════════════════ --}}
    @php
        $statusConfig = [
            'pending'               => ['bg'=>'#f1f5f9','text'=>'#475569','icon'=>'fa-clock',                'label'=>__('messages.waiting')],
            'submitted'             => ['bg'=>'#dbeafe','text'=>'#1d4ed8','icon'=>'fa-paper-plane',          'label'=>__('messages.sent')],
            'processing'            => ['bg'=>'#fef9c3','text'=>'#92400e','icon'=>'fa-spinner fa-spin',      'label'=>__('messages.processed')],
            'completed'             => ['bg'=>'#dcfce7','text'=>'#15803d','icon'=>'fa-check-circle',         'label'=>__('messages.completed')],
            'completed_with_warning'=> ['bg'=>'#ffedd5','text'=>'#c2410c','icon'=>'fa-exclamation-triangle', 'label'=>__('messages.completed_with_warning')],
            'failed'                => ['bg'=>'#fee2e2','text'=>'#b91c1c','icon'=>'fa-times-circle',         'label'=>__('messages.failed')],
            'cancelled'             => ['bg'=>'#f1f5f9','text'=>'#475569','icon'=>'fa-ban',                  'label'=>__('messages.cancelled')],
        ];
        $sc = $statusConfig[$job->status] ?? $statusConfig['pending'];
    @endphp

    <div class="step-card mb-5">
        <div class="step-body">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                {{-- Status badge --}}
                <div class="flex items-center gap-3 flex-1">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center flex-shrink-0"
                         style="background:{{ $sc['bg'] }}">
                        <i class="fas {{ $sc['icon'] }} text-base" style="color:{{ $sc['text'] }}"></i>
                    </div>
                    <div>
                        <div class="text-xs font-700 mb-0.5" style="color:{{ $sc['text'] }}; font-weight:800;">{{ $sc['label'] }}</div>
                        <div class="text-xs text-gray-500">{{ $job->status_message ?? __('messages.processing_job') }}</div>
                    </div>
                </div>

                {{-- Progress --}}
                @if(in_array($job->status, ['pending','submitted','processing']))
                    <div class="w-full sm:w-60">
                        <div class="flex justify-between text-xs font-bold mb-1">
                            <span class="text-gray-600">{{ __('messages.progress') }}</span>
                            <span id="progress-percent" style="color:var(--c-teal)">{{ $job->progress }}%</span>
                        </div>
                        <div class="progress-track">
                            <div id="progress-bar" class="progress-fill" style="width:{{ $job->progress }}%"></div>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Alerts --}}
            @if($job->warning_message)
                <div class="alert-warning mt-4">
                    <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5 flex-shrink-0"></i>
                    <div>
                        <p class="text-xs font-bold text-amber-800">{{ __('messages.warning') }}</p>
                        <p class="text-xs text-amber-700 mt-0.5">{{ $job->warning_message }}</p>
                    </div>
                </div>
            @endif
            @if($job->error_message)
                <div class="alert-danger mt-3">
                    <i class="fas fa-times-circle text-red-500 mt-0.5 flex-shrink-0"></i>
                    <div>
                        <p class="text-xs font-bold text-red-800">{{ __('messages.error') }}</p>
                        <p class="text-xs text-red-700 mt-0.5">{{ $job->error_message }}</p>
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         GRID UTAMA  (2/3 kiri + 1/3 kanan)
    ══════════════════════════════════════════ --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 mb-6">

        {{-- ─────────────── KOLOM KIRI ─────────────── --}}
        <div class="lg:col-span-2 space-y-5 order-2 lg:order-1">

            {{-- ── Lokasi ── --}}
            <div class="step-card" style="overflow:hidden">
                <div class="step-header">
                    <div class="step-badge"><i class="fas fa-map-marker-alt" style="font-size:0.8rem"></i></div>
                    <div>
                        <h3 class="text-sm font-extrabold text-gray-900">{{ __('messages.location_info') }}</h3>
                    </div>
                </div>
                <div class="step-body">
                    <div class="info-row">
                        <span class="info-label">{{ __('messages.location_name') }}</span>
                        <span class="info-value">{{ $job->location_name ?? __('messages.not_available') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('messages.coordinates') }}</span>
                        <span class="info-value font-mono text-xs">{{ $job->latitude }}, {{ $job->longitude }}</span>
                    </div>
                    @if($job->location_description)
                        <div class="info-row">
                            <span class="info-label">{{ __('messages.description') }}</span>
                            <span class="info-value text-left" style="text-align:left">{{ $job->location_description }}</span>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── Periode Analisis ── --}}
            <div class="step-card" style="overflow:hidden">
                <div class="step-header">
                    <div class="step-badge"><i class="fas fa-calendar-alt" style="font-size:0.8rem"></i></div>
                    <div>
                        <h3 class="text-sm font-extrabold text-gray-900">{{ __('messages.analysis_period') }}</h3>
                    </div>
                </div>
                <div class="step-body">
                    <div class="info-row">
                        <span class="info-label">{{ __('messages.start_date') }}</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($job->start_date)->format('d F Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('messages.end_date') }}</span>
                        <span class="info-value">{{ \Carbon\Carbon::parse($job->end_date)->format('d F Y') }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-label">{{ __('messages.duration') }}</span>
                        <span class="info-value">
                            {{ \Carbon\Carbon::parse($job->start_date)->diffInDays(\Carbon\Carbon::parse($job->end_date)) + 1 }} {{ __('messages.days') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- ══════════════════════════════════════════
                 RINGKASAN ANALISIS (jika ada)
            ══════════════════════════════════════════ --}}
            @if($summary)
                @php
                    \Log::info('TWI Analysis Debug', [
                        'job_id'          => $job->id ?? 'unknown',
                        'has_twi_analysis'=> isset($summary['twi_analysis']),
                        'twi_is_array'    => isset($summary['twi_analysis']) && is_array($summary['twi_analysis']),
                        'twi_status'      => $summary['twi_analysis']['status'] ?? 'no_status_key',
                        'twi_keys'        => isset($summary['twi_analysis']) ? array_keys($summary['twi_analysis']) : [],
                        'twi_enhanced_value' => $summary['twi_analysis']['twi_enhanced'] ?? 'not_found',
                        'twi_risk_level'  => $summary['twi_analysis']['risk_level'] ?? 'not_found',
                    ]);
                @endphp

                <div class="step-card" style="overflow:hidden">
                    <div class="step-header">
                        <div class="step-badge"><i class="fas fa-chart-line" style="font-size:0.8rem"></i></div>
                        <div class="flex-1">
                            <h3 class="text-sm font-extrabold text-gray-900">{{ __('messages.analysis_summary') }}</h3>
                            <p class="text-xs text-gray-400 mt-0.5">{{ __('messages.structured_summary') }}</p>
                        </div>
                    </div>
                    <div class="step-body space-y-0">

                        {{-- ── Job Info ── --}}
                        @if(isset($summary['job_info']))
                            <div class="summary-section">
                                <div class="summary-section-header">
                                    <i class="fas fa-info-circle" style="color:var(--c-teal)"></i>
                                    {{ __('messages.job_info') }}
                                </div>
                                <div class="summary-section-body">
                                    <div class="info-row">
                                        <span class="info-label">{{ __('messages.job_id') }}</span>
                                        <span class="info-value font-mono text-xs">{{ $summary['job_info']['job_id'] ?? __('messages.n_a') }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">{{ __('messages.status') }}</span>
                                        <span class="info-value" style="color:var(--c-teal)">{{ ucfirst(trans_api($summary['job_info']['status'] ?? __('messages.n_a'), 'status_umum')) }}</span>
                                    </div>
                                    @if(isset($summary['job_info']['created_at']))
                                        <div class="info-row">
                                            <span class="info-label">{{ __('messages.created') }}</span>
                                            <span class="info-value text-xs">{{ $summary['job_info']['created_at'] }}</span>
                                        </div>
                                    @endif
                                    @if(isset($summary['job_info']['completed_at']))
                                        <div class="info-row">
                                            <span class="info-label">{{ __('messages.completed_at') }}</span>
                                            <span class="info-value text-xs">{{ $summary['job_info']['completed_at'] }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- ── Water Balance ── --}}
                        @if(isset($summary['water_balance']))
                            <div class="summary-section">
                                <div class="summary-section-header">
                                    <i class="fas fa-balance-scale" style="color:#0e7490"></i>
                                    {{ __('messages.water_balance') }}
                                </div>
                                <div class="summary-section-body">
                                    <div class="grid grid-cols-2 gap-3 mb-3">
                                        <div style="background:#f0fdf4;border-radius:.5rem;padding:.625rem">
                                            <div class="text-xs text-gray-500 mb-1">{{ __('messages.total_input') }}</div>
                                            <div class="font-extrabold text-green-700" style="font-size:1rem">{{ $summary['water_balance']['total_input'] ?? 'N/A' }}</div>
                                        </div>
                                        <div style="background:#fef2f2;border-radius:.5rem;padding:.625rem">
                                            <div class="text-xs text-gray-500 mb-1">{{ __('messages.total_output') }}</div>
                                            <div class="font-extrabold text-red-700" style="font-size:1rem">{{ $summary['water_balance']['total_output'] ?? 'N/A' }}</div>
                                        </div>
                                        <div style="background:#eff6ff;border-radius:.5rem;padding:.625rem">
                                            <div class="text-xs text-gray-500 mb-1">{{ __('messages.residual') }}</div>
                                            <div class="font-extrabold text-blue-700" style="font-size:1rem">{{ $summary['water_balance']['residual'] ?? 'N/A' }}</div>
                                        </div>
                                        <div style="background:#fff7ed;border-radius:.5rem;padding:.625rem">
                                            <div class="text-xs text-gray-500 mb-1">{{ __('messages.error') }}</div>
                                            <div class="font-extrabold text-orange-700" style="font-size:1rem">{{ $summary['water_balance']['error_persen'] ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="text-center">
                                        <span class="text-xs text-gray-500 mr-2">{{ __('messages.status_balance') }}:</span>
                                        @php
                                            $bs = $summary['water_balance']['status'] ?? '';
                                            $bclass = strpos($bs,'Sangat Baik')!==false ? 'background:#dcfce7;color:#15803d' : (strpos($bs,'Baik')!==false ? 'background:#dbeafe;color:#1d4ed8' : (strpos($bs,'Cukup')!==false ? 'background:#fef9c3;color:#92400e' : 'background:#fee2e2;color:#b91c1c'));
                                        @endphp
                                        <span class="font-bold text-xs px-3 py-1 rounded-full" style="{{ $bclass }}">
                                            {{ trans_api($bs, 'status_balance') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- ── Kualitas Data ── --}}
                        @if(isset($summary['kualitas_data']))
                            <div class="summary-section">
                                <div class="summary-section-header">
                                    <i class="fas fa-check-circle" style="color:var(--c-teal)"></i>
                                    {{ __('messages.data_quality') }}
                                </div>
                                <div class="summary-section-body">
                                    <div class="info-row">
                                        <span class="info-label">{{ __('messages.data_completeness') }}</span>
                                        <span class="info-value" style="color:var(--c-teal)">{{ $summary['kualitas_data']['kelengkapan_data'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="info-row">
                                        <span class="info-label">{{ __('messages.valid_period') }}</span>
                                        <span class="info-value">
                                            @if(($summary['kualitas_data']['periode_valid'] ?? '') == 'Ya')
                                                <span style="color:#15803d">✔ {{ __('messages.yes') }}</span>
                                            @else
                                                <span style="color:#b91c1c">✘ {{ __('messages.no') }}</span>
                                            @endif
                                        </span>
                                    </div>
                                    @if(isset($summary['kualitas_data']['file_tersedia']))
                                        <div style="margin-top:.75rem;padding-top:.75rem;border-top:1px solid var(--c-border)">
                                            <p class="text-xs font-bold text-gray-500 mb-2">{{ __('messages.available_files') }}</p>
                                            <div class="space-y-1.5">
                                                <div class="info-row">
                                                    <span class="info-label"><i class="fas fa-chart-bar text-blue-500 mr-1.5"></i>{{ __('messages.visualization') }}</span>
                                                    <span class="info-value text-blue-700">{{ $summary['kualitas_data']['file_tersedia']['visualisasi'] ?? 'N/A' }}</span>
                                                </div>
                                                <div class="info-row">
                                                    <span class="info-label"><i class="fas fa-table text-green-500 mr-1.5"></i>{{ __('messages.data_csv') }}</span>
                                                    <span class="info-value text-green-700">{{ $summary['kualitas_data']['file_tersedia']['data_csv'] ?? 'N/A' }}</span>
                                                </div>
                                                <div class="info-row">
                                                    <span class="info-label"><i class="fas fa-file-code text-orange-500 mr-1.5"></i>{{ __('messages.metadata') }}</span>
                                                    <span class="info-value text-orange-700">{{ $summary['kualitas_data']['file_tersedia']['metadata'] ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- ── Kondisi Sungai & Soil Storage ── --}}
                        @if(isset($summary['analisis_kondisi_sungai_soil_storage']))
                            @php $akss = $summary['analisis_kondisi_sungai_soil_storage']; @endphp
                            <div class="summary-section">
                                <div class="summary-section-header">
                                    <i class="fas fa-water" style="color:var(--c-ocean)"></i>
                                    {{ __('messages.river_condition') ?? 'Kondisi Sungai & Soil Storage' }}
                                </div>
                                <div class="summary-section-body">
                                    @if(isset($akss['kondisi_sungai']))
                                        <div class="info-row">
                                            <span class="info-label">{{ __('messages.river_condition') ?? 'Kondisi' }}</span>
                                            <span class="info-value">{{ trans_api($akss['kondisi_sungai']['kondisi'] ?? 'N/A','kondisi_sungai') }}</span>
                                        </div>
                                        @if(isset($akss['kondisi_sungai']['debit_rata_rata']))
                                        <div class="info-row">
                                            <span class="info-label">{{ __('messages.avg_discharge') ?? 'Debit Rata-rata' }}</span>
                                            <span class="info-value">{{ $akss['kondisi_sungai']['debit_rata_rata'] }}</span>
                                        </div>
                                        @endif
                                    @endif
                                    @if(isset($akss['kondisi_soil_storage']))
                                        <div class="grid grid-cols-2 gap-2 mt-2">
                                            @foreach(['infiltration','percolation'] as $key)
                                                @if(isset($akss['kondisi_soil_storage'][$key]))
                                                    <div style="background:var(--c-surface);border-radius:.5rem;padding:.5rem">
                                                        <div class="text-xs text-gray-500 mb-1">{{ ucfirst($key) }}</div>
                                                        <div class="font-bold text-xs" style="color:var(--c-teal)">{{ $akss['kondisi_soil_storage'][$key]['rata_rata'] ?? 'N/A' }}</div>
                                                    </div>
                                                @endif
                                            @endforeach
                                        </div>
                                    @endif
                                    @if(isset($akss['ekologi']['ecosystem_health']) &&
                                        ($akss['ekologi']['ecosystem_health']['index'] ?? 'N/A') !== 'N/A' &&
                                        ($akss['ekologi']['ecosystem_health']['index'] ?? '') !== 'Data not available')
                                        <div class="info-row">
                                            <span class="info-label">{{ __('messages.ecosystem_health') }}</span>
                                            <span class="info-value">{{ $akss['ekologi']['ecosystem_health']['index'] }}
                                                <span class="text-xs text-gray-400 font-normal ml-1">({{ trans_api($akss['ekologi']['ecosystem_health']['status'] ?? 'N/A','status_ekosistem') }})</span>
                                            </span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- ── Pembagian & Prioritas Air ── --}}
                        @if(isset($summary['analysis_results']['water_supply_per_sector']) &&
                            is_array($summary['analysis_results']['water_supply_per_sector']) &&
                            !isset($summary['analysis_results']['water_supply_per_sector']['error']))
                            @php $sectors = $summary['analysis_results']['water_supply_per_sector']; @endphp
                            <div class="summary-section">
                                <div class="summary-section-header">
                                    <i class="fas fa-tint" style="color:var(--c-teal)"></i>
                                    {{ __('messages.water_distribution') ?? 'Pembagian & Prioritas Air' }}
                                </div>
                                <div class="summary-section-body">
                                    <table class="mini-table">
                                        <thead>
                                            <tr>
                                                <th>{{ __('messages.sector') ?? 'Sektor' }}</th>
                                                <th class="text-right">{{ __('messages.allocation') ?? 'Alokasi' }}</th>
                                                <th class="text-right">{{ __('messages.priority') ?? 'Prioritas' }}</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($sectors as $sector)
                                                <tr>
                                                    <td>{{ $sector['sector'] ?? 'N/A' }}</td>
                                                    <td class="text-right font-bold" style="color:var(--c-teal)">{{ $sector['allocation'] ?? 'N/A' }}</td>
                                                    <td class="text-right">
                                                        @php $p = $sector['priority'] ?? 'N/A'; @endphp
                                                        <span class="font-bold text-xs" style="color:{{ $p=='Tinggi'?'#b91c1c':($p=='Sedang'?'#92400e':'#1d4ed8') }}">{{ trans_api($p,'priority') }}</span>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif

                        {{-- ── TWI Analysis ── --}}
                        @if(isset($summary['twi_analysis']) && is_array($summary['twi_analysis']) && !isset($summary['twi_analysis']['status']))
                            @php $twi = $summary['twi_analysis']; @endphp
                            <div class="summary-section">
                                <div class="summary-section-header">
                                    <i class="fas fa-layer-group" style="color:var(--c-ocean)"></i>
                                    {{ __('messages.twi_title') ?? 'Analisis TWI' }}
                                </div>
                                <div class="summary-section-body">
                                    {{-- Overview --}}
                                    @if(isset($twi['interpretation']))
                                        <div class="info-row">
                                            <span class="info-label">{{ __('messages.twi_value') ?? 'Nilai TWI' }}</span>
                                            <span class="info-value">{{ $twi['interpretation']['twi_value'] ?? 'N/A' }}</span>
                                        </div>
                                        <div class="info-row">
                                            <span class="info-label">{{ __('messages.twi_risk_level') ?? 'Tingkat Risiko' }}</span>
                                            <span class="info-value">{{ trans_api($twi['interpretation']['risk_level'] ?? 'N/A','risk_level') }}</span>
                                        </div>
                                        @if(!empty($twi['interpretation']['action']))
                                            <p class="text-xs text-gray-600 mt-2 p-2 rounded" style="background:var(--c-surface)">{{ $twi['interpretation']['action'] }}</p>
                                        @endif
                                    @endif

                                    {{-- Flood Zones --}}
                                    @if(isset($twi['flood_zones']))
                                        <div class="grid grid-cols-3 gap-2 mt-3">
                                            <div class="text-center p-2 rounded" style="background:#fee2e2">
                                                <div class="text-lg font-extrabold text-red-700">{{ $twi['flood_zones']['high_risk'] ?? 0 }}</div>
                                                <div class="text-xs text-gray-500 mt-0.5">{{ __('messages.twi_high_risk') ?? 'Risiko Tinggi' }}</div>
                                            </div>
                                            <div class="text-center p-2 rounded" style="background:#fef9c3">
                                                <div class="text-lg font-extrabold text-yellow-700">{{ $twi['flood_zones']['moderate_risk'] ?? 0 }}</div>
                                                <div class="text-xs text-gray-500 mt-0.5">{{ __('messages.twi_moderate_risk') ?? 'Risiko Sedang' }}</div>
                                            </div>
                                            <div class="text-center p-2 rounded" style="background:#dcfce7">
                                                <div class="text-lg font-extrabold text-green-700">{{ $twi['flood_zones']['low_risk'] ?? 0 }}</div>
                                                <div class="text-xs text-gray-500 mt-0.5">{{ __('messages.twi_low_risk') ?? 'Risiko Rendah' }}</div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- RTH Recommendations --}}
                                    @if(isset($twi['rtho_recommendations']))
                                        @php $rth = $twi['rtho_recommendations']; @endphp
                                        <div class="mt-3 pt-3" style="border-top:1px solid var(--c-border)">
                                            <p class="text-xs font-extrabold text-gray-700 mb-2">🌳 {{ __('messages.twi_rth_title') }}</p>
                                            <div class="grid grid-cols-3 gap-2 mb-2">
                                                <div class="text-center p-2 rounded" style="background:#fee2e2">
                                                    <div class="text-base font-extrabold text-red-700">{{ $rth['high_priority'] ?? 0 }}</div>
                                                    <div class="text-xs text-gray-500">🔥 {{ __('messages.twi_high_priority') }}</div>
                                                </div>
                                                <div class="text-center p-2 rounded" style="background:#fef9c3">
                                                    <div class="text-base font-extrabold text-yellow-700">{{ $rth['moderate_priority'] ?? 0 }}</div>
                                                    <div class="text-xs text-gray-500">⭐ {{ __('messages.twi_medium_priority') }}</div>
                                                </div>
                                                <div class="text-center p-2 rounded" style="background:#dcfce7">
                                                    <div class="text-base font-extrabold text-green-700">{{ number_format($rth['total_area_ha'] ?? 0,1) }}</div>
                                                    <div class="text-xs text-gray-500">📏 ha</div>
                                                </div>
                                            </div>
                                            @if(isset($rth['recommendations_detail']) && count($rth['recommendations_detail']) > 0)
                                                <div class="scroll-list space-y-1.5">
                                                    @foreach($rth['recommendations_detail'] as $idx => $rec)
                                                        @php $hp = ($rec['priority'] ?? '') === 'HIGH'; @endphp
                                                        <div class="p-2 rounded text-xs {{ $hp ? 'prio-high' : 'prio-low' }}">
                                                            <div class="flex justify-between mb-1">
                                                                <span class="font-bold text-gray-800">{{ __('messages.twi_rth_location') }} {{ $idx+1 }}</span>
                                                                <span class="font-bold {{ $hp ? 'text-red-700' : 'text-blue-700' }}">{{ $hp ? '🔥 '.__('messages.twi_urgent_label') : '⭐ '.__('messages.twi_plan_label') }}</span>
                                                            </div>
                                                            <div class="text-gray-600">{{ number_format($rec['estimated_area_ha'] ?? 0,2) }} ha · <span class="font-mono">{{ number_format($rec['lat']??0,5) }}, {{ number_format($rec['lon']??0,5) }}</span></div>
                                                            @if(!empty($rec['reason']) && $rec['reason'] !== 'N/A')
                                                                <p class="text-gray-500 mt-0.5">💡 {{ $rec['reason'] }}</p>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- Drainage Recommendations --}}
                                    @if(isset($twi['drainage_recommendations']))
                                        @php $dr = $twi['drainage_recommendations']; @endphp
                                        <div class="mt-3 pt-3" style="border-top:1px solid var(--c-border)">
                                            <p class="text-xs font-extrabold text-gray-700 mb-2">🚰 {{ __('messages.twi_drainage_title') }}</p>
                                            <div class="info-row">
                                                <span class="info-label">{{ __('messages.twi_high_priority') }}</span>
                                                <span class="info-value text-red-700">{{ $dr['high_priority'] ?? 0 }}</span>
                                            </div>
                                            <div class="info-row">
                                                <span class="info-label">Total</span>
                                                <span class="info-value">{{ $dr['total'] ?? 0 }}</span>
                                            </div>
                                            @if(isset($dr['recommendations_detail']) && count($dr['recommendations_detail']) > 0)
                                                <div class="scroll-list space-y-1.5 mt-2">
                                                    @foreach($dr['recommendations_detail'] as $didx => $drain)
                                                        @php $dhp = ($drain['priority'] ?? '') === 'HIGH'; @endphp
                                                        <div class="p-2 rounded text-xs {{ $dhp ? 'prio-high' : 'prio-low' }}">
                                                            <div class="flex justify-between mb-1">
                                                                <span class="font-bold text-gray-800">{{ __('messages.twi_drainage_channels') ?? 'Saluran' }} {{ $didx+1 }}</span>
                                                                <span class="font-bold {{ $dhp ? 'text-red-700' : 'text-blue-700' }}">{{ $dhp ? '🔥 HIGH' : '⭐ MOD' }}</span>
                                                            </div>
                                                            <div class="text-gray-600 font-mono">{{ number_format($drain['lat']??0,5) }}, {{ number_format($drain['lon']??0,5) }}</div>
                                                            @if(!empty($drain['reasons']))
                                                                <ul class="text-gray-500 mt-0.5 ml-3 list-disc">
                                                                    @foreach($drain['reasons'] as $reason)
                                                                        <li>{{ $reason }}</li>
                                                                    @endforeach
                                                                </ul>
                                                            @endif
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    {{-- TWI Actions Summary --}}
                                    @if(isset($twi['interpretation']) || isset($twi['flood_zones']))
                                        @php
                                            $highRisk   = $twi['flood_zones']['high_risk'] ?? 0;
                                            $highRTH    = $twi['rtho_recommendations']['high_priority'] ?? 0;
                                            $totalRTH   = $twi['rtho_recommendations']['total'] ?? 0;
                                            $highDrain  = $twi['drainage_recommendations']['high_priority'] ?? 0;
                                            $totalDrain = $twi['drainage_recommendations']['total'] ?? 0;
                                        @endphp
                                        <div class="mt-3 p-3 rounded" style="background:var(--c-sky);border-radius:.625rem">
                                            <p class="text-xs font-extrabold text-gray-700 mb-2">⚡ {{ __('messages.twi_actions_title') }}</p>
                                            <ol class="text-xs text-gray-700 space-y-1 pl-4 list-decimal">
                                                @if($highRisk > 0)
                                                    <li><strong>{{ __('messages.twi_main_priority') }}:</strong> {{ __('messages.twi_high_risk_zones_action') }} <strong class="text-red-700">{{ $highRisk }}</strong> {{ __('messages.twi_high_risk_zones_action2') }}</li>
                                                @endif
                                                @if($highDrain > 0)
                                                    <li><strong>{{ __('messages.twi_drainage_action') }}:</strong> {{ __('messages.twi_build_label') }} <strong class="text-blue-700">{{ $highDrain }}</strong> {{ __('messages.twi_drainage_action2') }}</li>
                                                @endif
                                                @if($highRTH > 0)
                                                    <li><strong>{{ __('messages.twi_rth_action') }}:</strong> {{ __('messages.twi_build_label') }} <strong class="text-orange-700">{{ $highRTH }}</strong> {{ __('messages.twi_rth_action2') }}</li>
                                                @endif
                                                @if(($totalRTH > $highRTH) || ($totalDrain > $highDrain))
                                                    <li><strong>{{ __('messages.twi_medium_term') }}:</strong>
                                                        @if($totalDrain > $highDrain) {{ $totalDrain - $highDrain }} {{ __('messages.twi_additional_drainage') }} @endif
                                                        @if(($totalRTH > $highRTH) && ($totalDrain > $highDrain)) {{ __('messages.twi_and') }} @endif
                                                        @if($totalRTH > $highRTH) {{ $totalRTH - $highRTH }} {{ __('messages.twi_additional_rth') }} @endif
                                                    </li>
                                                @endif
                                                <li><strong>{{ __('messages.twi_regular_monitoring') }}:</strong> {{ __('messages.twi_monitoring_desc') }}</li>
                                                <li><strong>{{ __('messages.twi_coordination') }}:</strong> {{ __('messages.twi_coordination_desc') }}</li>
                                            </ol>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- TWI Error/Not Available --}}
                        @if(isset($summary['twi_analysis']['status']) &&
                            in_array($summary['twi_analysis']['status'], ['error','not_available']))
                            <div class="alert-warning">
                                <i class="fas fa-exclamation-triangle text-amber-500 mt-0.5 flex-shrink-0"></i>
                                <div>
                                    <p class="text-xs font-bold text-amber-800">TWI: {{ $summary['twi_analysis']['status'] }}</p>
                                    <p class="text-xs text-amber-700 mt-0.5">{{ $summary['twi_analysis']['message'] ?? '' }}</p>
                                </div>
                            </div>
                        @endif

                        {{-- ── Perencanaan Pembangunan Bendungan ── --}}
                        @if(isset($summary['dam_cost_estimate']) && !isset($summary['dam_cost_estimate']['error']) && !isset($summary['dam_cost_estimate']['status']))
                            @php
                                $dam     = $summary['dam_cost_estimate'];
                                $hps     = $dam['estimasi_hps'] ?? [];
                                $skenario= $dam['skenario'] ?? [];
                                $moderat = $skenario['moderat'] ?? [];
                                $jadwal  = $dam['jadwal'] ?? [];
                            @endphp
                            <div class="summary-section">
                                <div class="summary-section-header">
                                    <i class="fas fa-hard-hat" style="color:#15803d"></i>
                                    PERENCANAAN PEMBANGUNAN BENDUNGAN
                                </div>
                                <div class="summary-section-body">
                                    {{-- Info Bangunan --}}
                                    <div class="info-row"><span class="info-label">Tipe Bangunan</span><span class="info-value">{{ $dam['tipe_bangunan'] ?? 'N/A' }}</span></div>
                                    <div class="info-row"><span class="info-label">Provinsi</span><span class="info-value">{{ $dam['provinsi'] ?? 'N/A' }}</span></div>
                                    <div class="info-row"><span class="info-label">Wilayah (IKK)</span><span class="info-value">{{ $dam['ikk_wilayah'] ?? 'N/A' }}</span></div>

                                    {{-- Dimensi --}}
                                    @if(isset($dam['dimensi']))
                                        <div class="grid grid-cols-2 gap-2 mt-3">
                                            <div style="background:#eff6ff;border-radius:.5rem;padding:.625rem">
                                                <div class="text-xs text-gray-500">Volume Tampungan</div>
                                                <div class="font-extrabold text-blue-700">{{ number_format($dam['dimensi']['v_tampungan_m3'] ?? 0,0,',','.') }} m³</div>
                                            </div>
                                            <div style="background:#eff6ff;border-radius:.5rem;padding:.625rem">
                                                <div class="text-xs text-gray-500">Tinggi Rata-rata</div>
                                                <div class="font-extrabold text-blue-700">{{ number_format($dam['dimensi']['h_rata_m'] ?? 0,2,',','.') }} m</div>
                                            </div>
                                        </div>
                                    @endif

                                    {{-- HPS --}}
                                    <p class="text-xs font-bold text-gray-600 mt-3 mb-1.5">Estimasi HPS</p>
                                    <div class="grid grid-cols-3 gap-2">
                                        <div style="background:#f0fdf4;border-radius:.5rem;padding:.5rem;text-align:center">
                                            <div class="text-xs text-gray-500">Min</div>
                                            <div class="font-extrabold text-green-700 text-xs">Rp {{ number_format($hps['minimum_rp'] ?? 0,0,',','.') }}</div>
                                        </div>
                                        <div style="background:#fffbeb;border-radius:.5rem;padding:.5rem;text-align:center">
                                            <div class="text-xs text-gray-500">Moderat</div>
                                            <div class="font-extrabold text-amber-700 text-xs">Rp {{ number_format($hps['moderat_rp'] ?? 0,0,',','.') }}</div>
                                        </div>
                                        <div style="background:#fef2f2;border-radius:.5rem;padding:.5rem;text-align:center">
                                            <div class="text-xs text-gray-500">Max</div>
                                            <div class="font-extrabold text-red-700 text-xs">Rp {{ number_format($hps['maksimum_rp'] ?? 0,0,',','.') }}</div>
                                        </div>
                                    </div>
                                    @if(!empty($hps['sumber']))
                                        <p class="text-xs text-gray-400 mt-1"><i class="fas fa-info-circle mr-1"></i>{{ $hps['sumber'] }}</p>
                                    @endif

                                    {{-- Skenario RAB --}}
                                    <p class="text-xs font-bold text-gray-600 mt-3 mb-1.5">Skenario Total Proyek (RAB)</p>
                                    <table class="mini-table">
                                        <thead>
                                            <tr><th>Skenario</th><th class="text-right">Total Proyek</th><th class="text-right">Est. Kontrak</th></tr>
                                        </thead>
                                        <tbody>
                                            <tr><td class="text-green-700 font-bold">Minimum</td><td class="text-right">Rp {{ number_format($skenario['minimum']['total_proyek_rp'] ?? 0,0,',','.') }}</td><td class="text-right">Rp {{ number_format($skenario['minimum']['estimasi_kontrak_rp'] ?? 0,0,',','.') }}</td></tr>
                                            <tr><td class="text-amber-700 font-bold">Moderat</td><td class="text-right">Rp {{ number_format($moderat['total_proyek_rp'] ?? 0,0,',','.') }}</td><td class="text-right">Rp {{ number_format($moderat['estimasi_kontrak_rp'] ?? 0,0,',','.') }}</td></tr>
                                            <tr><td class="text-red-700 font-bold">Maksimum</td><td class="text-right">Rp {{ number_format($skenario['maksimum']['total_proyek_rp'] ?? 0,0,',','.') }}</td><td class="text-right">Rp {{ number_format($skenario['maksimum']['estimasi_kontrak_rp'] ?? 0,0,',','.') }}</td></tr>
                                        </tbody>
                                    </table>

                                    {{-- Komponen Biaya --}}
                                    @if(!empty($moderat['komponen_biaya']))
                                        <p class="text-xs font-bold text-gray-600 mt-3 mb-1.5">Komponen Biaya (Moderat)</p>
                                        <div class="grid grid-cols-2 gap-1.5">
                                            @foreach($moderat['komponen_biaya'] as $komponen => $detail)
                                                <div style="background:var(--c-surface);border-radius:.375rem;padding:.5rem" class="text-xs">
                                                    <div class="flex justify-between">
                                                        <span class="text-gray-600">{{ $komponen }}</span>
                                                        <span class="font-bold">{{ $detail['persentase_pct'] ?? 0 }}%</span>
                                                    </div>
                                                    <div class="text-right text-gray-500 mt-0.5">Rp {{ number_format($detail['jumlah_rp'] ?? 0,0,',','.') }}</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    {{-- Rasio Kontrak / HPS --}}
                                    @if(!empty($dam['rasio_kontrak_hps']))
                                        @php $rasio = $dam['rasio_kontrak_hps']; @endphp
                                        <p class="text-xs font-bold text-gray-600 mt-3 mb-1.5">Rasio Kontrak / HPS (LPSE)</p>
                                        <div class="grid grid-cols-3 gap-1.5 text-xs text-center">
                                            <div style="background:#f5f3ff;border-radius:.375rem;padding:.5rem"><div class="text-gray-500">Q1</div><div class="font-bold text-purple-700">{{ number_format($rasio['q1']??0,1) }}%</div></div>
                                            <div style="background:#ede9fe;border-radius:.375rem;padding:.5rem"><div class="text-gray-500">Median</div><div class="font-bold text-purple-800">{{ number_format($rasio['median']??0,1) }}%</div></div>
                                            <div style="background:#f5f3ff;border-radius:.375rem;padding:.5rem"><div class="text-gray-500">Q3</div><div class="font-bold text-purple-700">{{ number_format($rasio['q3']??0,1) }}%</div></div>
                                        </div>
                                        @if(!empty($rasio['sumber']))
                                            <p class="text-xs text-gray-400 mt-1"><i class="fas fa-info-circle mr-1"></i>{{ $rasio['sumber'] }} (n={{ $rasio['n'] ?? 0 }} paket)</p>
                                        @endif
                                    @endif

                                    {{-- Jadwal --}}
                                    @if(isset($dam['jadwal']))
                                        <p class="text-xs font-bold text-gray-600 mt-3 mb-1.5">Jadwal Pelaksanaan</p>
                                        <div class="info-row"><span class="info-label">Durasi</span><span class="info-value">{{ $jadwal['total_bulan'] ?? 0 }} bulan ({{ $jadwal['total_tahun'] ?? 0 }} tahun)</span></div>
                                        <div class="info-row"><span class="info-label">Rentang</span><span class="info-value">{{ $jadwal['rentang_bulan'] ?? 'N/A' }}</span></div>
                                        @if(!empty($jadwal['tahapan']))
                                            <div class="mt-2 space-y-1.5">
                                                @foreach($jadwal['tahapan'] as $tahap)
                                                    <div class="flex items-center gap-2 text-xs" style="background:var(--c-surface);border-radius:.375rem;padding:.5rem">
                                                        <span style="background:var(--c-teal);color:#fff;border-radius:.375rem;padding:.125rem .5rem;font-size:.7rem;white-space:nowrap;flex-shrink:0">Bln {{ $tahap['mulai_bulan'] }}-{{ $tahap['selesai_bulan'] }}</span>
                                                        <span class="text-gray-700 font-medium">{{ $tahap['tahap'] }}</span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        @endif

                        {{-- ── Rekomendasi ── --}}
                        @if(isset($summary['rekomendasi']) && is_array($summary['rekomendasi']) && count($summary['rekomendasi']) > 0)
                            <div class="summary-section">
                                <div class="summary-section-header">
                                    <i class="fas fa-lightbulb" style="color:#d97706"></i>
                                    {{ __('messages.management_recommendations') }} ({{ count($summary['rekomendasi']) }})
                                </div>
                                <div class="summary-section-body space-y-2">
                                    @foreach($summary['rekomendasi'] as $index => $rek)
                                        @php
                                            $p = $rek['prioritas'] ?? 'Normal';
                                            $pclass = $p=='Tinggi' ? 'prio-high' : ($p=='Sedang' ? 'prio-medium' : 'prio-low');
                                        @endphp
                                        <div class="{{ $pclass }} p-3 rounded-lg text-xs">
                                            <div class="flex justify-between items-center mb-1">
                                                <span class="font-bold text-gray-800">{{ $index+1 }}. {{ $rek['kategori'] ?? 'N/A' }}</span>
                                                <span class="font-bold {{ $p=='Tinggi'?'text-red-700':($p=='Sedang'?'text-amber-700':'text-blue-700') }}">{{ trans_api($p,'priority') }}</span>
                                            </div>
                                            <p class="text-gray-700 leading-relaxed">{{ $rek['rekomendasi'] ?? 'N/A' }}</p>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                    </div>{{-- /step-body --}}
                </div>{{-- /step-card summary --}}
            @endif

            {{-- ══════════════════════════════════════════
                 PETA SUNGAI INTERAKTIF
            ══════════════════════════════════════════ --}}
            @php
                $riverMapHtml     = $job->files->firstWhere('filename', 'RIVANA_Peta_Aliran_Sungai.html');
                $riverMapPng      = $job->files->firstWhere('filename', 'RIVANA_Peta_Aliran_Sungai.png');
                $riverMapMetadata = $job->files->firstWhere('filename', 'RIVANA_Metadata_Peta.json');
                \Log::info('Map Files Check', [
                    'job_id'        => $job->id,
                    'all_files'     => $job->files->pluck('filename')->toArray(),
                    'html_found'    => $riverMapHtml     ? 'YES' : 'NO',
                    'png_found'     => $riverMapPng      ? 'YES' : 'NO',
                    'metadata_found'=> $riverMapMetadata ? 'YES' : 'NO',
                ]);
            @endphp

            @if($riverMapHtml || $riverMapPng)
                <div class="step-card" style="overflow:hidden">
                    <div class="step-header">
                        <div class="step-badge"><i class="fas fa-water" style="font-size:0.8rem"></i></div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-extrabold text-gray-900">🌊 {{ __('messages.interactive_river_map') }}</h3>
                            <p class="text-xs text-gray-400 mt-0.5">{{ __('messages.river_network_visualization') }}</p>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            @if($riverMapHtml)
                                <button onclick="openMapFullscreen()" class="icon-btn icon-btn-view" title="Fullscreen">
                                    <i class="fas fa-expand" style="font-size:0.75rem"></i>
                                </button>
                                <a href="/hidrologi/file/download/{{ $riverMapHtml->id }}" class="icon-btn icon-btn-view" title="Download HTML">
                                    <i class="fas fa-download" style="font-size:0.75rem"></i>
                                </a>
                            @endif
                            @if($riverMapPng)
                                <a href="/hidrologi/file/download/{{ $riverMapPng->id }}" class="icon-btn" title="Download PNG">
                                    <i class="fas fa-image" style="font-size:0.75rem"></i>
                                </a>
                            @endif
                        </div>
                    </div>

                    {{-- Map viewer --}}
                    <div style="background:#f8fafc;border-bottom:1.5px solid var(--c-border);position:relative;overflow:hidden">
                        @if($riverMapHtml)
                            <div id="mapLoadingOverlay" style="position:absolute;inset:0;background:#fff;display:flex;align-items:center;justify-content:center;z-index:10">
                                <div class="text-center">
                                    <div class="animate-spin rounded-full h-10 w-10 border-b-4 mx-auto mb-3" style="border-color:var(--c-teal)"></div>
                                    <p class="text-xs text-gray-500">{{ __('messages.map_loading') }}</p>
                                </div>
                            </div>
                            <iframe id="riverMapFrame"
                                src="{{ route('hidrologi.file.preview', $riverMapHtml->id) }}"
                                class="w-full border-0"
                                style="height:520px"
                                title="{{ __('messages.interactive_river_map') }}"
                                onload="setTimeout(function(){ document.getElementById('mapLoadingOverlay').style.display='none'; }, 500);">
                            </iframe>
                            <div style="background:var(--c-sky);padding:.625rem 1rem;display:flex;justify-content:space-between;align-items:center;gap:.75rem;flex-wrap:wrap">
                                <p class="text-xs text-gray-600"><i class="fas fa-info-circle text-blue-500 mr-1.5"></i>{{ __('messages.map_info_tip') }}</p>
                                <a href="{{ route('hidrologi.file.preview', $riverMapHtml->id) }}" target="_blank"
                                   class="btn-submit" style="padding:.5rem .875rem;font-size:0.75rem">
                                    <i class="fas fa-external-link-alt" style="font-size:0.7rem"></i> Fullscreen
                                </a>
                            </div>
                        @elseif($riverMapPng)
                            <img src="{{ route('hidrologi.file.preview', $riverMapPng->id) }}"
                                 alt="{{ __('messages.interactive_river_map') }}"
                                 class="w-full h-auto" style="max-height:520px;object-fit:contain"
                                 onerror="this.onerror=null;this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22800%22 height=%22400%22%3E%3Crect fill=%22%23f0f0f0%22 width=%22800%22 height=%22400%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 fill=%22%23999%22 font-size=%2216%22%3EImage not available%3C/text%3E%3C/svg%3E'">
                            <div class="alert-warning" style="margin:.75rem;border-radius:.5rem">
                                <i class="fas fa-exclamation-triangle text-amber-500 flex-shrink-0"></i>
                                <span class="text-xs text-amber-800">{{ __('messages.map_static_fallback') }}</span>
                            </div>
                        @else
                            <div style="padding:3rem;text-align:center">
                                <i class="fas fa-map text-gray-300" style="font-size:2rem;margin-bottom:.75rem;display:block"></i>
                                <p class="text-sm font-bold text-gray-500">{{ __('messages.map_not_ready_title') }}</p>
                                <p class="text-xs text-gray-400 mt-1 mb-3">{{ __('messages.map_not_ready_desc') }}</p>
                                <button onclick="location.reload()" class="btn-submit" style="font-size:0.75rem;padding:.5rem .875rem">
                                    <i class="fas fa-sync-alt" style="font-size:0.7rem"></i> {{ __('messages.refresh_page') }}
                                </button>
                            </div>
                        @endif
                    </div>

                    {{-- Map info cards --}}
                    @if($riverMapPng || $riverMapHtml)
                        <div class="step-body">
                            <div class="grid grid-cols-3 gap-3 text-xs">
                                <div>
                                    <p class="text-gray-400 font-bold uppercase tracking-wide mb-0.5" style="font-size:.65rem">{{ __('messages.map_analysis_location') }}</p>
                                    <p class="font-bold text-gray-800 truncate">{{ $job->location_name ?? 'N/A' }}</p>
                                    <p class="font-mono text-gray-400 mt-0.5" style="font-size:.7rem">{{ number_format($job->latitude,4) }}, {{ number_format($job->longitude,4) }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 font-bold uppercase tracking-wide mb-0.5" style="font-size:.65rem">{{ __('messages.map_layers') }}</p>
                                    <p class="font-bold text-gray-800">4 {{ __('messages.map_data_sources') }}</p>
                                    <p class="text-gray-400 mt-0.5">HydroSHEDS, JRC, SRTM, OSM</p>
                                </div>
                                <div>
                                    <p class="text-gray-400 font-bold uppercase tracking-wide mb-0.5" style="font-size:.65rem">{{ __('messages.map_buffer_area') }}</p>
                                    <p class="font-bold text-gray-800">10 km radius</p>
                                    <p class="text-gray-400 mt-0.5">{{ __('messages.map_river_network_area') }}</p>
                                </div>
                            </div>

                            {{-- Metadata accordion --}}
                            @if($riverMapMetadata)
                                <div style="margin-top:1rem;padding-top:1rem;border-top:1px solid var(--c-border)">
                                    <button onclick="toggleRiverMetadata()" class="w-full flex items-center justify-between text-left">
                                        <span class="text-xs font-bold text-gray-700"><i class="fas fa-info-circle text-teal-500 mr-1.5"></i>{{ __('messages.map_metadata_detail') }}</span>
                                        <i id="metadataChevron" class="fas fa-chevron-down text-gray-400 collapsible-chevron" style="font-size:0.75rem"></i>
                                    </button>
                                    <div id="riverMetadataContent" class="hidden" style="margin-top:.75rem">
                                        <div class="grid grid-cols-2 gap-3 text-xs">
                                            <div style="background:var(--c-surface);border-radius:.5rem;padding:.75rem">
                                                <p class="font-bold text-gray-600 mb-1.5">📊 {{ __('messages.map_data_sources') }}</p>
                                                <ul class="space-y-1 text-gray-600">
                                                    <li><i class="fas fa-check-circle text-green-500 mr-1.5"></i>HydroSHEDS</li>
                                                    <li><i class="fas fa-check-circle text-green-500 mr-1.5"></i>JRC Global Surface Water</li>
                                                    <li><i class="fas fa-check-circle text-green-500 mr-1.5"></i>SRTM DEM</li>
                                                    <li><i class="fas fa-check-circle text-green-500 mr-1.5"></i>OpenStreetMap</li>
                                                </ul>
                                            </div>
                                            <div style="background:var(--c-surface);border-radius:.5rem;padding:.75rem">
                                                <p class="font-bold text-gray-600 mb-1.5">🗺️ {{ __('messages.map_features') }}</p>
                                                <ul class="space-y-1 text-gray-600">
                                                    <li><i class="fas fa-water text-blue-500 mr-1.5"></i>River network</li>
                                                    <li><i class="fas fa-tint text-cyan-500 mr-1.5"></i>Water occurrence</li>
                                                    <li><i class="fas fa-mountain text-amber-500 mr-1.5"></i>Topography (DEM)</li>
                                                    <li><i class="fas fa-layer-group text-purple-500 mr-1.5"></i>Layer control</li>
                                                </ul>
                                            </div>
                                        </div>
                                        <p class="text-xs text-gray-500 mt-2"><i class="fas fa-lightbulb text-yellow-500 mr-1.5"></i><strong>{{ __('messages.tip') }}:</strong> {{ __('messages.map_usage_tip') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            @else
                <div class="step-card" style="overflow:hidden">
                    <div class="step-body text-center py-8">
                        <i class="fas fa-map-marked-alt text-gray-300" style="font-size:2rem;display:block;margin-bottom:.75rem"></i>
                        <h3 class="text-sm font-bold text-gray-600 mb-1">{{ __('messages.map_not_available') }}</h3>
                        <p class="text-xs text-gray-400 mb-3">{{ __('messages.map_not_available_desc') }}</p>
                        <div class="flex justify-center gap-2">
                            <button onclick="location.reload()" class="btn-submit" style="font-size:0.75rem;padding:.5rem .875rem">
                                <i class="fas fa-sync-alt" style="font-size:0.7rem"></i> {{ __('messages.refresh_page') }}
                            </button>
                            @if(in_array($job->status, ['pending','submitted','processing']))
                                <span class="flex items-center gap-1.5 text-xs text-gray-400 font-semibold">
                                    <i class="fas fa-clock"></i>{{ __('messages.map_being_processed') }}
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            {{-- ══════════════════════════════════════════
                 FILE HASIL ANALISIS
            ══════════════════════════════════════════ --}}
            @if($job->files->count() > 0)
                <div class="step-card" style="overflow:hidden">
                    <div class="step-header">
                        <div class="step-badge"><i class="fas fa-file-download" style="font-size:0.8rem"></i></div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm font-extrabold text-gray-900">{{ __('messages.generated_files') }}</h3>
                            <p class="text-xs text-gray-400 mt-0.5">{{ $job->files->count() }} file tersedia</p>
                        </div>
                        <div class="flex gap-1.5 flex-wrap">
                            <button onclick="filterFiles('all')"  class="filter-btn active" data-type="all"><i class="fas fa-th mr-1"></i>All</button>
                            <button onclick="filterFiles('png')"  class="filter-btn" data-type="png"><i class="fas fa-image mr-1"></i>PNG</button>
                            <button onclick="filterFiles('csv')"  class="filter-btn" data-type="csv"><i class="fas fa-table mr-1"></i>CSV</button>
                            <button onclick="filterFiles('json')" class="filter-btn" data-type="json"><i class="fas fa-code mr-1"></i>JSON</button>
                        </div>
                    </div>
                    <div class="step-body space-y-2">
                        @foreach($job->files->sortBy('display_order') as $file)
                            <div class="file-item" data-file-type="{{ strtolower($file->file_type) }}"
                                 style="border:1.5px solid var(--c-border);border-radius:.75rem;padding:.875rem">
                                <div class="flex items-start justify-between gap-3">
                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 mb-0.5">
                                            @if($file->file_type === 'png')
                                                <i class="fas fa-image text-blue-500 flex-shrink-0" style="font-size:0.8rem"></i>
                                            @elseif($file->file_type === 'csv')
                                                <i class="fas fa-table text-green-500 flex-shrink-0" style="font-size:0.8rem"></i>
                                            @elseif($file->file_type === 'json')
                                                <i class="fas fa-code text-orange-500 flex-shrink-0" style="font-size:0.8rem"></i>
                                            @else
                                                <i class="fas fa-file text-gray-400 flex-shrink-0" style="font-size:0.8rem"></i>
                                            @endif
                                            <span class="text-sm font-bold text-gray-800 break-all">{{ $file->display_name ?? $file->filename }}</span>
                                        </div>
                                        @if($file->description)
                                            <p class="text-xs text-gray-500 mb-1 break-words">{{ $file->description }}</p>
                                        @endif
                                        <div class="flex flex-wrap gap-x-3 gap-y-0.5 text-xs text-gray-400">
                                            <span>{{ strtoupper($file->file_type) }}</span>
                                            <span>{{ number_format($file->file_size_mb,2) }} MB</span>
                                            @if($file->created_at)
                                                <span class="hidden sm:inline">{{ $file->created_at->format('d M Y, H:i') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    {{-- Actions --}}
                                    <div class="flex gap-1.5 flex-shrink-0">
                                        @if($file->file_type === 'png')
                                            <button onclick="viewImage({{ $file->id }}, '{{ addslashes($file->display_name ?? $file->filename) }}')"
                                                    class="icon-btn icon-btn-view" title="View"><i class="fas fa-eye" style="font-size:0.75rem"></i></button>
                                        @elseif($file->file_type === 'csv')
                                            <button onclick="viewCSV({{ $file->id }}, '{{ addslashes($file->display_name ?? $file->filename) }}')"
                                                    class="icon-btn icon-btn-view" title="View"><i class="fas fa-eye" style="font-size:0.75rem"></i></button>
                                        @elseif($file->file_type === 'json')
                                            <button onclick="viewJSON({{ $file->id }}, '{{ addslashes($file->display_name ?? $file->filename) }}')"
                                                    class="icon-btn icon-btn-view" title="View"><i class="fas fa-eye" style="font-size:0.75rem"></i></button>
                                        @endif
                                        <a href="{{ route('hidrologi.file.download', $file->id) }}"
                                           class="icon-btn" title="Download"><i class="fas fa-download" style="font-size:0.75rem"></i></a>
                                    </div>
                                </div>
                                {{-- Preview container --}}
                                <div id="preview-{{ $file->id }}" class="preview-container hidden" style="margin-top:.875rem;padding-top:.875rem;border-top:1px solid var(--c-border)">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-xs font-bold text-gray-600">Preview</span>
                                        <button onclick="closePreview({{ $file->id }})" class="icon-btn" style="width:1.75rem;height:1.75rem">
                                            <i class="fas fa-times" style="font-size:0.7rem"></i>
                                        </button>
                                    </div>
                                    <div id="preview-content-{{ $file->id }}"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

        </div>{{-- /kolom kiri --}}

        {{-- ─────────────── KOLOM KANAN (sidebar) ─────────────── --}}
        <div class="lg:col-span-1 space-y-5 order-1 lg:order-2">

            {{-- ── Timeline ── --}}
            <div class="step-card" style="overflow:hidden">
                <div class="step-header">
                    <div class="step-badge"><i class="fas fa-clock" style="font-size:0.8rem"></i></div>
                    <h3 class="text-sm font-extrabold text-gray-900">Timeline</h3>
                </div>
                <div class="step-body space-y-0">
                    <div class="flex items-start gap-3 py-2.5" style="border-bottom:1px solid var(--c-border)">
                        <div class="tl-dot" style="background:#2563eb;margin-top:.3rem"></div>
                        <div>
                            <p class="text-xs font-extrabold text-gray-800">{{ __('messages.created_label') }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $job->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    @if($job->submitted_at)
                        <div class="flex items-start gap-3 py-2.5" style="border-bottom:1px solid var(--c-border)">
                            <div class="tl-dot" style="background:#0891b2;margin-top:.3rem"></div>
                            <div>
                                <p class="text-xs font-extrabold text-gray-800">{{ __('messages.submitted_label') }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $job->submitted_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                    @if($job->started_at)
                        <div class="flex items-start gap-3 py-2.5" style="border-bottom:1px solid var(--c-border)">
                            <div class="tl-dot" style="background:#d97706;margin-top:.3rem"></div>
                            <div>
                                <p class="text-xs font-extrabold text-gray-800">{{ __('messages.started_processing') }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $job->started_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                    @if($job->completed_at)
                        <div class="flex items-start gap-3 py-2.5">
                            <div class="tl-dot" style="background:#16a34a;margin-top:.3rem"></div>
                            <div>
                                <p class="text-xs font-extrabold text-gray-800">{{ __('messages.finished_label') }}</p>
                                <p class="text-xs text-gray-500 mt-0.5">{{ $job->completed_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- ── Statistik File ── --}}
            <div class="step-card" style="overflow:hidden">
                <div class="step-header">
                    <div class="step-badge"><i class="fas fa-chart-bar" style="font-size:0.8rem"></i></div>
                    <h3 class="text-sm font-extrabold text-gray-900">{{ __('messages.statistics') }}</h3>
                </div>
                <div class="step-body space-y-0">
                    <div class="sidebar-stat">
                        <span class="flex items-center gap-2 text-xs font-bold text-gray-700">
                            <div style="width:1.75rem;height:1.75rem;background:#dbeafe;border-radius:.375rem;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <i class="fas fa-image text-blue-600" style="font-size:0.7rem"></i>
                            </div>
                            File PNG
                        </span>
                        <span class="text-base font-extrabold text-gray-900">{{ $job->png_count }}</span>
                    </div>
                    <div class="sidebar-stat">
                        <span class="flex items-center gap-2 text-xs font-bold text-gray-700">
                            <div style="width:1.75rem;height:1.75rem;background:#dcfce7;border-radius:.375rem;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <i class="fas fa-table text-green-600" style="font-size:0.7rem"></i>
                            </div>
                            File CSV
                        </span>
                        <span class="text-base font-extrabold text-gray-900">{{ $job->csv_count }}</span>
                    </div>
                    <div class="sidebar-stat">
                        <span class="flex items-center gap-2 text-xs font-bold text-gray-700">
                            <div style="width:1.75rem;height:1.75rem;background:#ffedd5;border-radius:.375rem;display:flex;align-items:center;justify-content:center;flex-shrink:0">
                                <i class="fas fa-code text-orange-600" style="font-size:0.7rem"></i>
                            </div>
                            File JSON
                        </span>
                        <span class="text-base font-extrabold text-gray-900">{{ $job->json_count }}</span>
                    </div>
                    <div class="sidebar-stat" style="border-bottom:none;padding-top:1rem;margin-top:.25rem;border-top:1.5px solid var(--c-border)">
                        <span class="text-xs font-extrabold text-gray-700">{{ __('messages.total_files') }}</span>
                        <span class="text-xl font-extrabold" style="color:var(--c-teal)">{{ $job->total_files }}</span>
                    </div>
                </div>
            </div>

        </div>{{-- /kolom kanan --}}
    </div>{{-- /grid --}}

</div>
@endsection

@push('styles')
<style>
/* File filter active state */
.filter-btn.active { background: var(--c-teal); border-color: var(--c-teal); color: #fff; }
</style>
@endpush

@push('scripts')
<script>
/* ── Filter file berdasarkan tipe ── */
function filterFiles(type) {
    document.querySelectorAll('.filter-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelector(`.filter-btn[data-type="${type}"]`)?.classList.add('active');
    document.querySelectorAll('.file-item').forEach(item => {
        item.style.display = (type === 'all' || item.dataset.fileType === type) ? '' : 'none';
    });
}

/* ── Preview ── */
function closePreview(fileId) {
    document.getElementById(`preview-${fileId}`)?.classList.add('hidden');
}

function viewImage(fileId, name) {
    const container = document.getElementById(`preview-content-${fileId}`);
    if (!container) return;
    container.innerHTML = `<img src="/hidrologi/file/preview/${fileId}" alt="${name}" style="max-width:100%;height:auto;border-radius:.5rem;border:1.5px solid var(--c-border)">`;
    document.getElementById(`preview-${fileId}`)?.classList.remove('hidden');
}

function viewCSV(fileId, name) {
    const container = document.getElementById(`preview-content-${fileId}`);
    if (!container) return;
    container.innerHTML = `<div style="text-align:center;padding:1rem"><div class="animate-spin rounded-full h-6 w-6 border-b-2 mx-auto" style="border-color:var(--c-teal)"></div></div>`;
    document.getElementById(`preview-${fileId}`)?.classList.remove('hidden');

    fetch(`/hidrologi/file/preview/${fileId}`)
        .then(r => r.text())
        .then(text => {
            const rows = text.trim().split('\n').slice(0,51);
            const headers = rows[0].split(',');
            let table = `<div style="overflow-x:auto"><table class="mini-table"><thead><tr>${headers.map(h=>`<th>${h.trim()}</th>`).join('')}</tr></thead><tbody>`;
            rows.slice(1).forEach(row => {
                const cols = row.split(',');
                table += `<tr>${cols.map(c=>`<td>${c.trim()}</td>`).join('')}</tr>`;
            });
            table += `</tbody></table></div>`;
            if (rows.length > 50) table += `<p class="text-xs text-gray-400 mt-2">Menampilkan 50 baris pertama.</p>`;
            container.setAttribute('data-csv-text', text);
            container.innerHTML = table + `<div style="margin-top:.75rem;display:flex;gap:.5rem">
                <button onclick="copyCSVData(${fileId})" class="btn-submit" style="font-size:0.72rem;padding:.375rem .75rem"><i class="fas fa-copy mr-1"></i>Copy CSV</button>
                <a href="/hidrologi/file/download/${fileId}" class="btn-submit" style="font-size:0.72rem;padding:.375rem .75rem;background:#475569"><i class="fas fa-download mr-1"></i>Download</a>
            </div>`;
        })
        .catch(() => { container.innerHTML = `<p class="text-xs text-red-600">Gagal memuat CSV.</p>`; });
}

function viewJSON(fileId, name) {
    const container = document.getElementById(`preview-content-${fileId}`);
    if (!container) return;
    container.innerHTML = `<div style="text-align:center;padding:1rem"><div class="animate-spin rounded-full h-6 w-6 border-b-2 mx-auto" style="border-color:var(--c-teal)"></div></div>`;
    document.getElementById(`preview-${fileId}`)?.classList.remove('hidden');

    fetch(`/hidrologi/file/preview/${fileId}`)
        .then(r => r.text())
        .then(text => {
            let formatted = text;
            try { formatted = JSON.stringify(JSON.parse(text), null, 2); } catch(e) {}
            container.setAttribute('data-json-text', text);
            container.innerHTML = `<div style="background:#1e293b;border-radius:.625rem;padding:.875rem;overflow:auto;max-height:20rem">
                <pre style="color:#94d1a0;font-size:0.72rem;font-family:monospace;white-space:pre-wrap;word-break:break-all">${formatted.replace(/</g,'&lt;').replace(/>/g,'&gt;')}</pre>
            </div><div style="margin-top:.75rem;display:flex;gap:.5rem">
                <button onclick="copyJSONData(${fileId})" class="btn-submit" style="font-size:0.72rem;padding:.375rem .75rem"><i class="fas fa-copy mr-1"></i>Copy JSON</button>
                <a href="/hidrologi/file/download/${fileId}" class="btn-submit" style="font-size:0.72rem;padding:.375rem .75rem;background:#475569"><i class="fas fa-download mr-1"></i>Download</a>
            </div>`;
        })
        .catch(() => { container.innerHTML = `<p class="text-xs text-red-600">Gagal memuat JSON.</p>`; });
}

function copyCSVData(fileId) {
    const text = document.getElementById(`preview-content-${fileId}`)?.getAttribute('data-csv-text');
    if (text) navigator.clipboard.writeText(text)
        .then(() => Swal.fire({ icon:'success', title:'Copied!', text:'CSV data copied', timer:1500, showConfirmButton:false }))
        .catch(() => Swal.fire({ icon:'error', title:'Error', text:'Failed to copy CSV', timer:1500, showConfirmButton:false }));
}

function copyJSONData(fileId) {
    const text = document.getElementById(`preview-content-${fileId}`)?.getAttribute('data-json-text');
    if (text) navigator.clipboard.writeText(text)
        .then(() => Swal.fire({ icon:'success', title:'Copied!', text:'JSON data copied', timer:1500, showConfirmButton:false }))
        .catch(() => Swal.fire({ icon:'error', title:'Error', text:'Failed to copy JSON', timer:1500, showConfirmButton:false }));
}

/* ── Map ── */
function openMapFullscreen() {
    const mapFrame = document.getElementById('riverMapFrame');
    if (mapFrame?.src) {
        Swal.fire({
            html: `<div style="width:100%;height:85vh"><iframe src="${mapFrame.src}" style="width:100%;height:100%;border:none;border-radius:.5rem" sandbox="allow-scripts allow-same-origin allow-popups" allow="fullscreen"></iframe></div>`,
            width: '95%', padding: '10px',
            showCloseButton: true, showConfirmButton: false,
            customClass: { popup:'rounded-2xl', htmlContainer:'p-2' },
            background: '#f9fafb'
        });
    }
}

function toggleRiverMetadata() {
    const content  = document.getElementById('riverMetadataContent');
    const chevron  = document.getElementById('metadataChevron');
    if (!content || !chevron) return;
    const hidden = content.classList.toggle('hidden');
    chevron.classList.toggle('open', !hidden);
}

/* ── Auto-refresh untuk job yang masih berjalan ── */
@if(in_array($job->status, ['pending', 'submitted', 'processing']))
    const refreshInterval = setInterval(() => {
        fetch(`/hidrologi/status/{{ $job->id }}`, { headers:{ 'Accept':'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (data.progress !== undefined) {
                    const bar     = document.getElementById('progress-bar');
                    const pct     = document.getElementById('progress-percent');
                    if (bar) bar.style.width  = data.progress + '%';
                    if (pct) pct.textContent  = data.progress + '%';
                }
                if (!['pending','submitted','processing'].includes(data.status)) {
                    clearInterval(refreshInterval);
                    location.reload();
                }
            })
            .catch(() => {});
    }, 5000);
@endif

/* ── Cancel job ── */
function cancelJob(jobId) {
    Swal.fire({
        title: 'Batalkan Pekerjaan?',
        text: 'Apakah Anda yakin ingin membatalkan pekerjaan ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d97706',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Batalkan!',
        cancelButtonText: 'Tidak'
    }).then(result => {
        if (!result.isConfirmed) return;
        Swal.fire({ title:'Membatalkan...', allowOutsideClick:false, didOpen:()=>Swal.showLoading() });
        fetch(`/hidrologi/cancel/${jobId}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN':'{{ csrf_token() }}', 'Content-Type':'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                Swal.fire({ icon:'success', title:'Berhasil!', text:data.message||'Pekerjaan dibatalkan.', showConfirmButton:false, timer:1500 })
                    .then(() => location.reload());
            } else {
                Swal.fire({ icon:'error', title:'Gagal!', text:data.message||'Gagal membatalkan pekerjaan.' });
            }
        })
        .catch(() => Swal.fire({ icon:'error', title:'Error!', text:'Terjadi kesalahan.' }));
    });
}

/* ── Delete job ── */
function deleteJob(jobId) {
    Swal.fire({
        title: 'Hapus Pekerjaan?',
        html: "Apakah Anda yakin?<br><strong class='text-red-600'>Tindakan ini tidak dapat dibatalkan!</strong>",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then(result => {
        if (!result.isConfirmed) return;
        Swal.fire({ title:'Menghapus...', allowOutsideClick:false, didOpen:()=>Swal.showLoading() });
        fetch(`/hidrologi/delete/${jobId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN':'{{ csrf_token() }}', 'Content-Type':'application/json', 'Accept':'application/json' }
        })
        .then(r => { if (!r.ok) throw new Error(`HTTP ${r.status}`); return r.json(); })
        .then(data => {
            if (data.success) {
                Swal.fire({ icon:'success', title:'Berhasil!', text:data.message||'Pekerjaan dihapus.', showConfirmButton:false, timer:1500 })
                    .then(() => window.location.href = '{{ route("hidrologi.index") }}');
            } else {
                Swal.fire({ icon:'error', title:'Gagal!', text:data.message||'Gagal menghapus pekerjaan.' });
            }
        })
        .catch(err => Swal.fire({ icon:'error', title:'Error!', text:'Terjadi kesalahan: '+err.message }));
    });
}
</script>
@endpush