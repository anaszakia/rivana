@extends('layouts.app')

@section('title', __('messages.job_detail') . ' - ' . $job->job_id)

@push('styles')
<style>
/* ── Design Tokens (same as create page) ── */
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

/* ── Status colors ── */
.status-pending    { --s-bg:#f1f5f9; --s-text:#475569; --s-border:#cbd5e1; --s-icon:#64748b; }
.status-submitted  { --s-bg:#eff6ff; --s-text:#1d4ed8; --s-border:#bfdbfe; --s-icon:#3b82f6; }
.status-processing { --s-bg:#fffbeb; --s-text:#92400e; --s-border:#fde68a; --s-icon:#f59e0b; }
.status-completed  { --s-bg:#f0fdf4; --s-text:#166534; --s-border:#a7f3d0; --s-icon:#10b981; }
.status-completed_with_warning { --s-bg:#fff7ed; --s-text:#9a3412; --s-border:#fdba74; --s-icon:#f97316; }
.status-failed     { --s-bg:#fef2f2; --s-text:#991b1b; --s-border:#fecaca; --s-icon:#ef4444; }
.status-cancelled  { --s-bg:#f8fafc; --s-text:#475569; --s-border:#cbd5e1; --s-icon:#94a3b8; }

/* ── Cards ── */
.info-card {
    background: var(--c-white);
    border-radius: var(--radius-card);
    border: 1.5px solid var(--c-border);
    box-shadow: var(--shadow-card);
}
.card-head {
    display: flex; align-items: center; gap: 0.75rem;
    padding: 1rem 1.25rem;
    border-bottom: 1.5px solid var(--c-border);
}
.card-icon {
    width: 2.25rem; height: 2.25rem;
    border-radius: 0.625rem;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.85rem; flex-shrink: 0;
}
.card-title { font-size: 0.875rem; font-weight: 800; color: var(--c-slate); }
.card-body  { padding: 1rem 1.25rem; }

/* ── Meta rows (label+value) ── */
.meta-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.625rem; }
@media (max-width: 480px) { .meta-grid { grid-template-columns: 1fr; } }

.meta-item {
    background: var(--c-surface);
    border: 1.5px solid var(--c-border);
    border-radius: 0.625rem;
    padding: 0.75rem 0.875rem;
}
.meta-item.full { grid-column: 1 / -1; }
.meta-label { font-size: 0.7rem; font-weight: 700; color: var(--c-muted); text-transform: uppercase; letter-spacing: 0.04em; margin-bottom: 0.25rem; }
.meta-value { font-size: 0.875rem; font-weight: 700; color: var(--c-slate); }
.meta-value.mono { font-family: ui-monospace, monospace; font-size: 0.8rem; }

/* ── Status banner ── */
.status-banner {
    display: flex; align-items: center; gap: 1rem;
    padding: 1.25rem;
    border-radius: var(--radius-card);
    border: 1.5px solid var(--s-border);
    background: var(--s-bg);
    flex-wrap: wrap;
}
.status-icon-wrap {
    width: 3rem; height: 3rem;
    border-radius: 0.875rem;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    border: 1.5px solid var(--s-border);
    background: var(--c-white);
}
.status-icon-wrap i { font-size: 1.25rem; color: var(--s-icon); }
.status-label { font-size: 1.25rem; font-weight: 800; color: var(--s-text); }
.status-msg   { font-size: 0.8rem; color: var(--c-muted); margin-top: 0.125rem; }

/* ── Progress bar ── */
.progress-wrap { flex: 1; min-width: 200px; }
.progress-bar-bg {
    width: 100%; height: 0.5rem;
    background: var(--c-border);
    border-radius: 9999px;
    overflow: hidden;
}
.progress-bar-fill {
    height: 100%;
    border-radius: 9999px;
    background: var(--c-teal);
    transition: width 0.5s ease;
}

/* ── Alert strips ── */
.alert-strip {
    display: flex; align-items: flex-start; gap: 0.625rem;
    padding: 0.75rem 1rem;
    border-radius: 0.625rem;
    font-size: 0.8rem; font-weight: 500;
    border-left: 3px solid;
}
.alert-strip.warning { background: #fffbeb; color: #92400e; border-color: #f59e0b; }
.alert-strip.error   { background: #fef2f2; color: #991b1b; border-color: #ef4444; }
.alert-strip.info    { background: var(--c-sky); color: #0369a1; border-color: #38bdf8; }

/* ── Section collapsible ── */
.section-toggle-btn {
    display: flex; align-items: center; justify-content: space-between;
    width: 100%; text-align: left; background: none; border: none; cursor: pointer;
    padding: 0.875rem 1.25rem;
    font-size: 0.875rem; font-weight: 700; color: var(--c-slate);
    border-radius: var(--radius-card);
    transition: background 0.15s;
}
.section-toggle-btn:hover { background: var(--c-surface); }
.section-toggle-btn .chev { transition: transform 0.2s; color: var(--c-muted); }
.section-toggle-btn.open .chev { transform: rotate(180deg); }
.section-body { display: none; padding: 0 1.25rem 1.25rem; }
.section-body.open { display: block; }

/* ── Summary sub-cards ── */
.sub-card {
    background: var(--c-surface);
    border: 1.5px solid var(--c-border);
    border-radius: 0.875rem;
    margin-bottom: 0.75rem;
}
.sub-card-head {
    display: flex; align-items: center; gap: 0.5rem;
    padding: 0.75rem 1rem;
    border-bottom: 1.5px solid var(--c-border);
    font-size: 0.8rem; font-weight: 700; color: var(--c-slate);
}
.sub-card-body { padding: 0.875rem 1rem; }

.kv-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; }
@media (max-width: 480px) { .kv-grid { grid-template-columns: 1fr; } }
.kv-item { background: var(--c-white); border-radius: 0.5rem; padding: 0.5rem 0.625rem; border: 1px solid var(--c-border); }
.kv-label { font-size: 0.68rem; color: var(--c-muted); font-weight: 600; }
.kv-value { font-size: 0.825rem; font-weight: 800; color: var(--c-slate); }

/* ── Map section ── */
.map-container {
    position: relative; overflow: hidden;
    isolation: isolate;
    transform: translateZ(0);
    border-radius: 0.875rem;
}

/* ── File list ── */
.file-item {
    display: flex; align-items: flex-start; gap: 0.75rem;
    padding: 0.875rem 1rem;
    border: 1.5px solid var(--c-border);
    border-radius: 0.75rem;
    background: var(--c-white);
    transition: all 0.15s;
}
.file-item:hover { border-color: var(--c-teal); box-shadow: 0 2px 8px rgba(13,148,136,.08); }
.file-icon {
    width: 2.25rem; height: 2.25rem; border-radius: 0.5rem;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0; font-size: 0.875rem;
}
.file-actions { display: flex; gap: 0.375rem; flex-shrink: 0; }
.file-btn {
    display: inline-flex; align-items: center; gap: 0.3rem;
    padding: 0.375rem 0.75rem;
    border-radius: 0.5rem;
    font-size: 0.75rem; font-weight: 600;
    border: 1.5px solid;
    cursor: pointer; transition: all 0.15s;
    text-decoration: none;
    white-space: nowrap;
}
.file-btn.view-png  { background: #eff6ff; color: #1d4ed8; border-color: #bfdbfe; }
.file-btn.view-csv  { background: #f0fdf4; color: #166534; border-color: #a7f3d0; }
.file-btn.view-json { background: #fff7ed; color: #9a3412; border-color: #fdba74; }
.file-btn.view-html { background: #f0fdfa; color: var(--c-teal-dk); border-color: var(--c-teal-lt); }
.file-btn.download  { background: var(--c-surface); color: var(--c-muted); border-color: var(--c-border); }
.file-btn:hover { filter: brightness(0.95); transform: translateY(-1px); }

/* ── Filter pills ── */
.filter-pill {
    padding: 0.375rem 0.875rem;
    border-radius: 9999px;
    font-size: 0.75rem; font-weight: 700;
    border: 1.5px solid var(--c-border);
    background: var(--c-white); color: var(--c-muted);
    cursor: pointer; transition: all 0.15s;
}
.filter-pill:hover  { border-color: var(--c-teal); color: var(--c-teal-dk); }
.filter-pill.active { background: var(--c-teal); color: #fff; border-color: var(--c-teal); }

/* ── Sidebar guide (same pattern as create) ── */
.guide-card { background: var(--c-white); border-radius: var(--radius-card); border: 1.5px solid var(--c-border); box-shadow: var(--shadow-card); overflow: hidden; }
.guide-head { display: flex; align-items: center; gap: 0.75rem; padding: 1rem 1.25rem; background: linear-gradient(135deg, #f0fdfa, var(--c-sky)); border-bottom: 1.5px solid #bae6fd; }
.guide-icon { width: 2rem; height: 2rem; border-radius: 0.5rem; background: var(--c-teal); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 0.8rem; flex-shrink: 0; }
.guide-title { font-size: 0.85rem; font-weight: 800; color: var(--c-slate); }
.guide-body  { padding: 0.875rem 1.25rem; }
.guide-row { display: flex; align-items: flex-start; gap: 0.625rem; padding: 0.5rem 0; border-bottom: 1px solid var(--c-border); font-size: 0.775rem; }
.guide-row:last-child { border-bottom: none; }
.guide-dot { width: 0.625rem; height: 0.625rem; border-radius: 50%; margin-top: 0.3rem; flex-shrink: 0; }
.guide-row-text { color: var(--c-muted); line-height: 1.45; }
.guide-row-text strong { color: var(--c-slate); }

/* ── Timeline ── */
.timeline-item { display: flex; align-items: flex-start; gap: 0.75rem; padding: 0.625rem 0; border-bottom: 1px solid var(--c-border); }
.timeline-item:last-child { border-bottom: none; }
.timeline-dot { width: 0.625rem; height: 0.625rem; border-radius: 50%; margin-top: 0.35rem; flex-shrink: 0; }

/* ── Button styles ── */
.btn-danger {
    display: inline-flex; align-items: center; gap: 0.5rem;
    padding: 0.625rem 1.125rem;
    border-radius: 0.625rem; border: 1.5px solid #fecaca;
    background: #fef2f2; color: #991b1b;
    font-size: 0.8rem; font-weight: 700;
    cursor: pointer; transition: all 0.15s;
}
.btn-danger:hover { background: #fee2e2; border-color: #ef4444; }

.btn-warn {
    display: inline-flex; align-items: center; gap: 0.5rem;
    padding: 0.625rem 1.125rem;
    border-radius: 0.625rem; border: 1.5px solid #fde68a;
    background: #fffbeb; color: #92400e;
    font-size: 0.8rem; font-weight: 700;
    cursor: pointer; transition: all 0.15s;
}
.btn-warn:hover { background: #fef3c7; border-color: #f59e0b; }

.btn-back {
    display: inline-flex; align-items: center; gap: 0.5rem;
    padding: 0.625rem 1.125rem;
    border-radius: 0.625rem; border: 1.5px solid var(--c-border);
    background: transparent; color: var(--c-muted);
    font-size: 0.8rem; font-weight: 600;
    transition: all 0.15s; text-decoration: none;
}
.btn-back:hover { background: var(--c-surface); color: var(--c-slate); }

/* ── Stat row ── */
.stat-row { display: flex; justify-content: space-between; align-items: center; padding: 0.625rem 0; border-bottom: 1px solid var(--c-border); }
.stat-row:last-child { border-bottom: none; }
.stat-row .s-label { display: flex; align-items: center; gap: 0.5rem; font-size: 0.8rem; font-weight: 600; color: var(--c-muted); }
.stat-row .s-val   { font-size: 0.975rem; font-weight: 800; color: var(--c-slate); }

/* ── Preview container ── */
.preview-container { display: none; margin-top: 0.75rem; padding-top: 0.75rem; border-top: 1.5px solid var(--c-border); }
.preview-content img { max-width: 100%; border-radius: 0.5rem; }
.preview-content table { font-size: 0.8rem; border-collapse: collapse; width: 100%; }
.preview-content table th { background: var(--c-surface); font-weight: 700; padding: 0.375rem 0.625rem; border: 1px solid var(--c-border); position: sticky; top: 0; }
.preview-content table td { padding: 0.375rem 0.625rem; border: 1px solid var(--c-border); }
.preview-content table tbody tr:hover { background: #f0fdfa; }

/* ── Processing pulse ── */
@keyframes pulse-teal {
    0%, 100% { opacity: 1; }
    50%       { opacity: 0.5; }
}
.processing-pulse { animation: pulse-teal 2s ease infinite; }

/* ── Badge ── */
.badge { display: inline-flex; align-items: center; gap: 0.3rem; padding: 0.2rem 0.6rem; border-radius: 9999px; font-size: 0.7rem; font-weight: 700; }

/* ── Recommendation items ── */
.rec-item { background: var(--c-white); border-radius: 0.625rem; border: 1.5px solid var(--c-border); overflow: hidden; margin-bottom: 0.5rem; }
.rec-head { display: flex; align-items: center; justify-content: space-between; padding: 0.625rem 0.875rem; background: var(--c-surface); border-bottom: 1px solid var(--c-border); }
.rec-body { padding: 0.75rem 0.875rem; font-size: 0.8rem; }
.priority-high   { background: #fef2f2; color: #991b1b; }
.priority-medium { background: #fffbeb; color: #92400e; }
.priority-low    { background: #f0fdf4; color: #166534; }
</style>
@endpush

@section('content')

@php
$statusConfig = [
    'pending'                => ['cls'=>'status-pending',   'icon'=>'fa-clock',              'label'=> __('messages.waiting')],
    'submitted'              => ['cls'=>'status-submitted',  'icon'=>'fa-paper-plane',         'label'=> __('messages.sent')],
    'processing'             => ['cls'=>'status-processing', 'icon'=>'fa-spinner fa-spin',     'label'=> __('messages.processed')],
    'completed'              => ['cls'=>'status-completed',  'icon'=>'fa-check-circle',        'label'=> __('messages.completed')],
    'completed_with_warning' => ['cls'=>'status-completed_with_warning', 'icon'=>'fa-exclamation-triangle', 'label'=> __('messages.completed_with_warning')],
    'failed'                 => ['cls'=>'status-failed',     'icon'=>'fa-times-circle',        'label'=> __('messages.failed')],
    'cancelled'              => ['cls'=>'status-cancelled',  'icon'=>'fa-ban',                 'label'=> __('messages.cancelled')],
];
$sc = $statusConfig[$job->status] ?? $statusConfig['pending'];
@endphp

<div class="w-full max-w-6xl mx-auto px-4 sm:px-5 lg:px-6 py-5">

    {{-- ── Page header ── --}}
    <div class="mb-5 flex items-start gap-3">
        <a href="{{ route('hidrologi.index') }}" class="btn-back mt-0.5">
            <i class="fas fa-arrow-left text-xs"></i>
        </a>
        <div class="flex-1 min-w-0">
            <p class="text-xs text-gray-400 font-medium mb-0.5">
                <a href="{{ route('hidrologi.index') }}" class="hover:text-teal-600 transition-colors">{{ __('messages.hydrology') }}</a>
                <span class="mx-1.5">·</span>
                {{ __('messages.job_detail') }}
            </p>
            <div class="flex flex-wrap items-center gap-2">
                <h1 class="text-xl font-extrabold text-gray-900">{{ __('messages.job_detail') }}</h1>
                <span class="font-mono text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-md truncate max-w-xs">{{ $job->job_id }}</span>
            </div>
        </div>
        {{-- Action buttons --}}
        <div class="flex gap-2 flex-shrink-0">
            @if(in_array($job->status, ['pending', 'submitted', 'processing']))
                <button onclick="cancelJob({{ $job->id }})" class="btn-warn">
                    <i class="fas fa-stop-circle text-xs"></i>
                    <span class="hidden sm:inline">{{ __('messages.cancel_job') }}</span>
                </button>
            @endif
            <button onclick="deleteJob({{ $job->id }})" class="btn-danger">
                <i class="fas fa-trash text-xs"></i>
                <span class="hidden sm:inline">{{ __('messages.delete') }}</span>
            </button>
        </div>
    </div>

    {{-- ── Status Banner ── --}}
    <div class="status-banner {{ $sc['cls'] }} mb-5">
        <div class="status-icon-wrap">
            <i class="fas {{ $sc['icon'] }}"></i>
        </div>
        <div class="flex-1 min-w-0">
            <div class="status-label">{{ $sc['label'] }}</div>
            <div class="status-msg">{{ $job->status_message ?? __('messages.processing_job') }}</div>
        </div>

        {{-- Progress bar for active jobs --}}
        @if(in_array($job->status, ['pending', 'submitted', 'processing']))
        <div class="progress-wrap">
            <div class="flex justify-between text-xs font-bold mb-1.5" style="color: var(--s-text)">
                <span>{{ __('messages.progress') }}</span>
                <span id="progress-percent">{{ $job->progress }}%</span>
            </div>
            <div class="progress-bar-bg">
                <div id="progress-bar" class="progress-bar-fill" style="width: {{ $job->progress }}%"></div>
            </div>
        </div>
        @endif
    </div>

    {{-- Warning / Error messages --}}
    @if($job->warning_message)
    <div class="alert-strip warning mb-3">
        <i class="fas fa-exclamation-triangle flex-shrink-0 mt-0.5" style="font-size:0.75rem"></i>
        <div><strong>{{ __('messages.warning') }}:</strong> {{ $job->warning_message }}</div>
    </div>
    @endif
    @if($job->error_message)
    <div class="alert-strip error mb-3">
        <i class="fas fa-times-circle flex-shrink-0 mt-0.5" style="font-size:0.75rem"></i>
        <div><strong>{{ __('messages.error') }}:</strong> {{ $job->error_message }}</div>
    </div>
    @endif

    {{-- ── Main grid ── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ── LEFT column ── --}}
        <div class="lg:col-span-2 space-y-4">

            {{-- Location info --}}
            <div class="info-card">
                <div class="card-head">
                    <div class="card-icon" style="background:#fef2f2; color:#ef4444;">
                        <i class="fas fa-map-marker-alt"></i>
                    </div>
                    <span class="card-title">{{ __('messages.location_info') }}</span>
                </div>
                <div class="card-body">
                    <div class="meta-grid">
                        <div class="meta-item">
                            <div class="meta-label"><i class="fas fa-map-pin mr-1"></i>{{ __('messages.location_name') }}</div>
                            <div class="meta-value">{{ $job->location_name ?? __('messages.not_available') }}</div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label"><i class="fas fa-crosshairs mr-1"></i>{{ __('messages.coordinates') }}</div>
                            <div class="meta-value mono">{{ $job->latitude }}, {{ $job->longitude }}</div>
                        </div>
                        @if($job->das_name)
                        <div class="meta-item">
                            <div class="meta-label"><i class="fas fa-draw-polygon mr-1"></i>DAS</div>
                            <div class="meta-value">{{ $job->das_name }} <span class="text-xs font-normal text-gray-400 ml-1">Level {{ $job->das_level }}</span></div>
                        </div>
                        @if($job->das_area_km2)
                        <div class="meta-item">
                            <div class="meta-label"><i class="fas fa-expand-arrows-alt mr-1"></i>{{ __('messages.das_area') }}</div>
                            <div class="meta-value">{{ number_format($job->das_area_km2, 1) }} km²</div>
                        </div>
                        @endif
                        @endif
                        @if($job->location_description)
                        <div class="meta-item full">
                            <div class="meta-label"><i class="fas fa-file-alt mr-1"></i>{{ __('messages.description') }}</div>
                            <div class="meta-value" style="font-weight:500; font-size:0.8rem;">{{ $job->location_description }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Analysis period --}}
            <div class="info-card">
                <div class="card-head">
                    <div class="card-icon" style="background:#f5f3ff; color:#7c3aed;">
                        <i class="fas fa-calendar-alt"></i>
                    </div>
                    <span class="card-title">{{ __('messages.analysis_period') }}</span>
                </div>
                <div class="card-body">
                    <div class="meta-grid">
                        <div class="meta-item">
                            <div class="meta-label"><i class="fas fa-calendar-check mr-1 text-emerald-500"></i>{{ __('messages.start_date') }}</div>
                            <div class="meta-value">{{ \Carbon\Carbon::parse($job->start_date)->format('d F Y') }}</div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label"><i class="fas fa-calendar-times mr-1 text-red-400"></i>{{ __('messages.end_date') }}</div>
                            <div class="meta-value">{{ \Carbon\Carbon::parse($job->end_date)->format('d F Y') }}</div>
                        </div>
                        <div class="meta-item full">
                            <div class="meta-label"><i class="fas fa-hourglass-half mr-1"></i>{{ __('messages.duration') }}</div>
                            <div class="meta-value">
                                {{ \Carbon\Carbon::parse($job->start_date)->diffInDays(\Carbon\Carbon::parse($job->end_date)) + 1 }} {{ __('messages.days') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Analysis Summary (collapsible sections) --}}
            @if($summary)

            {{-- Job Info --}}
            @if(isset($summary['job_info']))
            <div class="info-card">
                <button class="section-toggle-btn open" onclick="toggleSection(this)">
                    <div style="display:flex;align-items:center;gap:0.625rem;">
                        <div class="card-icon" style="background:var(--c-teal-lt);color:var(--c-teal-dk);width:1.75rem;height:1.75rem;font-size:0.75rem;">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <span>{{ __('messages.job_info') }}</span>
                    </div>
                    <i class="fas fa-chevron-down chev"></i>
                </button>
                <div class="section-body open">
                    <div class="kv-grid">
                        <div class="kv-item">
                            <div class="kv-label">{{ __('messages.job_id') }}</div>
                            <div class="kv-value" style="font-family:monospace;font-size:0.75rem;">{{ $summary['job_info']['job_id'] ?? __('messages.n_a') }}</div>
                        </div>
                        <div class="kv-item">
                            <div class="kv-label">{{ __('messages.status') }}</div>
                            <div class="kv-value" style="color:var(--c-teal-dk);">{{ ucfirst(trans_api($summary['job_info']['status'] ?? '', 'status_umum')) }}</div>
                        </div>
                        @if(isset($summary['job_info']['created_at']))
                        <div class="kv-item">
                            <div class="kv-label">{{ __('messages.created_at') }}</div>
                            <div class="kv-value">{{ $summary['job_info']['created_at'] }}</div>
                        </div>
                        @endif
                        @if(isset($summary['job_info']['completed_at']))
                        <div class="kv-item">
                            <div class="kv-label">{{ __('messages.completed_at') }}</div>
                            <div class="kv-value">{{ $summary['job_info']['completed_at'] }}</div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Water Balance --}}
            @if(isset($summary['water_balance']))
            <div class="info-card">
                <button class="section-toggle-btn open" onclick="toggleSection(this)">
                    <div style="display:flex;align-items:center;gap:0.625rem;">
                        <div class="card-icon" style="background:#e0f2fe;color:#0e7490;width:1.75rem;height:1.75rem;font-size:0.75rem;">
                            <i class="fas fa-balance-scale"></i>
                        </div>
                        <span>{{ __('messages.water_balance') }}</span>
                    </div>
                    <i class="fas fa-chevron-down chev"></i>
                </button>
                <div class="section-body open">
                    <div class="kv-grid">
                        <div class="kv-item" style="background:#f0fdf4;">
                            <div class="kv-label">{{ __('messages.total_input') }}</div>
                            <div class="kv-value" style="color:#166534; font-size:1rem;">{{ $summary['water_balance']['total_input'] ?? 'N/A' }}</div>
                        </div>
                        <div class="kv-item" style="background:#fef2f2;">
                            <div class="kv-label">{{ __('messages.total_output') }}</div>
                            <div class="kv-value" style="color:#991b1b; font-size:1rem;">{{ $summary['water_balance']['total_output'] ?? 'N/A' }}</div>
                        </div>
                        <div class="kv-item">
                            <div class="kv-label">{{ __('messages.residual') }}</div>
                            <div class="kv-value">{{ $summary['water_balance']['residual'] ?? 'N/A' }}</div>
                        </div>
                        <div class="kv-item">
                            <div class="kv-label">{{ __('messages.error') }} %</div>
                            <div class="kv-value">{{ $summary['water_balance']['error_persen'] ?? 'N/A' }}</div>
                        </div>
                    </div>
                    @if(isset($summary['water_balance']['status']))
                    <div class="mt-2 text-center">
                        @php
                            $ws = $summary['water_balance']['status'] ?? '';
                            $wsBg = str_contains($ws,'Sangat Baik') ? 'background:#f0fdf4;color:#166534;border-color:#a7f3d0;'
                                : (str_contains($ws,'Baik') ? 'background:#eff6ff;color:#1d4ed8;border-color:#bfdbfe;'
                                : (str_contains($ws,'Cukup') ? 'background:#fffbeb;color:#92400e;border-color:#fde68a;'
                                : 'background:#fef2f2;color:#991b1b;border-color:#fecaca;'));
                        @endphp
                        <span class="badge" style="{{ $wsBg }} border:1.5px solid; font-size:0.75rem; padding:0.3rem 0.875rem;">
                            {{ trans_api($ws, 'status_balance') }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- TWI Analysis --}}
            @if(isset($summary['twi_analysis']) && is_array($summary['twi_analysis']) && ($summary['twi_analysis']['status'] ?? '') !== 'error' && ($summary['twi_analysis']['status'] ?? '') !== 'not_available')
            <div class="info-card">
                <button class="section-toggle-btn open" onclick="toggleSection(this)">
                    <div style="display:flex;align-items:center;gap:0.625rem;">
                        <div class="card-icon" style="background:#e0f2fe;color:#0e7490;width:1.75rem;height:1.75rem;font-size:0.75rem;">
                            <i class="fas fa-water"></i>
                        </div>
                        <span>{{ __('messages.twi_analysis_title') }}</span>
                    </div>
                    <i class="fas fa-chevron-down chev"></i>
                </button>
                <div class="section-body open">
                    @php
                        $riskLevel = $summary['twi_analysis']['risk_level'] ?? '';
                        $riskStyle = match($riskLevel) {
                            'SANGAT TINGGI','VERY HIGH' => 'background:#fef2f2;color:#991b1b;border-color:#fecaca;',
                            'TINGGI','HIGH'             => 'background:#fff7ed;color:#9a3412;border-color:#fdba74;',
                            'SEDANG','MEDIUM','MODERATE'=> 'background:#fffbeb;color:#92400e;border-color:#fde68a;',
                            default                     => 'background:#f0fdf4;color:#166534;border-color:#a7f3d0;',
                        };
                    @endphp
                    <div class="kv-grid">
                        <div class="kv-item" style="background:#e0f2fe;">
                            <div class="kv-label">{{ __('messages.twi_wetness_index') }}</div>
                            <div class="kv-value" style="color:#0e7490; font-size:1.5rem;">{{ $summary['twi_analysis']['twi_enhanced'] ?? 'N/A' }}</div>
                            <div style="font-size:0.68rem; color:var(--c-muted); margin-top:0.25rem;">
                                TWI physics: {{ $summary['twi_analysis']['twi_physics'] ?? 'N/A' }}
                            </div>
                        </div>
                        <div class="kv-item" style="{{ $riskStyle }} border:1.5px solid;">
                            <div class="kv-label">{{ __('messages.twi_location_risk_status') }}</div>
                            <div class="kv-value" style="font-size:1rem;">{{ trans_api($riskLevel, 'risk_level') }}</div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- 30-Day Forecast --}}
            @if(isset($summary['prediksi_30_hari']))
            <div class="info-card">
                <button class="section-toggle-btn" onclick="toggleSection(this)">
                    <div style="display:flex;align-items:center;gap:0.625rem;">
                        <div class="card-icon" style="background:#e0f2fe;color:#0369a1;width:1.75rem;height:1.75rem;font-size:0.75rem;">
                            <i class="fas fa-cloud-sun-rain"></i>
                        </div>
                        <span>{{ __('messages.forecast_30_days') }}</span>
                    </div>
                    <i class="fas fa-chevron-down chev"></i>
                </button>
                <div class="section-body">
                    <div class="kv-grid">
                        @if(isset($summary['prediksi_30_hari']['rainfall']))
                        @foreach($summary['prediksi_30_hari']['rainfall'] as $k => $v)
                        <div class="kv-item">
                            <div class="kv-label">{{ __('messages.'.$k) ?? ucwords(str_replace('_',' ',$k)) }}</div>
                            <div class="kv-value">{{ $v }}</div>
                        </div>
                        @endforeach
                        @endif
                    </div>
                </div>
            </div>
            @endif

            {{-- Management Suggestions --}}
            @if(isset($summary['saran_pengelolaan']) && is_array($summary['saran_pengelolaan']))
            <div class="info-card">
                <button class="section-toggle-btn" onclick="toggleSection(this)">
                    <div style="display:flex;align-items:center;gap:0.625rem;">
                        <div class="card-icon" style="background:var(--c-teal-lt);color:var(--c-teal-dk);width:1.75rem;height:1.75rem;font-size:0.75rem;">
                            <i class="fas fa-tasks"></i>
                        </div>
                        <span>{{ __('messages.management_suggestions') }}</span>
                    </div>
                    <i class="fas fa-chevron-down chev"></i>
                </button>
                <div class="section-body">
                    <div class="space-y-2">
                        @foreach($summary['saran_pengelolaan'] as $i => $saran)
                        @php $isPriority = str_contains($saran,'🔴') || str_contains($saran,'⚠️'); @endphp
                        <div class="alert-strip {{ $isPriority ? 'warning' : 'info' }}">
                            <span style="font-weight:800; margin-right:0.25rem;">{{ $i + 1 }}.</span>
                            {!! nl2br(e($saran)) !!}
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Improvement suggestions --}}
            @if(isset($summary['saran_perbaikan_kondisi']) && is_array($summary['saran_perbaikan_kondisi']))
            <div class="info-card">
                <button class="section-toggle-btn" onclick="toggleSection(this)">
                    <div style="display:flex;align-items:center;gap:0.625rem;">
                        <div class="card-icon" style="background:#fef2f2;color:#991b1b;width:1.75rem;height:1.75rem;font-size:0.75rem;">
                            <i class="fas fa-tools"></i>
                        </div>
                        <span>{{ __('messages.improvement_suggestions') }}</span>
                    </div>
                    <i class="fas fa-chevron-down chev"></i>
                </button>
                <div class="section-body">
                    @foreach($summary['saran_perbaikan_kondisi'] as $pb)
                    @php
                        $pr = $pb['prioritas'] ?? 'NORMAL';
                        $prStyle = $pr === 'TINGGI' ? 'priority-high' : ($pr === 'SEDANG' ? 'priority-medium' : 'priority-low');
                    @endphp
                    <div class="rec-item">
                        <div class="rec-head">
                            <span style="font-size:0.8rem; font-weight:700; color:var(--c-slate);">{{ trans_api($pb['kategori'] ?? '', 'category') }}</span>
                            <span class="badge {{ $prStyle }}" style="border:1px solid currentColor;">{{ trans_api($pr, 'priority') }}</span>
                        </div>
                        <div class="rec-body">
                            <p class="mb-1.5"><strong>{{ __('messages.problem') }}:</strong> {{ $pb['masalah'] ?? 'N/A' }}</p>
                            @if(isset($pb['solusi']))
                            <div class="mb-2">
                                <strong>{{ __('messages.solution') }}:</strong>
                                <ul style="list-style:disc; padding-left:1.25rem; margin-top:0.25rem;">
                                    @foreach($pb['solusi'] as $s)<li>{{ $s }}</li>@endforeach
                                </ul>
                            </div>
                            @endif
                            <div style="display:flex; gap:1rem; font-size:0.75rem; color:var(--c-muted); border-top:1px solid var(--c-border); padding-top:0.5rem; margin-top:0.5rem;">
                                <span><strong>{{ __('messages.cost') }}:</strong> {{ $pb['estimasi_biaya'] ?? 'N/A' }}</span>
                                <span><strong>{{ __('messages.timeline') }}:</strong> {{ $pb['timeline'] ?? 'N/A' }}</span>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

            @endif {{-- /if $summary --}}

            {{-- ── River Map ── --}}
            @php
                $riverMapHtml     = $job->files->firstWhere('filename', 'RIVANA_Peta_Aliran_Sungai.html');
                $riverMapPng      = $job->files->firstWhere('filename', 'RIVANA_Peta_Aliran_Sungai.png');
                $riverMapMetadata = $job->files->firstWhere('filename', 'RIVANA_Metadata_Peta.json');
            @endphp

            @if($riverMapHtml || $riverMapPng)
            <div class="info-card">
                <div class="card-head" style="border-bottom-color:#bae6fd; background:linear-gradient(90deg,#f0fdfa,transparent);">
                    <div class="card-icon" style="background:var(--c-teal); color:#fff;">
                        <i class="fas fa-water"></i>
                    </div>
                    <div>
                        <span class="card-title">{{ __('messages.interactive_river_map') }}</span>
                        <div style="font-size:0.7rem; color:var(--c-muted); margin-top:0.125rem;">HydroSHEDS · JRC GSW · SRTM · OSM</div>
                    </div>
                    <div style="margin-left:auto; display:flex; gap:0.375rem;">
                        @if($riverMapHtml)
                        <a href="{{ route('hidrologi.file.download', $riverMapHtml->id) }}" target="_blank"
                           class="file-btn view-html">
                            <i class="fas fa-external-link-alt" style="font-size:0.65rem;"></i>
                            <span class="hidden sm:inline">Fullscreen</span>
                        </a>
                        @endif
                    </div>
                </div>
                <div style="padding:1rem 1.25rem;">
                    {{-- Map iframe --}}
                    @if($riverMapHtml)
                    <div class="map-container" style="height:480px; border:1.5px solid var(--c-border);">
                        <div id="mapLoadingOverlay" style="position:absolute;inset:0;background:var(--c-surface);display:flex;align-items:center;justify-content:center;z-index:10;">
                            <div style="text-align:center;">
                                <i class="fas fa-water text-3xl processing-pulse" style="color:var(--c-teal);"></i>
                                <p style="font-size:0.8rem; color:var(--c-muted); margin-top:0.5rem; font-weight:600;">{{ __('messages.map_loading') }}...</p>
                            </div>
                        </div>
                        <iframe id="riverMapFrame"
                            src="{{ route('hidrologi.file.preview', $riverMapHtml->id) }}"
                            style="width:100%;height:100%;border:none;opacity:0;transition:opacity 0.5s;"
                            sandbox="allow-scripts allow-same-origin allow-popups"
                            allow="fullscreen"
                            onload="document.getElementById('mapLoadingOverlay').style.display='none'; this.style.opacity='1';">
                        </iframe>
                    </div>
                    @elseif($riverMapPng)
                    <img src="{{ route('hidrologi.file.preview', $riverMapPng->id) }}"
                         alt="{{ __('messages.interactive_river_map') }}"
                         class="w-full h-auto" style="border-radius:0.75rem; max-height:480px; object-fit:contain;"
                         onerror="this.style.display='none'">
                    @endif

                    {{-- Map stats --}}
                    <div class="meta-grid mt-3">
                        <div class="meta-item">
                            <div class="meta-label"><i class="fas fa-map-marker-alt mr-1 text-red-400"></i>{{ __('messages.map_analysis_location') }}</div>
                            <div class="meta-value">{{ $job->location_name ?? 'N/A' }}</div>
                        </div>
                        <div class="meta-item">
                            <div class="meta-label"><i class="fas fa-crosshairs mr-1"></i>{{ __('messages.coordinates') }}</div>
                            <div class="meta-value mono">{{ number_format($job->latitude, 4) }}, {{ number_format($job->longitude, 4) }}</div>
                        </div>
                    </div>

                    {{-- Metadata accordion --}}
                    @if($riverMapMetadata)
                    <div style="margin-top:0.75rem; border:1.5px solid var(--c-border); border-radius:0.75rem; overflow:hidden;">
                        <button onclick="toggleSection(this)" class="section-toggle-btn" style="border-radius:0;">
                            <div style="display:flex;align-items:center;gap:0.5rem; font-size:0.8rem;">
                                <i class="fas fa-info-circle" style="color:var(--c-teal);"></i>
                                {{ __('messages.map_metadata_detail') }}
                            </div>
                            <i class="fas fa-chevron-down chev"></i>
                        </button>
                        <div class="section-body" style="padding:0 1rem 1rem;">
                            <div class="kv-grid">
                                <div class="kv-item">
                                    <div class="kv-label">{{ __('messages.map_data_sources') }}</div>
                                    <div style="font-size:0.75rem; color:var(--c-muted); line-height:1.6; margin-top:0.25rem;">
                                        HydroSHEDS · JRC GSW · SRTM DEM · OpenStreetMap
                                    </div>
                                </div>
                                <div class="kv-item">
                                    <div class="kv-label">{{ __('messages.map_buffer_area') }}</div>
                                    <div class="kv-value">10 km radius</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- ── Generated Files ── --}}
            @if($job->files->count() > 0)
            <div class="info-card">
                <div class="card-head">
                    <div class="card-icon" style="background:#eff6ff; color:#3b82f6;">
                        <i class="fas fa-file-download"></i>
                    </div>
                    <div>
                        <span class="card-title">{{ __('messages.generated_files') }}</span>
                        <div style="font-size:0.7rem; color:var(--c-muted); margin-top:0.1rem;">{{ $job->files->count() }} file</div>
                    </div>
                    {{-- Filter pills --}}
                    <div style="margin-left:auto; display:flex; gap:0.375rem; flex-wrap:wrap;">
                        <button onclick="filterFiles('all')"  class="filter-pill active" data-type="all">All</button>
                        <button onclick="filterFiles('png')"  class="filter-pill" data-type="png"><i class="fas fa-image mr-1" style="font-size:0.65rem;"></i>PNG</button>
                        <button onclick="filterFiles('csv')"  class="filter-pill" data-type="csv"><i class="fas fa-table mr-1" style="font-size:0.65rem;"></i>CSV</button>
                        <button onclick="filterFiles('json')" class="filter-pill" data-type="json"><i class="fas fa-code mr-1" style="font-size:0.65rem;"></i>JSON</button>
                    </div>
                </div>
                <div class="card-body">
                    <div style="display:flex; flex-direction:column; gap:0.5rem;" id="file-list">
                        @foreach($job->files->sortBy('display_order') as $file)
                        @php
                            $ft = strtolower($file->file_type);
                            $fileIconStyle = match($ft) {
                                'png'  => 'background:#eff6ff;color:#3b82f6;',
                                'csv'  => 'background:#f0fdf4;color:#16a34a;',
                                'json' => 'background:#fff7ed;color:#ea580c;',
                                'html' => 'background:var(--c-teal-lt);color:var(--c-teal-dk);',
                                default=> 'background:var(--c-surface);color:var(--c-muted);',
                            };
                            $fileIconClass = match($ft) {
                                'png'  => 'fa-image',
                                'csv'  => 'fa-table',
                                'json' => 'fa-code',
                                'html' => 'fa-map',
                                default=> 'fa-file',
                            };
                        @endphp
                        <div class="file-item" data-file-type="{{ $ft }}">
                            <div class="file-icon" style="{{ $fileIconStyle }}">
                                <i class="fas {{ $fileIconClass }}"></i>
                            </div>
                            <div style="flex:1; min-width:0;">
                                <div style="font-size:0.8rem; font-weight:700; color:var(--c-slate); white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                    {{ $file->display_name ?? $file->filename }}
                                </div>
                                @if($file->description)
                                <div style="font-size:0.72rem; color:var(--c-muted); margin-top:0.125rem;">{{ $file->description }}</div>
                                @endif
                                <div style="font-size:0.68rem; color:var(--c-muted); margin-top:0.25rem; display:flex; gap:0.75rem;">
                                    <span>{{ strtoupper($ft) }}</span>
                                    <span>{{ number_format($file->file_size_mb, 2) }} MB</span>
                                    @if($file->created_at)
                                    <span class="hidden sm:inline">{{ $file->created_at->format('d M Y, H:i') }}</span>
                                    @endif
                                </div>

                                {{-- Preview container --}}
                                <div id="preview-{{ $file->id }}" class="preview-container">
                                    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:0.5rem;">
                                        <span style="font-size:0.75rem; font-weight:700; color:var(--c-slate);">Preview</span>
                                        <button onclick="closePreview({{ $file->id }})" style="background:none;border:none;color:var(--c-muted);cursor:pointer;font-size:0.8rem;">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div id="preview-content-{{ $file->id }}"></div>
                                </div>
                            </div>
                            <div class="file-actions">
                                @if($ft === 'png')
                                    <button onclick="viewImage({{ $file->id }}, '{{ addslashes($file->display_name ?? $file->filename) }}')" class="file-btn view-png">
                                        <i class="fas fa-eye" style="font-size:0.65rem;"></i><span class="hidden sm:inline">View</span>
                                    </button>
                                @elseif($ft === 'csv')
                                    <button onclick="viewCSV({{ $file->id }}, '{{ addslashes($file->display_name ?? $file->filename) }}')" class="file-btn view-csv">
                                        <i class="fas fa-eye" style="font-size:0.65rem;"></i><span class="hidden sm:inline">View</span>
                                    </button>
                                @elseif($ft === 'json')
                                    <button onclick="viewJSON({{ $file->id }}, '{{ addslashes($file->display_name ?? $file->filename) }}')" class="file-btn view-json">
                                        <i class="fas fa-eye" style="font-size:0.65rem;"></i><span class="hidden sm:inline">View</span>
                                    </button>
                                @elseif($ft === 'html')
                                    <a href="{{ route('hidrologi.file.preview', $file->id) }}" target="_blank" class="file-btn view-html">
                                        <i class="fas fa-external-link-alt" style="font-size:0.65rem;"></i><span class="hidden sm:inline">Open</span>
                                    </a>
                                @endif
                                <a href="{{ route('hidrologi.file.download', $file->id) }}" class="file-btn download">
                                    <i class="fas fa-download" style="font-size:0.65rem;"></i><span class="hidden sm:inline">Download</span>
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

        </div>{{-- /left col --}}

        {{-- ── RIGHT sidebar ── --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- Timeline --}}
            <div class="guide-card">
                <div class="guide-head">
                    <div class="guide-icon"><i class="fas fa-clock"></i></div>
                    <span class="guide-title">Timeline</span>
                </div>
                <div class="guide-body">
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background:#3b82f6;"></div>
                        <div>
                            <div style="font-size:0.8rem; font-weight:700; color:var(--c-slate);">{{ __('messages.created_label') }}</div>
                            <div style="font-size:0.72rem; color:var(--c-muted); margin-top:0.1rem;">{{ $job->created_at->format('d M Y, H:i') }}</div>
                        </div>
                    </div>
                    @if($job->submitted_at)
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background:#0e7490;"></div>
                        <div>
                            <div style="font-size:0.8rem; font-weight:700; color:var(--c-slate);">{{ __('messages.submitted_label') }}</div>
                            <div style="font-size:0.72rem; color:var(--c-muted); margin-top:0.1rem;">{{ $job->submitted_at->format('d M Y, H:i') }}</div>
                        </div>
                    </div>
                    @endif
                    @if($job->started_at)
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background:#f59e0b;"></div>
                        <div>
                            <div style="font-size:0.8rem; font-weight:700; color:var(--c-slate);">{{ __('messages.started_processing') }}</div>
                            <div style="font-size:0.72rem; color:var(--c-muted); margin-top:0.1rem;">{{ $job->started_at->format('d M Y, H:i') }}</div>
                        </div>
                    </div>
                    @endif
                    @if($job->completed_at)
                    <div class="timeline-item">
                        <div class="timeline-dot" style="background:#10b981;"></div>
                        <div>
                            <div style="font-size:0.8rem; font-weight:700; color:var(--c-slate);">{{ __('messages.finished_label') }}</div>
                            <div style="font-size:0.72rem; color:var(--c-muted); margin-top:0.1rem;">{{ $job->completed_at->format('d M Y, H:i') }}</div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- File stats --}}
            <div class="guide-card">
                <div class="guide-head" style="background:linear-gradient(135deg,#f5f3ff,var(--c-sky)); border-bottom-color:#ddd6fe;">
                    <div class="guide-icon" style="background:#7c3aed;"><i class="fas fa-chart-bar"></i></div>
                    <span class="guide-title">{{ __('messages.statistics') }}</span>
                </div>
                <div class="guide-body">
                    <div class="stat-row">
                        <span class="s-label">
                            <span style="width:1.5rem;height:1.5rem;background:#eff6ff;border-radius:0.375rem;display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-image" style="font-size:0.65rem;color:#3b82f6;"></i>
                            </span>
                            PNG
                        </span>
                        <span class="s-val">{{ $job->png_count }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="s-label">
                            <span style="width:1.5rem;height:1.5rem;background:#f0fdf4;border-radius:0.375rem;display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-table" style="font-size:0.65rem;color:#16a34a;"></i>
                            </span>
                            CSV
                        </span>
                        <span class="s-val">{{ $job->csv_count }}</span>
                    </div>
                    <div class="stat-row">
                        <span class="s-label">
                            <span style="width:1.5rem;height:1.5rem;background:#fff7ed;border-radius:0.375rem;display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-code" style="font-size:0.65rem;color:#ea580c;"></i>
                            </span>
                            JSON
                        </span>
                        <span class="s-val">{{ $job->json_count }}</span>
                    </div>
                    <div class="stat-row" style="border-bottom:none; background:var(--c-teal-lt); margin:-0.875rem -1.25rem -0.875rem; padding:0.875rem 1.25rem; border-radius:0 0 var(--radius-card) var(--radius-card);">
                        <span class="s-label" style="color:var(--c-teal-dk); font-weight:800;">
                            <span style="width:1.5rem;height:1.5rem;background:var(--c-teal);border-radius:0.375rem;display:flex;align-items:center;justify-content:center;">
                                <i class="fas fa-folder" style="font-size:0.65rem;color:#fff;"></i>
                            </span>
                            {{ __('messages.total_files') }}
                        </span>
                        <span style="font-size:1.25rem; font-weight:800; color:var(--c-teal-dk);">{{ $job->total_files }}</span>
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="guide-card">
                <div class="guide-head" style="background:linear-gradient(135deg,#f8fafc,var(--c-surface));">
                    <div class="guide-icon" style="background:var(--c-slate);"><i class="fas fa-cog"></i></div>
                    <span class="guide-title">{{ __('messages.actions') ?? 'Actions' }}</span>
                </div>
                <div class="guide-body" style="display:flex;flex-direction:column;gap:0.5rem;">
                    <a href="{{ route('hidrologi.index') }}" class="btn-back" style="width:100%;justify-content:center;">
                        <i class="fas fa-list text-xs"></i>
                        {{ __('messages.back_to_list') ?? 'Back to list' }}
                    </a>
                    @if(in_array($job->status, ['pending', 'submitted', 'processing']))
                    <button onclick="cancelJob({{ $job->id }})" class="btn-warn" style="width:100%;justify-content:center;">
                        <i class="fas fa-stop-circle text-xs"></i>
                        {{ __('messages.cancel_job') }}
                    </button>
                    @endif
                    <button onclick="deleteJob({{ $job->id }})" class="btn-danger" style="width:100%;justify-content:center;">
                        <i class="fas fa-trash text-xs"></i>
                        {{ __('messages.delete') }}
                    </button>
                </div>
            </div>

        </div>{{-- /sidebar --}}
    </div>{{-- /grid --}}
</div>
@endsection

@push('scripts')
<script>
// ── Navbar height auto-detect (same as create page) ──
(function () {
    const navbar = document.querySelector('header') || document.querySelector('nav') || document.querySelector('[class*="navbar"]');
    if (navbar) {
        document.documentElement.style.setProperty('--navbar-height', navbar.getBoundingClientRect().height + 'px');
        window.addEventListener('resize', () => {
            document.documentElement.style.setProperty('--navbar-height', navbar.getBoundingClientRect().height + 'px');
        });
    }
})();

// ── Collapsible sections ──
function toggleSection(btn) {
    btn.classList.toggle('open');
    const body = btn.nextElementSibling;
    if (body && body.classList.contains('section-body')) {
        body.classList.toggle('open');
    }
}

// ── Auto-refresh for in-progress jobs ──
@if(in_array($job->status, ['pending', 'submitted', 'processing']))
let refreshInterval = setInterval(function () {
    fetch('/hidrologi/status/{{ $job->id }}')
        .then(r => r.json())
        .then(data => {
            if (!data.success) return;
            const job = data.job;

            // Update progress bar
            const bar  = document.getElementById('progress-bar');
            const pct  = document.getElementById('progress-percent');
            if (bar && pct) { bar.style.width = job.progress + '%'; pct.textContent = job.progress + '%'; }

            // Reload when done
            if (['completed','completed_with_warning','failed','cancelled'].includes(job.status)) {
                clearInterval(refreshInterval);
                let icon = job.status === 'completed' ? 'success' : job.status === 'completed_with_warning' ? 'warning' : job.status === 'cancelled' ? 'info' : 'error';
                Swal.fire({ icon, title: job.status, text: 'Reloading...', showConfirmButton: false, timer: 2000 })
                    .then(() => location.reload());
            }
        })
        .catch(() => {});
}, 10000);

window.addEventListener('beforeunload', () => clearInterval(refreshInterval));
@endif

// ── Cancel / Delete ──
function cancelJob(jobId) {
    Swal.fire({
        title: 'Batalkan Pekerjaan?',
        text: 'Apakah Anda yakin ingin membatalkan pekerjaan ini?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#0d9488',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Ya, Batalkan!',
        cancelButtonText: 'Batal'
    }).then(r => {
        if (!r.isConfirmed) return;
        Swal.fire({ title: 'Membatalkan...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        fetch(`/hidrologi/cancel/${jobId}`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' }
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: d.message, showConfirmButton: false, timer: 1500 })
                    .then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal!', text: d.message });
            }
        });
    });
}

function deleteJob(jobId) {
    Swal.fire({
        title: 'Hapus Pekerjaan?',
        html: "Tindakan ini <strong>tidak dapat dibatalkan</strong>.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then(r => {
        if (!r.isConfirmed) return;
        Swal.fire({ title: 'Menghapus...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        fetch(`/hidrologi/delete/${jobId}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json', 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                Swal.fire({ icon: 'success', title: 'Berhasil!', text: d.message, showConfirmButton: false, timer: 1500 })
                    .then(() => window.location.href = '/hidrologi');
            } else {
                Swal.fire({ icon: 'error', title: 'Gagal!', text: d.message });
            }
        });
    });
}

// ── File filter ──
function filterFiles(type) {
    document.querySelectorAll('.filter-pill').forEach(p => p.classList.remove('active'));
    document.querySelector(`[data-type="${type}"]`)?.classList.add('active');
    document.querySelectorAll('.file-item').forEach(item => {
        item.style.display = (type === 'all' || item.dataset.fileType === type) ? '' : 'none';
    });
}

// ── File previews ──
function viewImage(fileId, name) {
    const c = document.getElementById(`preview-content-${fileId}`);
    const p = document.getElementById(`preview-${fileId}`);
    c.innerHTML = `<img src="/hidrologi/file/${fileId}/preview" alt="${name}" style="max-width:100%; border-radius:0.5rem;">`;
    p.style.display = 'block';
}

function viewCSV(fileId, name) {
    const c = document.getElementById(`preview-content-${fileId}`);
    const p = document.getElementById(`preview-${fileId}`);
    c.innerHTML = '<div style="color:var(--c-muted); font-size:0.8rem; padding:0.5rem;">Loading...</div>';
    p.style.display = 'block';

    fetch(`/hidrologi/file/${fileId}/preview`)
        .then(r => r.text())
        .then(text => {
            const rows = text.split('\n').filter(r => r.trim()).slice(0, 100);
            if (!rows.length) { c.innerHTML = '<div style="color:var(--c-muted);font-size:0.8rem;">Empty file</div>'; return; }
            const headers = rows[0].split(',');
            let html = `<div style="overflow-x:auto;max-height:320px;overflow-y:auto;border:1.5px solid var(--c-border);border-radius:0.5rem;"><table>
                <thead><tr>${headers.map(h=>`<th>${h.trim()}</th>`).join('')}</tr></thead>
                <tbody>${rows.slice(1).map(r=>`<tr>${r.split(',').map(v=>`<td>${v.trim()}</td>`).join('')}</tr>`).join('')}</tbody>
            </table></div>`;
            if (rows.length >= 100) html += `<div style="font-size:0.7rem;color:var(--c-muted);margin-top:0.375rem;">Showing first 100 rows</div>`;
            c.innerHTML = html;
        })
        .catch(() => { c.innerHTML = '<div style="color:#991b1b;font-size:0.8rem;">Failed to load CSV</div>'; });
}

function viewJSON(fileId, name) {
    const c = document.getElementById(`preview-content-${fileId}`);
    const p = document.getElementById(`preview-${fileId}`);
    c.innerHTML = '<div style="color:var(--c-muted);font-size:0.8rem;">Loading...</div>';
    p.style.display = 'block';

    fetch(`/hidrologi/file/${fileId}/preview`)
        .then(r => r.json())
        .then(json => {
            const text = JSON.stringify(json, null, 2);
            c.setAttribute('data-json-text', text);
            c.innerHTML = `<div style="display:flex;justify-content:flex-end;margin-bottom:0.375rem;">
                <button onclick="copyJSONData(${fileId})" style="font-size:0.72rem;padding:0.25rem 0.625rem;border:1.5px solid var(--c-border);border-radius:0.375rem;background:var(--c-white);color:var(--c-muted);cursor:pointer;">
                    <i class="fas fa-copy mr-1"></i>Copy
                </button>
            </div>
            <div style="max-height:320px;overflow-y:auto;background:#1e293b;border-radius:0.625rem;padding:0.875rem;">
                <pre style="font-size:0.75rem;color:#a3e635;white-space:pre-wrap;margin:0;">${text.substring(0, 10000)}${text.length > 10000 ? '\n... (truncated)' : ''}</pre>
            </div>`;
        })
        .catch(() => { c.innerHTML = '<div style="color:#991b1b;font-size:0.8rem;">Failed to load JSON</div>'; });
}

function closePreview(fileId) {
    const p = document.getElementById(`preview-${fileId}`);
    if (p) p.style.display = 'none';
}

function copyJSONData(fileId) {
    const c = document.getElementById(`preview-content-${fileId}`);
    const text = c.getAttribute('data-json-text');
    if (text) navigator.clipboard.writeText(text)
        .then(() => Swal.fire({ icon: 'success', title: 'Copied!', timer: 1200, showConfirmButton: false, toast: true, position: 'top-end' }));
}

function copyCSVData(fileId) {
    const table = document.querySelector(`#preview-content-${fileId} table`);
    if (!table) return;
    const rows = [...table.querySelectorAll('tr')].map(r => [...r.querySelectorAll('th,td')].map(c => c.textContent).join(','));
    navigator.clipboard.writeText(rows.join('\n'))
        .then(() => Swal.fire({ icon: 'success', title: 'Copied!', timer: 1200, showConfirmButton: false, toast: true, position: 'top-end' }));
}

function toggleRiverMetadata() {
    const content = document.getElementById('riverMetadataContent');
    const chevron = document.getElementById('metadataChevron');
    if (content && chevron) {
        content.classList.toggle('hidden');
        chevron.style.transform = content.classList.contains('hidden') ? 'rotate(0deg)' : 'rotate(180deg)';
    }
}
</script>
@endpush