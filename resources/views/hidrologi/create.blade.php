@extends('layouts.app')

@section('title', 'Buat Analisis Baru')

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder@2.4.0/dist/Control.Geocoder.css" crossorigin=""/>
<style>
    /* ── Design Tokens ── */
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
        --shadow-lift: 0 4px 24px rgba(0,0,0,.12);
    }

    /* ── Base ── */
    body { background: var(--c-surface); }

    /* ── Progress Rail (top sticky) ── */
    .progress-rail {
        display: flex;
        align-items: center;
        gap: 0;
        background: var(--c-white);
        border-bottom: 1px solid var(--c-border);
        padding: 0 1.5rem;
        position: sticky;
        top: var(--navbar-height, 64px); /* set via JS to match actual navbar */
        z-index: 40;                     /* below navbar (usually z-50) */
        box-shadow: 0 2px 8px rgba(0,0,0,.07);
        overflow-x: auto;
        scrollbar-width: none;
    }
    .progress-rail::-webkit-scrollbar { display: none; }

    .rail-step {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.875rem 1.25rem;
        white-space: nowrap;
        border-bottom: 3px solid transparent;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .rail-step.active  { border-bottom-color: var(--c-teal); }
    .rail-step.done    { border-bottom-color: #10b981; }

    .rail-num {
        width: 1.625rem; height: 1.625rem;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.7rem; font-weight: 800;
        transition: all 0.2s;
        flex-shrink: 0;
    }
    .rail-num.active   { background: var(--c-teal); color: #fff; }
    .rail-num.done     { background: #10b981; color: #fff; }
    .rail-num.inactive { background: var(--c-border); color: var(--c-muted); }

    .rail-label { font-size: 0.8rem; font-weight: 600; }
    .rail-step.active   .rail-label { color: var(--c-teal-dk); }
    .rail-step.done     .rail-label { color: #059669; }
    .rail-step.inactive .rail-label { color: var(--c-muted); }

    .rail-sep { width: 2rem; height: 1px; background: var(--c-border); flex-shrink: 0; }

    /* ── Map ── */
    #map {
        height: 440px;
        width: 100%;
        border-radius: 0.75rem;
    }
    .map-wrapper {
        position: relative;
        border-radius: 0.875rem;
        overflow: hidden;
        border: 1.5px solid var(--c-border);
        /* Isolate Leaflet's z-index stack so tiles can't bleed outside */
        isolation: isolate;
        transform: translateZ(0);
    }

    /* Keep all Leaflet layers inside the wrapper's stacking context */
    .map-wrapper .leaflet-pane,
    .map-wrapper .leaflet-control-container {
        z-index: auto !important;
    }
    .map-overlay-hint {
        position: absolute;
        bottom: 0.75rem;
        left: 50%;
        transform: translateX(-50%);
        z-index: 400;
        background: rgba(15, 118, 110, 0.92);
        backdrop-filter: blur(8px);
        color: #fff;
        padding: 0.5rem 1.1rem;
        border-radius: 2rem;
        font-size: 0.78rem;
        font-weight: 600;
        pointer-events: none;
        display: flex;
        align-items: center;
        gap: 0.4rem;
        white-space: nowrap;
        transition: opacity 0.4s;
    }
    .map-overlay-hint.hidden { opacity: 0; }

    /* ── Coord chips ── */
    .coord-chip {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        background: var(--c-surface);
        border: 1.5px solid var(--c-border);
        border-radius: 0.625rem;
        padding: 0.5rem 0.875rem;
        font-size: 0.8rem;
    }
    .coord-chip .label { color: var(--c-muted); font-weight: 600; font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.04em; }
    .coord-chip .value { font-family: 'JetBrains Mono', 'Fira Code', ui-monospace, monospace; color: var(--c-slate); font-weight: 700; }
    .coord-chip .dot   { width: 0.5rem; height: 0.5rem; border-radius: 50%; }

    /* ── Step cards ── */
    .step-card {
        background: var(--c-white);
        border-radius: var(--radius-card);
        border: 1.5px solid var(--c-border);
        box-shadow: var(--shadow-card);
        transition: box-shadow 0.2s;
    }
    .step-card.is-active {
        border-color: var(--c-teal);
        box-shadow: 0 0 0 3px rgba(13,148,136,.1), var(--shadow-card);
    }
    .step-card.is-done {
        border-color: #a7f3d0;
    }

    .step-header {
        display: flex;
        align-items: center;
        gap: 0.875rem;
        padding: 1.25rem 1.5rem;
        border-bottom: 1.5px solid var(--c-border);
    }
    .step-card.is-active .step-header { border-bottom-color: #ccfbf1; }
    .step-card.is-done   .step-header { border-bottom-color: #d1fae5; }

    .step-badge {
        width: 2.25rem; height: 2.25rem;
        border-radius: 0.625rem;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 0.875rem;
        flex-shrink: 0;
    }
    .step-badge.active   { background: var(--c-teal); color: #fff; }
    .step-badge.done     { background: #10b981; color: #fff; }
    .step-badge.inactive { background: var(--c-border); color: var(--c-muted); }

    .step-card.is-active .step-header { background: linear-gradient(90deg, #f0fdfa, transparent); }
    .step-card.is-done   .step-header { background: linear-gradient(90deg, #f0fdf4, transparent); }

    /* ── DAS Level buttons ── */
    .das-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.625rem;
    }
    @media (max-width: 480px) { .das-grid { grid-template-columns: repeat(2, 1fr); } }

    .das-btn {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        gap: 0.2rem;
        padding: 0.875rem;
        border: 1.5px solid var(--c-border);
        border-radius: 0.875rem;
        background: var(--c-white);
        cursor: pointer;
        transition: all 0.15s ease;
        text-align: left;
    }
    .das-btn:hover:not(:disabled) {
        border-color: var(--c-teal);
        background: #f0fdfa;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(13,148,136,.15);
    }
    .das-btn.selected {
        border-color: var(--c-teal);
        background: linear-gradient(135deg, #f0fdfa, #ccfbf1);
        box-shadow: 0 0 0 3px rgba(13,148,136,.15);
    }
    .das-btn:disabled { opacity: 0.45; cursor: not-allowed; }

    .das-lvl-tag {
        font-size: 0.65rem; font-weight: 800;
        letter-spacing: 0.05em;
        padding: 0.15rem 0.5rem;
        border-radius: 9999px;
        background: var(--c-teal-lt);
        color: var(--c-teal-dk);
    }
    .das-btn.selected .das-lvl-tag { background: var(--c-teal); color: #fff; }

    .das-name { font-size: 0.75rem; font-weight: 700; color: var(--c-slate); line-height: 1.2; }
    .das-range { font-size: 0.68rem; color: var(--c-muted); }
    .das-btn.selected .das-name  { color: var(--c-teal-dk); }
    .das-btn.selected .das-range { color: var(--c-teal); }

    /* ── DAS info badge ── */
    .das-found-strip {
        display: flex; align-items: center; gap: 0.75rem;
        background: linear-gradient(90deg, #f0fdf4, #f0fdfa);
        border: 1.5px solid #a7f3d0;
        border-radius: 0.75rem;
        padding: 0.875rem 1.125rem;
    }
    .das-found-icon {
        width: 2.25rem; height: 2.25rem;
        border-radius: 0.625rem;
        background: #10b981;
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
    }

    /* ── Form inputs ── */
    .field-label {
        display: flex; align-items: center; gap: 0.375rem;
        font-size: 0.8rem; font-weight: 700; color: var(--c-slate);
        margin-bottom: 0.375rem;
    }
    .field-label i { color: var(--c-muted); font-size: 0.75rem; }

    .field-input {
        width: 100%;
        padding: 0.625rem 0.875rem;
        border: 1.5px solid var(--c-border);
        border-radius: 0.625rem;
        font-size: 0.875rem;
        color: var(--c-slate);
        background: var(--c-white);
        transition: border-color 0.15s, box-shadow 0.15s;
        font-family: inherit;
    }
    .field-input:focus {
        outline: none;
        border-color: var(--c-teal);
        box-shadow: 0 0 0 3px rgba(13,148,136,.12);
    }
    .field-input:read-only { background: var(--c-surface); color: var(--c-muted); cursor: default; }

    .field-input[type="date"] { appearance: none; }

    /* ── Alert/note strips ── */
    .note-strip {
        display: flex; align-items: flex-start; gap: 0.625rem;
        padding: 0.75rem 1rem;
        border-radius: 0.625rem;
        font-size: 0.8rem;
        font-weight: 500;
    }
    .note-strip.info    { background: var(--c-sky); color: #0369a1; }
    .note-strip.warning { background: #fffbeb; color: #92400e; border: 1px solid #fde68a; }
    .note-strip.error   { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
    .note-strip.success { background: #f0fdf4; color: #166534; border: 1px solid #a7f3d0; }

    /* ── Submit button ── */
    .btn-submit {
        display: inline-flex; align-items: center; justify-content: center; gap: 0.625rem;
        padding: 0.875rem 2.25rem;
        background: var(--c-teal);
        color: #fff;
        font-weight: 800;
        font-size: 0.9rem;
        border-radius: 0.75rem;
        border: none;
        cursor: pointer;
        transition: all 0.15s;
        box-shadow: 0 4px 14px rgba(13,148,136,.3);
    }
    .btn-submit:hover:not(:disabled) {
        background: var(--c-teal-dk);
        box-shadow: 0 6px 20px rgba(13,148,136,.4);
        transform: translateY(-1px);
    }
    .btn-submit:disabled {
        background: #cbd5e1;
        box-shadow: none;
        cursor: not-allowed;
        transform: none;
    }
    .btn-back {
        display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
        padding: 0.875rem 1.5rem;
        background: transparent;
        color: var(--c-muted);
        font-weight: 600;
        font-size: 0.875rem;
        border-radius: 0.75rem;
        border: 1.5px solid var(--c-border);
        transition: all 0.15s;
    }
    .btn-back:hover { background: var(--c-surface); color: var(--c-slate); border-color: #94a3b8; }

    /* ── Sidebar guide ── */
    .guide-card {
        background: var(--c-white);
        border-radius: var(--radius-card);
        border: 1.5px solid var(--c-border);
        box-shadow: var(--shadow-card);
        overflow: hidden;
    }
    .guide-head {
        display: flex; align-items: center; gap: 0.75rem;
        padding: 1rem 1.25rem;
        background: linear-gradient(135deg, #f0fdfa, var(--c-sky));
        border-bottom: 1.5px solid #bae6fd;
    }
    .guide-icon {
        width: 2rem; height: 2rem;
        border-radius: 0.5rem;
        background: var(--c-teal);
        color: #fff;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.8rem;
        flex-shrink: 0;
    }
    .guide-title { font-size: 0.85rem; font-weight: 800; color: var(--c-slate); }
    .guide-body  { padding: 0.875rem 1.25rem; }

    .guide-row {
        display: flex; align-items: flex-start; gap: 0.625rem;
        padding: 0.5rem 0;
        border-bottom: 1px solid var(--c-border);
        font-size: 0.775rem;
    }
    .guide-row:last-child { border-bottom: none; }
    .guide-num {
        width: 1.375rem; height: 1.375rem;
        border-radius: 0.375rem;
        background: var(--c-teal-lt);
        color: var(--c-teal-dk);
        font-size: 0.65rem; font-weight: 800;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        margin-top: 0.05rem;
    }
    .guide-row-text { color: var(--c-muted); line-height: 1.45; }
    .guide-row-text strong { color: var(--c-slate); }

    /* ── Leaflet override ── */
    .leaflet-control-geocoder { border-radius: 0.5rem; box-shadow: var(--shadow-lift); }
    .leaflet-control-geocoder-form input {
        border-radius: 0.5rem; padding: 0.625rem 0.875rem;
        font-size: 0.875rem; border: 1.5px solid var(--c-border);
        font-family: inherit;
    }
    .leaflet-control-geocoder-form input:focus { border-color: var(--c-teal); outline: none; }
</style>
@endpush

@section('content')

{{-- ── Progress Rail ── --}}
<div class="progress-rail" id="progressRail">
    <div class="rail-step active" id="rail-1">
        <div class="rail-num active" id="rail-num-1">1</div>
        <span class="rail-label">Pilih Lokasi</span>
    </div>
    <div class="rail-sep"></div>
    <div class="rail-step inactive" id="rail-2">
        <div class="rail-num inactive" id="rail-num-2">2</div>
        <span class="rail-label">Level DAS</span>
    </div>
    <div class="rail-sep"></div>
    <div class="rail-step inactive" id="rail-3">
        <div class="rail-num inactive" id="rail-num-3">3</div>
        <span class="rail-label">Info Lokasi</span>
    </div>
    <div class="rail-sep"></div>
    <div class="rail-step inactive" id="rail-4">
        <div class="rail-num inactive" id="rail-num-4">4</div>
        <span class="rail-label">Periode Analisis</span>
    </div>
</div>

<div class="container mx-auto px-4 sm:px-5 lg:px-6 py-6 max-w-6xl">

    {{-- ── Page title ── --}}
    <div class="mb-5 flex items-center gap-3">
        <a href="{{ route('hidrologi.index') }}" class="w-8 h-8 flex items-center justify-center rounded-lg bg-white border-1.5 border-gray-200 text-gray-400 hover:text-gray-700 hover:border-gray-300 transition-all" style="border: 1.5px solid #e2e8f0;">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <p class="text-xs text-gray-400 font-medium mb-0.5">
                <a href="{{ route('hidrologi.index') }}" class="hover:text-teal-600 transition-colors">Hidrologi</a>
                <span class="mx-1.5">·</span>
                Buat Analisis Baru
            </p>
            <h1 class="text-xl font-extrabold text-gray-900 leading-tight">Buat Analisis Hidrologi Baru</h1>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- ── FORM COLUMN ── --}}
        <div class="lg:col-span-2 space-y-4">
            <form id="hidrologiForm" action="{{ route('hidrologi.submit') }}" method="POST">
                @csrf

                {{-- Hidden fields --}}
                <input type="hidden" name="longitude"    id="longitude"    value="">
                <input type="hidden" name="latitude"     id="latitude"     value="">
                <input type="hidden" name="das_level"    id="das_level"    value="">
                <input type="hidden" name="das_name"     id="das_name"     value="">
                <input type="hidden" name="das_area_km2" id="das_area_km2" value="">
                <input type="hidden" name="hybas_id"     id="hybas_id"     value="">
                <input type="hidden" name="das_geojson"  id="das_geojson"  value="">

                {{-- ══════════════════════════════════════ --}}
                {{-- STEP 1 — Pilih Lokasi di Peta         --}}
                {{-- ══════════════════════════════════════ --}}
                <div class="step-card is-active" id="step1-card">
                    <div class="step-header">
                        <div class="step-badge active" id="step1-dot">1</div>
                        <div>
                            <h3 class="text-sm font-extrabold text-gray-900">Pilih Titik Lokasi pada Peta</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Klik pada peta atau cari nama lokasi/koordinat untuk menentukan titik outlet analisis</p>
                        </div>
                        {{-- Loading indicator --}}
                        <div id="step2-loading" class="ml-auto hidden flex items-center gap-1.5 text-teal-600 text-xs font-semibold bg-teal-50 px-2.5 py-1 rounded-full">
                            <i class="fas fa-spinner fa-spin text-xs"></i>
                            <span>Mencari batas DAS...</span>
                        </div>
                    </div>

                    <div class="p-4">
                        {{-- Map --}}
                        <div class="map-wrapper mb-3">
                            <div id="map"></div>
                            <div class="map-overlay-hint" id="mapHint">
                                <i class="fas fa-mouse-pointer text-xs"></i>
                                <span>Klik di mana saja pada peta untuk menentukan titik analisis</span>
                            </div>
                        </div>

                        {{-- Coordinate inputs --}}
                        <div class="grid grid-cols-2 gap-3" id="coord-row">
                            <div>
                                <label for="longitude_input" class="field-label">
                                    <i class="fas fa-arrows-alt-h text-teal-500"></i>
                                    Longitude
                                </label>
                                <input type="number" name="longitude_input" id="longitude_input" 
                                    class="field-input"
                                    placeholder="e.g., 110.4053"
                                    step="0.000001"
                                    min="-180" max="180">
                                <p class="text-xs text-gray-400 mt-1">-180 hingga 180</p>
                            </div>
                            <div>
                                <label for="latitude_input" class="field-label">
                                    <i class="fas fa-arrows-alt-v text-sky-500"></i>
                                    Latitude
                                </label>
                                <input type="number" name="latitude_input" id="latitude_input" 
                                    class="field-input"
                                    placeholder="e.g., -7.2525"
                                    step="0.000001"
                                    min="-90" max="90">
                                <p class="text-xs text-gray-400 mt-1">-90 hingga 90</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════ --}}
                {{-- STEP 2 — Pilih Level DAS              --}}
                {{-- ══════════════════════════════════════ --}}
                <div class="step-card" id="step2-card">
                    <div class="step-header">
                        <div class="step-badge inactive" id="step2-dot">2</div>
                        <div>
                            <h3 class="text-sm font-extrabold text-gray-900">Pilih Batas Daerah Aliran Sungai (DAS)</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Pilih tingkat kedalaman skala DAS yang mencakup titik lokasi Anda</p>
                        </div>
                    </div>

                    <div class="p-4 space-y-3">
                        @php
                        $levels = [
                            3 => ['name' => 'DAS Sangat Besar', 'range' => '> 100.000 km²', 'icon' => 'fa-mountain'],
                            4 => ['name' => 'DAS Besar',        'range' => '30.000 - 100.000 km²', 'icon' => 'fa-water'],
                            5 => ['name' => 'Sub DAS Utama',    'range' => '10.000 - 30.000 km²', 'icon' => 'fa-stream'],
                            6 => ['name' => 'Sub DAS Sedang',   'range' => '3.000 - 10.000 km²', 'icon' => 'fa-tint'],
                            7 => ['name' => 'Sub DAS Kecil',    'range' => '500 - 3.000 km²', 'icon' => 'fa-tint-slash'],
                            8 => ['name' => 'Mikro DAS',        'range' => '< 500 km²', 'icon' => 'fa-circle'],
                        ];
                        @endphp

                        <div class="das-grid" id="das-level-grid">
                            @foreach($levels as $lvl => $info)
                            <button type="button"
                                class="das-btn"
                                id="das-btn-{{ $lvl }}"
                                data-level="{{ $lvl }}"
                                disabled
                                onclick="selectDasLevel({{ $lvl }})">
                                <span class="das-lvl-tag">Level {{ $lvl }}</span>
                                <span class="das-name">{{ $info['name'] }}</span>
                            </button>
                            @endforeach
                        </div>

                        {{-- DAS Found --}}
                        <div id="das-info-panel" class="hidden">
                            <div class="das-found-strip">
                                <div class="das-found-icon">
                                    <i class="fas fa-check text-sm"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-semibold text-emerald-600 mb-0.5">DAS Terpilih</p>
                                    <p class="text-sm font-extrabold text-gray-900 truncate" id="das-info-name">—</p>
                                    <div class="flex flex-wrap gap-2 mt-1">
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-teal-700 bg-teal-50 px-2 py-0.5 rounded-full border border-teal-200">
                                            <i class="fas fa-expand-arrows-alt" style="font-size:0.6rem"></i>
                                            <span id="das-info-area">—</span>
                                        </span>
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-sky-700 bg-sky-50 px-2 py-0.5 rounded-full border border-sky-200">
                                            <i class="fas fa-layer-group" style="font-size:0.6rem"></i>
                                            Level <span id="das-info-level">—</span>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- DAS Error --}}
                        <div id="das-error-panel" class="hidden">
                            <div class="note-strip error">
                                <i class="fas fa-exclamation-circle mt-0.5 flex-shrink-0"></i>
                                <span id="das-error-msg">Level DAS ini tidak tersedia untuk titik yang dipilih.</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════ --}}
                {{-- STEP 3 — Info Lokasi (readonly)       --}}
                {{-- ══════════════════════════════════════ --}}
                <div class="step-card" id="step3-card">
                    <div class="step-header">
                        <div class="step-badge inactive" id="step3-dot">3</div>
                        <div>
                            <h3 class="text-sm font-extrabold text-gray-900">Informasi Lokasi & Alamat</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Alamat diperoleh otomatis melalui reverse geocoding</p>
                        </div>
                        <span id="location-loading" class="ml-auto hidden text-xs text-sky-600 font-semibold bg-sky-50 px-2.5 py-1 rounded-full flex items-center gap-1.5">
                            <i class="fas fa-spinner fa-spin text-xs"></i>
                            Memuat nama...
                        </span>
                    </div>

                    <div class="p-4 space-y-3">
                        <div>
                            <label class="field-label">
                                <i class="fas fa-map-pin text-red-400"></i>
                                Nama Lokasi
                            </label>
                            <input type="text" name="location_name" id="location_name" readonly
                                class="field-input"
                                placeholder="Nama lokasi akan muncul otomatis setelah titik dipilih">
                        </div>
                        <div>
                            <label class="field-label">
                                <i class="fas fa-file-alt text-purple-400"></i>
                                Deskripsi Alamat Lengkap
                            </label>
                            <textarea name="location_description" id="location_description" rows="2" readonly
                                class="field-input resize-none"
                                style="resize: none;"
                                placeholder="Deskripsi alamat lengkap wilayah"></textarea>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════ --}}
                {{-- STEP 4 — Periode Analisis             --}}
                {{-- ══════════════════════════════════════ --}}
                <div class="step-card" id="step4-card">
                    <div class="step-header">
                        <div class="step-badge inactive" id="step4-dot">4</div>
                        <div>
                            <h3 class="text-sm font-extrabold text-gray-900">Periode Waktu Analisis</h3>
                            <p class="text-xs text-gray-400 mt-0.5">Tentukan rentang tanggal data satelit yang akan dianalisis</p>
                        </div>
                    </div>

                    <div class="p-4 space-y-3">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <label for="start_date" class="field-label">
                                    <i class="fas fa-calendar-check text-emerald-500"></i>
                                    Tanggal Mulai
                                    <span class="text-red-500 ml-0.5">*</span>
                                </label>
                                <input type="date" name="start_date" id="start_date"
                                    class="field-input"
                                    required>
                            </div>
                            <div>
                                <label for="end_date" class="field-label">
                                    <i class="fas fa-calendar-times text-red-400"></i>
                                    Tanggal Selesai
                                    <span class="text-red-500 ml-0.5">*</span>
                                </label>
                                <input type="date" name="end_date" id="end_date"
                                    class="field-input"
                                    required max="{{ date('Y-m-d') }}">
                            </div>
                        </div>

                        {{-- Dynamic Duration Badge & Recommendation --}}
                        <div id="date-duration-box" class="hidden mt-3 p-3.5 rounded-xl border transition-all">
                            <div class="flex items-start gap-2.5">
                                <i id="date-duration-icon" class="fas fa-info-circle text-sm mt-0.5 shrink-0"></i>
                                <div class="text-xs">
                                    <p class="font-bold text-slate-800" id="date-duration-title">Rentang Waktu Terpilih: <span id="date-duration-days">0</span> Hari</p>
                                    <p class="mt-0.5 leading-relaxed" id="date-duration-desc"></p>
                                </div>
                            </div>
                        </div>

                        <div class="note-strip warning mt-3">
                            <i class="fas fa-lightbulb text-amber-500 flex-shrink-0 mt-0.5" style="font-size:0.8rem"></i>
                            <span>
                                <b>Panduan Akurasi Model:</b> Rentang minimal adalah <b>90 hari</b>. Namun, untuk hasil model Machine Learning dan neraca air yang <b>jauh lebih presisi dan valid</b>, sangat direkomendasikan memilih rentang <b>1 hingga 3 tahun</b> (siklus musim hujan & kemarau penuh).
                            </span>
                        </div>
                    </div>
                </div>

                {{-- ── Submit bar ── --}}
                <div class="step-card p-4">
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                        <a href="{{ route('hidrologi.index') }}" class="btn-back">
                            <i class="fas fa-arrow-left text-xs"></i>
                            Kembali
                        </a>

                        <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 sm:gap-4">
                            {{-- Status badge --}}
                            <div id="submit-status" class="text-xs text-gray-400 font-semibold text-center sm:text-right hidden sm:block"></div>
                            <button type="submit" id="submitBtn" disabled class="btn-submit">
                                <i class="fas fa-paper-plane" id="submitIcon" style="font-size:0.8rem"></i>
                                <span id="submitText">Pilih DAS Terlebih Dahulu</span>
                            </button>
                        </div>
                    </div>

                    {{-- Mobile status --}}
                    <div id="submit-hint" class="mt-3 hidden">
                        <div class="note-strip info">
                            <i class="fas fa-info-circle flex-shrink-0" style="font-size:0.75rem; margin-top:0.05rem"></i>
                            <span id="submit-hint-text"></span>
                        </div>
                    </div>
                </div>

            </form>
        </div>

        {{-- ── SIDEBAR ── --}}
        <div class="lg:col-span-1 space-y-4">

            {{-- How it works --}}
            <div class="guide-card">
                <div class="guide-head">
                    <div class="guide-icon"><i class="fas fa-info-circle"></i></div>
                    <span class="guide-title">Cara Kerja</span>
                </div>
                <div class="guide-body">
                    @php
                    $stepsGuide = [
                        ['icon'=>'fa-mouse-pointer', 'text'=>'Klik pada peta untuk menentukan titik koordinat atau cari lokasi yang diinginkan.'],
                        ['icon'=>'fa-layer-group',   'text'=>'Pilih level DAS (Daerah Aliran Sungai) yang sesuai dengan luas kajian analisis.'],
                        ['icon'=>'fa-draw-polygon',  'text'=>'Sistem otomatis memuat poligon batas DAS dari HydroSHEDS.'],
                        ['icon'=>'fa-calendar-alt',  'text'=>'Tentukan rentang tanggal mulai dan selesai data historis.'],
                        ['icon'=>'fa-paper-plane',   'text'=>'Kirim analisis untuk memproses data satelit dan permodelan hidrologi.'],
                    ];
                    @endphp
                    @foreach($stepsGuide as $i => $step)
                    <div class="guide-row">
                        <div class="guide-num">{{ $i + 1 }}</div>
                        <span class="guide-row-text">{{ $step['text'] }}</span>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- DAS Level Reference --}}
            <div class="guide-card">
                <div class="guide-head" style="background: linear-gradient(135deg, #f0fdfa, #e0f2fe); border-bottom-color: #bae6fd;">
                    <div class="guide-icon" style="background: var(--c-ocean)"><i class="fas fa-layer-group"></i></div>
                    <span class="guide-title">Panduan Level DAS</span>
                </div>
                <div class="guide-body" style="padding: 0.625rem 1rem;">
                    @foreach($levels as $lvl => $info)
                    <div class="guide-row">
                        <span class="guide-num" style="background: #e0f2fe; color: #0e7490;">{{ $lvl }}</span>
                        <div class="guide-row-text">
                            <strong>{{ $info['name'] }}</strong>
                        </div>
                    </div>
                    @endforeach
                    <p class="mt-2 text-xs text-gray-400 pt-2" style="border-top: 1px solid var(--c-border);">
                        <i class="fas fa-lightbulb text-yellow-400 mr-1"></i>
                        Level 8 memberikan presisi batas DAS mikro terbaik untuk analisis lokal.
                    </p>
                </div>
            </div>

            {{-- Notes --}}
            <div class="guide-card">
                <div class="guide-head" style="background: linear-gradient(135deg, #fffbeb, #fef3c7); border-bottom-color: #fde68a;">
                    <div class="guide-icon" style="background: #d97706"><i class="fas fa-exclamation-triangle"></i></div>
                    <span class="guide-title">Catatan Penting</span>
                </div>
                <div class="guide-body">
                    @php
                    $notes = [
                        ['icon'=>'fa-clock', 'text'=>'Proses analisis membutuhkan waktu <b>1-3 menit</b> tergantung rentang periode dan luas wilayah DAS.'],
                        ['icon'=>'fa-bell',  'text'=>'Anda dapat meninggalkan halaman ini, notifikasi status akan diperbarui otomatis.'],
                        ['icon'=>'fa-save',  'text'=>'Seluruh hasil kalkulasi dan grafik akan tersimpan secara permanen di riwayat akun Anda.'],
                    ];
                    @endphp
                    @foreach($notes as $note)
                    <div class="guide-row">
                        <div class="guide-num" style="background: #fef3c7; color: #d97706;">
                            <i class="fas {{ $note['icon'] }}" style="font-size:0.55rem"></i>
                        </div>
                        <span class="guide-row-text">{!! $note['text'] !!}</span>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>{{-- /sidebar --}}
    </div>{{-- /grid --}}
</div>
@endsection

@push('scripts')
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
<script src="https://unpkg.com/leaflet-control-geocoder@2.4.0/dist/Control.Geocoder.js" crossorigin=""></script>

<script>
// ════════════════════════════════════════════════════════════════
// STATE
// ════════════════════════════════════════════════════════════════
let map, marker, dasPolygonLayer;
let clickedLat  = null;
let clickedLng  = null;
let selectedLevel = null;
let dasReady    = false;

// ════════════════════════════════════════════════════════════════
// TRANSLATIONS
// ════════════════════════════════════════════════════════════════
const trans = {
    submitClickMap:       "Klik Peta Terlebih Dahulu",
    submitSelectDas:      "Pilih DAS Terlebih Dahulu",
    submitPeriod:         "Lengkapi Periode Tanggal",
    submitSending:        "Mengirim Analisis...",
    createAnalysis:       "Buat Analisis",
    swalNoPointTitle:     "Titik Belum Dipilih",
    swalNoPointText:      "Silakan klik pada peta terlebih dahulu untuk menentukan titik outlet lokasi analisis.",
    swalNoDasTitle:       "DAS Belum Dipilih",
    swalNoDasText:        "Silakan pilih level DAS yang sesuai sebelum melanjutkan analisis.",
    submitSuccessText:    "Analisis Anda berhasil dibuat dan sedang diproses di server.",
    dasNotFoundMsg:       "Batas DAS tidak ditemukan untuk titik ini pada level tersebut.",
    dasErrorNotFound:     "Batas DAS tidak ditemukan pada titik koordinat ini.",
    dasErrorGeneric:      "Terjadi kendala saat memuat batas DAS.",
    timeoutError:         "Waktu permintaan habis (timeout). Silakan coba lagi.",
    responseNotJson:      "Format respons server tidak sesuai.",
    checkConsole:         "Silakan periksa log konsol untuk detail kesalahan.",
    failedTitle:          "Gagal",
    successTitle:         "Berhasil",
    mapPopupPoint:        "Titik Analisis Terpilih",
    mapPopupArea:         "Luas",
    mapPopupLevel:        "Level",
    geocoderPlaceholder:  "Cari lokasi atau masukkan koordinat...",
};

// ════════════════════════════════════════════════════════════════
// INIT MAP
// ════════════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', function () {

    // ── Auto-detect navbar height so progress rail sits flush below it ──
    (function () {
        const navbar =
            document.querySelector('nav[class*="navbar"]')   ||
            document.querySelector('header')                  ||
            document.querySelector('nav')                     ||
            document.querySelector('[class*="navbar"]')       ||
            document.querySelector('[id*="navbar"]')          ||
            document.querySelector('[id*="header"]');

        const h = navbar ? navbar.getBoundingClientRect().height : 64;
        document.documentElement.style.setProperty('--navbar-height', h + 'px');

        window.addEventListener('resize', () => {
            if (navbar) {
                document.documentElement.style.setProperty(
                    '--navbar-height', navbar.getBoundingClientRect().height + 'px'
                );
            }
        });
    })();

    map = L.map('map').setView([-7.2525, 110.4053], 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    // Geocoder
    L.Control.geocoder({
        defaultMarkGeocode: false,
        placeholder: trans.geocoderPlaceholder,
        geocoder: L.Control.Geocoder.nominatim({
            geocodingQueryParams: { countrycodes: 'id' }
        })
    })
    .on('markgeocode', function (e) {
        const ll = e.geocode.center;
        handleMapClick(ll.lat, ll.lng);
        map.setView(ll, 12);
    })
    .addTo(map);

    // Map click
    map.on('click', function (e) {
        handleMapClick(e.latlng.lat, e.latlng.lng);
    });

    // Coordinate input listeners
    document.getElementById('longitude_input').addEventListener('change', handleCoordinateInput);
    document.getElementById('latitude_input').addEventListener('change', handleCoordinateInput);

    // Date validation
    document.getElementById('start_date').addEventListener('change', validateDates);
    document.getElementById('end_date').addEventListener('change', validateDates);
    document.getElementById('start_date').addEventListener('change', function () {
        document.getElementById('end_date').min = this.value;
    });

    // Form submit
    document.getElementById('hidrologiForm').addEventListener('submit', handleSubmit);
});

// ════════════════════════════════════════════════════════════════
// PROGRESS RAIL UPDATE
// ════════════════════════════════════════════════════════════════
function updateRail(step, status) {
    // status: 'active' | 'done' | 'inactive'
    const num = document.getElementById(`rail-num-${step}`);
    const rail = document.getElementById(`rail-${step}`);
    if (!num || !rail) return;

    num.className = `rail-num ${status}`;
    rail.className = `rail-step ${status}`;

    if (status === 'done') {
        num.innerHTML = '<i class="fas fa-check" style="font-size:0.6rem"></i>';
    } else {
        num.textContent = step;
    }
}

function updateStepCard(step, status) {
    // status: 'active' | 'done' | 'inactive'
    const card = document.getElementById(`step${step}-card`);
    const dot  = document.getElementById(`step${step}-dot`);
    if (!card || !dot) return;

    card.className = 'step-card';
    if (status === 'active') card.classList.add('is-active');
    if (status === 'done')   card.classList.add('is-done');

    dot.className = `step-badge ${status === 'inactive' ? 'inactive' : status === 'done' ? 'done' : 'active'}`;
    if (status === 'done') {
        dot.innerHTML = '<i class="fas fa-check" style="font-size:0.65rem"></i>';
    } else {
        dot.textContent = step;
    }
}

// ════════════════════════════════════════════════════════════════
// STEP 1 — klik peta & input manual
// ════════════════════════════════════════════════════════════════
function handleCoordinateInput() {
    const lng = parseFloat(document.getElementById('longitude_input').value);
    const lat = parseFloat(document.getElementById('latitude_input').value);

    // Validasi
    if (isNaN(lng) || isNaN(lat)) return;
    if (lng < -180 || lng > 180 || lat < -90 || lat > 90) return;

    handleMapClick(lat, lng);
    map.setView([lat, lng], 12);
}

function handleMapClick(lat, lng) {
    clickedLat = lat;
    clickedLng = lng;

    // Marker
    if (marker) {
        marker.setLatLng([lat, lng]);
    } else {
        marker = L.marker([lat, lng], { draggable: true }).addTo(map);
        marker.on('dragend', function (e) {
            const p = e.target.getLatLng();
            handleMapClick(p.lat, p.lng);
        });
    }
    marker.bindPopup(`<b>${trans.mapPopupPoint}</b><br>
        Lat: ${lat.toFixed(6)}<br>Lng: ${lng.toFixed(6)}`).openPopup();

    // Koordinat inputs & hidden fields
    document.getElementById('longitude_input').value = lng.toFixed(6);
    document.getElementById('latitude_input').value  = lat.toFixed(6);
    document.getElementById('longitude').value = lng.toFixed(6);
    document.getElementById('latitude').value  = lat.toFixed(6);

    // Sembunyikan hint overlay
    document.getElementById('mapHint').classList.add('hidden');

    // Reverse geocode
    fetchLocationName(lat, lng);

    // Step states
    updateRail(1, 'done');
    updateRail(2, 'active');
    updateStepCard(1, 'done');
    updateStepCard(2, 'active');

    activateStep2();
    resetDasSelection();
    checkSubmitReady();
}

// ════════════════════════════════════════════════════════════════
// STEP 2 — pilih level DAS
// ════════════════════════════════════════════════════════════════
function activateStep2() {
    document.querySelectorAll('.das-btn').forEach(btn => {
        btn.disabled = false;
    });
}

function selectDasLevel(level) {
    if (!clickedLat || !clickedLng) return;

    selectedLevel = level;

    document.querySelectorAll('.das-btn').forEach(btn => btn.classList.remove('selected'));
    document.getElementById(`das-btn-${level}`).classList.add('selected');

    document.getElementById('das-info-panel').classList.add('hidden');
    document.getElementById('das-error-panel').classList.add('hidden');
    document.getElementById('step2-loading').classList.remove('hidden');

    fetchDasPolygon(clickedLat, clickedLng, level);
}

async function fetchDasPolygon(lat, lng, level) {
    try {
        const resp = await fetch(`{{ route('hidrologi.das-info') }}`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ lat, lon: lng, level })
        });

        if (!resp.ok) throw new Error(`HTTP ${resp.status}`);
        const data = await resp.json();

        if (!data.success) throw new Error(data.message || trans.dasNotFoundMsg);

        displayDasPolygon(data.geometry, data);

        document.getElementById('das_level').value    = level;
        document.getElementById('das_name').value     = data.name || `DAS Level ${level}`;
        document.getElementById('das_area_km2').value = data.area_km2 || 0;
        document.getElementById('hybas_id').value     = data.hybas_id || '';
        document.getElementById('das_geojson').value  = data.geometry ? JSON.stringify(data.geometry) : '';

        document.getElementById('das-info-name').textContent  = data.name || `DAS Level ${level}`;
        document.getElementById('das-info-area').textContent  = `${parseFloat(data.area_km2 || 0).toFixed(1)} km²`;
        document.getElementById('das-info-level').textContent = level;
        document.getElementById('das-info-panel').classList.remove('hidden');

        // Step rail update
        updateRail(2, 'done');
        updateRail(3, 'done');
        updateRail(4, 'active');
        updateStepCard(2, 'done');
        updateStepCard(3, 'done');
        updateStepCard(4, 'active');
        dasReady = true;

    } catch (err) {
        console.error('DAS fetch error:', err);
        document.getElementById('das-error-msg').textContent =
            err.message.includes('404') || err.message.includes('tidak ditemukan') || err.message === trans.dasNotFoundMsg
            ? trans.dasErrorNotFound.replace(':level', level)
            : trans.dasErrorGeneric.replace(':message', err.message);
        document.getElementById('das-error-panel').classList.remove('hidden');
        dasReady = false;

        document.getElementById('das_level').value    = '';
        document.getElementById('das_name').value     = '';
        document.getElementById('das_area_km2').value = '';
        document.getElementById('hybas_id').value     = '';
        document.getElementById('das_geojson').value  = '';
    } finally {
        document.getElementById('step2-loading').classList.add('hidden');
        checkSubmitReady();
    }
}

function displayDasPolygon(geometry, dasData) {
    if (dasPolygonLayer) map.removeLayer(dasPolygonLayer);
    if (!geometry) return;

    try {
        dasPolygonLayer = L.geoJSON(geometry, {
            style: {
                color:       '#0d9488',
                weight:      2.5,
                opacity:     0.9,
                fillColor:   '#14b8a6',
                fillOpacity: 0.12,
            }
        })
        .bindPopup(`
            <div style="min-width:170px; font-family: inherit;">
                <b style="font-size:13px; color: #1e293b;">${dasData.name || 'DAS'}</b><br>
                <span style="color:#64748b; font-size:11px;">${trans.mapPopupArea}: ${parseFloat(dasData.area_km2||0).toFixed(1)} km²</span><br>
                <span style="color:#64748b; font-size:11px;">${trans.mapPopupLevel}: ${dasData.level || selectedLevel}</span>
            </div>
        `)
        .addTo(map);

        map.fitBounds(dasPolygonLayer.getBounds(), { padding: [30, 30] });
    } catch (e) {
        console.warn('Gagal render polygon DAS:', e);
    }
}

function resetDasSelection() {
    selectedLevel = null;
    dasReady      = false;
    document.querySelectorAll('.das-btn').forEach(btn => btn.classList.remove('selected'));
    document.getElementById('das-info-panel').classList.add('hidden');
    document.getElementById('das-error-panel').classList.add('hidden');
    if (dasPolygonLayer) { map.removeLayer(dasPolygonLayer); dasPolygonLayer = null; }
    document.getElementById('das_level').value    = '';
    document.getElementById('das_name').value     = '';
    document.getElementById('das_area_km2').value = '';
    document.getElementById('hybas_id').value     = '';

    updateRail(2, 'active');
    updateRail(3, 'inactive');
    updateRail(4, 'inactive');
    updateStepCard(3, '');
    updateStepCard(4, '');
}

// ════════════════════════════════════════════════════════════════
// REVERSE GEOCODING
// ════════════════════════════════════════════════════════════════
function fetchLocationName(lat, lng) {
    const nameEl  = document.getElementById('location_name');
    const descEl  = document.getElementById('location_description');
    const loadEl  = document.getElementById('location-loading');

    loadEl.classList.remove('hidden');

    fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=14&addressdetails=1`)
        .then(r => r.json())
        .then(data => {
            loadEl.classList.add('hidden');
            if (data && data.display_name) {
                const addr = data.address || {};
                const name = addr.village || addr.suburb || addr.city ||
                             addr.town || addr.county || addr.state || data.display_name;
                nameEl.value = name;
                descEl.value = data.display_name;
            } else {
                nameEl.value = `Lokasi (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
                descEl.value = `Koordinat: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
            }
        })
        .catch(() => {
            loadEl.classList.add('hidden');
            nameEl.value = `Lokasi (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
            descEl.value = `Koordinat: ${lat.toFixed(6)}, ${lng.toFixed(6)}`;
        });
}

// ════════════════════════════════════════════════════════════════
// VALIDASI & SUBMIT
// ════════════════════════════════════════════════════════════════
function validateDates() {
    const s = document.getElementById('start_date').value;
    const e = document.getElementById('end_date').value;
    const box = document.getElementById('date-duration-box');
    const icon = document.getElementById('date-duration-icon');
    const daysEl = document.getElementById('date-duration-days');
    const descEl = document.getElementById('date-duration-desc');

    if (s && e) {
        const start = new Date(s);
        const end   = new Date(e);

        if (end < start) {
            document.getElementById('end_date').value = '';
            if (box) box.classList.add('hidden');
            checkSubmitReady();
            return;
        }

        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;

        if (box) {
            box.classList.remove('hidden');
            daysEl.textContent = diffDays;

            if (diffDays < 90) {
                box.className = 'mt-3 p-3.5 rounded-xl border bg-red-50 border-red-200 text-red-700';
                icon.className = 'fas fa-exclamation-circle text-red-600 text-sm mt-0.5 shrink-0';
                descEl.innerHTML = `<b class="text-red-800">Rentang terlalu pendek!</b> Minimal durasi adalah 90 hari agar pemodelan hidrologi dapat berjalan.`;
            } else if (diffDays >= 90 && diffDays < 365) {
                box.className = 'mt-3 p-3.5 rounded-xl border bg-amber-50 border-amber-200 text-amber-800';
                icon.className = 'fas fa-info-circle text-amber-600 text-sm mt-0.5 shrink-0';
                descEl.innerHTML = `Durasi memenuhi syarat (≥90 hari). Namun, akurasi model ML & neraca air akan <b>jauh lebih sempurna</b> jika memilih rentang <b>1–3 tahun (365–1095 hari)</b>.`;
            } else {
                box.className = 'mt-3 p-3.5 rounded-xl border bg-emerald-50 border-emerald-200 text-emerald-800';
                icon.className = 'fas fa-check-circle text-emerald-600 text-sm mt-0.5 shrink-0';
                descEl.innerHTML = `<b>Pilihan Optimal!</b> Durasi mencakup siklus musim hujan & kemarau penuh sehingga validasi model ML akan mencapai skor akurasi tertinggi.`;
            }
        }
    } else {
        if (box) box.classList.add('hidden');
    }

    checkSubmitReady();
}

function checkSubmitReady() {
    const btn     = document.getElementById('submitBtn');
    const txtEl   = document.getElementById('submitText');
    const statusEl = document.getElementById('submit-status');

    const hasClick = !!clickedLat;
    const hasDas   = dasReady;
    const hasStart = !!document.getElementById('start_date').value;
    const hasEnd   = !!document.getElementById('end_date').value;

    if (!hasClick) {
        btn.disabled = true;
        txtEl.textContent = trans.submitClickMap;
        if (statusEl) statusEl.textContent = '';
    } else if (!hasDas) {
        btn.disabled = true;
        txtEl.textContent = trans.submitSelectDas;
        if (statusEl) statusEl.textContent = '';
    } else if (!hasStart || !hasEnd) {
        btn.disabled = true;
        txtEl.textContent = trans.submitPeriod;
        if (statusEl) statusEl.textContent = '';
    } else {
        const sVal = new Date(document.getElementById('start_date').value);
        const eVal = new Date(document.getElementById('end_date').value);
        const dDays = Math.ceil(Math.abs(eVal - sVal) / (1000 * 60 * 60 * 24)) + 1;

        if (dDays < 90) {
            btn.disabled = true;
            txtEl.textContent = 'Pilih Minimal 90 Hari';
            if (statusEl) statusEl.textContent = 'Durasi saat ini: ' + dDays + ' hari (kurang ' + (90 - dDays) + ' hari lagi)';
        } else {
            btn.disabled = false;
            txtEl.textContent = trans.createAnalysis;
            if (statusEl) statusEl.textContent = '';
            updateRail(4, 'done');
            updateStepCard(4, 'done');
        }
    }
}

// ════════════════════════════════════════════════════════════════
// FORM SUBMIT
// ════════════════════════════════════════════════════════════════
function handleSubmit(e) {
    e.preventDefault();

    if (!clickedLat || !clickedLng) {
        Swal.fire({ icon:'warning', title: trans.swalNoPointTitle, text: trans.swalNoPointText });
        return;
    }
    if (!dasReady || !document.getElementById('das_level').value) {
        Swal.fire({ icon:'warning', title: trans.swalNoDasTitle, text: trans.swalNoDasText });
        return;
    }

    const submitBtn  = document.getElementById('submitBtn');
    const submitIcon = document.getElementById('submitIcon');
    const submitText = document.getElementById('submitText');
    submitBtn.disabled = true;
    submitIcon.className = 'fas fa-spinner fa-spin';
    submitText.textContent = trans.submitSending;

    const formData = new FormData(this);
    const controller = new AbortController();
    const timeout = setTimeout(() => controller.abort(), 30000);

    fetch(this.action, {
        method: 'POST',
        body:   formData,
        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') },
        signal: controller.signal
    })
    .then(r => {
        clearTimeout(timeout);
        if (!r.headers.get('content-type')?.includes('application/json'))
            throw new Error(trans.responseNotJson);
        return r.json();
    })
    .then(data => {
        if (data.success || data.job_id) {
            Swal.fire({
                icon: 'success', title: trans.successTitle,
                text: data.message || trans.submitSuccessText,
                showConfirmButton: false, timer: 2000
            }).then(() => {
                window.location.href = `{{ url('hidrologi/show') }}/${data.job_id}`;
            });
        } else {
            throw new Error(data.message || data.error || 'Gagal mengirim');
        }
    })
    .catch(err => {
        clearTimeout(timeout);
        submitBtn.disabled = false;
        submitIcon.className = 'fas fa-paper-plane';
        submitText.textContent = trans.createAnalysis;

        const msg = err.name === 'AbortError' ? trans.timeoutError : err.message;
        Swal.fire({
            icon: 'error', title: trans.failedTitle,
            html: `<p>${msg}</p>`,
            footer: `<p class="text-xs">${trans.checkConsole}</p>`
        });
    });
}
</script>
@endpush