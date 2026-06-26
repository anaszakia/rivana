@extends('layouts.app')

@section('title', __('messages.job_detail') . ' - ' . $job->job_id)

@section('content')
<div class="w-full max-w-7xl mx-auto px-3 sm:px-4 lg:px-6 py-4 sm:py-6 pt-20 lg:pt-6">
    <!-- Modern Header Banner -->
    <div class="mb-6">
        <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-3xl shadow-2xl">
            <!-- Animated Background Circles -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute w-96 h-96 -top-48 -right-48 bg-white rounded-full animate-pulse"></div>
                <div class="absolute w-64 h-64 -bottom-32 -left-32 bg-white rounded-full animate-pulse" style="animation-delay: 1s;"></div>
                <div class="absolute w-40 h-40 top-1/2 left-1/3 bg-white rounded-full animate-pulse" style="animation-delay: 2s;"></div>
            </div>
            
            <div class="relative z-10 p-5 sm:p-8">
                <!-- Breadcrumb -->
                <div class="flex items-center gap-2 text-blue-100 mb-4 text-xs sm:text-sm flex-wrap">
                    <a href="{{ route('hidrologi.index') }}" class="flex items-center gap-1 hover:text-white transition-colors bg-white/10 backdrop-blur-sm px-3 py-1.5 rounded-lg">
                        <i class="fas fa-water"></i>
                        <span class="hidden sm:inline">{{ __('messages.hydrology') }}</span>
                    </a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span class="text-white font-bold bg-white/20 backdrop-blur-sm px-3 py-1.5 rounded-lg">{{ __('messages.job_detail') }}</span>
                </div>
                
                <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center gap-4">
                    <div class="flex items-center gap-3 sm:gap-4">
                        <div class="w-12 h-12 sm:w-16 sm:h-16 bg-white/20 backdrop-blur-sm rounded-2xl flex items-center justify-center shrink-0">
                            <i class="fas fa-file-alt text-2xl sm:text-3xl text-white"></i>
                        </div>
                        <div class="min-w-0">
                            <h1 class="text-xl sm:text-2xl lg:text-3xl font-extrabold text-white mb-1 tracking-tight">{{ __('messages.job_detail') }}</h1>
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs text-blue-100">{{ __('messages.job_id') }}:</span>
                                <span class="text-blue-100 text-xs sm:text-sm font-mono bg-black/20 px-2 sm:px-3 py-1 rounded-lg truncate">{{ $job->job_id }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-2">
                        @if(in_array($job->status, ['pending', 'submitted', 'processing']))
                            <button onclick="cancelJob({{ $job->id }})" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-yellow-500 to-orange-600 hover:from-yellow-600 hover:to-orange-700 text-white rounded-xl transition-all shadow-lg font-bold hover:scale-105">
                                <i class="fas fa-stop-circle"></i>
                                <span class="hidden sm:inline">{{ __('messages.cancel_job') }}</span>
                                <span class="sm:hidden">{{ __('messages.cancel') }}</span>
                            </button>
                        @endif
                        
                        <button onclick="deleteJob({{ $job->id }})" class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white rounded-xl transition-all shadow-lg font-bold hover:scale-105">
                            <i class="fas fa-trash"></i>
                            <span class="hidden sm:inline">{{ __('messages.delete') }}</span>
                            <span class="sm:hidden">{{ __('messages.delete') }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modern Status Card -->
    <div class="bg-white rounded-3xl shadow-xl p-5 sm:p-6 lg:p-8 mb-6 border border-gray-100">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex items-center gap-4 sm:gap-6">
                @php
                    $statusConfig = [
                        'pending' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-clock', 'ring' => 'ring-gray-300', 'gradient' => 'from-gray-400 to-gray-500', 'label' => __('messages.waiting')],
                        'submitted' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-paper-plane', 'ring' => 'ring-blue-300', 'gradient' => 'from-blue-400 to-blue-600', 'label' => __('messages.sent')],
                        'processing' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'fa-spinner fa-spin', 'ring' => 'ring-yellow-300', 'gradient' => 'from-yellow-400 to-yellow-600', 'label' => __('messages.processed')],
                        'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-check-circle', 'ring' => 'ring-green-300', 'gradient' => 'from-green-400 to-green-600', 'label' => __('messages.completed')],
                        'completed_with_warning' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'icon' => 'fa-exclamation-triangle', 'ring' => 'ring-orange-300', 'gradient' => 'from-orange-400 to-orange-600', 'label' => __('messages.completed_with_warning')],
                        'failed' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'fa-times-circle', 'ring' => 'ring-red-300', 'gradient' => 'from-red-400 to-red-600', 'label' => __('messages.failed')],
                        'cancelled' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-ban', 'ring' => 'ring-gray-300', 'gradient' => 'from-gray-400 to-gray-500', 'label' => __('messages.cancelled')]
                    ];
                    $config = $statusConfig[$job->status] ?? $statusConfig['pending'];
                @endphp
                <div class="relative shrink-0">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 {{ $config['bg'] }} rounded-2xl flex items-center justify-center ring-4 {{ $config['ring'] }} shadow-lg">
                        <i class="fas {{ $config['icon'] }} text-2xl sm:text-3xl {{ $config['text'] }}"></i>
                    </div>
                    <div class="absolute -bottom-2 -right-2 w-6 h-6 bg-gradient-to-br {{ $config['gradient'] }} rounded-full border-2 border-white shadow-md"></div>
                </div>
                <div class="flex-1 min-w-0">
                    <h3 class="text-xl sm:text-2xl lg:text-3xl font-extrabold {{ $config['text'] }} mb-1">{{ $config['label'] }}</h3>
                    <p class="text-gray-600 text-xs sm:text-sm truncate">{{ $job->status_message ?? __('messages.processing_job') }}</p>
                </div>
            </div>
            
            <!-- Progress Bar -->
            @if(in_array($job->status, ['pending', 'submitted', 'processing']))
                <div class="w-full lg:w-80">
                    <div class="flex justify-between text-xs sm:text-sm font-bold mb-2">
                        <span class="text-gray-700">{{ __('messages.progress') }}</span>
                        <span id="progress-percent" class="text-blue-600">{{ $job->progress }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-4 shadow-inner overflow-hidden">
                        <div id="progress-bar" class="bg-gradient-to-r from-blue-500 via-blue-600 to-indigo-600 h-4 rounded-full transition-all duration-500 shadow-lg" style="width: {{ $job->progress }}%"></div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Warning/Error Messages -->
        @if($job->warning_message)
            <div class="mt-4 sm:mt-6 p-3 sm:p-4 bg-gradient-to-r from-orange-50 to-yellow-50 border-l-4 border-orange-500 rounded-lg sm:rounded-xl shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-orange-600 text-sm sm:text-lg"></i>
                    </div>
                    <div class="ml-3 sm:ml-4">
                        <h4 class="font-bold text-orange-800 text-base sm:text-lg">{{ __('messages.warning') }}</h4>
                        <p class="text-xs sm:text-sm text-orange-700 mt-1">{{ $job->warning_message }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($job->error_message)
            <div class="mt-4 sm:mt-6 p-3 sm:p-4 bg-gradient-to-r from-red-50 to-pink-50 border-l-4 border-red-500 rounded-lg sm:rounded-xl shadow-sm">
                <div class="flex items-start">
                    <div class="flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 bg-red-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-times-circle text-red-600 text-sm sm:text-lg"></i>
                    </div>
                    <div class="ml-3 sm:ml-4">
                        <h4 class="font-bold text-red-800 text-base sm:text-lg">{{ __('messages.error') }}</h4>
                        <p class="text-xs sm:text-sm text-red-700 mt-1">{{ $job->error_message }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Main Content Wrapper -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 sm:gap-6 mb-6">
        <!-- Main Content Column (Left) -->
        <div class="lg:col-span-2 space-y-5 sm:space-y-6 order-2 lg:order-1">
            <!-- Location Info Card -->
            <div class="bg-white rounded-3xl shadow-xl p-5 sm:p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300">
                <div class="flex items-center gap-3 mb-5 sm:mb-6">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-red-500 to-red-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-map-marker-alt text-white text-base sm:text-lg"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-extrabold text-gray-900">{{ __('messages.location_info') }}</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl border-2 border-blue-200">
                        <p class="text-xs sm:text-sm font-bold text-blue-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-map-pin"></i>{{ __('messages.location_name') }}
                        </p>
                        <p class="font-extrabold text-gray-900 text-sm sm:text-base">{{ $job->location_name ?? __('messages.not_available') }}</p>
                    </div>
                    <div class="p-4 bg-gradient-to-br from-green-50 to-green-100 rounded-2xl border-2 border-green-200">
                        <p class="text-xs sm:text-sm font-bold text-green-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-globe"></i>{{ __('messages.coordinates') }}
                        </p>
                        <p class="font-extrabold text-gray-900 text-xs sm:text-sm break-all">{{ $job->latitude }}, {{ $job->longitude }}</p>
                    </div>
                    @if($job->location_description)
                        <div class="col-span-1 sm:col-span-2 p-4 bg-gradient-to-br from-purple-50 to-purple-100 rounded-2xl border-2 border-purple-200">
                            <p class="text-xs sm:text-sm font-bold text-purple-700 mb-2 flex items-center gap-2">
                                <i class="fas fa-file-alt"></i>{{ __('messages.description') }}
                            </p>
                            <p class="text-gray-800 text-sm">{{ $job->location_description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Analysis Period Card -->
            <div class="bg-white rounded-3xl shadow-xl p-5 sm:p-6 border border-gray-100 hover:shadow-2xl transition-all duration-300">
                <div class="flex items-center gap-3 mb-5 sm:mb-6">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg shrink-0">
                        <i class="fas fa-calendar-alt text-white text-base sm:text-lg"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-extrabold text-gray-900">{{ __('messages.analysis_period') }}</h3>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-4 bg-gradient-to-br from-green-50 to-emerald-100 rounded-2xl border-2 border-green-200">
                        <p class="text-xs sm:text-sm font-bold text-green-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-calendar-check"></i>{{ __('messages.start_date') }}
                        </p>
                        <p class="font-extrabold text-gray-900 text-sm sm:text-base">{{ \Carbon\Carbon::parse($job->start_date)->format('d F Y') }}</p>
                    </div>
                    <div class="p-4 bg-gradient-to-br from-red-50 to-red-100 rounded-2xl border-2 border-red-200">
                        <p class="text-xs sm:text-sm font-bold text-red-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-calendar-times"></i>{{ __('messages.end_date') }}
                        </p>
                        <p class="font-extrabold text-gray-900 text-sm sm:text-base">{{ \Carbon\Carbon::parse($job->end_date)->format('d F Y') }}</p>
                    </div>
                    <div class="col-span-1 sm:col-span-2 p-4 bg-gradient-to-br from-blue-50 to-indigo-100 rounded-2xl border-2 border-blue-200">
                        <p class="text-xs sm:text-sm font-bold text-blue-700 mb-2 flex items-center gap-2">
                            <i class="fas fa-hourglass-half"></i>{{ __('messages.duration') }}
                        </p>
                        <p class="font-extrabold text-gray-900 text-sm sm:text-base">
                            {{ \Carbon\Carbon::parse($job->start_date)->diffInDays(\Carbon\Carbon::parse($job->end_date)) + 1 }} {{ __('messages.days') }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Analysis Summary - STRUCTURED (Ringkasan Terstruktur) -->
            @if($summary)
                @php
                    // DEBUG: Log TWI analysis availability untuk debugging di VPS
                    \Log::info('TWI Analysis Debug', [
                        'job_id' => $job->id ?? 'unknown',
                        'has_twi_analysis' => isset($summary['twi_analysis']),
                        'twi_is_array' => isset($summary['twi_analysis']) && is_array($summary['twi_analysis']),
                        'twi_status' => $summary['twi_analysis']['status'] ?? 'no_status_key',
                        'twi_keys' => isset($summary['twi_analysis']) ? array_keys($summary['twi_analysis']) : [],
                        'twi_enhanced_value' => $summary['twi_analysis']['twi_enhanced'] ?? 'not_found',
                        'twi_risk_level' => $summary['twi_analysis']['risk_level'] ?? 'not_found',
                    ]);
                @endphp
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg shadow-md p-6 border border-blue-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-blue-900 flex items-center">
                            <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                            📊 {{ strtoupper(__('messages.analysis_summary')) }}
                        </h3>
                        <span class="text-xs text-blue-700 bg-blue-100 px-3 py-1 rounded-full">
                            <i class="fas fa-layer-group mr-1"></i>{{ __('messages.structured_summary') }}
                        </span>
                    </div>

                    <!-- Job Info -->
                    @if(isset($summary['job_info']))
                        <div class="bg-white rounded-lg p-4 mb-4 shadow-sm border border-gray-200">
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                {{ __('messages.job_info') }}
                            </h4>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <span class="text-gray-600">{{ __('messages.job_id') }}:</span>
                                    <span class="font-mono text-gray-800 ml-2 text-xs">{{ $summary['job_info']['job_id'] ?? __('messages.n_a') }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">{{ __('messages.status') }}:</span>
                                    <span class="font-semibold text-green-600 ml-2">{{ ucfirst(trans_api($summary['job_info']['status'] ?? __('messages.n_a'), 'status_umum')) }}</span>
                                </div>
                                @if(isset($summary['job_info']['created_at']))
                                    <div>
                                        <span class="text-gray-600">{{ __('messages.created') }}:</span>
                                        <span class="text-gray-800 ml-2 text-xs">{{ $summary['job_info']['created_at'] }}</span>
                                    </div>
                                @endif
                                @if(isset($summary['job_info']['completed_at']))
                                    <div>
                                        <span class="text-gray-600">{{ __('messages.completed_at') }}:</span>
                                        <span class="text-gray-800 ml-2 text-xs">{{ $summary['job_info']['completed_at'] }}</span>
                                    </div>
                                @endif
                                @if(isset($summary['job_info']['files_generated']))
                                    <div class="col-span-2 pt-2 border-t">
                                        <span class="text-gray-600 block mb-2">{{ __('messages.files_generated') }}:</span>
                                        <div class="flex space-x-4">
                                            <span class="text-blue-600"><i class="fas fa-image mr-1"></i>{{ $summary['job_info']['files_generated']['png'] ?? 0 }} PNG</span>
                                            <span class="text-green-600"><i class="fas fa-file-csv mr-1"></i>{{ $summary['job_info']['files_generated']['csv'] ?? 0 }} CSV</span>
                                            <span class="text-orange-600"><i class="fas fa-file-code mr-1"></i>{{ $summary['job_info']['files_generated']['json'] ?? 0 }} JSON</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Input Parameters -->
                    @if(isset($summary['input_parameters']))
                        <div class="bg-white rounded-lg p-4 mb-4 shadow-sm border border-gray-200">
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-cog text-purple-500 mr-2"></i>
                                {{ __('messages.input_parameters') }}
                            </h4>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <span class="text-gray-600">{{ __('messages.longitude') }}:</span>
                                    <span class="font-medium text-gray-800 ml-2">{{ $summary['input_parameters']['longitude'] ?? __('messages.n_a') }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">{{ __('messages.latitude') }}:</span>
                                    <span class="font-medium text-gray-800 ml-2">{{ $summary['input_parameters']['latitude'] ?? __('messages.n_a') }}</span>
                                </div>
                                <div class="col-span-2">
                                    <span class="text-gray-600">{{ __('messages.period') }}:</span>
                                    <span class="font-medium text-gray-800 ml-2">{{ $summary['input_parameters']['periode_analisis'] ?? __('messages.n_a') }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Statistik Data -->
                    @if(isset($summary['statistik_data']))
                        <div class="bg-white rounded-lg p-4 mb-4 shadow-sm border border-gray-200">
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-chart-bar text-green-500 mr-2"></i>
                                {{ __('messages.data_statistics') }}
                            </h4>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between items-center pb-2 border-b">
                                    <span class="text-gray-600 font-medium">{{ __('messages.total_analysis_days') }}:</span>
                                    <span class="font-bold text-blue-700 text-lg">{{ $summary['statistik_data']['total_hari'] ?? __('messages.n_a') }} {{ __('messages.days') }}</span>
                                </div>
                                
                                @if(isset($summary['statistik_data']['curah_rainfall']))
                                    <div class="bg-blue-50 rounded p-3">
                                        <p class="font-medium text-blue-900 mb-2 flex items-center">
                                            <i class="fas fa-cloud-rain text-blue-600 mr-2"></i>
                                            {{ __('messages.rainfall') }}
                                        </p>
                                        <div class="grid grid-cols-2 gap-2 text-xs">
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">{{ __('messages.average') }}</div>
                                                <div class="font-bold text-blue-700">{{ $summary['statistik_data']['curah_rainfall']['rata_rata'] ?? __('messages.n_a') }}</div>
                                            </div>
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">{{ __('messages.maximum') }}</div>
                                                <div class="font-bold text-red-600">{{ $summary['statistik_data']['curah_rainfall']['maximum'] ?? __('messages.n_a') }}</div>
                                            </div>
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">{{ __('messages.minimum') }}</div>
                                                <div class="font-bold text-green-600">{{ $summary['statistik_data']['curah_rainfall']['minimum'] ?? __('messages.n_a') }}</div>
                                            </div>
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">{{ __('messages.total') }}</div>
                                                <div class="font-bold text-purple-600">{{ $summary['statistik_data']['curah_rainfall']['total'] ?? __('messages.n_a') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if(isset($summary['statistik_data']['volume_reservoir']))
                                    <div class="bg-cyan-50 rounded p-3">
                                        <p class="font-medium text-cyan-900 mb-2 flex items-center">
                                            <i class="fas fa-water text-cyan-600 mr-2"></i>
                                            {{ __('messages.retention_pond_volume') }}
                                        </p>
                                        <div class="grid grid-cols-2 gap-2 text-xs">
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">{{ __('messages.average') }}</div>
                                                <div class="font-bold text-cyan-700">{{ $summary['statistik_data']['volume_reservoir']['rata_rata'] ?? __('messages.n_a') }}</div>
                                            </div>
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">{{ __('messages.maximum') }}</div>
                                                <div class="font-bold text-blue-600">{{ $summary['statistik_data']['volume_reservoir']['maximum'] ?? __('messages.n_a') }}</div>
                                            </div>
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">{{ __('messages.minimum') }}</div>
                                                <div class="font-bold text-orange-600">{{ $summary['statistik_data']['volume_reservoir']['minimum'] ?? __('messages.n_a') }}</div>
                                            </div>
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">{{ __('messages.end_of_period') }}</div>
                                                <div class="font-bold text-indigo-600">{{ $summary['statistik_data']['volume_reservoir']['akhir_periode'] ?? __('messages.n_a') }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if(isset($summary['statistik_data']['reliability_sistem']))
                                    <div class="bg-green-50 rounded p-3">
                                        <p class="font-medium text-green-900 mb-2 flex items-center">
                                            <i class="fas fa-shield-alt text-green-600 mr-2"></i>
                                            {{ __('messages.system_reliability') }}
                                        </p>
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <div class="text-gray-600 text-xs">{{ __('messages.average') }}</div>
                                                <div class="font-bold text-2xl text-green-700">{{ $summary['statistik_data']['reliability_sistem']['rata_rata'] ?? __('messages.n_a') }}</div>
                                            </div>
                                            <div class="text-right">
                                                <span class="px-4 py-2 rounded-full text-sm font-bold {{ 
                                                    strpos($summary['statistik_data']['reliability_sistem']['status'] ?? '', 'Sangat Baik') !== false ? 'bg-green-200 text-green-900' : 
                                                    (strpos($summary['statistik_data']['reliability_sistem']['status'] ?? '', 'Baik') !== false ? 'bg-blue-200 text-blue-900' : 
                                                    'bg-yellow-200 text-yellow-900') 
                                                }}">
                                                    {{ trans_api($summary['statistik_data']['reliability_sistem']['status'] ?? 'N/A', 'status_keandalan') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Hasil Analisis -->
                    @if(isset($summary['analisis_keseimbangan_air']) || isset($summary['analisis_kondisi_sungai_soil_storage']))
                        <div class="bg-white rounded-lg p-4 mb-4 shadow-sm border border-gray-200">
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-microscope text-indigo-500 mr-2"></i>
                                {{ __('messages.analysis_results') }}
                            </h4>
                            <div class="space-y-3 text-sm">
                                @if(isset($summary['analisis_keseimbangan_air']['komponen_output']))
                                    <div class="bg-blue-50 rounded p-3">
                                        <p class="font-medium text-blue-900 mb-2 flex items-center">
                                            <i class="fas fa-arrow-up text-blue-600 mr-2"></i>
                                            {{ __('messages.output_components') ?? 'Output Components' }}
                                        </p>
                                        <div class="grid grid-cols-2 gap-2 text-xs mb-2">
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">{{ __('messages.total_supply') }}</div>
                                                <div class="font-bold text-green-700">{{ $summary['analisis_keseimbangan_air']['komponen_output']['total_supply'] ?? 'N/A' }}</div>
                                            </div>
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">Evapotranspiration</div>
                                                <div class="font-bold text-orange-700">{{ $summary['analisis_keseimbangan_air']['komponen_output']['evapotranspirasi'] ?? 'N/A' }}</div>
                                            </div>
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">Runoff</div>
                                                <div class="font-bold text-blue-700">{{ $summary['analisis_keseimbangan_air']['komponen_output']['runoff'] ?? 'N/A' }}</div>
                                            </div>
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">Total Output</div>
                                                <div class="font-bold text-purple-700">{{ $summary['analisis_keseimbangan_air']['komponen_output']['total_output'] ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if(isset($summary['analisis_keseimbangan_air']['komponen_input']))
                                    <div class="bg-green-50 rounded p-3">
                                        <p class="font-medium text-green-900 mb-2 flex items-center">
                                            <i class="fas fa-arrow-down text-green-600 mr-2"></i>
                                            {{ __('messages.input_components') }}
                                        </p>
                                        <div class="grid grid-cols-2 gap-2 text-xs mb-2">
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">{{ __('messages.rainfall') }}</div>
                                                <div class="font-bold text-blue-700">{{ $summary['analisis_keseimbangan_air']['komponen_input']['rainfall'] ?? '0.00 mm' }}</div>
                                            </div>
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">River Inflow</div>
                                                <div class="font-bold text-cyan-700">
                                                    @php
                                                        $inflow = $summary['analisis_keseimbangan_air']['komponen_input']['inflow_sungai'] ?? 'N/A';
                                                        echo ($inflow === 'N/A') ? '0.00 mm' : $inflow;
                                                    @endphp
                                                </div>
                                            </div>
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">Groundwater</div>
                                                <div class="font-bold text-teal-700">
                                                    @php
                                                        $groundwater = $summary['analisis_keseimbangan_air']['komponen_input']['groundwater_recharge'] ?? 'N/A';
                                                        echo ($groundwater === 'N/A') ? '0.00 mm' : $groundwater;
                                                    @endphp
                                                </div>
                                            </div>
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">Total Input</div>
                                                <div class="font-bold text-green-700">{{ $summary['analisis_keseimbangan_air']['komponen_input']['total_input'] ?? '0.00 mm' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if(isset($summary['analisis_kondisi_sungai_soil_storage']['morfologi_sungai']))
                                    <div class="bg-cyan-50 rounded p-3">
                                        <p class="font-medium text-cyan-900 mb-2 flex items-center">
                                            <i class="fas fa-water text-cyan-600 mr-2"></i>
                                            {{ __('messages.river_morphology') }}
                                        </p>
                                        <div class="grid grid-cols-2 gap-2 text-xs mb-2">
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">{{ __('messages.river_width') }}</div>
                                                <div class="font-bold text-blue-700">{{ $summary['analisis_kondisi_sungai_soil_storage']['morfologi_sungai']['lebar_sungai']['rata_rata'] ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500 mt-1">{{ $summary['analisis_kondisi_sungai_soil_storage']['morfologi_sungai']['lebar_sungai']['status'] ?? '' }}</div>
                                            </div>
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">{{ __('messages.slope') }}</div>
                                                <div class="font-bold text-purple-700">{{ $summary['analisis_kondisi_sungai_soil_storage']['morfologi_sungai']['kemiringan']['rata_rata'] ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500 mt-1">{{ $summary['analisis_kondisi_sungai_soil_storage']['morfologi_sungai']['kemiringan']['kategori'] ?? '' }}</div>
                                            </div>
                                            <div class="bg-white rounded p-2 col-span-2">
                                                <div class="text-gray-600">{{ __('messages.sediment_load') }}</div>
                                                <div class="font-bold text-orange-700">{{ $summary['analisis_kondisi_sungai_soil_storage']['morfologi_sungai']['beban_sediment']['rata_rata'] ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500 mt-1">{{ $summary['analisis_kondisi_sungai_soil_storage']['morfologi_sungai']['beban_sediment']['status'] ?? '' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if(isset($summary['analisis_kondisi_sungai_soil_storage']['kondisi_soil_storage']))
                                    <div class="bg-amber-50 rounded p-3">
                                        <p class="font-medium text-amber-900 mb-2 flex items-center">
                                            <i class="fas fa-mountain text-amber-600 mr-2"></i>
                                            {{ __('messages.soil_storage_condition') }}
                                        </p>
                                        <div class="grid grid-cols-2 gap-2 text-xs mb-2">
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">{{ __('messages.soil_moisture') }}</div>
                                                <div class="font-bold text-blue-700">{{ $summary['analisis_kondisi_sungai_soil_storage']['kondisi_soil_storage']['soil_moisture']['rata_rata'] ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500 mt-1">{{ $summary['analisis_kondisi_sungai_soil_storage']['kondisi_soil_storage']['soil_moisture']['status'] ?? '' }}</div>
                                            </div>
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">{{ __('messages.infiltration') }}</div>
                                                <div class="font-bold text-green-700">{{ $summary['analisis_kondisi_sungai_soil_storage']['kondisi_soil_storage']['infiltration']['rata_rata'] ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500 mt-1">{{ $summary['analisis_kondisi_sungai_soil_storage']['kondisi_soil_storage']['infiltration']['capacity'] ?? '' }}</div>
                                            </div>
                                            <div class="bg-white rounded p-2 col-span-2">
                                                <div class="text-gray-600">{{ __('messages.percolation') }}</div>
                                                <div class="font-bold text-teal-700">{{ $summary['analisis_kondisi_sungai_soil_storage']['kondisi_soil_storage']['percolation']['rata_rata'] ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500 mt-1">To groundwater: {{ $summary['analisis_kondisi_sungai_soil_storage']['kondisi_soil_storage']['percolation']['ke_groundwater'] ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if(isset($summary['ekologi']['ecosystem_health']) && 
                                    ($summary['ekologi']['ecosystem_health']['index'] ?? 'N/A') !== 'N/A' &&
                                    ($summary['ekologi']['ecosystem_health']['index'] ?? 'N/A') !== 'Data not available')
                                    <div class="bg-green-50 rounded p-3">
                                        <p class="font-medium text-green-900 mb-2 flex items-center">
                                            <i class="fas fa-leaf text-green-600 mr-2"></i>
                                            {{ __('messages.ecosystem_health') }}
                                        </p>
                                        <div class="grid grid-cols-2 gap-2 text-xs mb-2">
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">{{ __('messages.health_index') }}</div>
                                                <div class="font-bold text-green-700">{{ $summary['ekologi']['ecosystem_health']['index'] }}</div>
                                                <div class="text-xs text-gray-500 mt-1">{{ trans_api($summary['ekologi']['ecosystem_health']['status'] ?? 'N/A', 'status_ekosistem') }}</div>
                                            </div>
                                            @if(isset($summary['ekologi']['ecosystem_health']['habitat_fish']) && 
                                                $summary['ekologi']['ecosystem_health']['habitat_fish'] !== 'N/A')
                                                <div class="bg-white rounded p-2">
                                                    <div class="text-gray-600">{{ __('messages.fish_habitat') }} (HSI)</div>
                                                    <div class="font-bold text-blue-700">{{ $summary['ekologi']['ecosystem_health']['habitat_fish'] }}</div>
                                                </div>
                                            @endif
                                        </div>
                                        @if(isset($summary['ekologi']['ecosystem_health']['habitat_vegetation']) && 
                                            $summary['ekologi']['ecosystem_health']['habitat_vegetation'] !== 'N/A')
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600 text-xs">{{ __('messages.vegetation_habitat') }}</div>
                                                <div class="font-bold text-green-700">{{ $summary['ekologi']['ecosystem_health']['habitat_vegetation'] }}</div>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Water Balance -->
                    @if(isset($summary['water_balance']))
                        <div class="bg-white rounded-lg p-4 mb-4 shadow-sm border border-gray-200">
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-balance-scale text-cyan-500 mr-2"></i>
                                {{ __('messages.water_balance') }}
                            </h4>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="bg-green-50 rounded p-3">
                                    <div class="text-gray-600 text-xs mb-1">{{ __('messages.total_input') }}</div>
                                    <div class="font-bold text-lg text-green-700">{{ $summary['water_balance']['total_input'] ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-arrow-down text-green-600"></i> {{ __('messages.rainfall') }}
                                    </div>
                                </div>
                                <div class="bg-red-50 rounded p-3">
                                    <div class="text-gray-600 text-xs mb-1">{{ __('messages.total_output') }}</div>
                                    <div class="font-bold text-lg text-red-700">{{ $summary['water_balance']['total_output'] ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-arrow-up text-red-600"></i> ET + Runoff + ΔS
                                    </div>
                                </div>
                                <div class="bg-blue-50 rounded p-3">
                                    <div class="text-gray-600 text-xs mb-1">{{ __('messages.residual') }}</div>
                                    <div class="font-bold text-lg text-blue-700">{{ $summary['water_balance']['residual'] ?? 'N/A' }}</div>
                                </div>
                                <div class="bg-orange-50 rounded p-3">
                                    <div class="text-gray-600 text-xs mb-1">{{ __('messages.error') }}</div>
                                    <div class="font-bold text-lg text-orange-700">{{ $summary['water_balance']['error_persen'] ?? 'N/A' }}</div>
                                </div>
                                <div class="col-span-2 mt-2 pt-3 border-t text-center">
                                    <div class="text-gray-600 text-xs mb-2">{{ __('messages.status_balance') }}</div>
                                    <span class="px-4 py-2 rounded-full text-sm font-bold {{ 
                                        strpos($summary['water_balance']['status'] ?? '', 'Sangat Baik') !== false ? 'bg-green-200 text-green-900' : 
                                        (strpos($summary['water_balance']['status'] ?? '', 'Baik') !== false ? 'bg-blue-200 text-blue-900' : 
                                        (strpos($summary['water_balance']['status'] ?? '', 'Cukup') !== false ? 'bg-yellow-200 text-yellow-900' : 
                                        'bg-red-200 text-red-900')) 
                                    }}">
                                        {{ trans_api($summary['water_balance']['status'] ?? 'N/A', 'status_balance') }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Kualitas Data -->
                    @if(isset($summary['kualitas_data']))
                        <div class="bg-white rounded-lg p-4 mb-4 shadow-sm border border-gray-200">
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-check-circle text-teal-500 mr-2"></i>
                                {{ __('messages.data_quality') }}
                            </h4>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between items-center py-2 border-b">
                                    <span class="text-gray-600 flex items-center">
                                        <i class="fas fa-database text-blue-500 mr-2"></i>
                                        {{ __('messages.data_completeness') }}
                                    </span>
                                    <span class="font-semibold text-green-700 text-lg">{{ $summary['kualitas_data']['kelengkapan_data'] ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b">
                                    <span class="text-gray-600 flex items-center">
                                        <i class="fas fa-calendar-check text-purple-500 mr-2"></i>
                                        {{ __('messages.valid_period') }}
                                    </span>
                                    <span class="font-semibold {{ ($summary['kualitas_data']['periode_valid'] ?? '') == 'Ya' ? 'text-green-700' : 'text-red-700' }}">
                                        {{ ($summary['kualitas_data']['periode_valid'] ?? '') == 'Ya' ? '✅ '.__('messages.yes') : '❌ '.__('messages.no') }}
                                    </span>
                                </div>
                                @if(isset($summary['kualitas_data']['file_tersedia']))
                                    <div class="bg-gray-50 rounded p-3">
                                        <p class="text-gray-600 font-medium mb-2 text-xs">{{ __('messages.available_files') }}:</p>
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between bg-white rounded p-2">
                                                <div class="flex items-center">
                                                    <i class="fas fa-chart-bar text-blue-500 mr-2"></i>
                                                    <span class="text-gray-700 text-xs">{{ __('messages.visualization') }}</span>
                                                </div>
                                                <span class="font-bold text-blue-600 text-xs">{{ $summary['kualitas_data']['file_tersedia']['visualisasi'] ?? 'N/A' }}</span>
                                            </div>
                                            <div class="flex items-center justify-between bg-white rounded p-2">
                                                <div class="flex items-center">
                                                    <i class="fas fa-table text-green-500 mr-2"></i>
                                                    <span class="text-gray-700 text-xs">{{ __('messages.data_csv') }}</span>
                                                </div>
                                                <span class="font-bold text-green-600 text-xs">{{ $summary['kualitas_data']['file_tersedia']['data_csv'] ?? 'N/A' }}</span>
                                            </div>
                                            <div class="flex items-center justify-between bg-white rounded p-2">
                                                <div class="flex items-center">
                                                    <i class="fas fa-file-code text-orange-500 mr-2"></i>
                                                    <span class="text-gray-700 text-xs">{{ __('messages.metadata') }}</span>
                                                </div>
                                                <span class="font-bold text-orange-600 text-xs">{{ $summary['kualitas_data']['file_tersedia']['metadata'] ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Rekomendasi -->
                    @if(isset($summary['rekomendasi']) && is_array($summary['rekomendasi']) && count($summary['rekomendasi']) > 0)
                        <div class="bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                                {{ __('messages.management_recommendations') }} ({{ count($summary['rekomendasi']) }})
                            </h4>
                            <div class="space-y-3">
                                @foreach($summary['rekomendasi'] as $index => $rekomendasi)
                                    @php
                                        $prioritas = $rekomendasi['prioritas'] ?? 'Normal';
                                        $borderColor = $prioritas == 'Tinggi' ? 'border-red-500' : ($prioritas == 'Sedang' ? 'border-yellow-500' : 'border-blue-500');
                                        $bgColor = $prioritas == 'Tinggi' ? 'bg-red-50' : ($prioritas == 'Sedang' ? 'bg-yellow-50' : 'bg-blue-50');
                                        $badgeColor = $prioritas == 'Tinggi' ? 'bg-red-200 text-red-900' : ($prioritas == 'Sedang' ? 'bg-yellow-200 text-yellow-900' : 'bg-blue-200 text-blue-900');
                                        $iconColor = $prioritas == 'Tinggi' ? 'text-red-600' : ($prioritas == 'Sedang' ? 'text-yellow-600' : 'text-blue-600');
                                    @endphp
                                    <div class="border-l-4 {{ $borderColor }} {{ $bgColor }} p-4 rounded-r-lg hover:shadow-lg transition duration-300">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="flex items-center space-x-2">
                                                <span class="flex items-center justify-center w-6 h-6 rounded-full bg-white {{ $iconColor }} font-bold text-sm">{{ $index + 1 }}</span>
                                                <span class="font-semibold text-gray-800">{{ $rekomendasi['kategori'] ?? 'N/A' }}</span>
                                            </div>
                                            <span class="text-xs px-3 py-1 rounded-full font-bold {{ $badgeColor }}">
                                                @if($prioritas == 'Tinggi')
                                                    <i class="fas fa-exclamation-circle mr-1"></i>
                                                @elseif($prioritas == 'Sedang')
                                                    <i class="fas fa-info-circle mr-1"></i>
                                                @else
                                                    <i class="fas fa-check-circle mr-1"></i>
                                                @endif
                                                {{ trans_api($prioritas, 'priority') }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-700 leading-relaxed pl-8">{{ $rekomendasi['rekomendasi'] ?? 'N/A' }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- ====================== ADDITIONAL DETAILED SECTIONS (Matching Python Output) ====================== -->
                    
                    <!-- BAGIAN 1: PEMBAGIAN & PRIORITAS AIR -->
                    @if(isset($summary['analysis_results']['water_supply_per_sector']) && is_array($summary['analysis_results']['water_supply_per_sector']) && !isset($summary['analysis_results']['water_supply_per_sector']['error']))
                        <div class="bg-white rounded-lg p-4 mt-4 shadow-sm border border-blue-200">
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center bg-blue-50 p-3 rounded">
                                <i class="fas fa-water text-blue-600 mr-2"></i>
                                {{ strtoupper(__('messages.water_distribution_priority')) }}
                            </h4>
                            <div class="space-y-2 text-sm">
                                @foreach($summary['analysis_results']['water_supply_per_sector'] as $sector => $data)
                                    @if(is_array($data))
                                    <div class="bg-gray-50 rounded p-3 hover:bg-gray-100 transition">
                                        <div class="font-medium text-blue-900 mb-2 flex items-center">
                                            <i class="fas fa-tint text-blue-600 mr-2"></i>
                                            {{ trans_api($sector, 'sector') }}
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 text-xs pl-6">
                                            <div><span class="text-gray-600">{{ __('messages.legal_quota') }}:</span> <span class="font-bold">{{ $data['quota'] ?? 'N/A' }}</span></div>
                                            <div><span class="text-gray-600">{{ __('messages.allocation') }}:</span> <span class="font-bold text-green-700">{{ $data['alokasi'] ?? 'N/A' }}</span></div>
                                            <div><span class="text-gray-600">{{ __('messages.priority') }}:</span> <span class="font-bold text-purple-700">{{ $data['prioritas'] ?? 'N/A' }}</span></div>
                                            <div><span class="text-gray-600">{{ __('messages.fulfillment') }}:</span> <span class="font-bold text-blue-700">{{ $data['pemenuhan'] ?? 'N/A' }}</span></div>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- BAGIAN 2: SUMBER-SUMBER AIR -->
                    @if(isset($summary['analysis_results']['water_sources']) && is_array($summary['analysis_results']['water_sources']) && !isset($summary['analysis_results']['water_sources']['error']))
                        <div class="bg-white rounded-lg p-4 mt-4 shadow-sm border border-cyan-200">
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center bg-cyan-50 p-3 rounded">
                                <i class="fas fa-stream text-cyan-600 mr-2"></i>
                                {{ strtoupper(__('messages.water_sources')) }}
                            </h4>
                            <div class="space-y-2 text-sm">
                                @foreach($summary['analysis_results']['water_sources'] as $source => $data)
                                    @if(is_array($data))
                                    <div class="bg-cyan-50 rounded p-3 hover:bg-cyan-100 transition">
                                        <div class="font-medium text-cyan-900 mb-2 flex items-center">
                                            <i class="fas fa-water text-cyan-600 mr-2"></i>
                                            {{ trans_api($source, 'source') }}
                                        </div>
                                        <div class="grid grid-cols-2 gap-2 text-xs pl-6">
                                            <div><span class="text-gray-600">{{ __('messages.supply') }}:</span> <span class="font-bold text-green-700">{{ $data['supply'] ?? 'N/A' }}</span></div>
                                            <div><span class="text-gray-600">{{ __('messages.cost') }}:</span> <span class="font-bold text-red-700">{{ $data['biaya'] ?? 'N/A' }}</span></div>
                                            <div class="col-span-2"><span class="text-gray-600">{{ __('messages.contribution') }}:</span> <span class="font-bold text-blue-700">{{ $data['kontribusi'] ?? 'N/A' }}</span></div>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- BAGIAN 3: PERENCANAAN PEMBANGUNAN BENDUNGAN -->
                    @if(isset($summary['dam_cost_estimate']) && !isset($summary['dam_cost_estimate']['error']) && !isset($summary['dam_cost_estimate']['status']))
                        @php
                            $dam = $summary['dam_cost_estimate'];
                            $hps = $dam['estimasi_hps'] ?? [];
                            $skenario = $dam['skenario'] ?? [];
                            $moderat = $skenario['moderat'] ?? [];
                            $jadwal = $dam['jadwal'] ?? [];
                        @endphp
                        <div class="bg-white rounded-lg p-4 mt-4 shadow-sm border border-green-200">
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center bg-green-50 p-3 rounded">
                                <i class="fas fa-hard-hat text-green-600 mr-2"></i>
                                PERENCANAAN PEMBANGUNAN BENDUNGAN
                            </h4>

                            <!-- Info Bangunan -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm mb-4">
                                <div class="bg-gray-50 rounded p-3">
                                    <div class="text-xs text-gray-600 mb-1">Tipe Bangunan</div>
                                    <div class="font-bold text-gray-800">{{ $dam['tipe_bangunan'] ?? 'N/A' }}</div>
                                </div>
                                <div class="bg-gray-50 rounded p-3">
                                    <div class="text-xs text-gray-600 mb-1">Provinsi</div>
                                    <div class="font-bold text-gray-800">{{ $dam['provinsi'] ?? 'N/A' }}</div>
                                </div>
                                <div class="bg-gray-50 rounded p-3">
                                    <div class="text-xs text-gray-600 mb-1">Wilayah (IKK)</div>
                                    <div class="font-bold text-gray-800">{{ $dam['ikk_wilayah'] ?? 'N/A' }}</div>
                                </div>
                            </div>

                            <!-- Dimensi Tampungan -->
                            @if(isset($dam['dimensi']))
                                <div class="grid grid-cols-2 gap-3 text-sm mb-4">
                                    <div class="bg-blue-50 rounded p-3">
                                        <div class="text-xs text-gray-600 mb-1">Volume Tampungan</div>
                                        <div class="font-bold text-lg text-blue-700">{{ number_format($dam['dimensi']['v_tampungan_m3'] ?? 0, 0, ',', '.') }} m³</div>
                                    </div>
                                    <div class="bg-blue-50 rounded p-3">
                                        <div class="text-xs text-gray-600 mb-1">Tinggi Rata-rata</div>
                                        <div class="font-bold text-lg text-blue-700">{{ number_format($dam['dimensi']['h_rata_m'] ?? 0, 2, ',', '.') }} m</div>
                                    </div>
                                </div>
                            @endif

                            <!-- Estimasi HPS (Harga Perkiraan Sendiri) -->
                            <div class="mb-4">
                                <div class="text-xs font-medium text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-calculator text-gray-500 mr-1"></i>
                                    Estimasi HPS (Harga Perkiraan Sendiri)
                                </div>
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-sm">
                                    <div class="bg-emerald-50 rounded p-3">
                                        <div class="text-xs text-gray-600 mb-1">Minimum</div>
                                        <div class="font-bold text-xl text-emerald-700">Rp {{ number_format($hps['minimum_rp'] ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="bg-amber-50 rounded p-3">
                                        <div class="text-xs text-gray-600 mb-1">Moderat</div>
                                        <div class="font-bold text-xl text-amber-700">Rp {{ number_format($hps['moderat_rp'] ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                    <div class="bg-red-50 rounded p-3">
                                        <div class="text-xs text-gray-600 mb-1">Maksimum</div>
                                        <div class="font-bold text-xl text-red-700">Rp {{ number_format($hps['maksimum_rp'] ?? 0, 0, ',', '.') }}</div>
                                    </div>
                                </div>
                                @if(!empty($hps['sumber']))
                                    <div class="text-xs text-gray-500 mt-2">
                                        <i class="fas fa-info-circle mr-1"></i>Sumber: {{ $hps['sumber'] }}
                                    </div>
                                @endif
                            </div>

                            <!-- Skenario Total Proyek -->
                            <div class="mb-4">
                                <div class="text-xs font-medium text-gray-700 mb-2 flex items-center">
                                    <i class="fas fa-chart-bar text-gray-500 mr-1"></i>
                                    Skenario Total Proyek (RAB)
                                </div>
                                <div class="overflow-x-auto">
                                    <table class="w-full text-xs border-collapse">
                                        <thead>
                                            <tr class="bg-gray-100">
                                                <th class="text-left p-2 border">Skenario</th>
                                                <th class="text-right p-2 border">Total Proyek</th>
                                                <th class="text-right p-2 border">Estimasi Kontrak</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td class="p-2 border font-medium text-emerald-700">Minimum</td>
                                                <td class="p-2 border text-right">Rp {{ number_format($skenario['minimum']['total_proyek_rp'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="p-2 border text-right">Rp {{ number_format($skenario['minimum']['estimasi_kontrak_rp'] ?? 0, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr class="bg-amber-50">
                                                <td class="p-2 border font-medium text-amber-700">Moderat</td>
                                                <td class="p-2 border text-right">Rp {{ number_format($moderat['total_proyek_rp'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="p-2 border text-right">Rp {{ number_format($moderat['estimasi_kontrak_rp'] ?? 0, 0, ',', '.') }}</td>
                                            </tr>
                                            <tr>
                                                <td class="p-2 border font-medium text-red-700">Maksimum</td>
                                                <td class="p-2 border text-right">Rp {{ number_format($skenario['maksimum']['total_proyek_rp'] ?? 0, 0, ',', '.') }}</td>
                                                <td class="p-2 border text-right">Rp {{ number_format($skenario['maksimum']['estimasi_kontrak_rp'] ?? 0, 0, ',', '.') }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Komponen Biaya (Breakdown skenario moderat) -->
                            @if(!empty($moderat['komponen_biaya']))
                                <div class="mb-4 pt-3 border-t">
                                    <div class="text-xs font-medium text-gray-700 mb-2">Komponen Biaya (Skenario Moderat):</div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        @foreach($moderat['komponen_biaya'] as $komponen => $detail)
                                            <div class="bg-gray-50 rounded p-2 text-xs">
                                                <div class="flex justify-between">
                                                    <span class="text-gray-600">{{ $komponen }}</span>
                                                    <span class="font-bold text-gray-800">{{ $detail['persentase_pct'] ?? 0 }}%</span>
                                                </div>
                                                <div class="text-right text-gray-700 mt-1">Rp {{ number_format($detail['jumlah_rp'] ?? 0, 0, ',', '.') }}</div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            @if(!empty($moderat['breakdown']))
                                <div class="mb-4 pt-3 border-t">
                                    <div class="text-xs font-medium text-gray-700 mb-2">Rincian Breakdown (Moderat):</div>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                        @foreach($moderat['breakdown'] as $item => $nilai)
                                            <div class="bg-gray-50 rounded p-2 text-xs flex justify-between">
                                                <span class="text-gray-600">{{ $item }}:</span>
                                                <span class="font-bold text-gray-800">
                                                    @if(is_numeric($nilai))
                                                        Rp {{ number_format($nilai, 0, ',', '.') }}
                                                    @else
                                                        {{ $nilai }}
                                                    @endif
                                                </span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Rasio Kontrak / HPS -->
                            @if(!empty($dam['rasio_kontrak_hps']))
                                @php $rasio = $dam['rasio_kontrak_hps']; @endphp
                                <div class="mb-4 pt-3 border-t">
                                    <div class="text-xs font-medium text-gray-700 mb-2">Rasio Kontrak terhadap HPS (Benchmark LPSE):</div>
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                                        <div class="bg-purple-50 rounded p-2 text-xs text-center">
                                            <div class="text-gray-600">Q1</div>
                                            <div class="font-bold text-purple-700">{{ number_format($rasio['q1'] ?? 0, 1) }}%</div>
                                        </div>
                                        <div class="bg-purple-100 rounded p-2 text-xs text-center">
                                            <div class="text-gray-600">Median</div>
                                            <div class="font-bold text-purple-800">{{ number_format($rasio['median'] ?? 0, 1) }}%</div>
                                        </div>
                                        <div class="bg-purple-50 rounded p-2 text-xs text-center">
                                            <div class="text-gray-600">Q3</div>
                                            <div class="font-bold text-purple-700">{{ number_format($rasio['q3'] ?? 0, 1) }}%</div>
                                        </div>
                                    </div>
                                    @if(!empty($rasio['sumber']))
                                        <div class="text-xs text-gray-500 mt-1">
                                            <i class="fas fa-info-circle mr-1"></i>{{ $rasio['sumber'] }} (n={{ $rasio['n'] ?? 0 }} paket)
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Jadwal Pelaksanaan -->
                            @if(isset($dam['jadwal']))
                                <div class="mb-4 pt-3 border-t">
                                    <div class="text-xs font-medium text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-calendar-alt text-gray-500 mr-1"></i>
                                        Jadwal Pelaksanaan
                                    </div>
                                    <div class="grid grid-cols-2 gap-3 text-sm mb-3">
                                        <div class="bg-indigo-50 rounded p-3">
                                            <div class="text-xs text-gray-600 mb-1">Durasi</div>
                                            <div class="font-bold text-indigo-700">{{ $jadwal['total_bulan'] ?? 0 }} bulan ({{ $jadwal['total_tahun'] ?? 0 }} tahun)</div>
                                        </div>
                                        <div class="bg-indigo-50 rounded p-3">
                                            <div class="text-xs text-gray-600 mb-1">Rentang Waktu</div>
                                            <div class="font-bold text-indigo-700">{{ $jadwal['rentang_bulan'] ?? 'N/A' }}</div>
                                        </div>
                                    </div>

                                    @if(!empty($jadwal['tahapan']))
                                        <div class="space-y-2">
                                            @foreach($jadwal['tahapan'] as $tahap)
                                                <div class="bg-gray-50 rounded p-3 flex items-center justify-between gap-3">
                                                    <div class="flex items-center gap-3">
                                                        <span class="bg-indigo-600 text-white rounded-full w-14 h-6 flex items-center justify-center text-[10px] font-bold flex-shrink-0">
                                                            Bln {{ $tahap['mulai_bulan'] }}-{{ $tahap['selesai_bulan'] }}
                                                        </span>
                                                        <div class="text-xs font-medium text-gray-800">{{ $tahap['tahap'] }}</div>
                                                    </div>
                                                    <div class="text-xs text-gray-500 flex-shrink-0">{{ $tahap['durasi_bulan'] }} bln</div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <!-- Paket LPSE Serupa (data pembanding dari hasil scraping) -->
                            @if(!empty($dam['paket_lpse_serupa']))
                                <div class="mb-4 pt-3 border-t">
                                    <div class="text-xs font-medium text-gray-700 mb-2 flex items-center">
                                        <i class="fas fa-file-contract text-gray-500 mr-1"></i>
                                        Paket LPSE Serupa — Data Pembanding ({{ count($dam['paket_lpse_serupa']) }} referensi)
                                    </div>
                                    <div class="overflow-x-auto">
                                        <table class="w-full text-xs border-collapse">
                                            <thead>
                                                <tr class="bg-gray-100">
                                                    <th class="text-left p-2 border">Nama Paket</th>
                                                    <th class="text-left p-2 border">Provinsi</th>
                                                    <th class="text-left p-2 border">Tahun</th>
                                                    <th class="text-right p-2 border">HPS</th>
                                                    <th class="text-right p-2 border">Nilai Kontrak</th>
                                                    <th class="text-right p-2 border">% Kontrak/HPS</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($dam['paket_lpse_serupa'] as $paket)
                                                    <tr>
                                                        <td class="p-2 border">
                                                            @if(!empty($paket['url_detail']))
                                                                <a href="{{ $paket['url_detail'] }}" target="_blank" class="text-blue-600 hover:underline">
                                                                    {{ $paket['nama_paket'] ?? '-' }}
                                                                </a>
                                                            @else
                                                                {{ $paket['nama_paket'] ?? '-' }}
                                                            @endif
                                                            @if(!empty($paket['tahapan_status']))
                                                                <span class="block text-[10px] text-gray-500">{{ $paket['tahapan_status'] }}</span>
                                                            @endif
                                                        </td>
                                                        <td class="p-2 border">{{ $paket['provinsi'] ?? '-' }}</td>
                                                        <td class="p-2 border">{{ $paket['tahun_anggaran'] ?? '-' }}</td>
                                                        <td class="p-2 border text-right">Rp {{ number_format($paket['hps_rp'] ?? 0, 0, ',', '.') }}</td>
                                                        <td class="p-2 border text-right">Rp {{ number_format($paket['nilai_kontrak_rp'] ?? 0, 0, ',', '.') }}</td>
                                                        <td class="p-2 border text-right">{{ isset($paket['pct_kontrak_vs_hps']) ? number_format($paket['pct_kontrak_vs_hps'], 1) . '%' : '-' }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif

                            <!-- Referensi & Disclaimer -->
                            @if(!empty($dam['referensi']) || !empty($dam['disclaimer']))
                                <div class="mt-3 pt-3 border-t text-xs text-gray-500">
                                    @if(!empty($dam['referensi']))
                                        <div class="mb-1">
                                            <i class="fas fa-book mr-1"></i><strong>Referensi:</strong>
                                            @foreach($dam['referensi'] as $ref)
                                                <span class="block ml-4">- {{ $ref }}</span>
                                            @endforeach
                                        </div>
                                    @endif
                                    @if(!empty($dam['disclaimer']))
                                        <div class="italic">
                                            <i class="fas fa-exclamation-triangle mr-1"></i>{{ $dam['disclaimer'] }}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    @elseif(isset($summary['dam_cost_estimate']['status']))
                        <div class="bg-white rounded-lg p-4 mt-4 shadow-sm border border-gray-200">
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center bg-gray-50 p-3 rounded">
                                <i class="fas fa-hard-hat text-gray-500 mr-2"></i>
                                PERENCANAAN PEMBANGUNAN BENDUNGAN
                            </h4>
                            <p class="text-sm text-gray-500 italic">{{ $summary['dam_cost_estimate']['status'] }}</p>
                        </div>
                    @endif

                    <!-- BAGIAN 4: WATER QUALITY -->
                    @if(isset($summary['analysis_results']['water_quality']) && !isset($summary['analysis_results']['water_quality']['error']))
                        <div class="bg-white rounded-lg p-4 mt-4 shadow-sm border border-cyan-200">
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center bg-cyan-50 p-3 rounded">
                                <i class="fas fa-flask text-cyan-600 mr-2"></i>
                                {{ strtoupper(__('messages.water_quality')) }}
                            </h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 text-sm">
                                <div class="bg-cyan-50 rounded p-4 col-span-2">
                                    <div class="text-xs text-gray-600 mb-1">WQI (Water Quality Index)</div>
                                    <div class="font-bold text-4xl text-cyan-700 mb-2">{{ $summary['analysis_results']['water_quality']['WQI_rata_rata'] ?? 'N/A' }}</div>
                                    <div class="text-sm">
                                        <span class="px-3 py-1 rounded-full text-xs font-bold {{
                                            strpos($summary['analysis_results']['water_quality']['status'] ?? '', 'Excellent') !== false ? 'bg-green-200 text-green-900' :
                                            (strpos($summary['analysis_results']['water_quality']['status'] ?? '', 'Good') !== false ? 'bg-blue-200 text-blue-900' :
                                            (strpos($summary['analysis_results']['water_quality']['status'] ?? '', 'Fair') !== false ? 'bg-yellow-200 text-yellow-900' :
                                            'bg-red-200 text-red-900'))
                                        }}">
                                            {{ $summary['analysis_results']['water_quality']['status'] ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                                <div class="space-y-3">
                                    <div class="bg-blue-50 rounded p-3">
                                        <div class="text-xs text-gray-600">pH Level</div>
                                        <div class="font-bold text-xl text-blue-700">{{ $summary['analysis_results']['water_quality']['pH'] ?? 'N/A' }}</div>
                                    </div>
                                    <div class="bg-green-50 rounded p-3">
                                        <div class="text-xs text-gray-600">DO (Dissolved Oxygen)</div>
                                        <div class="font-bold text-xl text-green-700">{{ $summary['analysis_results']['water_quality']['DO'] ?? 'N/A' }}</div>
                                    </div>
                                </div>
                                <div class="bg-purple-50 rounded p-3 col-span-full">
                                    <div class="text-xs text-gray-600 mb-1">TDS (Total Dissolved Solids)</div>
                                    <div class="font-bold text-2xl text-purple-700">{{ $summary['analysis_results']['water_quality']['TDS'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- BAGIAN 5: KONDISI SUNGAI & LINGKUNGAN -->
                    @if(isset($summary['analysis_results']['morfologi']) || isset($summary['analysis_results']['ecosystem_health']))
                        <div class="bg-white rounded-lg p-4 mt-4 shadow-sm border border-amber-200">
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center bg-amber-50 p-3 rounded">
                                <i class="fas fa-mountain text-amber-600 mr-2"></i>
                                {{ strtoupper(__('messages.river_environment')) }}
                            </h4>
                            
                           @if(isset($summary['analysis_results']['morfologi']))
                                <div class="bg-gray-50 rounded p-3 mb-3">
                                    <div class="font-medium text-gray-800 mb-2 flex items-center">
                                        <i class="fas fa-layer-group text-amber-600 mr-2"></i>
                                        {{ __('messages.river_morphology') }}
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-xs pl-6">
                                        @php
                                            $morfologiLabels = [
                                                'lebar_sungai'     => __('messages.river_width'),
                                                'kemiringan'       => __('messages.slope'),
                                                'beban_sediment'   => __('messages.sediment_load'),
                                                'erosion_rata_rata'=> __('messages.average_erosion'),
                                            ];
                                        @endphp
                                        @foreach($summary['analysis_results']['morfologi'] as $param => $nilai)
                                            <div>
                                                <span class="text-gray-600">{{ $morfologiLabels[$param] ?? ucwords(str_replace('_', ' ', $param)) }}:</span>
                                                <span class="font-bold">{{ $nilai }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            
                            @if(isset($summary['analysis_results']['ecosystem_health']) && 
                                ($summary['analysis_results']['ecosystem_health']['index'] ?? 'N/A') !== 'N/A' &&
                                ($summary['analysis_results']['ecosystem_health']['index'] ?? 'N/A') !== 'Data not available')
                                <div class="bg-green-50 rounded p-3">
                                    <div class="font-medium text-gray-800 mb-2 flex items-center">
                                        <i class="fas fa-leaf text-green-600 mr-2"></i>
                                        {{ __('messages.ecosystem_health') }}
                                    </div>
                                    <div class="grid grid-cols-2 gap-2 text-xs pl-6">
                                        <div class="col-span-2 mb-2">
                                            <span class="text-gray-600">{{ __('messages.health_index') }}:</span>
                                            <span class="font-bold text-2xl text-green-700 ml-2">{{ $summary['analysis_results']['ecosystem_health']['index'] }}</span>
                                            <span class="text-xs text-gray-500 ml-2">({{ trans_api($summary['analysis_results']['ecosystem_health']['status'] ?? 'N/A', 'status_ekosistem') }})</span>
                                        </div>
                                        @if(isset($summary['analysis_results']['ecosystem_health']['habitat_fish']) && 
                                            $summary['analysis_results']['ecosystem_health']['habitat_fish'] !== 'N/A')
                                            <div><span class="text-gray-600">{{ __('messages.fish_habitat') }}:</span> <span class="font-bold text-blue-700">{{ $summary['analysis_results']['ecosystem_health']['habitat_fish'] }}</span></div>
                                        @endif
                                        @if(isset($summary['analysis_results']['ecosystem_health']['habitat_vegetation']) && 
                                            $summary['analysis_results']['ecosystem_health']['habitat_vegetation'] !== 'N/A')
                                            <div><span class="text-gray-600">{{ __('messages.vegetation_habitat') }}:</span> <span class="font-bold text-green-700">{{ $summary['analysis_results']['ecosystem_health']['habitat_vegetation'] }}</span></div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- BAGIAN 6: RINGKASAN KONDISI SISTEM -->
                    @if(isset($summary['statistik_data']['reliability_sistem']))
                        <div class="bg-gradient-to-br from-indigo-50 to-purple-50 rounded-lg p-4 mt-4 shadow-md border-2 border-indigo-200">
                            <h4 class="font-bold text-gray-800 mb-3 flex items-center bg-white bg-opacity-70 p-3 rounded-lg">
                                <i class="fas fa-chart-line text-indigo-600 mr-2"></i>
                                📊 {{ strtoupper(__('messages.system_conditions_summary')) }}
                            </h4>
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-white bg-opacity-70 rounded-lg p-4 shadow">
                                    <div class="text-xs text-gray-600 mb-1">{{ __('messages.water_availability_condition') }}</div>
                                    <div class="font-bold text-3xl text-green-700 mb-1">{{ $summary['statistik_data']['reliability_sistem']['rata_rata'] ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-600">{{ __('messages.status') }}: <span class="font-semibold text-green-800">{{ trans_api($summary['statistik_data']['reliability_sistem']['status'] ?? 'N/A', 'status_keandalan') }}</span></div>
                                </div>
                                <div class="bg-white bg-opacity-70 rounded-lg p-4 shadow">
                                    <div class="text-xs text-gray-600 mb-1">{{ __('messages.retention_pond_volume') }}</div>
                                    <div class="font-bold text-3xl text-cyan-700 mb-1">{{ $summary['statistik_data']['volume_reservoir']['akhir_periode'] ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-600">Max: <span class="font-semibold">{{ $summary['statistik_data']['volume_reservoir']['maximum'] ?? 'N/A' }}</span></div>
                                </div>
                                <div class="bg-white bg-opacity-70 rounded-lg p-4 shadow col-span-2">
                                    <div class="text-xs text-gray-600 mb-1">{{ __('messages.average_rainfall') }}</div>
                                    <div class="font-bold text-2xl text-blue-700">{{ $summary['statistik_data']['curah_rainfall']['rata_rata'] ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- ⭐ TWI: FLOOD RISK ANALYSIS & GREEN SPACE RECOMMENDATIONS -->
                    @if(isset($summary['twi_analysis']) && 
                        is_array($summary['twi_analysis']) && 
                        (!isset($summary['twi_analysis']['status']) || 
                        (isset($summary['twi_analysis']['status']) && $summary['twi_analysis']['status'] !== 'error' && $summary['twi_analysis']['status'] !== 'not_available')))
                        <div class="bg-gradient-to-br from-cyan-50 via-blue-50 to-indigo-50 rounded-xl p-5 mt-4 shadow-md border-2 border-cyan-300">

                            {{-- HEADER --}}
                            <div class="bg-white rounded-xl p-4 mb-4 border-l-4 border-cyan-500 shadow-sm">
                                <h4 class="font-bold text-cyan-900 text-lg mb-2 flex items-center gap-2">
                                    <i class="fas fa-water text-cyan-600"></i>
                                    🌊 {{ __('messages.twi_analysis_title') }}
                                </h4>
                                <p class="text-sm text-gray-700 leading-relaxed">
                                    <strong>{{ __('messages.twi_what_is') }}</strong>&nbsp;{!! __('messages.twi_explanation') !!}
                                </p>
                            </div>

                            {{-- RISK STATUS CARDS --}}
                            <div class="bg-white rounded-xl p-4 mb-4 shadow-sm">
                                <h5 class="font-bold text-gray-800 mb-3 flex items-center gap-2">
                                    <i class="fas fa-exclamation-circle text-gray-600"></i>
                                    {{ __('messages.twi_location_risk_status') }}
                                </h5>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                                    {{-- TWI Value --}}
                                    <div class="bg-gradient-to-br from-cyan-50 to-blue-100 rounded-xl p-4 border-2 border-cyan-300">
                                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                                            📊 {{ __('messages.twi_wetness_index') }}
                                        </div>
                                        <div class="font-black text-4xl text-cyan-700 mb-2 leading-none">
                                            {{ $summary['twi_analysis']['twi_enhanced'] ?? 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-600 bg-white/70 rounded-lg p-2 leading-relaxed">
                                            <strong>{{ __('messages.twi_analysis_result') }}:</strong>
                                            {{ __('messages.twi_value') }} {{ $summary['twi_analysis']['twi_physics'] ?? 'N/A' }},
                                            {{ __('messages.twi_ml_enhanced') }} {{ $summary['twi_analysis']['ml_correction_factor'] ?? 'N/A' }}
                                        </div>
                                    </div>

                                    {{-- Risk Level --}}
                                    @php
                                        $riskLevel = $summary['twi_analysis']['risk_level'] ?? '';
                                        $riskColorClass = match($riskLevel) {
                                            'VERY_HIGH' => 'bg-gradient-to-br from-red-50 to-red-100 border-red-400',
                                            'HIGH'      => 'bg-gradient-to-br from-orange-50 to-orange-100 border-orange-400',
                                            'MODERATE'  => 'bg-gradient-to-br from-yellow-50 to-yellow-100 border-yellow-400',
                                            default     => 'bg-gradient-to-br from-green-50 to-green-100 border-green-400',
                                        };
                                        $riskTextClass = match($riskLevel) {
                                            'VERY_HIGH' => 'text-red-700',
                                            'HIGH'      => 'text-orange-700',
                                            'MODERATE'  => 'text-yellow-700',
                                            default     => 'text-green-700',
                                        };
                                        $riskLabel = match($riskLevel) {
                                            'VERY_HIGH' => __('messages.twi_risk_very_high'),
                                            'HIGH'      => __('messages.twi_risk_high'),
                                            'MODERATE'  => __('messages.twi_risk_moderate'),
                                            default     => __('messages.twi_risk_low'),
                                        };
                                    @endphp
                                    <div class="rounded-xl p-4 border-2 {{ $riskColorClass }}">
                                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-1">
                                            🚨 {{ __('messages.twi_flood_risk_level') }}
                                        </div>
                                        <div class="font-black text-3xl mb-2 leading-none {{ $riskTextClass }}">
                                            {{ $riskLabel }}
                                        </div>
                                        <div class="text-xs text-gray-700 bg-white/70 rounded-lg p-2 leading-relaxed">
                                            <strong>{{ __('messages.twi_meaning') }}:</strong>
                                            {{ $summary['twi_analysis']['interpretation']['risk'] ?? 'N/A' }}
                                        </div>
                                    </div>

                                </div>
                            </div>

                            {{-- FLOOD ZONES --}}
                            @if(isset($summary['twi_analysis']['flood_zones']))
                            <div class="bg-white rounded-xl p-4 mb-4 shadow-sm border-l-4 border-red-500">
                                <h5 class="font-bold text-red-800 mb-2 flex items-center gap-2">
                                    <i class="fas fa-map-marker-alt text-red-600"></i>
                                    🚨 {{ __('messages.twi_flood_zones_title') }}
                                </h5>
                                <p class="text-sm text-gray-600 mb-4">
                                    {{ __('messages.twi_flood_zones_desc') }}
                                    <strong class="text-red-600">{{ $summary['twi_analysis']['flood_zones']['total'] ?? 0 }} {{ __('messages.twi_zones') }}</strong>
                                    {{ __('messages.twi_flood_zones_desc2') }}
                                </p>

                                {{-- Risk Summary Pills --}}
                                <div class="grid grid-cols-3 gap-3 mb-4">
                                    <div class="bg-red-50 border-2 border-red-200 rounded-xl p-3 text-center">
                                        <div class="text-2xl font-black text-red-700">{{ $summary['twi_analysis']['flood_zones']['high_risk'] ?? 0 }}</div>
                                        <div class="text-xs text-gray-600 mt-1">⚠️ {{ __('messages.twi_high_risk') }}</div>
                                        <div class="text-xs font-semibold text-red-600 mt-0.5">{{ __('messages.twi_top_priority') }}</div>
                                    </div>
                                    <div class="bg-orange-50 border-2 border-orange-200 rounded-xl p-3 text-center">
                                        <div class="text-2xl font-black text-orange-700">{{ $summary['twi_analysis']['flood_zones']['moderate_risk'] ?? 0 }}</div>
                                        <div class="text-xs text-gray-600 mt-1">⚡ {{ __('messages.twi_moderate_risk') }}</div>
                                        <div class="text-xs font-semibold text-orange-600 mt-0.5">{{ __('messages.twi_needs_monitoring') }}</div>
                                    </div>
                                    <div class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-3 text-center">
                                        <div class="text-2xl font-black text-yellow-700">{{ $summary['twi_analysis']['flood_zones']['low_risk'] ?? 0 }}</div>
                                        <div class="text-xs text-gray-600 mt-1">💧 {{ __('messages.twi_low_risk') }}</div>
                                        <div class="text-xs font-semibold text-yellow-600 mt-0.5">{{ __('messages.twi_stay_alert') }}</div>
                                    </div>
                                </div>

                                {{-- Zone Detail List --}}
                                @if(isset($summary['twi_analysis']['flood_zones']['zones_detail']) && count($summary['twi_analysis']['flood_zones']['zones_detail']) > 0)
                                <div class="bg-gray-50 rounded-xl p-3">
                                    <h6 class="text-sm font-bold text-gray-700 mb-2">
                                        📍 {{ __('messages.twi_flood_zone_detail') }}
                                    </h6>
                                    <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                                        @foreach($summary['twi_analysis']['flood_zones']['zones_detail'] as $index => $zone)
                                        @php
                                            $zRisk = $zone['risk'] ?? '';
                                            $zBorder = $zRisk === 'HIGH' ? 'border-red-500' : ($zRisk === 'MODERATE' ? 'border-orange-500' : 'border-yellow-500');
                                            $zBadge  = $zRisk === 'HIGH' ? 'bg-red-100 text-red-800' : ($zRisk === 'MODERATE' ? 'bg-orange-100 text-orange-800' : 'bg-yellow-100 text-yellow-800');
                                            $zLabel  = $zRisk === 'HIGH' ? '⚠️ '.__('messages.twi_priority_label') : ($zRisk === 'MODERATE' ? '⚡ '.__('messages.twi_watch_label') : '💧 '.__('messages.twi_monitor_label'));
                                        @endphp
                                        <div class="bg-white rounded-lg p-3 border-l-4 {{ $zBorder }} shadow-sm">
                                            <div class="flex justify-between items-center mb-2">
                                                <span class="font-bold text-sm text-gray-800">{{ __('messages.twi_zone') }} {{ $index + 1 }}</span>
                                                <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $zBadge }}">{{ $zLabel }}</span>
                                            </div>
                                            <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs text-gray-600">
                                                <div><span class="font-semibold">{{ __('messages.twi_twi_index') }}:</span> <span class="font-bold text-gray-800">{{ number_format($zone['twi_value'] ?? 0, 1) }}</span></div>
                                                <div><span class="font-semibold">{{ __('messages.twi_area_size') }}:</span> <span class="font-bold text-gray-800">{{ number_format($zone['area_ha'] ?? 0, 2) }} ha</span></div>
                                                <div class="col-span-2"><span class="font-semibold">{{ __('messages.twi_coordinates') }}:</span> <span class="font-mono text-blue-600">{{ number_format($zone['lat'] ?? 0, 5) }}, {{ number_format($zone['lon'] ?? 0, 5) }}</span></div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endif

                            {{-- RTH RECOMMENDATIONS --}}
                            @if(isset($summary['twi_analysis']['rtho_recommendations']))
                            <div class="bg-white rounded-xl p-4 mb-4 shadow-sm border-l-4 border-green-500">
                                <h5 class="font-bold text-green-800 mb-2 flex items-center gap-2">
                                    <i class="fas fa-tree text-green-600"></i>
                                    🌳 {{ __('messages.twi_rth_title') }}
                                </h5>
                                <p class="text-sm text-gray-600 mb-4">
                                    {{ __('messages.twi_rth_desc') }}
                                    <strong class="text-green-700">{{ $summary['twi_analysis']['rtho_recommendations']['total'] ?? 0 }} {{ __('messages.twi_rth_locations') }}</strong>
                                    {{ __('messages.twi_rth_desc2') }}
                                </p>

                                {{-- RTH Summary --}}
                                <div class="grid grid-cols-3 gap-3 mb-4">
                                    <div class="bg-red-50 border-2 border-red-200 rounded-xl p-3 text-center">
                                        <div class="text-2xl font-black text-red-700">{{ $summary['twi_analysis']['rtho_recommendations']['high_priority'] ?? 0 }}</div>
                                        <div class="text-xs text-gray-600 mt-1">🔥 {{ __('messages.twi_high_priority') }}</div>
                                        <div class="text-xs font-semibold text-red-600 mt-0.5">{{ __('messages.twi_immediately_build') }}</div>
                                    </div>
                                    <div class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-3 text-center">
                                        <div class="text-2xl font-black text-yellow-700">{{ $summary['twi_analysis']['rtho_recommendations']['moderate_priority'] ?? 0 }}</div>
                                        <div class="text-xs text-gray-600 mt-1">⭐ {{ __('messages.twi_medium_priority') }}</div>
                                        <div class="text-xs font-semibold text-yellow-600 mt-0.5">{{ __('messages.twi_medium_term_plan') }}</div>
                                    </div>
                                    <div class="bg-green-50 border-2 border-green-200 rounded-xl p-3 text-center">
                                        <div class="text-2xl font-black text-green-700">{{ number_format($summary['twi_analysis']['rtho_recommendations']['total_area_ha'] ?? 0, 1) }}</div>
                                        <div class="text-xs text-gray-600 mt-1">📏 {{ __('messages.twi_total_area') }}</div>
                                        <div class="text-xs font-semibold text-green-600 mt-0.5">{{ __('messages.twi_hectares') }}</div>
                                    </div>
                                </div>

                                {{-- RTH Detail List --}}
                                @if(isset($summary['twi_analysis']['rtho_recommendations']['recommendations_detail']) && count($summary['twi_analysis']['rtho_recommendations']['recommendations_detail']) > 0)
                                <div class="bg-green-50 rounded-xl p-3">
                                    <h6 class="text-sm font-bold text-gray-700 mb-2">
                                        📍 {{ __('messages.twi_rth_locations') }}
                                    </h6>
                                    <div class="space-y-2 max-h-64 overflow-y-auto pr-1">
                                        @foreach($summary['twi_analysis']['rtho_recommendations']['recommendations_detail'] as $index => $rec)
                                        @php
                                            $rPriority = $rec['priority'] ?? '';
                                            $rBorder   = $rPriority === 'HIGH' ? 'border-red-500' : 'border-green-500';
                                            $rBadge    = $rPriority === 'HIGH' ? 'bg-red-100 text-red-800' : 'bg-green-100 text-green-800';
                                            $rLabel    = $rPriority === 'HIGH' ? '🔥 '.__('messages.twi_urgent_label') : '⭐ '.__('messages.twi_plan_label');
                                        @endphp
                                        <div class="bg-white rounded-lg p-3 border-l-4 {{ $rBorder }} shadow-sm">
                                            <div class="flex justify-between items-center mb-2">
                                                <span class="font-bold text-sm text-gray-800">{{ __('messages.twi_rth_location') }} {{ $index + 1 }}</span>
                                                <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $rBadge }}">{{ $rLabel }}</span>
                                            </div>
                                            <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs text-gray-600 mb-2">
                                                <div><span class="font-semibold">{{ __('messages.twi_estimated_area') }}:</span> <span class="font-bold text-gray-800">{{ number_format($rec['estimated_area_ha'] ?? 0, 2) }} ha</span></div>
                                                <div><span class="font-semibold">{{ __('messages.twi_coordinates') }}:</span> <span class="font-mono text-blue-600">{{ number_format($rec['lat'] ?? 0, 5) }}, {{ number_format($rec['lon'] ?? 0, 5) }}</span></div>
                                            </div>
                                            @if(!empty($rec['reason']) && $rec['reason'] !== 'N/A')
                                            <div class="bg-gray-50 rounded-lg p-2 text-xs">
                                                <span class="font-semibold text-gray-700">💡 {{ __('messages.twi_reason') }}:</span>
                                                <p class="text-gray-600 mt-0.5">{{ $rec['reason'] }}</p>
                                            </div>
                                            @else
                                            <div class="bg-blue-50 rounded-lg p-2 text-xs">
                                                <span class="font-semibold text-blue-700">ℹ️ {{ __('messages.twi_reason') }}:</span>
                                                <p class="text-blue-600 mt-0.5">{{ __('messages.twi_rth_default_reason') }}</p>
                                            </div>
                                            @endif
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endif

                            {{-- DRAINAGE RECOMMENDATIONS --}}
                            @if(isset($summary['twi_analysis']['drainage_recommendations']))
                            <div class="bg-white rounded-xl p-4 mb-4 shadow-sm border-l-4 border-blue-500">
                                <h5 class="font-bold text-blue-800 mb-2 flex items-center gap-2">
                                    <i class="fas fa-water text-blue-600"></i>
                                    🚰 {{ __('messages.twi_drainage_title') }}
                                </h5>
                                <p class="text-sm text-gray-600 mb-4">
                                    {{ __('messages.twi_drainage_desc') }}
                                    <strong class="text-blue-700">{{ $summary['twi_analysis']['drainage_recommendations']['total'] ?? 0 }} {{ __('messages.twi_drainage_channels') }}</strong>
                                    {{ __('messages.twi_drainage_desc2') }}
                                </p>

                                {{-- Drainage Summary --}}
                                <div class="grid grid-cols-3 gap-3 mb-4">
                                    <div class="bg-red-50 border-2 border-red-200 rounded-xl p-3 text-center">
                                        <div class="text-2xl font-black text-red-700">{{ $summary['twi_analysis']['drainage_recommendations']['high_priority'] ?? 0 }}</div>
                                        <div class="text-xs text-gray-600 mt-1">🔥 {{ __('messages.twi_high_priority') }}</div>
                                        <div class="text-xs font-semibold text-red-600 mt-0.5">{{ __('messages.twi_immediately_build') }}</div>
                                    </div>
                                    <div class="bg-yellow-50 border-2 border-yellow-200 rounded-xl p-3 text-center">
                                        <div class="text-2xl font-black text-yellow-700">{{ $summary['twi_analysis']['drainage_recommendations']['medium_priority'] ?? 0 }}</div>
                                        <div class="text-xs text-gray-600 mt-1">⭐ {{ __('messages.twi_medium_priority') }}</div>
                                        <div class="text-xs font-semibold text-yellow-600 mt-0.5">{{ __('messages.twi_medium_term_plan') }}</div>
                                    </div>
                                    <div class="bg-blue-50 border-2 border-blue-200 rounded-xl p-3 text-center">
                                        <div class="text-2xl font-black text-blue-700">{{ number_format($summary['twi_analysis']['drainage_recommendations']['total_capacity_m3_per_hour'] ?? 0, 0) }}</div>
                                        <div class="text-xs text-gray-600 mt-1">⚡ {{ __('messages.twi_total_capacity') }}</div>
                                        <div class="text-xs font-semibold text-blue-600 mt-0.5">m³/{{ __('messages.twi_hour') }}</div>
                                    </div>
                                </div>

                                {{-- Drainage Detail List --}}
                                @if(isset($summary['twi_analysis']['drainage_recommendations']['recommendations_detail']) && count($summary['twi_analysis']['drainage_recommendations']['recommendations_detail']) > 0)
                                <div class="bg-blue-50 rounded-xl p-3">
                                    <h6 class="text-sm font-bold text-gray-700 mb-2">
                                        📍 {{ __('messages.twi_drainage_locations') }}
                                    </h6>
                                    <div class="space-y-3 max-h-96 overflow-y-auto pr-1">
                                        @foreach($summary['twi_analysis']['drainage_recommendations']['recommendations_detail'] as $index => $drain)
                                        @php
                                            $dPriority = $drain['priority'] ?? '';
                                            $dBorder   = $dPriority === 'HIGH' ? 'border-red-500' : ($dPriority === 'MEDIUM' ? 'border-yellow-500' : 'border-blue-500');
                                            $dBadge    = $dPriority === 'HIGH' ? 'bg-red-100 text-red-800' : ($dPriority === 'MEDIUM' ? 'bg-yellow-100 text-yellow-800' : 'bg-blue-100 text-blue-800');
                                            $dLabel    = $dPriority === 'HIGH' ? '🔥 '.__('messages.twi_urgent_label') : ($dPriority === 'MEDIUM' ? '⭐ '.__('messages.twi_medium_label') : '📋 '.__('messages.twi_low_label'));
                                        @endphp
                                        <div class="bg-white rounded-xl p-4 border-l-4 {{ $dBorder }} shadow-sm">

                                            {{-- Header --}}
                                            <div class="flex justify-between items-center mb-3">
                                                <span class="font-bold text-sm text-gray-800">{{ $drain['location_id'] ?? 'DRAIN_'.($index+1) }}</span>
                                                <span class="px-2 py-0.5 rounded-full text-xs font-bold {{ $dBadge }}">{{ $dLabel }}</span>
                                            </div>

                                            {{-- Type & Location --}}
                                            <div class="grid grid-cols-2 gap-2 text-xs mb-3">
                                                <div class="bg-gray-50 rounded-lg p-2">
                                                    <span class="text-gray-500 block mb-0.5">{{ __('messages.twi_drainage_type') }}</span>
                                                    <span class="font-semibold text-gray-800">{{ $drain['drainage_type'] ?? 'Primary Drainage Channel' }}</span>
                                                </div>
                                                <div class="bg-gray-50 rounded-lg p-2">
                                                    <span class="text-gray-500 block mb-0.5">📍 {{ __('messages.twi_coordinates') }}</span>
                                                    <span class="font-mono text-blue-600 text-xs">{{ number_format($drain['coordinates']['lat'] ?? 0, 5) }}, {{ number_format($drain['coordinates']['lon'] ?? 0, 5) }}</span>
                                                </div>
                                                <div class="bg-gray-50 rounded-lg p-2 col-span-2">
                                                    <span class="text-gray-500 block mb-0.5">⚡ {{ __('messages.twi_capacity') }}</span>
                                                    <span class="font-bold text-blue-700">{{ number_format($drain['capacity']['design_capacity_m3_per_hour'] ?? 0, 0) }} m³/{{ __('messages.twi_hour') }}</span>
                                                </div>
                                            </div>

                                            {{-- Technical Specs --}}
                                            @if(isset($drain['specifications']))
                                            <div class="bg-indigo-50 rounded-lg p-3 mb-2">
                                                <h6 class="text-xs font-bold text-indigo-800 mb-2">🔧 {{ __('messages.twi_tech_specs') }}</h6>
                                                <div class="grid grid-cols-2 gap-x-4 gap-y-1 text-xs text-gray-700">
                                                    <div><span class="font-semibold">{{ __('messages.twi_width') }}:</span> {{ $drain['specifications']['channel_width_m'] ?? 'N/A' }} m</div>
                                                    <div><span class="font-semibold">{{ __('messages.twi_depth') }}:</span> {{ $drain['specifications']['channel_depth_m'] ?? 'N/A' }} m</div>
                                                    <div><span class="font-semibold">{{ __('messages.twi_channel_slope') }}:</span> {{ $drain['specifications']['channel_slope_percent'] ?? 'N/A' }}%</div>
                                                    <div><span class="font-semibold">{{ __('messages.twi_material') }}:</span> {{ $drain['specifications']['lining_type'] ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                            @endif

                                            {{-- Expected Benefits --}}
                                            @if(isset($drain['expected_benefits']))
                                            <div class="bg-green-50 rounded-lg p-3 mb-2">
                                                <h6 class="text-xs font-bold text-green-800 mb-2">✨ {{ __('messages.twi_expected_benefits') }}</h6>
                                                <div class="space-y-1 text-xs text-gray-700">
                                                    <div>🛡️ {{ __('messages.twi_flood_reduction') }}: <strong class="text-green-700">{{ $drain['expected_benefits']['flood_reduction_percent'] ?? 0 }}%</strong></div>
                                                    <div>⏱️ {{ __('messages.twi_ponding_time_reduction') }}: <strong class="text-blue-700">{{ $drain['expected_benefits']['ponding_time_reduction_hours'] ?? 0 }} {{ __('messages.twi_hours') }}</strong></div>
                                                    <div>📏 {{ __('messages.twi_protected_area') }}: <strong class="text-purple-700">{{ $drain['expected_benefits']['affected_area_ha'] ?? 0 }} ha</strong></div>
                                                </div>
                                            </div>
                                            @endif

                                            {{-- Maintenance --}}
                                            @if(isset($drain['maintenance_requirements']))
                                            <div class="bg-orange-50 rounded-lg p-3 mb-2">
                                                <h6 class="text-xs font-bold text-orange-800 mb-2">🔧 {{ __('messages.twi_maintenance') }}</h6>
                                                <div class="space-y-1 text-xs text-gray-700">
                                                    <div>🧹 {{ __('messages.twi_cleaning_freq') }}: {{ $drain['maintenance_requirements']['cleaning_frequency'] ?? 'N/A' }}</div>
                                                    <div>🔍 {{ __('messages.twi_inspection_freq') }}: {{ $drain['maintenance_requirements']['inspection_frequency'] ?? 'N/A' }}</div>
                                                    <div>💰 {{ __('messages.twi_annual_cost') }}: <strong class="text-orange-700">Rp {{ number_format(($drain['maintenance_requirements']['estimated_annual_cost_million_idr'] ?? 0) * 1000000, 0, ',', '.') }}</strong></div>
                                                </div>
                                            </div>
                                            @endif

                                            {{-- Build Reasons --}}
                                            @if(isset($drain['reasons']) && is_array($drain['reasons']) && count($drain['reasons']) > 0)
                                            <div class="bg-gray-50 rounded-lg p-3 text-xs">
                                                <span class="font-semibold text-gray-700">💡 {{ __('messages.twi_build_reason') }}:</span>
                                                <ul class="list-disc list-inside text-gray-600 mt-1 space-y-0.5">
                                                    @foreach($drain['reasons'] as $reason)
                                                    <li>{{ $reason }}</li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                            @endif

                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                                @endif
                            </div>
                            @endif

                            {{-- ACTIONS & RECOMMENDATIONS --}}
                            @if(isset($summary['twi_analysis']['interpretation']) || isset($summary['twi_analysis']['flood_zones']) || isset($summary['twi_analysis']['rtho_recommendations']) || isset($summary['twi_analysis']['drainage_recommendations']))
                            @php
                                $highRiskZones       = $summary['twi_analysis']['flood_zones']['high_risk'] ?? 0;
                                $highPriorityRTH     = $summary['twi_analysis']['rtho_recommendations']['high_priority'] ?? 0;
                                $totalRTH            = $summary['twi_analysis']['rtho_recommendations']['total'] ?? 0;
                                $highPriorityDrain   = $summary['twi_analysis']['drainage_recommendations']['high_priority'] ?? 0;
                                $totalDrain          = $summary['twi_analysis']['drainage_recommendations']['total'] ?? 0;
                            @endphp
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 border-l-4 border-blue-500 shadow-sm">
                                <div class="flex items-start gap-3">
                                    <i class="fas fa-clipboard-check text-blue-600 text-2xl mt-1 shrink-0"></i>
                                    <div class="flex-1">
                                        <h6 class="font-bold text-blue-900 text-base mb-1">⚡ {{ __('messages.twi_actions_title') }}</h6>

                                        @if(!empty($summary['twi_analysis']['interpretation']['action']))
                                        <p class="text-sm text-blue-800 mb-3 leading-relaxed">{{ $summary['twi_analysis']['interpretation']['action'] }}</p>
                                        @endif

                                        <div class="bg-white rounded-xl p-3">
                                            <div class="text-sm font-bold text-blue-700 mb-2">📋 {{ __('messages.twi_priority_recommendations') }}</div>
                                            <ol class="space-y-2 text-sm text-gray-700">

                                                @if($highRiskZones > 0)
                                                <li class="flex gap-2 bg-red-50 p-2 rounded-lg border-l-2 border-red-400">
                                                    <span class="shrink-0">1.</span>
                                                    <div><strong>{{ __('messages.twi_main_priority') }}:</strong> {{ __('messages.twi_high_risk_zones_action') }} <strong class="text-red-700">{{ $highRiskZones }} {{ __('messages.twi_high_risk_zones_action2') }}</strong></div>
                                                </li>
                                                @endif

                                                @if($highPriorityDrain > 0)
                                                <li class="flex gap-2 bg-blue-50 p-2 rounded-lg border-l-2 border-blue-400">
                                                    <span class="shrink-0">2.</span>
                                                    <div><strong>{{ __('messages.twi_drainage_action') }}:</strong> {{ __('messages.twi_build_label') }} <strong class="text-blue-700">{{ $highPriorityDrain }} {{ __('messages.twi_drainage_action2') }}</strong></div>
                                                </li>
                                                @endif

                                                @if($highPriorityRTH > 0)
                                                <li class="flex gap-2 bg-orange-50 p-2 rounded-lg border-l-2 border-orange-400">
                                                    <span class="shrink-0">3.</span>
                                                    <div><strong>{{ __('messages.twi_rth_action') }}:</strong> {{ __('messages.twi_build_label') }} <strong class="text-orange-700">{{ $highPriorityRTH }} {{ __('messages.twi_rth_action2') }}</strong></div>
                                                </li>
                                                @endif

                                                @if(($totalRTH > $highPriorityRTH) || ($totalDrain > $highPriorityDrain))
                                                <li class="flex gap-2 bg-yellow-50 p-2 rounded-lg border-l-2 border-yellow-400">
                                                    <span class="shrink-0">4.</span>
                                                    <div>
                                                        <strong>{{ __('messages.twi_medium_term') }}:</strong>
                                                        {{ __('messages.twi_build_label') }}
                                                        @if($totalDrain > $highPriorityDrain)
                                                            {{ $totalDrain - $highPriorityDrain }} {{ __('messages.twi_additional_drainage') }}
                                                        @endif
                                                        @if(($totalRTH > $highPriorityRTH) && ($totalDrain > $highPriorityDrain))
                                                            {{ __('messages.twi_and') }}
                                                        @endif
                                                        @if($totalRTH > $highPriorityRTH)
                                                            {{ $totalRTH - $highPriorityRTH }} {{ __('messages.twi_additional_rth') }}
                                                        @endif
                                                        {{ __('messages.twi_medium_term_desc2') }}
                                                    </div>
                                                </li>
                                                @endif

                                                <li class="flex gap-2 bg-purple-50 p-2 rounded-lg border-l-2 border-purple-400">
                                                    <span class="shrink-0">5.</span>
                                                    <div><strong>{{ __('messages.twi_regular_monitoring') }}:</strong> {{ __('messages.twi_monitoring_desc') }}</div>
                                                </li>

                                                <li class="flex gap-2 bg-green-50 p-2 rounded-lg border-l-2 border-green-400">
                                                    <span class="shrink-0">6.</span>
                                                    <div><strong>{{ __('messages.twi_coordination') }}:</strong> {{ __('messages.twi_coordination_desc') }}</div>
                                                </li>

                                            </ol>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                        </div>
                    @endif

                    {{-- TWI ERROR / NOT AVAILABLE --}}
                    @if(isset($summary['twi_analysis']['status']) && 
                        ($summary['twi_analysis']['status'] === 'error' || 
                        $summary['twi_analysis']['status'] === 'not_available'))
                    <div class="bg-yellow-50 rounded-xl border-2 border-yellow-300 p-5 mt-4 shadow-md">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center shrink-0">
                                <i class="fas fa-exclamation-triangle text-yellow-600 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-yellow-900 text-lg mb-2">⚠️ {{ __('messages.twi_not_available_title') }}</h4>
                                <div class="bg-white rounded-lg p-3 mb-3 border border-yellow-200">
                                    <p class="text-sm text-yellow-800 font-semibold mb-1">Status:</p>
                                    <p class="text-sm text-gray-700">{{ $summary['twi_analysis']['status'] ?? 'Unknown' }}</p>
                                </div>
                                @if(isset($summary['twi_analysis']['error']))
                                <div class="bg-red-50 rounded-lg p-3 mb-3 border border-red-200">
                                    <p class="text-sm text-red-800 font-semibold mb-1">🐛 Error Detail:</p>
                                    <p class="text-xs text-red-700 font-mono">{{ $summary['twi_analysis']['error'] }}</p>
                                </div>
                                @endif
                                <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                                    <p class="text-sm text-blue-800 font-semibold mb-2">💡 Possible Causes:</p>
                                    <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                                        <li>File <code class="bg-gray-200 px-1 rounded">RIVANA_TWI_Analysis.json</code> was not generated by the API server</li>
                                        <li>TWI analysis process failed or timed out during execution</li>
                                        <li>Error reading or parsing the JSON file</li>
                                        <li>Insufficient morphology data for TWI analysis</li>
                                    </ul>
                                </div>
                                <div class="bg-gray-50 rounded-lg p-3 mt-3 border border-gray-200">
                                    <p class="text-sm text-gray-800 font-semibold mb-2">🔍 Debugging Steps:</p>
                                    <ol class="list-decimal list-inside text-xs text-gray-600 space-y-1">
                                        <li>Check if <strong>RIVANA_TWI_Dashboard.png</strong> was generated (if Yes, TWI calculation ran)</li>
                                        <li>Review full log files for error messages</li>
                                        <li>Ensure the API server v2 is running the latest code</li>
                                        <li>Try re-running the analysis with the same parameters</li>
                                    </ol>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- TWI DATA NOT FOUND --}}
                    @if(!isset($summary['twi_analysis']))
                    <div class="bg-red-50 rounded-xl border-2 border-red-300 p-5 mt-4 shadow-md">
                        <div class="flex items-start gap-4">
                            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center shrink-0">
                                <i class="fas fa-times-circle text-red-600 text-xl"></i>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-red-900 text-lg mb-2">❌ {{ __('messages.twi_not_found_title') }}</h4>
                                <p class="text-sm text-red-700 mb-3">
                                    Key <code class="bg-gray-200 px-2 py-1 rounded text-xs font-mono">twi_analysis</code> not found in summary data.
                                </p>
                                <div class="bg-white rounded-lg p-3 mb-3 border border-red-200">
                                    <p class="text-sm text-red-800 font-semibold mb-2">🔍 Possible Causes:</p>
                                    <ul class="list-disc list-inside text-sm text-gray-700 space-y-1">
                                        <li><strong>RIVANA_TWI_Analysis.json was not generated</strong> by the Python API</li>
                                        <li><strong>Laravel controller failed to read/parse the TWI JSON file</strong></li>
                                        <li><strong>TWI file path mismatch</strong> between local and VPS environment</li>
                                        <li><strong>Permission issue</strong> on the results directory on VPS</li>
                                    </ul>
                                </div>
                                <div class="bg-yellow-50 rounded-lg p-3 border border-yellow-200">
                                    <p class="text-sm text-yellow-800 font-semibold mb-2">🛠️ Debugging Steps:</p>
                                    <ol class="list-decimal list-inside text-xs text-gray-600 space-y-1">
                                        <li>SSH to VPS, check directory: <code class="bg-gray-200 px-1 rounded">results/{{ $job->job_id }}/</code></li>
                                        <li>Look for: <code class="bg-gray-200 px-1 rounded">RIVANA_TWI_Analysis.json</code></li>
                                        <li>If found, inspect contents: <code class="bg-gray-200 px-1 rounded">cat RIVANA_TWI_Analysis.json | head -20</code></li>
                                        <li>Check Laravel log: <code class="bg-gray-200 px-1 rounded">tail -100 storage/logs/laravel.log</code></li>
                                        <li>Check Python API log for TWI calculation errors</li>
                                    </ol>
                                </div>
                                <div class="bg-blue-50 rounded-lg p-3 mt-3 border border-blue-200">
                                    <p class="text-sm text-blue-800 font-semibold mb-2">💡 Local vs VPS Differences:</p>
                                    <ul class="list-disc list-inside text-xs text-gray-600 space-y-1">
                                        <li><strong>Absolute paths differ:</strong> Local (E:\laragon\...) vs VPS (/var/www/...)</li>
                                        <li><strong>Python environment:</strong> Ensure all dependencies are installed on VPS</li>
                                        <li><strong>File permissions:</strong> VPS requires proper chmod/chown on results directory</li>
                                        <li><strong>Memory/timeout:</strong> VPS may have stricter limits</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- BAGIAN 6: 30-DAY RAINFALL FORECAST -->
                    @if(isset($summary['prediksi_30_hari']))
                    <div class="bg-gradient-to-br from-sky-50 to-blue-50 rounded-xl p-5 mt-4 shadow-md border-2 border-sky-300">
                        <h4 class="font-bold text-sky-900 text-lg mb-4 flex items-center gap-2">
                            <i class="fas fa-cloud-sun-rain text-sky-600"></i>
                            📅 {{ __('messages.forecast_30_days') }}
                        </h4>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                            {{-- Rainfall Forecast --}}
                            @if(isset($summary['prediksi_30_hari']['rainfall']))
                            <div class="bg-white rounded-xl p-4 shadow-sm border border-sky-100">
                                <h5 class="font-semibold text-sky-800 mb-3 flex items-center gap-2">
                                    <i class="fas fa-cloud-rain text-sky-500 text-sm"></i>
                                    {{ __('messages.rainfall_forecast') }}
                                </h5>
                                <div class="space-y-2 text-sm">
                                    @foreach($summary['prediksi_30_hari']['rainfall'] as $key => $value)
                                    @php
                                        $rainfallLabels = [
                                            'rata_rata' => __('messages.average'),
                                            'minimum'   => __('messages.minimum'),
                                            'maximum'   => __('messages.maximum'),
                                            'total'     => __('messages.total'),
                                        ];
                                        $label = $rainfallLabels[strtolower($key)] ?? ucwords(str_replace('_', ' ', $key));
                                    @endphp
                                    <div class="flex justify-between items-center py-1 border-b border-gray-50 last:border-0">
                                        <span class="text-gray-500">{{ $label }}</span>
                                        <span class="font-bold text-gray-800">{{ $value }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            {{-- Retention Pond Forecast --}}
                            @if(isset($summary['prediksi_30_hari']['reservoir']))
                            <div class="bg-white rounded-xl p-4 shadow-sm border border-blue-100">
                                <h5 class="font-semibold text-blue-800 mb-3 flex items-center gap-2">
                                    <i class="fas fa-water text-blue-500 text-sm"></i>
                                    {{ __('messages.retention_pond') }}
                                </h5>
                                <div class="space-y-2 text-sm">
                                    @foreach($summary['prediksi_30_hari']['reservoir'] as $key => $value)
                                    @php
                                        $reservoirLabels = [
                                            'kondisi_saat_ini'    => __('messages.forecast_current_condition'),
                                            'prediksi_30_hari'    => __('messages.forecast_30_day_prediction'),
                                            'persentase_capacity' => __('messages.forecast_capacity_percent'),
                                        ];
                                        $label = $reservoirLabels[strtolower($key)] ?? ucwords(str_replace('_', ' ', $key));
                                    @endphp
                                    <div class="flex justify-between items-center py-1 border-b border-gray-50 last:border-0">
                                        <span class="text-gray-500">{{ $label }}</span>
                                        <span class="font-bold text-gray-800">{{ $value }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            {{-- Reliability Forecast --}}
                            @if(isset($summary['prediksi_30_hari']['reliability']))
                            <div class="bg-white rounded-xl p-4 shadow-sm border border-green-100">
                                <h5 class="font-semibold text-green-800 mb-3 flex items-center gap-2">
                                    <i class="fas fa-shield-alt text-green-500 text-sm"></i>
                                    {{ __('messages.reliability') }}
                                </h5>
                                <div class="space-y-2 text-sm">
                                    @foreach($summary['prediksi_30_hari']['reliability'] as $key => $value)
                                    @php
                                        $reliabilityLabels = [
                                            'saat_ini'        => __('messages.forecast_current'),
                                            'prediksi_30_hari'=> __('messages.forecast_30_day_prediction'),
                                            'tren'            => __('messages.forecast_trend'),
                                        ];
                                        $label = $reliabilityLabels[strtolower($key)] ?? ucwords(str_replace('_', ' ', $key));
                                    @endphp
                                    <div class="flex justify-between items-center py-1 border-b border-gray-50 last:border-0">
                                        <span class="text-gray-500">{{ $label }}</span>
                                        <span class="font-bold text-gray-800">{{ $value }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif

                        </div>

                        {{-- Forecast Recommendation --}}
                        @if(isset($summary['prediksi_30_hari']['rekomendasi_forecast']))
                        <div class="bg-blue-100 rounded-xl p-4 mt-4 border-l-4 border-blue-500">
                            <div class="flex items-start gap-2">
                                <i class="fas fa-lightbulb text-blue-600 mt-0.5 shrink-0"></i>
                                <p class="text-sm font-semibold text-blue-900 leading-relaxed">
                                    {!! nl2br(e($summary['prediksi_30_hari']['rekomendasi_forecast'])) !!}
                                </p>
                            </div>
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- BAGIAN 7: PEMENUHAN KEBUTUHAN AIR -->
                    @if(isset($summary['pemenuhan_kebutuhan_air']))
                        <div class="bg-gradient-to-br from-emerald-50 to-teal-50 rounded-lg p-4 mt-4 shadow-md border-2 border-emerald-300">
                            <h4 class="font-bold text-emerald-900 text-lg mb-3 flex items-center">
                                <i class="fas fa-tint mr-2 text-emerald-600"></i>
                                <span>💧 {{ __('messages.water_demand_fulfillment') }}</span>
                            </h4>
                            
                            @if(isset($summary['pemenuhan_kebutuhan_air']['ringkasan']))
                                <div class="bg-white rounded-lg p-4 mb-3 shadow-sm">
                                    <h5 class="font-bold text-emerald-800 mb-2">{{ __('messages.summary') }}</h5>
                                    <div class="grid grid-cols-4 gap-2 text-sm">
                                        @foreach($summary['pemenuhan_kebutuhan_air']['ringkasan'] as $key => $value)
                                            <div class="text-center">
                                                <p class="text-gray-600 text-xs">{{ ucwords(str_replace('_', ' ', $key)) }}</p>
                                                <p class="font-bold">{{ $value }}</p>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                            
                            @if(isset($summary['pemenuhan_kebutuhan_air']['detail_sektor']))
                                <div class="space-y-2">
                                    @foreach($summary['pemenuhan_kebutuhan_air']['detail_sektor'] as $sektor => $data)
                                        <div class="bg-white rounded-lg p-3 shadow-sm">
                                            <div class="flex justify-between items-center mb-2">
                                                <h6 class="font-bold capitalize">{{ $sektor }}</h6>
                                                <span class="px-2 py-1 rounded text-xs font-bold {{ $data['status'] === 'SURPLUS' ? 'bg-green-100 text-green-800' : ($data['status'] === 'DEFISIT' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                                    {{ $data['status'] }}
                                                </span>
                                            </div>
                                            <div class="grid grid-cols-4 gap-2 text-xs">
                                                @foreach($data as $key => $value)
                                                    @if($key !== 'status')
                                                        <div>
                                                            <span class="text-gray-600">{{ ucfirst($key) }}:</span>
                                                            <span class="font-bold">{{ $value }}</span>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endif

                    <!-- BAGIAN 8: SARAN PENGELOLAAN -->
                    @if(isset($summary['saran_pengelolaan']) && is_array($summary['saran_pengelolaan']))
                        <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-lg p-4 mt-4 shadow-md border-2 border-cyan-300">
                            <h4 class="font-bold text-cyan-900 text-lg mb-3 flex items-center">
                                <i class="fas fa-tasks mr-2 text-cyan-600"></i>
                                <span>📋 {{ __('messages.management_suggestions') }}</span>
                            </h4>
                            
                            <div class="space-y-2">
                                @foreach($summary['saran_pengelolaan'] as $index => $saran)
                                    @php
                                        $isPriority = strpos($saran, '🔴') !== false || strpos($saran, '⚠️') !== false;
                                        $bgClass = $isPriority ? 'bg-red-50 border-red-400' : 'bg-green-50 border-green-400';
                                    @endphp
                                    <div class="bg-white rounded-lg p-3 border-l-4 {{ $bgClass }}">
                                        <p class="text-sm"><strong>{{ $index + 1 }}.</strong> {!! nl2br(e($saran)) !!}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- BAGIAN 9: SARAN PERBAIKAN KONDISI -->
                    @if(isset($summary['saran_perbaikan_kondisi']) && is_array($summary['saran_perbaikan_kondisi']))
                        <div class="bg-gradient-to-br from-rose-50 to-pink-50 rounded-lg p-4 mt-4 shadow-md border-2 border-rose-300">
                            <h4 class="font-bold text-rose-900 text-lg mb-3 flex items-center">
                                <i class="fas fa-tools mr-2 text-rose-600"></i>
                                <span>🔧 {{ __('messages.improvement_suggestions') }}</span>
                            </h4>
                            
                            <div class="space-y-3">
                                @foreach($summary['saran_perbaikan_kondisi'] as $index => $perbaikan)
                                    @php
                                        $prioritas = $perbaikan['prioritas'] ?? 'NORMAL';
                                        $badgeClass = $prioritas === 'TINGGI' ? 'bg-red-500' : ($prioritas === 'SEDANG' ? 'bg-yellow-500' : 'bg-green-500');
                                    @endphp
                                    
                                    <div class="bg-white rounded-lg shadow-sm border overflow-hidden">
                                        <div class="bg-gray-100 px-4 py-2 flex justify-between items-center">
                                            <h5 class="font-bold">{{ trans_api($perbaikan['kategori'] ?? 'N/A', 'category') }}</h5>
                                            <span class="px-2 py-1 {{ $badgeClass }} text-white text-xs rounded">{{ trans_api($prioritas, 'priority') }}</span>
                                        </div>
                                        <div class="p-4">
                                            <p class="text-sm text-gray-700 mb-2"><strong>{{ __('messages.problem') }}:</strong> {{ $perbaikan['masalah'] ?? 'N/A' }}</p>
                                            
                                            @if(isset($perbaikan['solusi']))
                                                <div class="mb-2">
                                                    <strong class="text-sm">{{ __('messages.solution') }}:</strong>
                                                    <ul class="list-disc list-inside text-sm text-gray-700 mt-1">
                                                        @foreach($perbaikan['solusi'] as $solusi)
                                                            <li>{{ $solusi }}</li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                            
                                            <div class="grid grid-cols-2 gap-2 pt-2 border-t text-xs">
                                                <div>
                                                    <span class="text-gray-600">{{ __('messages.cost') }}:</span>
                                                    <span class="font-bold">{{ $perbaikan['estimasi_biaya'] ?? 'N/A' }}</span>
                                                </div>
                                                <div>
                                                    <span class="text-gray-600">{{ __('messages.timeline') }}:</span>
                                                    <span class="font-bold">{{ $perbaikan['timeline'] ?? 'N/A' }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Error Display (jika ada) -->
                    @if(isset($summary['error']))
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 mt-4 rounded-r-lg">
                            <div class="flex items-start">
                                <i class="fas fa-exclamation-triangle text-red-500 text-lg mt-1 mr-3"></i>
                                <div>
                                    <h4 class="font-semibold text-red-800 mb-1">Error</h4>
                                    <p class="text-sm text-red-700">{{ $summary['error'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endif

            <!-- Full Process Logs - COMPLETE OUTPUT (Output Lengkap) -->
            {{-- @if($fullLogs)
                <div class="bg-gray-900 rounded-lg shadow-xl p-6 mb-6 border-2 border-green-500">
                    <div class="flex justify-between items-center mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-white flex items-center mb-1">
                                <i class="fas fa-terminal text-green-400 mr-2 animate-pulse"></i>
                                📋 Complete Analysis Output
                            </h3>
                            <p class="text-gray-400 text-sm">
                                <i class="fas fa-info-circle text-blue-400 mr-1"></i> 
                                Full output dari Python API - Semua log proses analisis lengkap
                            </p>
                        </div>
                        <div class="flex space-x-2">
                            <button onclick="downloadLogs()" class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm rounded-lg transition duration-200 flex items-center">
                                <i class="fas fa-download mr-2"></i>
                                Download
                            </button>
                            <button onclick="copyLogs()" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm rounded-lg transition duration-200 flex items-center">
                                <i class="fas fa-copy mr-2"></i>
                                Copy
                            </button>
                            <button onclick="toggleLogs()" id="toggleLogsBtn" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white text-sm rounded-lg transition duration-200">
                                <i class="fas fa-chevron-down mr-1"></i>
                                <span id="toggleLogsText">Show</span>
                            </button>
                        </div>
                    </div>
                    
                    <div id="fullLogsContent" class="hidden">
                        <div class="mb-4 flex items-center justify-between">
                            <div class="flex space-x-2">
                                <span class="text-xs text-gray-400">
                                    <i class="fas fa-file-alt text-green-400 mr-1"></i>
                                    {{ strlen($fullLogs) }} characters
                                </span>
                                <span class="text-xs text-gray-400">
                                    <i class="fas fa-list-ol text-blue-400 mr-1"></i>
                                    {{ count(explode("\n", $fullLogs)) }} lines
                                </span>
                            </div>
                            <button onclick="searchInLogs()" class="text-xs px-3 py-1 bg-gray-700 hover:bg-gray-600 text-white rounded transition">
                                <i class="fas fa-search mr-1"></i>
                                Search
                            </button>
                        </div>
                        
                        <div class="bg-black rounded-lg p-4 overflow-x-auto max-h-[600px] overflow-y-auto border-2 border-gray-700 shadow-inner" id="logsContainer">
                            <pre class="text-green-400 text-xs font-mono whitespace-pre-wrap leading-relaxed">{{ $fullLogs }}</pre>
                        </div>
                        
                        <div class="mt-4 p-3 bg-gray-800 rounded-lg border border-gray-700">
                            <details class="text-gray-300">
                                <summary class="cursor-pointer font-semibold hover:text-white transition">
                                    <i class="fas fa-info-circle text-blue-400 mr-2"></i>
                                    What's included in this output?
                                </summary>
                                <ul class="mt-3 ml-6 space-y-2 text-sm text-gray-400">
                                    <li><i class="fas fa-check text-green-500 mr-2"></i>🏔️ Data Morfologi - Topografi dan karakteristik lahan</li>
                                    <li><i class="fas fa-check text-green-500 mr-2"></i>🤖 Model Training Progress - Status pelatihan 12 model ML</li>
                                    <li><i class="fas fa-check text-green-500 mr-2"></i>⚖️ Water Balance Validation - Validasi keseimbangan air</li>
                                    <li><i class="fas fa-check text-green-500 mr-2"></i>📊 Laporan Komprehensif - Pembagian & prioritas air</li>
                                    <li><i class="fas fa-check text-green-500 mr-2"></i>💰 Analisis Ekonomi - Biaya & manfaat</li>
                                    <li><i class="fas fa-check text-green-500 mr-2"></i>💦 Kualitas Air - Parameter WQI, pH, DO, TDS</li>
                                    <li><i class="fas fa-check text-green-500 mr-2"></i>🌊 Laporan Keseimbangan Air Bulanan - Detail per bulan</li>
                                    <li><i class="fas fa-check text-green-500 mr-2"></i>🌿 Kondisi Sungai & Lingkungan - Erosi, sedimen, habitat</li>
                                    <li><i class="fas fa-check text-green-500 mr-2"></i>📈 Ringkasan Statistik - Keandalan & prediksi</li>
                                </ul>
                            </details>
                        </div>
                    </div>
                </div>
            @endif --}}

            <!-- 🌊 Interactive River Network Map Section  -->
            @php
                $riverMapHtml     = $job->files->firstWhere('filename', 'RIVANA_Peta_Aliran_Sungai.html');
                $riverMapPng      = $job->files->firstWhere('filename', 'RIVANA_Peta_Aliran_Sungai.png');
                $riverMapMetadata = $job->files->firstWhere('filename', 'RIVANA_Metadata_Peta.json');

                \Log::info('Map Files Check', [
                    'job_id'          => $job->id,
                    'all_files'       => $job->files->pluck('filename')->toArray(),
                    'html_found'      => $riverMapHtml     ? 'YES' : 'NO',
                    'png_found'       => $riverMapPng      ? 'YES' : 'NO',
                    'metadata_found'  => $riverMapMetadata ? 'YES' : 'NO',
                ]);
            @endphp

            @if($riverMapHtml || $riverMapPng)
            <div class="bg-gradient-to-br from-cyan-50 to-blue-50 rounded-xl shadow-xl p-4 sm:p-6 border-2 border-cyan-200 mb-6">

                {{-- Header --}}
                <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gradient-to-br from-cyan-500 to-blue-600 rounded-xl flex items-center justify-center shadow-lg shrink-0">
                            <i class="fas fa-water text-white text-lg sm:text-xl"></i>
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-lg sm:text-xl font-bold text-gray-800 flex items-center flex-wrap gap-2">
                                🌊 {{ __('messages.interactive_river_map') }}
                                <span class="text-xs bg-green-500 text-white px-2 py-0.5 rounded-full animate-pulse">NEW</span>
                            </h3>
                            <p class="text-xs sm:text-sm text-gray-500 mt-0.5">{{ __('messages.river_network_visualization') }}</p>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="flex flex-wrap gap-2 shrink-0">
                        @if($riverMapHtml)
                        <button onclick="openMapFullscreen()"
                            class="px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition shadow-lg hover:scale-105 text-xs sm:text-sm whitespace-nowrap">
                            <i class="fas fa-expand mr-1 sm:mr-2"></i>Fullscreen
                        </button>
                        <a href="/hidrologi/file/download/{{ $riverMapHtml->id }}"
                            class="px-3 sm:px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-semibold rounded-xl transition shadow-lg hover:scale-105 text-xs sm:text-sm whitespace-nowrap">
                            <i class="fas fa-download mr-1 sm:mr-2"></i>HTML
                        </a>
                        @endif
                        @if($riverMapPng)
                        <a href="/hidrologi/file/download/{{ $riverMapPng->id }}"
                            class="px-3 sm:px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-semibold rounded-xl transition shadow-lg hover:scale-105 text-xs sm:text-sm whitespace-nowrap">
                            <i class="fas fa-image mr-1 sm:mr-2"></i>PNG
                        </a>
                        @endif
                    </div>
                </div>

                {{-- Map Container --}}
                <div class="bg-white rounded-xl shadow-inner border-2 border-gray-200 overflow-hidden mb-4 relative">

                    @if($riverMapHtml)
                    {{-- Interactive HTML Map --}}
                    <div class="relative map-container">
                        <div id="mapLoadingOverlay" class="absolute inset-0 bg-white flex items-center justify-center z-10">
                            <div class="text-center">
                                <div class="animate-spin rounded-full h-16 w-16 border-b-4 border-blue-600 mx-auto mb-4"></div>
                                <p class="text-gray-600 font-semibold">{{ __('messages.map_loading') }}</p>
                                <p class="text-sm text-gray-400 mt-1">{{ __('messages.map_loading_wait') }}</p>
                            </div>
                        </div>
                        <iframe
                            id="riverMapFrame"
                            src="{{ route('hidrologi.file.preview', $riverMapHtml->id) }}"
                            class="w-full border-0"
                            style="height: 600px; min-height: 600px;"
                            title="{{ __('messages.interactive_river_map') }}"
                            onload="setTimeout(function(){ document.getElementById('mapLoadingOverlay').style.display='none'; }, 500);"
                        ></iframe>
                    </div>

                    {{-- Info Banner --}}
                    <div class="bg-gradient-to-r from-blue-50 to-cyan-50 border-t-2 border-blue-200 p-3">
                        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div class="flex items-center gap-2 text-sm text-gray-600">
                                <i class="fas fa-info-circle text-blue-500 shrink-0"></i>
                                <span>{{ __('messages.map_info_tip') }}</span>
                            </div>
                            <div class="flex gap-2 shrink-0">
                                <a href="{{ route('hidrologi.file.preview', $riverMapHtml->id) }}" target="_blank"
                                    class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-lg transition inline-flex items-center gap-2 whitespace-nowrap">
                                    <i class="fas fa-external-link-alt"></i>Fullscreen
                                </a>
                                <a href="{{ route('hidrologi.file.download', $riverMapHtml->id) }}"
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-semibold rounded-lg transition inline-flex items-center gap-2 whitespace-nowrap">
                                    <i class="fas fa-download"></i>{{ __('messages.download') }}
                                </a>
                            </div>
                        </div>
                    </div>

                    @elseif($riverMapPng)
                    {{-- Static PNG Fallback --}}
                    <img
                        src="{{ route('hidrologi.file.preview', $riverMapPng->id) }}"
                        alt="{{ __('messages.interactive_river_map') }}"
                        class="w-full h-auto"
                        style="max-height: 600px; object-fit: contain;"
                        onerror="this.onerror=null; this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22800%22 height=%22400%22%3E%3Crect fill=%22%23f0f0f0%22 width=%22800%22 height=%22400%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 text-anchor=%22middle%22 fill=%22%23999%22 font-size=%2216%22%3EImage not available%3C/text%3E%3C/svg%3E';"
                    />
                    <div class="bg-gradient-to-r from-yellow-50 to-orange-50 border-t-2 border-yellow-200 p-3">
                        <div class="flex items-center gap-2 text-sm text-yellow-800">
                            <i class="fas fa-exclamation-triangle text-yellow-600 shrink-0"></i>
                            <span>{{ __('messages.map_static_fallback') }}</span>
                        </div>
                    </div>

                    @else
                    {{-- No map at all --}}
                    <div class="flex items-center justify-center py-20 bg-gradient-to-br from-yellow-50 to-orange-50">
                        <div class="text-center max-w-sm">
                            <div class="w-20 h-20 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-5">
                                <i class="fas fa-map text-yellow-600 text-3xl"></i>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800 mb-2">{{ __('messages.map_not_ready_title') }}</h4>
                            <p class="text-gray-500 text-sm mb-4">{{ __('messages.map_not_ready_desc') }}</p>
                            <button onclick="location.reload()"
                                class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition shadow-lg hover:scale-105">
                                <i class="fas fa-sync-alt mr-2"></i>{{ __('messages.refresh_page') }}
                            </button>
                        </div>
                    </div>
                    @endif

                </div>

                {{-- Map Info Cards --}}
                @if($riverMapPng || $riverMapHtml)
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

                    <div class="bg-white rounded-xl p-4 shadow-sm border border-cyan-200">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center shrink-0">
                                <i class="fas fa-map-marker-alt text-blue-600"></i>
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">{{ __('messages.map_analysis_location') }}</p>
                                <p class="text-sm font-bold text-gray-800 truncate">{{ $job->location_name ?? 'N/A' }}</p>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 bg-blue-50 rounded-lg px-2 py-1.5 font-mono">
                            <i class="fas fa-crosshairs mr-1 text-blue-400"></i>
                            {{ number_format($job->latitude, 4) }}, {{ number_format($job->longitude, 4) }}
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-4 shadow-sm border border-cyan-200">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-9 h-9 bg-green-100 rounded-lg flex items-center justify-center shrink-0">
                                <i class="fas fa-layer-group text-green-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">{{ __('messages.map_layers') }}</p>
                                <p class="text-sm font-bold text-gray-800">4 {{ __('messages.map_data_sources') }}</p>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 bg-green-50 rounded-lg px-2 py-1.5">
                            <i class="fas fa-database mr-1 text-green-400"></i>
                            HydroSHEDS, JRC GSW, SRTM, OSM
                        </div>
                    </div>

                    <div class="bg-white rounded-xl p-4 shadow-sm border border-cyan-200">
                        <div class="flex items-center gap-3 mb-2">
                            <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center shrink-0">
                                <i class="fas fa-expand-arrows-alt text-purple-600"></i>
                            </div>
                            <div>
                                <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide">{{ __('messages.map_buffer_area') }}</p>
                                <p class="text-sm font-bold text-gray-800">10 km radius</p>
                            </div>
                        </div>
                        <div class="text-xs text-gray-500 bg-purple-50 rounded-lg px-2 py-1.5">
                            <i class="fas fa-ruler-combined mr-1 text-purple-400"></i>
                            {{ __('messages.map_river_network_area') }}
                        </div>
                    </div>

                </div>

                {{-- Metadata Accordion --}}
                @if($riverMapMetadata)
                <div class="mt-4 bg-white rounded-xl p-4 shadow-sm border border-cyan-200">
                    <button onclick="toggleRiverMetadata()" class="w-full flex items-center justify-between text-left">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-info-circle text-cyan-600"></i>
                            <span class="font-semibold text-gray-800">{{ __('messages.map_metadata_detail') }}</span>
                        </div>
                        <i id="metadataChevron" class="fas fa-chevron-down text-gray-400 transition-transform duration-300"></i>
                    </button>
                    <div id="riverMetadataContent" class="hidden mt-4 pt-4 border-t border-gray-100">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div class="bg-blue-50 rounded-xl p-3">
                                <p class="text-xs font-semibold text-blue-700 mb-2">📊 {{ __('messages.map_data_sources') }}</p>
                                <ul class="text-sm text-gray-700 space-y-1">
                                    <li><i class="fas fa-check-circle text-green-500 mr-2"></i>HydroSHEDS - Flow Accumulation</li>
                                    <li><i class="fas fa-check-circle text-green-500 mr-2"></i>JRC Global Surface Water</li>
                                    <li><i class="fas fa-check-circle text-green-500 mr-2"></i>SRTM DEM - Elevation</li>
                                    <li><i class="fas fa-check-circle text-green-500 mr-2"></i>OpenStreetMap - Basemap</li>
                                </ul>
                            </div>
                            <div class="bg-green-50 rounded-xl p-3">
                                <p class="text-xs font-semibold text-green-700 mb-2">🗺️ {{ __('messages.map_features') }}</p>
                                <ul class="text-sm text-gray-700 space-y-1">
                                    <li><i class="fas fa-water text-blue-500 mr-2"></i>River network visualization</li>
                                    <li><i class="fas fa-tint text-cyan-500 mr-2"></i>Water occurrence overlay</li>
                                    <li><i class="fas fa-mountain text-amber-500 mr-2"></i>Topography (DEM)</li>
                                    <li><i class="fas fa-layer-group text-purple-500 mr-2"></i>Interactive layer control</li>
                                </ul>
                            </div>
                        </div>
                        <div class="mt-4 p-3 bg-gradient-to-r from-cyan-50 to-blue-50 rounded-xl border border-cyan-200">
                            <p class="text-xs text-gray-600">
                                <i class="fas fa-lightbulb text-yellow-500 mr-2"></i>
                                <strong>{{ __('messages.tip') }}:</strong> {{ __('messages.map_usage_tip') }}
                            </p>
                        </div>
                    </div>
                </div>
                @endif
                @endif

            </div>

            @else
            {{-- No map available fallback --}}
            <div class="bg-yellow-50 rounded-xl border-2 border-yellow-200 p-5 text-center mb-6">
                <div class="w-14 h-14 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-map-marked-alt text-yellow-600 text-2xl"></i>
                </div>
                <h3 class="text-base sm:text-lg font-bold text-yellow-800 mb-2">{{ __('messages.map_not_available') }}</h3>
                <p class="text-yellow-700 text-sm mb-1">{{ __('messages.map_not_available_desc') }}</p>
                @if($riverMapPng)
                <p class="text-yellow-600 text-xs mb-4">
                    <i class="fas fa-info-circle mr-1"></i>
                    {{ __('messages.map_png_available_hint') }}
                </p>
                @else
                <div class="mb-4"></div>
                @endif
                <div class="flex flex-col sm:flex-row justify-center gap-2">
                    <button onclick="location.reload()"
                        class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition text-sm">
                        <i class="fas fa-sync-alt mr-2"></i>{{ __('messages.refresh_page') }}
                    </button>
                    @if(in_array($job->status, ['pending', 'submitted', 'processing']))
                    <span class="px-4 py-2 bg-gray-100 text-gray-500 font-semibold rounded-xl text-sm inline-flex items-center justify-center gap-2">
                        <i class="fas fa-clock"></i>{{ __('messages.map_being_processed') }}
                    </span>
                    @endif
                </div>
            </div>
            @endif
            <!-- Generated Files -->
            @if($job->files->count() > 0)
                <div class="bg-white rounded-xl sm:rounded-2xl shadow-lg p-4 sm:p-6 border border-gray-100">
                    <!-- Header Section -->
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-4 mb-6">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center flex-shrink-0">
                                <i class="fas fa-file-download text-blue-600 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-lg sm:text-xl font-bold text-gray-800">{{ __('messages.generated_files') }}</h3>
                                <p class="text-xs sm:text-sm text-gray-500">Total {{ $job->files->count() }} file</p>
                            </div>
                        </div>
                        
                        <!-- Filter Buttons - Responsive -->
                        <div class="flex flex-wrap gap-2">
                            <button onclick="filterFiles('all')" class="filter-btn active px-3 sm:px-4 py-2 text-xs font-semibold rounded-lg transition-all shadow-sm flex-shrink-0" data-type="all">
                                <i class="fas fa-th mr-1"></i><span class="hidden xs:inline">{{ __('messages.filter_all') }}</span><span class="xs:hidden">All</span>
                            </button>
                            <button onclick="filterFiles('png')" class="filter-btn px-3 sm:px-4 py-2 text-xs font-semibold rounded-lg transition-all shadow-sm flex-shrink-0" data-type="png">
                                <i class="fas fa-image mr-1"></i>PNG
                            </button>
                            <button onclick="filterFiles('csv')" class="filter-btn px-3 sm:px-4 py-2 text-xs font-semibold rounded-lg transition-all shadow-sm flex-shrink-0" data-type="csv">
                                <i class="fas fa-table mr-1"></i>CSV
                            </button>
                            <button onclick="filterFiles('json')" class="filter-btn px-3 sm:px-4 py-2 text-xs font-semibold rounded-lg transition-all shadow-sm flex-shrink-0" data-type="json">
                                <i class="fas fa-code mr-1"></i>JSON
                            </button>
                        </div>
                    </div>
                    
                    <!-- File List -->
                    <div class="grid grid-cols-1 gap-3 sm:gap-4">
                        @foreach($job->files->sortBy('display_order') as $file)
                            <div class="file-item border border-gray-200 rounded-lg p-3 sm:p-4 hover:shadow-md transition duration-200" data-file-type="{{ strtolower($file->file_type) }}">
                                <!-- File Info and Actions -->
                                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                                    <!-- File Details -->
                                    <div class="flex-1 min-w-0">
                                        <h4 class="font-medium text-gray-800 mb-1 flex items-center text-sm sm:text-base break-words">
                                            @if($file->file_type === 'png')
                                                <i class="fas fa-image text-blue-500 mr-2 flex-shrink-0"></i>
                                            @elseif($file->file_type === 'csv')
                                                <i class="fas fa-table text-green-500 mr-2 flex-shrink-0"></i>
                                            @elseif($file->file_type === 'json')
                                                <i class="fas fa-code text-orange-500 mr-2 flex-shrink-0"></i>
                                            @else
                                                <i class="fas fa-file text-gray-500 mr-2 flex-shrink-0"></i>
                                            @endif
                                            <span class="break-all">{{ $file->display_name ?? $file->filename }}</span>
                                        </h4>
                                        @if($file->description)
                                            <p class="text-xs text-gray-600 mb-2 break-words">{{ $file->description }}</p>
                                        @endif
                                        <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs text-gray-500">
                                            <span class="flex items-center"><i class="fas fa-file mr-1"></i>{{ strtoupper($file->file_type) }}</span>
                                            <span class="flex items-center"><i class="fas fa-weight mr-1"></i>{{ number_format($file->file_size_mb, 2) }} MB</span>
                                            @if($file->created_at)
                                                <span class="flex items-center hidden sm:inline-flex"><i class="fas fa-clock mr-1"></i>{{ $file->created_at->format('d M Y, H:i') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Action Buttons - Stack on mobile, inline on desktop -->
                                    <div class="flex flex-row sm:flex-row gap-2 sm:ml-4 flex-shrink-0">
                                        @if($file->file_type === 'png')
                                            <button onclick="viewImage({{ $file->id }}, '{{ addslashes($file->display_name ?? $file->filename) }}')" class="flex-1 sm:flex-initial px-3 py-2 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition duration-200 text-xs sm:text-sm whitespace-nowrap">
                                                <i class="fas fa-eye mr-1"></i><span class="hidden sm:inline">View</span><i class="fas fa-eye sm:hidden"></i>
                                            </button>
                                        @elseif($file->file_type === 'csv')
                                            <button onclick="viewCSV({{ $file->id }}, '{{ addslashes($file->display_name ?? $file->filename) }}')" class="flex-1 sm:flex-initial px-3 py-2 bg-green-100 text-green-700 rounded hover:bg-green-200 transition duration-200 text-xs sm:text-sm whitespace-nowrap">
                                                <i class="fas fa-eye mr-1"></i><span class="hidden sm:inline">View</span><i class="fas fa-eye sm:hidden"></i>
                                            </button>
                                        @elseif($file->file_type === 'json')
                                            <button onclick="viewJSON({{ $file->id }}, '{{ addslashes($file->display_name ?? $file->filename) }}')" class="flex-1 sm:flex-initial px-3 py-2 bg-orange-100 text-orange-700 rounded hover:bg-orange-200 transition duration-200 text-xs sm:text-sm whitespace-nowrap">
                                                <i class="fas fa-eye mr-1"></i><span class="hidden sm:inline">View</span><i class="fas fa-eye sm:hidden"></i>
                                            </button>
                                        @endif
                                        <a href="{{ route('hidrologi.file.download', $file->id) }}" class="flex-1 sm:flex-initial px-3 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition duration-200 text-xs sm:text-sm whitespace-nowrap text-center">
                                            <i class="fas fa-download mr-1"></i><span class="hidden sm:inline">Download</span><i class="fas fa-download sm:hidden"></i>
                                        </a>
                                    </div>
                                </div>
                                
                                <!-- Preview Container (Hidden by default) -->
                                <div id="preview-{{ $file->id }}" class="preview-container hidden mt-4 border-t pt-4">
                                    <div class="flex justify-between items-center mb-2">
                                        <span class="text-sm font-medium text-gray-700">Preview:</span>
                                        <button onclick="closePreview({{ $file->id }})" class="text-gray-500 hover:text-gray-700">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div id="preview-content-{{ $file->id }}" class="preview-content">
                                        <!-- Content will be loaded here -->
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar (Right) -->
        <div class="lg:col-span-1 space-y-5 sm:space-y-6 order-1 lg:order-2">
            <!-- Timeline Card -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-3xl shadow-xl p-5 sm:p-6 border-2 border-blue-200">
                <div class="flex items-center gap-3 mb-5 sm:mb-6">
                    <div class="w-11 h-11 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg shrink-0">
                        <i class="fas fa-clock text-white text-lg"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-extrabold text-blue-900">Timeline</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex items-start p-3 sm:p-4 bg-white rounded-2xl shadow-sm hover:shadow-md transition-all">
                        <div class="w-3 h-3 bg-blue-600 rounded-full mt-1.5 mr-3 shadow shrink-0"></div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs sm:text-sm font-extrabold text-gray-900">{{ __('messages.created_label') }}</p>
                            <p class="text-xs text-gray-600 mt-0.5 break-words">{{ $job->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    @if($job->submitted_at)
                        <div class="flex items-start p-3 sm:p-4 bg-white rounded-2xl shadow-sm hover:shadow-md transition-all">
                            <div class="w-3 h-3 bg-cyan-600 rounded-full mt-1.5 mr-3 shadow shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs sm:text-sm font-extrabold text-gray-900">{{ __('messages.submitted_label') }}</p>
                                <p class="text-xs text-gray-600 mt-0.5 break-words">{{ $job->submitted_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                    @if($job->started_at)
                        <div class="flex items-start p-3 sm:p-4 bg-white rounded-2xl shadow-sm hover:shadow-md transition-all">
                            <div class="w-3 h-3 bg-yellow-600 rounded-full mt-1.5 mr-3 shadow shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs sm:text-sm font-extrabold text-gray-900">{{__('messages.started_processing') }}</p>
                                <p class="text-xs text-gray-600 mt-0.5 break-words">{{ $job->started_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                    @if($job->completed_at)
                        <div class="flex items-start p-3 sm:p-4 bg-white rounded-2xl shadow-sm hover:shadow-md transition-all">
                            <div class="w-3 h-3 bg-green-600 rounded-full mt-1.5 mr-3 shadow shrink-0"></div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs sm:text-sm font-extrabold text-gray-900">{{__('messages.finished_label') }}</p>
                                <p class="text-xs text-gray-600 mt-0.5 break-words">{{ $job->completed_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistics Card -->
            <div class="bg-gradient-to-br from-purple-50 to-purple-100 rounded-3xl shadow-xl p-5 sm:p-6 border-2 border-purple-200">
                <div class="flex items-center gap-3 mb-5 sm:mb-6">
                    <div class="w-11 h-11 bg-gradient-to-br from-purple-600 to-purple-700 rounded-2xl flex items-center justify-center shadow-lg shrink-0">
                        <i class="fas fa-chart-bar text-white text-lg"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-extrabold text-purple-900">{{ __('messages.statistics') }}</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center p-4 bg-white rounded-2xl shadow-sm hover:shadow-md transition-all">
                        <span class="text-xs sm:text-sm font-bold text-gray-700 flex items-center gap-2">
                            <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-image text-blue-600"></i>
                            </div>
                            File PNG
                        </span>
                        <span class="font-extrabold text-gray-900 text-base sm:text-lg">{{ $job->png_count }}</span>
                    </div>
                    <div class="flex justify-between items-center p-4 bg-white rounded-2xl shadow-sm hover:shadow-md transition-all">
                        <span class="text-xs sm:text-sm font-bold text-gray-700 flex items-center gap-2">
                            <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-table text-green-600"></i>
                            </div>
                            File CSV
                        </span>
                        <span class="font-extrabold text-gray-900 text-base sm:text-lg">{{ $job->csv_count }}</span>
                    </div>
                    <div class="flex justify-between items-center p-4 bg-white rounded-2xl shadow-sm hover:shadow-md transition-all">
                        <span class="text-xs sm:text-sm font-bold text-gray-700 flex items-center gap-2">
                            <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                <i class="fas fa-code text-orange-600"></i>
                            </div>
                            File JSON
                        </span>
                        <span class="font-extrabold text-gray-900 text-base sm:text-lg">{{ $job->json_count }}</span>
                    </div>
                    <div class="flex justify-between items-center p-4 bg-gradient-to-r from-purple-600 via-purple-700 to-indigo-600 rounded-2xl shadow-lg">
                        <span class="text-xs sm:text-sm font-extrabold text-white flex items-center gap-2">
                            <div class="w-8 h-8 bg-white/20 rounded-lg flex items-center justify-center">
                                <i class="fas fa-folder text-white"></i>
                            </div>
                            {{ __('messages.total_files') }}
                        </span>
                        <span class="font-extrabold text-white text-xl sm:text-2xl">{{ $job->total_files }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Clear all floats and ensure proper footer placement -->
    <div class="clear-both h-8"></div>
</div>

@push('styles')
<style>
/* River Map Styles */
#riverMapFrame {
    transition: opacity 0.5s ease;
}

#mapLoadingOverlay {
    transition: opacity 0.5s ease, display 0s 0.5s;
}

.map-container {
    position: relative;
    overflow: hidden;
}

.map-container iframe {
    display: block;
    width: 100%;
}

/* Map Controls */
.map-controls button {
    transition: all 0.2s ease;
}

.map-controls button:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.map-controls button:active {
    transform: translateY(0);
}

/* Mobile responsive adjustments */
@media (max-width: 768px) {
    #riverMapFrame {
        height: 400px !important;
        min-height: 400px !important;
    }
    
    .container {
        padding-left: 0.5rem !important;
        padding-right: 0.5rem !important;
    }
    
    /* Ensure text doesn't overflow on small screens */
    .break-words {
        word-break: break-word;
        overflow-wrap: break-word;
    }
    
    .break-all {
        word-break: break-all;
        overflow-wrap: anywhere;
    }
    
    /* Adjust font sizes for mobile */
    .mobile-text-sm {
        font-size: 0.75rem;
    }
    
    .mobile-text-base {
        font-size: 0.875rem;
    }
    
    /* Better button layout on mobile */
    .mobile-btn-stack > * {
        width: 100%;
        margin-bottom: 0.5rem;
    }
    
    .mobile-btn-stack > *:last-child {
        margin-bottom: 0;
    }
    
    /* Stack flex items on mobile */
    .flex-col-mobile {
        flex-direction: column;
    }
    
    /* Full width buttons on mobile */
    .w-full-mobile {
        width: 100%;
    }
    
    /* Hide on mobile */
    .hidden-mobile {
        display: none !important;
    }
    
    /* Adjust grid gaps */
    .grid {
        gap: 1rem;
    }
    
    /* Better sidebar stacking - sidebar appears FIRST on mobile */
    .order-1 {
        order: 1 !important;
    }
    
    .order-2 {
        order: 2 !important;
    }
    
    /* Prevent horizontal overflow */
    body {
        overflow-x: hidden;
    }
    
    .container, .max-w-7xl {
        max-width: 100vw;
        overflow-x: hidden;
    }
    
    /* Fix grid column spans */
    .grid > * {
        min-width: 0;
    }
    
    /* Ensure map container doesn't overflow */
    .map-container,
    .bg-white.rounded-xl {
        max-width: 100%;
        overflow: hidden;
    }
    
    /* Fix image responsiveness */
    img {
        max-width: 100%;
        height: auto;
    }
}

/* Extra small screens */
@media (max-width: 480px) {
    /* Hide text on very small screens, keep icons only */
    .xs\:hidden {
        display: none;
    }
    
    .xs\:inline {
        display: inline;
    }
    
    /* Reduce padding on small screens */
    .sm\:p-6 {
        padding: 1rem !important;
    }
    
    /* Stack buttons vertically */
    .filter-btn {
        min-width: auto;
        font-size: 0.7rem;
    }
    
    /* Smaller icons on mobile */
    .w-12, .h-12 {
        width: 2.5rem !important;
        height: 2.5rem !important;
    }
    
    /* Reduce header size */
    h3 {
        font-size: 1rem !important;
    }
    
    /* Force single column on very small screens */
    .grid {
        grid-template-columns: 1fr !important;
    }
    
    .md\:grid-cols-3,
    .sm\:grid-cols-2 {
        grid-template-columns: 1fr !important;
    }
}

/* Landscape mobile optimization */
@media (max-width: 768px) and (orientation: landscape) {
    .grid {
        grid-template-columns: 1fr !important;
    }
    
    .lg\:col-span-2,
    .lg\:col-span-1 {
        grid-column: 1 / -1 !important;
    }
}

/* Prevent layout shifts */
* {
    box-sizing: border-box;
}

/* Smooth scrolling */
html {
    scroll-behavior: smooth;
}

/* Prevent horizontal scroll */
body, html {
    overflow-x: hidden;
    max-width: 100vw;
}

/* Fix main content wrapper */
main {
    overflow-x: hidden;
}

/* Ensure grid doesn't break layout */
.grid {
    width: 100%;
    max-width: 100%;
}

/* Utility classes for layout stability */
.layout-stable {
    position: relative;
    contain: layout;
}

.no-overflow {
    overflow: hidden;
}

.full-width {
    width: 100%;
    max-width: 100%;
}

/* Fix any floating issues */
.clear-both {
    clear: both;
}

.clearfix::after {
    content: "";
    display: table;
    clear: both;
}

/* Mobile-first responsive breakpoint fixes */
@media screen and (max-width: 640px) {
    * {
        max-width: 100vw !important;
    }
    
    .container {
        padding-left: 0.5rem;
        padding-right: 0.5rem;
    }
}

/* Clear floats */
.clearfix::after {
    content: "";
    display: table;
    clear: both;
}

/* Ensure proper container behavior */
.container, .max-w-7xl {
    position: relative;
    z-index: 1;
}

/* Fix any potential z-index issues */
.relative {
    z-index: auto;
}

/* Ensure all sections are properly contained */
.space-y-4 > *,
.space-y-6 > * {
    position: relative;
}

/* Grid fixes */
.grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(0, 1fr));
}

@media (min-width: 1024px) {
    .lg\:grid-cols-3 {
        grid-template-columns: repeat(3, 1fr);
    }
    
    .lg\:col-span-2 {
        grid-column: span 2 / span 2;
    }
    
    .lg\:col-span-1 {
        grid-column: span 1 / span 1;
    }
}

/* Custom styles for summary sections */
.summary-card {
    transition: all 0.3s ease;
}

.summary-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.stat-item {
    position: relative;
    padding-left: 1rem;
}

.stat-item::before {
    content: '';
    position: absolute;
    left: 0;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 60%;
    background: linear-gradient(to bottom, #3b82f6, #1d4ed8);
    border-radius: 2px;
}

.recommendation-item {
    animation: slideIn 0.3s ease-out;
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateX(-10px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.metric-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    font-weight: 600;
    transition: all 0.2s;
}

.metric-badge:hover {
    transform: scale(1.05);
}

/* Loading animation for auto-refresh */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.pulse-animation {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

/* Log Section Styles */
.log-section {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.log-section:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2);
}

.section-content {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.section-chevron-rotate {
    transform: rotate(180deg);
}

/* Scrollbar Styling for Log Containers */
.bg-black::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

.bg-black::-webkit-scrollbar-track {
    background: #1f2937;
    border-radius: 4px;
}

.bg-black::-webkit-scrollbar-thumb {
    background: #4b5563;
    border-radius: 4px;
}

.bg-black::-webkit-scrollbar-thumb:hover {
    background: #6b7280;
}

/* Section Tab Styling */
.section-tab {
    transition: all 0.2s ease;
}

.section-tab:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
}

/* Highlight Animation */
@keyframes highlight {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(234, 179, 8, 0);
    }
    50% {
        box-shadow: 0 0 0 10px rgba(234, 179, 8, 0.4);
    }
}

.ring-yellow-500 {
    animation: highlight 2s ease-in-out;
}

/* Smooth Rotate Animation */
.rotate-180 {
    transform: rotate(180deg);
}

i.fas {
    transition: transform 0.3s ease;
}

/* Glow Effect for Headers */
@keyframes glow {
    0%, 100% {
        text-shadow: 0 0 5px rgba(74, 222, 128, 0.5);
    }
    50% {
        text-shadow: 0 0 20px rgba(74, 222, 128, 0.8);
    }
}

.animate-pulse {
    animation: glow 2s ease-in-out infinite;
}

/* Print Styles */
@media print {
    .log-section {
        page-break-inside: avoid;
        border: 1px solid #e5e7eb;
        margin-bottom: 20px;
    }
    
    .section-content {
        display: block !important;
    }
    
    button {
        display: none !important;
    }
}

/* Tooltip styles */
.tooltip {
    position: relative;
    display: inline-block;
}

.tooltip .tooltiptext {
    visibility: hidden;
    width: 200px;
    background-color: #333;
    color: #fff;
    text-align: center;
    border-radius: 6px;
    padding: 5px;
    position: absolute;
    z-index: 1;
    bottom: 125%;
    left: 50%;
    margin-left: -100px;
    opacity: 0;
    transition: opacity 0.3s;
    font-size: 12px;
}

.tooltip:hover .tooltiptext {
    visibility: visible;
    opacity: 1;
}

/* Filter Button Styles */
.filter-btn {
    background-color: #f3f4f6;
    color: #374151;
    transition: all 0.2s ease;
}

.filter-btn:hover {
    background-color: #e5e7eb;
}

.filter-btn.active {
    background-color: #2563eb;
    color: white;
}

/* File Item Styles */
.file-item {
    transition: all 0.2s ease;
}

.file-item:hover {
    border-color: #3b82f6;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
}

/* Preview Container Styles */
.preview-container {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.preview-content img {
    transition: transform 0.2s ease;
}

.preview-content img:hover {
    transform: scale(1.02);
}

/* Table Styles for CSV Preview */
.preview-content table {
    font-size: 0.875rem;
    border-collapse: separate;
    border-spacing: 0;
}

.preview-content table th {
    position: sticky;
    top: 0;
    background-color: #f9fafb;
    z-index: 10;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.preview-content table tbody tr {
    transition: all 0.15s ease;
}

.preview-content table tbody tr:hover {
    background-color: #eff6ff !important;
    transform: scale(1.01);
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.preview-content table td {
    max-width: 200px;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Scrollbar for CSV table */
.preview-content > div > div::-webkit-scrollbar {
    width: 10px;
    height: 10px;
}

.preview-content > div > div::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 5px;
}

.preview-content > div > div::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 5px;
}

.preview-content > div > div::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endpush

@push('scripts')
<script>
// Auto-refresh status for processing jobs
@if(in_array($job->status, ['pending', 'submitted', 'processing']))
console.log('🔄 Auto-refresh enabled - checking status every 10 seconds');

let refreshInterval = setInterval(function() {
    const timestamp = new Date().toLocaleTimeString();
    console.log('[' + timestamp + '] 🔍 Checking job status...');
    
    fetch('/hidrologi/status/{{ $job->id }}')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const job = data.job;
                console.log('[' + timestamp + '] ✓ Status: ' + job.status + ', Progress: ' + job.progress + '%');
                
                // Update progress bar
                const progressBar = document.getElementById('progress-bar');
                const progressPercent = document.getElementById('progress-percent');
                if (progressBar && progressPercent) {
                    progressBar.style.width = job.progress + '%';
                    progressPercent.textContent = job.progress + '%';
                }
                
                // If status changed to completed or failed, reload page
                if (['completed', 'completed_with_warning', 'failed', 'cancelled'].includes(job.status)) {
                    console.log('[' + timestamp + '] 🎉 Job finished with status: ' + job.status);
                    console.log('🔄 Reloading page to show results...');
                    clearInterval(refreshInterval);
                    
                    // Show notification before reload
                    if (typeof Swal !== 'undefined') {
                        let icon = 'error';
                        let title = 'Job Failed';
                        
                        if (job.status === 'completed') {
                            icon = 'success';
                            title = 'Analysis Complete!';
                        } else if (job.status === 'completed_with_warning') {
                            icon = 'warning';
                            title = 'Completed with Warnings';
                        } else if (job.status === 'cancelled') {
                            icon = 'info';
                            title = 'Job Cancelled';
                        }
                        
                        Swal.fire({
                            icon: icon,
                            title: title,
                            text: 'Page will reload to show results...',
                            showConfirmButton: false,
                            timer: 2000,
                            allowOutsideClick: false
                        }).then(function() {
                            location.reload();
                        });
                    } else {
                        // Fallback if SweetAlert not loaded
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    }
                }
            }
        })
        .catch(function(error) {
            console.error('[' + timestamp + '] ❌ Error checking status:', error);
        });
}, 10000); // Check every 10 seconds

// Cleanup on page unload
window.addEventListener('beforeunload', function() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
        console.log('🛑 Auto-refresh stopped');
    }
});
@endif

function cancelJob(jobId) {
    Swal.fire({
        title: 'Batalkan Pekerjaan?',
        text: "Apakah Anda yakin ingin membatalkan pekerjaan ini?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Batalkan!',
        cancelButtonText: 'Tidak, Biarkan'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Membatalkan...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/hidrologi/cancel/${jobId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message || 'Pekerjaan berhasil dibatalkan.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Gagal membatalkan pekerjaan'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat membatalkan pekerjaan'
                });
            });
        }
    });
}

function deleteJob(jobId) {
    Swal.fire({
        title: 'Hapus Pekerjaan?',
        html: "Apakah Anda yakin ingin menghapus pekerjaan ini?<br><strong class='text-red-600'>Tindakan ini tidak dapat dibatalkan!</strong>",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Menghapus...',
                text: 'Mohon tunggu sebentar',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch(`/hidrologi/delete/${jobId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('HTTP error! status: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        text: data.message || 'Pekerjaan berhasil dihapus.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = '/hidrologi';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Gagal menghapus pekerjaan'
                    });
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'Terjadi kesalahan saat menghapus pekerjaan: ' + error.message
                });
            });
        }
    });
}

// Toggle full logs display
function toggleLogs() {
    const logsContent = document.getElementById('fullLogsContent');
    const toggleBtn = document.getElementById('toggleLogsBtn');
    const toggleText = document.getElementById('toggleLogsText');
    const chevron = toggleBtn.querySelector('i');
    
    if (logsContent.classList.contains('hidden')) {
        logsContent.classList.remove('hidden');
        toggleText.textContent = 'Hide';
        chevron.classList.remove('fa-chevron-down');
        chevron.classList.add('fa-chevron-up');
    } else {
        logsContent.classList.add('hidden');
        toggleText.textContent = 'Show';
        chevron.classList.remove('fa-chevron-up');
        chevron.classList.add('fa-chevron-down');
    }
}

// Copy summary text to clipboard
function copySummaryText(elementId) {
    const element = document.getElementById(elementId);
    if (element) {
        const text = element.innerText;
        navigator.clipboard.writeText(text).then(() => {
            // Show notification
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'success',
                    title: 'Copied!',
                    text: 'Summary text copied to clipboard',
                    showConfirmButton: false,
                    timer: 1500,
                    toast: true,
                    position: 'top-end'
                });
            } else {
                alert('Summary text copied to clipboard!');
            }
        }).catch(err => {
            console.error('Failed to copy text: ', err);
            alert('Failed to copy text to clipboard');
        });
    }
}

// Print summary section
function printSummary() {
    window.print();
}

// Export summary as JSON
function exportSummaryJSON() {
    const summaryData = @json($summary ?? []);
    const dataStr = JSON.stringify(summaryData, null, 2);
    const dataBlob = new Blob([dataStr], {type: 'application/json'});
    const url = URL.createObjectURL(dataBlob);
    const link = document.createElement('a');
    link.href = url;
    link.download = 'summary_{{ $job->job_id }}.json';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    URL.revokeObjectURL(url);
}

// Add animation to summary cards on page load
document.addEventListener('DOMContentLoaded', function() {
    const summaryCards = document.querySelectorAll('.summary-card');
    summaryCards.forEach((card, index) => {
        setTimeout(() => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.5s ease';
            
            setTimeout(() => {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 50);
        }, index * 100);
    });
});
</script>

<script>
// Toggle full logs display
function toggleLogs() {
    const logsContent = document.getElementById('fullLogsContent');
    const toggleBtn = document.getElementById('toggleLogsBtn');
    const toggleText = document.getElementById('toggleLogsText');
    const chevron = toggleBtn.querySelector('i');
    
    if (logsContent.classList.contains('hidden')) {
        logsContent.classList.remove('hidden');
        toggleText.textContent = 'Hide';
        chevron.classList.remove('fa-chevron-down');
        chevron.classList.add('fa-chevron-up');
    } else {
        logsContent.classList.add('hidden');
        toggleText.textContent = 'Show';
        chevron.classList.remove('fa-chevron-up');
        chevron.classList.add('fa-chevron-down');
    }
}

// Download logs as text file
function downloadLogs() {
    const logContent = @json($fullLogs ?? '');
    const blob = new Blob([logContent], { type: 'text/plain' });
    const url = window.URL.createObjectURL(blob);
    const a = document.createElement('a');
    a.href = url;
    a.download = 'analysis_logs_{{ $job->job_id }}.txt';
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
    document.body.removeChild(a);
    
    // Show success message
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'success',
            title: 'Downloaded!',
            text: 'Logs file has been downloaded',
            timer: 2000,
            showConfirmButton: false
        });
    }
}

// Copy logs to clipboard
function copyLogs() {
    const logContent = @json($fullLogs ?? '');
    navigator.clipboard.writeText(logContent).then(function() {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'Logs copied to clipboard',
                timer: 2000,
                showConfirmButton: false
            });
        } else {
            alert('Logs copied to clipboard!');
        }
    }).catch(function(err) {
        console.error('Failed to copy:', err);
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Failed!',
                text: 'Failed to copy logs to clipboard'
            });
        }
    });
}

// Search in logs
function searchInLogs() {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: 'Search in Logs',
            input: 'text',
            inputPlaceholder: 'Enter search term...',
            showCancelButton: true,
            confirmButtonText: 'Search',
            cancelButtonText: 'Cancel',
            inputValidator: (value) => {
                if (!value) {
                    return 'Please enter a search term!';
                }
            }
        }).then((result) => {
            if (result.isConfirmed && result.value) {
                const searchTerm = result.value;
                const logsContainer = document.getElementById('logsContainer');
                const pre = logsContainer.querySelector('pre');
                const originalText = @json($fullLogs ?? '');
                
                // Highlight search term
                const regex = new RegExp(searchTerm, 'gi');
                const highlightedText = originalText.replace(regex, match => {
                    return `<mark class="bg-yellow-300 text-black">${match}</mark>`;
                });
                
                pre.innerHTML = highlightedText;
                
                // Scroll to first match
                const firstMatch = logsContainer.querySelector('mark');
                if (firstMatch) {
                    firstMatch.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    Swal.fire({
                        icon: 'success',
                        title: 'Found!',
                        text: `Found "${searchTerm}" in logs`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    Swal.fire({
                        icon: 'info',
                        title: 'Not Found',
                        text: `"${searchTerm}" not found in logs`,
                        timer: 2000,
                        showConfirmButton: false
                    });
                }
            }
        });
    }
}

// File filtering functions
function filterFiles(type) {
    const fileItems = document.querySelectorAll('.file-item');
    const filterBtns = document.querySelectorAll('.filter-btn');
    
    // Update active button
    filterBtns.forEach(btn => {
        btn.classList.remove('active', 'bg-blue-600', 'text-white');
        btn.classList.add('bg-gray-100', 'text-gray-700');
    });
    
    const activeBtn = document.querySelector(`[data-type="${type}"]`);
    if (activeBtn) {
        activeBtn.classList.add('active', 'bg-blue-600', 'text-white');
        activeBtn.classList.remove('bg-gray-100', 'text-gray-700');
    }
    
    // Filter files
    fileItems.forEach(item => {
        const fileType = item.getAttribute('data-file-type');
        if (type === 'all' || fileType === type) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

// View PNG Image - Preview inline dengan proteksi anti-crash
function viewImage(fileId, fileName) {
    const previewContainer = document.getElementById(`preview-${fileId}`);
    const previewContent = document.getElementById(`preview-content-${fileId}`);
    
    if (previewContainer.classList.contains('hidden')) {
        // Show loading
        previewContent.innerHTML = `
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                <span class="ml-3 text-gray-600">Memuat gambar...</span>
            </div>
        `;
        previewContainer.classList.remove('hidden');
        
        const downloadUrl = `/hidrologi/file/download/${fileId}`;
        console.log('Loading image from:', downloadUrl);
        
        // Fetch dengan HEAD request dulu untuk cek ukuran
        fetch(downloadUrl, { method: 'HEAD' })
            .then(response => {
                const contentLength = response.headers.get('Content-Length');
                const fileSizeBytes = parseInt(contentLength || '0');
                const fileSizeMB = (fileSizeBytes / 1024 / 1024).toFixed(2);
                
                console.log('Image size:', fileSizeMB, 'MB');
                
                // Jika file > 10MB, jangan preview inline - terlalu besar
                if (fileSizeBytes > 10 * 1024 * 1024) {
                    previewContent.innerHTML = `
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-4xl mb-3"></i>
                            <p class="text-yellow-800 font-semibold text-lg mb-2">Gambar Terlalu Besar</p>
                            <p class="text-yellow-700 text-sm mb-4">Ukuran: ${fileSizeMB} MB. Gunakan tombol di bawah untuk melihat gambar.</p>
                            <div class="space-x-2">
                                <button onclick="window.open('/hidrologi/file/download/${fileId}', '_blank')" 
                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                    <i class="fas fa-external-link-alt mr-1"></i>Buka di Tab Baru
                                </button>
                                <a href="/hidrologi/file/download/${fileId}" download="${fileName}"
                                   class="inline-block px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                                    <i class="fas fa-download mr-1"></i>Download
                                </a>
                            </div>
                        </div>
                    `;
                    return Promise.reject('File too large');
                }
                
                // File cukup kecil, lanjutkan fetch untuk preview
                return fetch(downloadUrl);
            })
            .then(response => {
                if (!response || !response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.blob();
            })
            .then(blob => {
                console.log('Image blob received:', blob.type, blob.size, 'bytes');
                
                // Convert blob to data URL (untuk bypass CSP yang tidak allow blob:)
                const reader = new FileReader();
                reader.onload = function(e) {
                    const imageUrl = e.target.result; // data: URL
                    const fileSizeKB = (blob.size / 1024).toFixed(2);
                    const fileSizeMB = (blob.size / 1024 / 1024).toFixed(2);
                    const displaySize = blob.size > 1024 * 1024 ? `${fileSizeMB} MB` : `${fileSizeKB} KB`;
                    
                    console.log('Image converted to data URL, size:', displaySize);
                    
                    previewContent.innerHTML = `
                        <div class="bg-gray-50 rounded-lg p-4">
                            <!-- Image Info Bar -->
                            <div class="flex justify-between items-center mb-3 p-2 bg-white rounded border border-gray-200">
                                <div class="text-sm text-gray-600">
                                    <i class="fas fa-image text-blue-600 mr-2"></i>
                                    <span class="font-semibold">${fileName}</span>
                                    <span class="ml-2 text-xs text-gray-500">(${displaySize})</span>
                                </div>
                                <div class="space-x-2">
                                    <button onclick="window.open('/hidrologi/file/download/${fileId}', '_blank')" 
                                            class="text-xs px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition">
                                        <i class="fas fa-external-link-alt mr-1"></i>Tab Baru
                                    </button>
                                    <a href="/hidrologi/file/download/${fileId}" download="${fileName}"
                                       class="text-xs px-3 py-1 bg-green-100 text-green-700 rounded hover:bg-green-200 transition inline-block">
                                        <i class="fas fa-download mr-1"></i>Download
                                    </a>
                                </div>
                            </div>
                            
                            <!-- Image Preview dengan loading lazy -->
                            <div class="overflow-auto rounded border border-gray-200 bg-white" style="max-height: 500px;">
                                <img src="${imageUrl}" 
                                     alt="${fileName}"
                                     loading="lazy"
                                     class="max-w-full h-auto mx-auto cursor-zoom-in hover:opacity-90 transition"
                                     style="max-height: 480px;"
                                     onclick="openImageFullscreen('${imageUrl}', '${fileName}')">
                            </div>
                            
                            <div class="text-center mt-3">
                                <span class="text-xs text-gray-500">
                                    <i class="fas fa-info-circle mr-1"></i>
                                    Klik gambar untuk melihat fullscreen
                                </span>
                            </div>
                        </div>
                    `;
                    
                    const img = previewContent.querySelector('img');
                    img.onload = function() {
                        console.log('Image loaded successfully from data URL');
                    };
                    img.onerror = function() {
                        console.error('Failed to load image from data URL');
                        previewContent.innerHTML = `
                            <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                                <i class="fas fa-exclamation-triangle text-red-600 text-3xl mb-2"></i>
                                <p class="text-red-700 font-medium">Gagal memuat gambar</p>
                                <p class="text-red-600 text-sm mt-1">Gambar corrupt atau format tidak didukung</p>
                                <div class="mt-3 space-x-2">
                                    <button onclick="viewImage(${fileId}, '${fileName}')" 
                                            class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                        <i class="fas fa-redo mr-1"></i>Coba Lagi
                                    </button>
                                    <a href="/hidrologi/file/download/${fileId}" download="${fileName}"
                                       class="inline-block px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                                        <i class="fas fa-download mr-1"></i>Download
                                    </a>
                                </div>
                            </div>
                        `;
                    };
                };
                
                reader.onerror = function() {
                    console.error('Failed to convert blob to data URL');
                    previewContent.innerHTML = `
                        <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                            <i class="fas fa-exclamation-triangle text-red-600 text-3xl mb-2"></i>
                            <p class="text-red-700 font-medium">Gagal memproses gambar</p>
                            <p class="text-red-600 text-sm mt-1">Tidak dapat membaca file image</p>
                            <div class="mt-3">
                                <button onclick="window.open('/hidrologi/file/download/${fileId}', '_blank')" 
                                        class="px-4 py-2 text-sm bg-blue-600 hover:bg-blue-700 text-white rounded">
                                    <i class="fas fa-external-link-alt mr-1"></i>
                                    Buka di Tab Baru
                                </button>
                            </div>
                        </div>
                    `;
                };
                
                // Read blob as data URL
                reader.readAsDataURL(blob);
            })
            .catch(error => {
                if (error === 'File too large') {
                    // Already handled above
                    return;
                }
                
                console.error('Error loading image:', error);
                previewContent.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-3xl mb-2"></i>
                        <p class="text-red-700 font-medium">Gagal memuat gambar</p>
                        <p class="text-red-600 text-sm mt-1">${error.message}</p>
                        <div class="mt-3 space-x-2">
                            <button onclick="viewImage(${fileId}, '${fileName}')" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                <i class="fas fa-redo mr-1"></i>Coba Lagi
                            </button>
                            <button onclick="window.open('/hidrologi/file/download/${fileId}', '_blank')" 
                                    class="px-4 py-2 bg-purple-600 text-white rounded hover:bg-purple-700 transition">
                                <i class="fas fa-external-link-alt mr-1"></i>Buka Tab Baru
                            </button>
                            <a href="/hidrologi/file/download/${fileId}" download="${fileName}"
                               class="inline-block px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                                <i class="fas fa-download mr-1"></i>Download
                            </a>
                        </div>
                    </div>
                `;
            });
    } else {
        previewContainer.classList.add('hidden');
    }
}

// Open image in fullscreen modal
function openImageFullscreen(imageUrl, fileName) {
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            title: fileName,
            imageUrl: imageUrl,
            imageAlt: fileName,
            width: '90%',
            showCloseButton: true,
            showConfirmButton: false,
            customClass: {
                image: 'max-h-screen'
            },
            background: '#000',
            backdrop: 'rgba(0,0,0,0.9)'
        });
    } else {
        // Fallback: open in new tab
        window.open(imageUrl, '_blank');
    }
}

// Download image from blob
function downloadImageFromBlob(blobUrl, fileName) {
    const link = document.createElement('a');
    link.href = blobUrl;
    link.download = fileName;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// View CSV File - Inline preview dengan limit untuk menghindari crash
function viewCSV(fileId, fileName) {
    const previewContainer = document.getElementById(`preview-${fileId}`);
    const previewContent = document.getElementById(`preview-content-${fileId}`);
    
    if (previewContainer.classList.contains('hidden')) {
        // Show loading
        previewContent.innerHTML = `
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
                <span class="ml-3 text-gray-600">{{ __('messages.loading_csv_data') }}</span>
            </div>
        `;
        previewContainer.classList.remove('hidden');
        
        // Gunakan download URL
        const downloadUrl = `/hidrologi/file/download/${fileId}`;
        console.log('Loading CSV from:', downloadUrl);
        
        // Fetch CSV content
        fetch(downloadUrl)
            .then(response => {
                console.log('CSV Response status:', response.status, response.statusText);
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.text();
            })
            .then(csvText => {
                // Parse CSV
                const lines = csvText.trim().split('\n');
                if (lines.length === 0) {
                    throw new Error('Empty CSV file');
                }
                
                // Calculate file size first
                const fileSize = new Blob([csvText]).size;
                const fileSizeKB = (fileSize / 1024).toFixed(2);
                const fileSizeMB = (fileSize / 1024 / 1024).toFixed(2);
                
                // Jika file terlalu besar (> 2MB), buka di tab baru saja
                if (fileSize > 2 * 1024 * 1024) {
                    previewContent.innerHTML = `
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-4xl mb-3"></i>
                            <p class="text-yellow-800 font-semibold text-lg mb-2">File Terlalu Besar untuk Preview</p>
                            <p class="text-yellow-700 text-sm mb-4">Ukuran file: ${fileSizeMB} MB. Preview inline tidak tersedia untuk file besar.</p>
                            <div class="space-x-2">
                                <button onclick="window.open('/hidrologi/file/download/${fileId}', '_blank')" 
                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                    <i class="fas fa-external-link-alt mr-1"></i>Buka di Tab Baru
                                </button>
                                <a href="/hidrologi/file/download/${fileId}" 
                                   class="inline-block px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                                    <i class="fas fa-download mr-1"></i>Download CSV
                                </a>
                            </div>
                        </div>
                    `;
                    return;
                }
                
                // Get headers and rows
                const headers = lines[0].split(',').map(h => h.trim().replace(/"/g, ''));
                const maxRows = 50; // Kurangi jadi 50 rows untuk performa lebih baik
                const dataRows = lines.slice(1, Math.min(lines.length, maxRows + 1));
                
                // Calculate statistics
                const totalRows = lines.length - 1;
                const displayedRows = dataRows.length;
                
                // Build table HTML with enhanced header
                let tableHtml = `
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden shadow-sm">
                        <!-- CSV Info Card -->
                        <div class="bg-gradient-to-r from-green-50 to-blue-50 px-4 py-3 border-b border-gray-200">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h4 class="font-semibold text-gray-800 mb-1 flex items-center">
                                        <i class="fas fa-table text-green-600 mr-2"></i>
                                        ${fileName}
                                    </h4>
                                    <div class="flex flex-wrap gap-4 text-xs text-gray-600">
                                        <span><i class="fas fa-columns mr-1"></i>${headers.length} columns</span>
                                        <span><i class="fas fa-list mr-1"></i>${totalRows.toLocaleString()} rows total</span>
                                        <span><i class="fas fa-eye mr-1"></i>Showing ${displayedRows} rows</span>
                                        <span><i class="fas fa-weight mr-1"></i>${fileSizeKB} KB</span>
                                    </div>
                                </div>
                                <div class="flex space-x-2 ml-4">
                                    <button onclick="copyCSVData('${fileId}')" 
                                            class="px-3 py-1.5 bg-blue-600 text-white rounded hover:bg-blue-700 transition text-xs font-medium">
                                        <i class="fas fa-copy mr-1"></i>Copy
                                    </button>
                                    <button onclick="exportCSVToExcel('${fileId}', '${fileName}')" 
                                            class="px-3 py-1.5 bg-green-600 text-white rounded hover:bg-green-700 transition text-xs font-medium">
                                        <i class="fas fa-file-excel mr-1"></i>Excel
                                    </button>
                                    <a href="/hidrologi/file/download/${fileId}" 
                                       class="px-3 py-1.5 bg-gray-600 text-white rounded hover:bg-gray-700 transition text-xs font-medium inline-flex items-center">
                                        <i class="fas fa-download mr-1"></i>Download
                                    </a>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Data Preview Notice -->
                        ${totalRows > maxRows ? `
                        <div class="bg-yellow-50 border-b border-yellow-200 px-4 py-2 text-xs text-yellow-800">
                            <i class="fas fa-info-circle mr-1"></i>
                            <strong>Note:</strong> Preview limited to first ${maxRows} rows. Download the file to view all ${totalRows.toLocaleString()} rows.
                        </div>
                        ` : ''}
                        
                        <!-- Table Container -->
                        <div class="overflow-x-auto overflow-y-auto" style="max-height: 450px;">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-100 sticky top-0 z-10">
                                    <tr>
                `;
                
                // Add headers with column numbers
                headers.forEach((header, index) => {
                    tableHtml += `
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider whitespace-nowrap border-r border-gray-200">
                            <div class="flex flex-col">
                                <span>${header}</span>
                                <span class="text-gray-400 font-normal normal-case">#${index + 1}</span>
                            </div>
                        </th>
                    `;
                });
                
                tableHtml += `
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                `;
                
                // Add data rows with row numbers
                dataRows.forEach((row, index) => {
                    const cells = row.split(',').map(c => c.trim().replace(/"/g, ''));
                    const rowClass = index % 2 === 0 ? 'bg-white' : 'bg-gray-50';
                    tableHtml += `
                        <tr class="${rowClass} hover:bg-blue-50 transition">
                            ${cells.map(cell => `
                                <td class="px-4 py-2 text-sm text-gray-700 whitespace-nowrap border-r border-gray-100">
                                    ${cell || '<span class="text-gray-400 italic">empty</span>'}
                                </td>
                            `).join('')}
                        </tr>
                    `;
                });
                
                tableHtml += `
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Footer -->
                        <div class="bg-gray-50 px-4 py-2 border-t border-gray-200 text-xs text-gray-600">
                            <i class="fas fa-check-circle text-green-600 mr-1"></i>
                            CSV data loaded successfully
                        </div>
                    </div>
                `;
                
                previewContent.innerHTML = tableHtml;
                
                // Store CSV data for copying and export
                previewContent.setAttribute('data-csv-text', csvText);
                previewContent.setAttribute('data-csv-rows', totalRows);
            })
            .catch(error => {
                console.error('Error loading CSV:', error);
                previewContent.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-4xl mb-3"></i>
                        <p class="text-red-700 font-semibold text-lg mb-2">Gagal memuat file CSV</p>
                        <p class="text-red-600 text-sm mb-4">${error.message}</p>
                        <div class="space-x-2">
                            <button onclick="retryCSVLoad(${fileId}, '${fileName}')" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                <i class="fas fa-redo mr-1"></i>Coba Lagi
                            </button>
                            <a href="/hidrologi/file/download/${fileId}" 
                               class="inline-block px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                                <i class="fas fa-download mr-1"></i>Download CSV
                            </a>
                        </div>
                    </div>
                `;
            });
    } else {
        previewContainer.classList.add('hidden');
    }
}

// Retry CSV load
function retryCSVLoad(fileId, fileName) {
    const previewContainer = document.getElementById(`preview-${fileId}`);
    previewContainer.classList.add('hidden');
    setTimeout(() => viewCSV(fileId, fileName), 100);
}

// Export CSV to Excel format
function exportCSVToExcel(fileId, fileName) {
    const previewContent = document.getElementById(`preview-content-${fileId}`);
    const csvText = previewContent.getAttribute('data-csv-text');
    
    if (csvText) {
        // Create blob with proper Excel CSV format
        const blob = new Blob(['\uFEFF' + csvText], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        
        link.setAttribute('href', url);
        link.setAttribute('download', fileName.replace('.csv', '') + '_excel.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
        
        Swal.fire({
            icon: 'success',
            title: 'Exported!',
            text: 'CSV file exported for Excel',
            timer: 1500,
            showConfirmButton: false
        });
    }
}

// View JSON File - Inline preview dengan size limit
function viewJSON(fileId, fileName) {
    const previewContainer = document.getElementById(`preview-${fileId}`);
    const previewContent = document.getElementById(`preview-content-${fileId}`);
    
    if (previewContainer.classList.contains('hidden')) {
        // Show loading
        previewContent.innerHTML = `
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-600"></div>
                <span class="ml-3 text-gray-600">Memuat data JSON...</span>
            </div>
        `;
        previewContainer.classList.remove('hidden');
        
        // Gunakan download URL
        const downloadUrl = `/hidrologi/file/download/${fileId}`;
        console.log('Loading JSON from:', downloadUrl);
        
        // Fetch JSON content
        fetch(downloadUrl)
            .then(response => {
                console.log('JSON Response status:', response.status, response.statusText);
                if (!response.ok) throw new Error(`Failed to load JSON: ${response.status}`);
                return response.text();
            })
            .then(text => {
                // Check file size first
                const fileSize = new Blob([text]).size;
                const fileSizeKB = (fileSize / 1024).toFixed(2);
                const fileSizeMB = (fileSize / 1024 / 1024).toFixed(2);
                
                // Jika file terlalu besar (> 1MB), buka di tab baru
                if (fileSize > 1 * 1024 * 1024) {
                    previewContent.innerHTML = `
                        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                            <i class="fas fa-exclamation-triangle text-yellow-600 text-4xl mb-3"></i>
                            <p class="text-yellow-800 font-semibold text-lg mb-2">File JSON Terlalu Besar</p>
                            <p class="text-yellow-700 text-sm mb-4">Ukuran file: ${fileSizeMB} MB. Preview inline tidak tersedia untuk file besar.</p>
                            <div class="space-x-2">
                                <button onclick="window.open('/hidrologi/file/download/${fileId}', '_blank')" 
                                        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                    <i class="fas fa-external-link-alt mr-1"></i>Buka di Tab Baru
                                </button>
                                <a href="/hidrologi/file/download/${fileId}" 
                                   class="inline-block px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                                    <i class="fas fa-download mr-1"></i>Download JSON
                                </a>
                            </div>
                        </div>
                    `;
                    return;
                }
                
                // Parse JSON from text
                const jsonData = JSON.parse(text);
                const jsonString = JSON.stringify(jsonData, null, 2);
                
                // Limit displayed characters to prevent browser crash
                const maxChars = 50000; // 50K characters max
                const displayString = jsonString.length > maxChars 
                    ? jsonString.substring(0, maxChars) + '\n\n... (truncated)'
                    : jsonString;
                
                previewContent.innerHTML = `
                    <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                        <div class="bg-gray-100 px-4 py-2 border-b border-gray-200 flex justify-between items-center">
                            <div class="text-sm text-gray-700">
                                <i class="fas fa-code mr-1"></i>
                                JSON Data <span class="text-xs text-gray-500">(${fileSizeKB} KB)</span>
                            </div>
                            <div class="space-x-2">
                                <button onclick="copyJSONData('${fileId}')" 
                                        class="px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition text-xs">
                                    <i class="fas fa-copy mr-1"></i>Copy
                                </button>
                                <button onclick="window.open('/hidrologi/file/download/${fileId}', '_blank')" 
                                        class="px-3 py-1 bg-green-100 text-green-700 rounded hover:bg-green-200 transition text-xs">
                                    <i class="fas fa-external-link-alt mr-1"></i>Buka Tab Baru
                                </button>
                            </div>
                        </div>
                        ${jsonString.length > maxChars ? `
                        <div class="bg-yellow-50 px-4 py-2 text-xs text-yellow-800 border-b border-yellow-200">
                            <i class="fas fa-info-circle mr-1"></i>
                            Preview terpotong untuk performa. Buka di tab baru atau download untuk melihat semua data.
                        </div>
                        ` : ''}
                        <div class="overflow-auto p-4" style="max-height: 400px;">
                            <pre class="text-xs text-gray-800 font-mono">${displayString}</pre>
                        </div>
                    </div>
                `;
                
                // Store JSON data for copying
                previewContent.setAttribute('data-json-text', jsonString);
            })
            .catch(error => {
                previewContent.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-3xl mb-2"></i>
                        <p class="text-red-700 font-medium">Failed to load JSON file</p>
                        <p class="text-red-600 text-sm mt-1">${error.message}</p>
                        <a href="/hidrologi/file/download/${fileId}" 
                           class="mt-3 inline-block px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 transition">
                            <i class="fas fa-download mr-1"></i>
                            Download JSON instead
                        </a>
                    </div>
                `;
            });
    } else {
        previewContainer.classList.add('hidden');
    }
}

// Close preview
function closePreview(fileId) {
    const previewContainer = document.getElementById(`preview-${fileId}`);
    previewContainer.classList.add('hidden');
}

// Copy CSV data
function copyCSVData(fileId) {
    const previewContent = document.getElementById(`preview-content-${fileId}`);
    const csvText = previewContent.getAttribute('data-csv-text');
    
    if (csvText) {
        navigator.clipboard.writeText(csvText).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'CSV data copied to clipboard',
                timer: 1500,
                showConfirmButton: false
            });
        }).catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to copy CSV data',
                timer: 1500,
                showConfirmButton: false
            });
        });
    }
}

// Copy JSON data
function copyJSONData(fileId) {
    const previewContent = document.getElementById(`preview-content-${fileId}`);
    const jsonText = previewContent.getAttribute('data-json-text');
    
    if (jsonText) {
        navigator.clipboard.writeText(jsonText).then(() => {
            Swal.fire({
                icon: 'success',
                title: 'Copied!',
                text: 'JSON data copied to clipboard',
                timer: 1500,
                showConfirmButton: false
            });
        }).catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to copy JSON data',
                timer: 1500,
                showConfirmButton: false
            });
        });
    }
}

// 🌊 River Map Functions - Simple version
// No complex error handling, just display PNG preview with button to open HTML



function zoomIn() {
    // Note: Zoom control requires communication with iframe content
    // This is a placeholder for future implementation
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'info',
            title: 'Zoom In',
            text: 'Gunakan kontrol zoom di pojok kiri peta atau scroll mouse',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
        });
    }
}

function zoomOut() {
    // Note: Zoom control requires communication with iframe content
    // This is a placeholder for future implementation
    if (typeof Swal !== 'undefined') {
        Swal.fire({
            icon: 'info',
            title: 'Zoom Out',
            text: 'Gunakan kontrol zoom di pojok kiri peta atau scroll mouse',
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 2000
        });
    }
}

function openMapFullscreen() {
    const mapFrame = document.getElementById('riverMapFrame');
    
    if (mapFrame && mapFrame.src) {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                html: `
                    <div style="width: 100%; height: 85vh; position: relative;">
                        <iframe 
                            src="${mapFrame.src}" 
                            style="width: 100%; height: 100%; border: none; border-radius: 8px;"
                            sandbox="allow-scripts allow-same-origin allow-popups"
                            allow="fullscreen">
                        </iframe>
                    </div>
                `,
                width: '95%',
                padding: '10px',
                showCloseButton: true,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-2xl',
                    htmlContainer: 'p-2'
                },
                background: '#f9fafb',
                didOpen: () => {
                    // Add download button in modal
                    const downloadBtn = document.createElement('button');
                    downloadBtn.innerHTML = '<i class="fas fa-download mr-2"></i>Download Peta';
                    downloadBtn.className = 'absolute top-4 right-16 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition shadow-lg z-50';
                    downloadBtn.onclick = () => window.open(mapFrame.src, '_blank');
                    document.querySelector('.swal2-popup').appendChild(downloadBtn);
                }
            });
        } else {
            // Fallback: open in new tab
            window.open(mapFrame.src, '_blank', 'width=1200,height=800');
        }
    } else {
        if (typeof Swal !== 'undefined') {
            Swal.fire({
                icon: 'error',
                title: 'Peta Tidak Tersedia',
                text: 'Peta belum dimuat atau terjadi kesalahan',
                confirmButtonText: 'OK'
            });
        } else {
            alert('Peta tidak tersedia');
        }
    }
}

function toggleRiverMetadata() {
    const content = document.getElementById('riverMetadataContent');
    const chevron = document.getElementById('metadataChevron');
    
    if (content && chevron) {
        if (content.classList.contains('hidden')) {
            content.classList.remove('hidden');
            chevron.style.transform = 'rotate(180deg)';
        } else {
            content.classList.add('hidden');
            chevron.style.transform = 'rotate(0deg)';
        }
    }
}

</script>

@endpush
@endsection