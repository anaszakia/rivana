@extends('layouts.app')

@section('title', __('messages.create_new_analysis'))

@push('styles')
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder@2.4.0/dist/Control.Geocoder.css" crossorigin=""/>
<style>
    #map {
        height: 520px;
        width: 100%;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1);
    }

    /* ── DAS Level Selector ── */
    .das-level-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.625rem;
    }
    @media (max-width: 480px) {
        .das-level-grid { grid-template-columns: repeat(2, 1fr); }
    }

    .das-level-btn {
        position: relative;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 0.25rem;
        padding: 0.75rem 0.5rem;
        border: 2px solid #e5e7eb;
        border-radius: 0.875rem;
        background: #fff;
        cursor: pointer;
        transition: all 0.18s ease;
        text-align: center;
        min-height: 80px;
    }
    .das-level-btn:hover:not(:disabled) {
        border-color: #3b82f6;
        background: #eff6ff;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(59,130,246,0.18);
    }
    .das-level-btn.selected {
        border-color: #2563eb;
        background: linear-gradient(135deg, #eff6ff, #dbeafe);
        box-shadow: 0 0 0 3px rgba(37,99,235,0.18);
    }
    .das-level-btn:disabled {
        opacity: 0.45;
        cursor: not-allowed;
    }
    .das-level-badge {
        font-size: 0.65rem;
        font-weight: 800;
        letter-spacing: 0.03em;
        padding: 0.15rem 0.5rem;
        border-radius: 9999px;
        background: #e0e7ff;
        color: #3730a3;
    }
    .das-level-btn.selected .das-level-badge {
        background: #2563eb;
        color: #fff;
    }
    .das-level-name {
        font-size: 0.72rem;
        font-weight: 700;
        color: #374151;
        line-height: 1.2;
    }
    .das-level-range {
        font-size: 0.65rem;
        color: #6b7280;
        line-height: 1.2;
    }
    .das-level-btn.selected .das-level-name { color: #1d4ed8; }
    .das-level-btn.selected .das-level-range { color: #3b82f6; }

    /* ── DAS Info Panel ── */
    #das-info-panel {
        transition: all 0.3s ease;
    }
    #das-polygon-layer {
        transition: opacity 0.3s;
    }

    /* ── Step indicator ── */
    .step-dot {
        width: 2rem; height: 2rem;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.8rem; font-weight: 800;
        flex-shrink: 0;
        transition: all 0.2s;
    }
    .step-dot.active   { background: #2563eb; color: #fff; box-shadow: 0 0 0 4px rgba(37,99,235,0.18); }
    .step-dot.done     { background: #16a34a; color: #fff; }
    .step-dot.inactive { background: #e5e7eb; color: #9ca3af; }

    /* ── Leaflet Geocoder ── */
    .leaflet-control-geocoder { border-radius: 0.5rem; box-shadow: 0 4px 6px rgba(0,0,0,0.15); }
    .leaflet-control-geocoder-form input {
        border-radius: 0.5rem; padding: 0.75rem; font-size: 14px;
        border: 2px solid #e5e7eb;
    }
    .leaflet-control-geocoder-form input:focus { border-color: #3b82f6; outline: none; }

    /* ── Pulse animation for click prompt ── */
    @keyframes pulse-ring {
        0%   { transform: scale(0.8); opacity: 1; }
        100% { transform: scale(2.0); opacity: 0; }
    }
    .info-card { transition: all 0.3s ease; }
    .info-card:hover { transform: translateY(-2px); box-shadow: 0 10px 25px rgba(0,0,0,0.15); }
</style>
@endpush

@section('content')
<div class="container mx-auto px-3 sm:px-4 lg:px-6 py-4 sm:py-6">

    {{-- ── Header ── --}}
    <div class="mb-6">
        <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-3xl shadow-2xl">
            <div class="absolute inset-0 opacity-10">
                <div class="absolute w-96 h-96 -top-48 -right-48 bg-white rounded-full animate-pulse"></div>
                <div class="absolute w-64 h-64 -bottom-32 -left-32 bg-white rounded-full animate-pulse" style="animation-delay:1s"></div>
            </div>
            <div class="relative z-10 p-6 sm:p-8">
                <div class="flex items-center gap-2 text-blue-100 mb-4 text-xs sm:text-sm flex-wrap">
                    <a href="{{ route('hidrologi.index') }}" class="flex items-center gap-1 hover:text-white transition-colors bg-white/10 backdrop-blur-sm px-3 py-1.5 rounded-lg">
                        <i class="fas fa-water"></i>
                        <span class="hidden sm:inline">{{ __('messages.hydrology') }}</span>
                    </a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span class="text-white font-bold bg-white/20 backdrop-blur-sm px-3 py-1.5 rounded-lg">{{ __('messages.create_new_analysis') }}</span>
                </div>
                <div class="flex items-center gap-4">
                    <div class="w-14 h-14 sm:w-16 sm:h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shrink-0">
                        <i class="fas fa-draw-polygon text-2xl sm:text-3xl text-white"></i>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white mb-1 tracking-tight">{{ __('messages.create_new_hydrology_analysis') }}</h1>
                        <p class="text-blue-100 text-sm sm:text-base">Pilih wilayah DAS, tentukan periode analisis, dan kirim pekerjaan</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 sm:gap-6">

        {{-- ── FORM ── --}}
        <div class="lg:col-span-2 space-y-5">
            <form id="hidrologiForm" action="{{ route('hidrologi.submit') }}" method="POST">
                @csrf

                {{-- Hidden fields --}}
                <input type="hidden" name="longitude"   id="longitude"   value="">
                <input type="hidden" name="latitude"    id="latitude"    value="">
                <input type="hidden" name="das_level"   id="das_level"   value="">
                <input type="hidden" name="das_name"    id="das_name"    value="">
                <input type="hidden" name="das_area_km2" id="das_area_km2" value="">
                <input type="hidden" name="hybas_id"    id="hybas_id"    value="">

                {{-- ══════════════════════════════════════ --}}
                {{-- STEP 1 — Klik Peta                    --}}
                {{-- ══════════════════════════════════════ --}}
                <div class="bg-white rounded-3xl shadow-xl p-5 sm:p-6 border border-gray-100">

                    {{-- Step header --}}
                    <div class="flex items-center gap-3 mb-5">
                        <div class="step-dot active" id="step1-dot">1</div>
                        <div>
                            <h3 class="text-lg font-extrabold text-gray-900">Klik Titik di Peta</h3>
                            <p class="text-xs text-gray-500">Klik sembarang titik di dalam wilayah DAS yang ingin dianalisis</p>
                        </div>
                    </div>

                    {{-- Map --}}
                    <div id="map" class="border-4 border-blue-200 mb-4"></div>

                    {{-- Map hint --}}
                    <div id="map-hint" class="p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border-2 border-blue-200">
                        <p class="text-sm text-blue-900 flex items-start gap-2">
                            <i class="fas fa-mouse-pointer text-blue-600 mt-0.5 shrink-0"></i>
                            <span><strong>Klik peta</strong> untuk menentukan titik lokasi, lalu pilih level DAS di bawah.
                            Gunakan fitur pencarian (ikon kaca pembesar) untuk mencari nama lokasi.</span>
                        </p>
                    </div>

                    {{-- Koordinat readonly --}}
                    <div class="grid grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1 flex items-center gap-1">
                                <i class="fas fa-globe text-blue-500"></i> Longitude
                            </label>
                            <input type="text" id="longitude_display" readonly
                                class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl bg-gray-50 text-sm font-mono text-gray-700 cursor-default"
                                placeholder="Klik peta...">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-600 mb-1 flex items-center gap-1">
                                <i class="fas fa-globe text-green-500"></i> Latitude
                            </label>
                            <input type="text" id="latitude_display" readonly
                                class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-xl bg-gray-50 text-sm font-mono text-gray-700 cursor-default"
                                placeholder="Klik peta...">
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════ --}}
                {{-- STEP 2 — Pilih Level DAS              --}}
                {{-- ══════════════════════════════════════ --}}
                <div class="bg-white rounded-3xl shadow-xl p-5 sm:p-6 border border-gray-100" id="step2-card">

                    <div class="flex items-center gap-3 mb-5">
                        <div class="step-dot inactive" id="step2-dot">2</div>
                        <div>
                            <h3 class="text-lg font-extrabold text-gray-900">Pilih Level DAS</h3>
                            <p class="text-xs text-gray-500">Level menentukan ukuran wilayah DAS yang akan dianalisis</p>
                        </div>
                        <div id="step2-loading" class="ml-auto hidden">
                            <div class="flex items-center gap-2 text-blue-600 text-sm font-semibold">
                                <i class="fas fa-spinner fa-spin"></i>
                                <span>Memuat DAS...</span>
                            </div>
                        </div>
                    </div>

                    {{-- Level buttons --}}
                    <div class="das-level-grid mb-4" id="das-level-grid">
                        @php
                        $levels = [
                            3 => ['name' => 'DAS Besar',        'range' => '> 10.000 km²',     'icon' => 'fa-mountain'],
                            4 => ['name' => 'DAS Menengah',     'range' => '1.000–10.000 km²',  'icon' => 'fa-water'],
                            5 => ['name' => 'Sub-DAS',          'range' => '100–1.000 km²',     'icon' => 'fa-stream'],
                            6 => ['name' => 'Sub-DAS Kecil',    'range' => '10–100 km²',        'icon' => 'fa-tint'],
                            7 => ['name' => 'Mikro-DAS',        'range' => '1–10 km²',          'icon' => 'fa-tint-slash'],
                            8 => ['name' => 'Mikro-DAS Kecil',  'range' => '< 1 km²',           'icon' => 'fa-circle'],
                        ];
                        @endphp

                        @foreach($levels as $lvl => $info)
                        <button type="button"
                            class="das-level-btn"
                            id="das-btn-{{ $lvl }}"
                            data-level="{{ $lvl }}"
                            disabled
                            onclick="selectDasLevel({{ $lvl }})">
                            <i class="fas {{ $info['icon'] }} text-blue-400 text-lg mb-1"></i>
                            <span class="das-level-badge">Level {{ $lvl }}</span>
                            <span class="das-level-name">{{ $info['name'] }}</span>
                            <span class="das-level-range">{{ $info['range'] }}</span>
                        </button>
                        @endforeach
                    </div>

                    {{-- DAS Info Panel --}}
                    <div id="das-info-panel" class="hidden">
                        <div class="p-4 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-2xl border-2 border-emerald-200">
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 bg-emerald-500 rounded-xl flex items-center justify-center shrink-0">
                                    <i class="fas fa-check-circle text-white"></i>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-extrabold text-emerald-900 mb-1">DAS Terpilih</p>
                                    <p class="text-base font-bold text-emerald-800 truncate" id="das-info-name">—</p>
                                    <div class="flex flex-wrap gap-3 mt-2">
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-emerald-700 bg-emerald-100 px-2.5 py-1 rounded-lg">
                                            <i class="fas fa-expand-arrows-alt"></i>
                                            <span id="das-info-area">—</span>
                                        </span>
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-blue-700 bg-blue-100 px-2.5 py-1 rounded-lg">
                                            <i class="fas fa-layer-group"></i>
                                            Level <span id="das-info-level">—</span>
                                        </span>
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold text-purple-700 bg-purple-100 px-2.5 py-1 rounded-lg">
                                            <i class="fas fa-database"></i>
                                            HydroSHEDS
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Error panel --}}
                    <div id="das-error-panel" class="hidden">
                        <div class="p-4 bg-red-50 rounded-2xl border-2 border-red-200">
                            <p class="text-sm font-semibold text-red-700 flex items-center gap-2">
                                <i class="fas fa-exclamation-circle"></i>
                                <span id="das-error-msg">Level ini tidak tersedia untuk lokasi ini.</span>
                            </p>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════ --}}
                {{-- STEP 3 — Info Lokasi (readonly)       --}}
                {{-- ══════════════════════════════════════ --}}
                <div class="bg-white rounded-3xl shadow-xl p-5 sm:p-6 border border-gray-100" id="step3-card">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="step-dot inactive" id="step3-dot">3</div>
                        <div>
                            <h3 class="text-lg font-extrabold text-gray-900">Informasi Lokasi</h3>
                            <p class="text-xs text-gray-500">Terisi otomatis dari koordinat yang dipilih</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-map-pin text-red-500"></i>
                                Nama Lokasi
                                <span id="location-loading" class="hidden text-blue-500 text-xs ml-1">
                                    <i class="fas fa-spinner fa-spin"></i> Mengambil nama...
                                </span>
                            </label>
                            <input type="text" name="location_name" id="location_name" readonly
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-2xl bg-gray-50 text-sm font-medium text-gray-700 cursor-default"
                                placeholder="Otomatis dari koordinat...">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-file-alt text-purple-500"></i>
                                Deskripsi Lokasi
                            </label>
                            <textarea name="location_description" id="location_description" rows="3" readonly
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-2xl bg-gray-50 text-sm text-gray-700 cursor-default resize-none"
                                placeholder="Alamat lengkap otomatis..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- ══════════════════════════════════════ --}}
                {{-- STEP 4 — Periode Analisis             --}}
                {{-- ══════════════════════════════════════ --}}
                <div class="bg-white rounded-3xl shadow-xl p-5 sm:p-6 border border-gray-100" id="step4-card">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="step-dot inactive" id="step4-dot">4</div>
                        <div>
                            <h3 class="text-lg font-extrabold text-gray-900">Periode Analisis</h3>
                            <p class="text-xs text-gray-500">Tentukan rentang waktu data yang akan diproses</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label for="start_date" class="flex items-center gap-2 text-sm font-bold text-gray-800 mb-2">
                                <div class="w-5 h-5 bg-green-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-calendar-check text-green-600 text-xs"></i>
                                </div>
                                {{ __('messages.start_date') }} <span class="text-red-600">*</span>
                            </label>
                            <input type="date" name="start_date" id="start_date"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-medium"
                                required>
                        </div>
                        <div>
                            <label for="end_date" class="flex items-center gap-2 text-sm font-bold text-gray-800 mb-2">
                                <div class="w-5 h-5 bg-red-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-calendar-times text-red-600 text-xs"></i>
                                </div>
                                {{ __('messages.end_date') }} <span class="text-red-600">*</span>
                            </label>
                            <input type="date" name="end_date" id="end_date"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all font-medium"
                                required max="{{ date('Y-m-d') }}">
                        </div>
                    </div>

                    <div class="mt-4 p-4 bg-yellow-50 rounded-2xl border-2 border-yellow-200">
                        <p class="text-sm text-yellow-900 flex items-start gap-2 font-medium">
                            <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5 shrink-0"></i>
                            <span>{!! __('messages.date_validation_important') !!}</span>
                        </p>
                    </div>
                </div>

                {{-- ── Submit ── --}}
                <div class="bg-white rounded-3xl shadow-xl p-5 sm:p-6 border border-gray-100">
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
                        <a href="{{ route('hidrologi.index') }}"
                           class="inline-flex items-center justify-center gap-2 px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 font-bold rounded-2xl transition-all">
                            <i class="fas fa-arrow-left"></i>
                            <span>{{ __('messages.back') }}</span>
                        </a>
                        <button type="submit" id="submitBtn" disabled
                            class="inline-flex items-center justify-center gap-2 px-8 py-3
                                   bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-600
                                   hover:from-blue-700 hover:via-blue-800 hover:to-indigo-700
                                   disabled:from-gray-400 disabled:via-gray-400 disabled:to-gray-400
                                   disabled:cursor-not-allowed
                                   text-white font-extrabold rounded-2xl transition-all shadow-xl">
                            <i class="fas fa-paper-plane" id="submitIcon"></i>
                            <span id="submitText">Pilih DAS terlebih dahulu</span>
                        </button>
                    </div>
                    {{-- Validation hint --}}
                    <div id="submit-hint" class="mt-3 hidden">
                        <p class="text-xs text-red-600 font-semibold flex items-center gap-1">
                            <i class="fas fa-exclamation-circle"></i>
                            <span id="submit-hint-text"></span>
                        </p>
                    </div>
                </div>

            </form>
        </div>

        {{-- ── SIDEBAR ── --}}
        <div class="lg:col-span-1 space-y-5">

            {{-- DAS Level Guide --}}
            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-3xl p-5 shadow-xl info-card border-2 border-blue-200">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-11 h-11 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-layer-group text-white text-lg"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-blue-900">Panduan Level DAS</h3>
                </div>
                <div class="space-y-2 text-sm">
                    @foreach($levels as $lvl => $info)
                    <div class="flex items-center gap-3 p-3 bg-white rounded-xl shadow-sm">
                        <span class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center text-xs font-extrabold text-blue-700 shrink-0">{{ $lvl }}</span>
                        <div class="min-w-0">
                            <p class="font-bold text-gray-800 text-xs">{{ $info['name'] }}</p>
                            <p class="text-gray-500 text-xs">{{ $info['range'] }}</p>
                        </div>
                    </div>
                    @endforeach
                </div>
                <p class="mt-4 text-xs text-blue-800 font-medium bg-white rounded-xl p-3">
                    <i class="fas fa-lightbulb text-yellow-500 mr-1"></i>
                    <strong>Rekomendasi:</strong> Level 5–6 untuk analisis sub-DAS yang umum digunakan dalam perencanaan SDA.
                </p>
            </div>

            {{-- Cara Kerja --}}
            <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-3xl p-5 shadow-xl info-card border-2 border-green-200">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-11 h-11 bg-gradient-to-br from-green-600 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-info-circle text-white text-lg"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-green-900">{{ __('messages.how_it_works') }}</h3>
                </div>
                <ol class="space-y-2 text-sm text-green-900">
                    @foreach([
                        ['icon'=>'fa-mouse-pointer', 'text'=>'Klik titik di peta di dalam wilayah DAS target'],
                        ['icon'=>'fa-layer-group',   'text'=>'Pilih level DAS sesuai skala analisis'],
                        ['icon'=>'fa-draw-polygon',  'text'=>'Batas DAS otomatis tampil di peta'],
                        ['icon'=>'fa-calendar-alt',  'text'=>'Tentukan periode waktu analisis'],
                        ['icon'=>'fa-paper-plane',   'text'=>'Kirim — RIVANA akan memproses secara otomatis'],
                    ] as $step)
                    <li class="flex items-start gap-3 p-3 bg-white rounded-xl shadow-sm">
                        <i class="fas {{ $step['icon'] }} text-green-600 mt-0.5 shrink-0 w-4 text-center"></i>
                        <span class="font-medium">{{ $step['text'] }}</span>
                    </li>
                    @endforeach
                </ol>
            </div>

            {{-- Catatan --}}
            <div class="bg-gradient-to-br from-yellow-50 to-orange-100 rounded-3xl p-5 shadow-xl info-card border-2 border-yellow-200">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-11 h-11 bg-gradient-to-br from-yellow-600 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                    </div>
                    <h3 class="text-lg font-extrabold text-yellow-900">{{ __('messages.important_notes') }}</h3>
                </div>
                <ul class="space-y-2 text-sm text-yellow-900">
                    @foreach([
                        ['icon'=>'fa-clock',       'text'=>__('messages.processing_time')],
                        ['icon'=>'fa-bell',        'text'=>__('messages.notification')],
                        ['icon'=>'fa-save',        'text'=>__('messages.storage')],
                    ] as $note)
                    <li class="flex items-start gap-3 p-3 bg-white rounded-xl shadow-sm">
                        <i class="fas {{ $note['icon'] }} text-yellow-600 mt-0.5 shrink-0"></i>
                        <span class="font-medium">{!! $note['text'] !!}</span>
                    </li>
                    @endforeach
                </ul>
            </div>
        </div>

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

// HydroSHEDS API endpoint — sesuaikan jika proxy berbeda
const HYDROSHEDS_BASE = 'https://earthengine.googleapis.com';

// ════════════════════════════════════════════════════════════════
// INIT MAP
// ════════════════════════════════════════════════════════════════
document.addEventListener('DOMContentLoaded', function () {

    map = L.map('map').setView([-7.2525, 110.4053], 11);

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; OpenStreetMap contributors',
        maxZoom: 19
    }).addTo(map);

    // Geocoder
    L.Control.geocoder({
        defaultMarkGeocode: false,
        placeholder: 'Cari lokasi...',
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
// STEP 1 — klik peta
// ════════════════════════════════════════════════════════════════
function handleMapClick(lat, lng) {
    clickedLat = lat;
    clickedLng = lng;

    // Update marker
    if (marker) {
        marker.setLatLng([lat, lng]);
    } else {
        marker = L.marker([lat, lng], { draggable: true }).addTo(map);
        marker.on('dragend', function (e) {
            const p = e.target.getLatLng();
            handleMapClick(p.lat, p.lng);
        });
    }
    marker.bindPopup(`<b>Titik Terpilih</b><br>
        Lat: ${lat.toFixed(6)}<br>Lng: ${lng.toFixed(6)}`).openPopup();

    // Update display inputs
    document.getElementById('longitude_display').value = lng.toFixed(6);
    document.getElementById('latitude_display').value  = lat.toFixed(6);
    document.getElementById('longitude').value = lng.toFixed(6);
    document.getElementById('latitude').value  = lat.toFixed(6);

    // Reverse geocode
    fetchLocationName(lat, lng);

    // Aktifkan step 2
    activateStep2();
    resetDasSelection();
    checkSubmitReady();
}

// ════════════════════════════════════════════════════════════════
// STEP 2 — pilih level DAS
// ════════════════════════════════════════════════════════════════
function activateStep2() {
    document.getElementById('step2-dot').className = 'step-dot active';
    // Enable semua tombol level
    document.querySelectorAll('.das-level-btn').forEach(btn => {
        btn.disabled = false;
    });
}

function selectDasLevel(level) {
    if (!clickedLat || !clickedLng) return;

    selectedLevel = level;

    // Update UI tombol
    document.querySelectorAll('.das-level-btn').forEach(btn => {
        btn.classList.remove('selected');
    });
    document.getElementById(`das-btn-${level}`).classList.add('selected');

    // Sembunyikan panel lama
    document.getElementById('das-info-panel').classList.add('hidden');
    document.getElementById('das-error-panel').classList.add('hidden');

    // Loading
    document.getElementById('step2-loading').classList.remove('hidden');

    // Fetch DAS polygon dari GEE via API server RIVANA
    fetchDasPolygon(clickedLat, clickedLng, level);
}

async function fetchDasPolygon(lat, lng, level) {
    try {
        // Endpoint ini memanggil Python RIVANA API untuk ambil polygon DAS
        // Sesuaikan URL dengan endpoint API server kamu
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

        if (!data.success) throw new Error(data.message || 'DAS tidak ditemukan');

        // Tampilkan polygon di peta
        displayDasPolygon(data.geometry, data);

        // Isi hidden fields
        document.getElementById('das_level').value    = level;
        document.getElementById('das_name').value     = data.name || `DAS Level ${level}`;
        document.getElementById('das_area_km2').value = data.area_km2 || 0;
        document.getElementById('hybas_id').value     = data.hybas_id || '';

        // Tampilkan info panel
        document.getElementById('das-info-name').textContent  = data.name || `DAS Level ${level}`;
        document.getElementById('das-info-area').textContent  = `${parseFloat(data.area_km2 || 0).toFixed(1)} km²`;
        document.getElementById('das-info-level').textContent = level;
        document.getElementById('das-info-panel').classList.remove('hidden');

        // Update step dots
        document.getElementById('step3-dot').className = 'step-dot active';
        document.getElementById('step4-dot').className = 'step-dot active';
        dasReady = true;

    } catch (err) {
        console.error('DAS fetch error:', err);
        document.getElementById('das-error-msg').textContent =
            err.message.includes('404') || err.message.includes('tidak ditemukan')
            ? `Level ${level} tidak tersedia untuk lokasi ini. Coba level lain.`
            : `Gagal memuat DAS: ${err.message}`;
        document.getElementById('das-error-panel').classList.remove('hidden');
        dasReady = false;

        // Reset hidden fields
        document.getElementById('das_level').value    = '';
        document.getElementById('das_name').value     = '';
        document.getElementById('das_area_km2').value = '';
        document.getElementById('hybas_id').value     = '';
    } finally {
        document.getElementById('step2-loading').classList.add('hidden');
        checkSubmitReady();
    }
}

function displayDasPolygon(geometry, dasData) {
    // Hapus polygon lama
    if (dasPolygonLayer) {
        map.removeLayer(dasPolygonLayer);
    }

    if (!geometry) return;

    try {
        dasPolygonLayer = L.geoJSON(geometry, {
            style: {
                color:       '#2563eb',
                weight:      3,
                opacity:     0.9,
                fillColor:   '#3b82f6',
                fillOpacity: 0.12,
                dashArray:   null,
            }
        })
        .bindPopup(`
            <div style="min-width:180px">
                <b style="font-size:14px">${dasData.name || 'DAS'}</b><br>
                <span style="color:#6b7280;font-size:12px">Luas: ${parseFloat(dasData.area_km2||0).toFixed(1)} km²</span><br>
                <span style="color:#6b7280;font-size:12px">Level: ${dasData.level || selectedLevel}</span>
            </div>
        `)
        .addTo(map);

        // Zoom ke polygon
        map.fitBounds(dasPolygonLayer.getBounds(), { padding: [30, 30] });

    } catch (e) {
        console.warn('Gagal render polygon DAS:', e);
    }
}

function resetDasSelection() {
    // Reset pilihan level
    selectedLevel = null;
    dasReady      = false;
    document.querySelectorAll('.das-level-btn').forEach(btn => btn.classList.remove('selected'));
    document.getElementById('das-info-panel').classList.add('hidden');
    document.getElementById('das-error-panel').classList.add('hidden');
    if (dasPolygonLayer) { map.removeLayer(dasPolygonLayer); dasPolygonLayer = null; }
    document.getElementById('das_level').value    = '';
    document.getElementById('das_name').value     = '';
    document.getElementById('das_area_km2').value = '';
    document.getElementById('hybas_id').value     = '';
    document.getElementById('step2-dot').className = 'step-dot inactive';
    document.getElementById('step3-dot').className = 'step-dot inactive';
    document.getElementById('step4-dot').className = 'step-dot inactive';
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
// VALIDASI & SUBMIT BUTTON
// ════════════════════════════════════════════════════════════════
function validateDates() {
    const s = document.getElementById('start_date').value;
    const e = document.getElementById('end_date').value;
    if (s && e && new Date(e) < new Date(s)) {
        document.getElementById('end_date').value = '';
    }
    checkSubmitReady();
}

function checkSubmitReady() {
    const btn      = document.getElementById('submitBtn');
    const txtEl    = document.getElementById('submitText');
    const hintDiv  = document.getElementById('submit-hint');
    const hintTxt  = document.getElementById('submit-hint-text');

    const hasClick = !!clickedLat;
    const hasDas   = dasReady;
    const hasStart = !!document.getElementById('start_date').value;
    const hasEnd   = !!document.getElementById('end_date').value;

    if (!hasClick) {
        btn.disabled = true;
        txtEl.textContent = 'Klik peta terlebih dahulu';
        hintDiv.classList.add('hidden');
    } else if (!hasDas) {
        btn.disabled = true;
        txtEl.textContent = 'Pilih level DAS terlebih dahulu';
        hintDiv.classList.add('hidden');
    } else if (!hasStart || !hasEnd) {
        btn.disabled = true;
        txtEl.textContent = 'Lengkapi periode analisis';
        hintDiv.classList.add('hidden');
    } else {
        btn.disabled = false;
        txtEl.textContent = '{{ __("messages.create_analysis") }}';
        hintDiv.classList.add('hidden');
    }
}

// ════════════════════════════════════════════════════════════════
// FORM SUBMIT
// ════════════════════════════════════════════════════════════════
function handleSubmit(e) {
    e.preventDefault();

    // Validasi akhir
    if (!clickedLat || !clickedLng) {
        Swal.fire({ icon:'warning', title:'Belum Ada Titik', text:'Klik peta untuk menentukan lokasi.' });
        return;
    }
    if (!dasReady || !document.getElementById('das_level').value) {
        Swal.fire({ icon:'warning', title:'DAS Belum Dipilih', text:'Pilih level DAS terlebih dahulu.' });
        return;
    }

    const submitBtn  = document.getElementById('submitBtn');
    const submitIcon = document.getElementById('submitIcon');
    const submitText = document.getElementById('submitText');
    submitBtn.disabled = true;
    submitIcon.className = 'fas fa-spinner fa-spin';
    submitText.textContent = 'Mengirim...';

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
            throw new Error('Respon bukan JSON');
        return r.json();
    })
    .then(data => {
        if (data.success || data.job_id) {
            Swal.fire({
                icon: 'success', title: 'Berhasil!',
                text: data.message || 'Analisis berhasil dikirim!',
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
        submitText.textContent = '{{ __("messages.create_analysis") }}';

        const msg = err.name === 'AbortError'
            ? 'Waktu habis! Periksa koneksi ke server RIVANA.'
            : err.message;

        Swal.fire({
            icon: 'error', title: 'Gagal!',
            html: `<p>${msg}</p>`,
            footer: '<p class="text-xs">Periksa konsol browser (F12) untuk detail</p>'
        });
    });
}
</script>
@endpush