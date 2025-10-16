@extends('layouts.app')

@section('title', 'Job Details - ' . $job->job_id)

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('hidrologi.index') }}" class="hover:text-blue-600">Hidrologi</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-800">Job Details</span>
        </div>
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Job Details</h1>
                <p class="text-gray-600 mt-1">Job ID: <span class="font-mono">{{ $job->job_id }}</span></p>
            </div>
            <div class="flex space-x-2">
                @can('edit hidrologi')
                    @if(in_array($job->status, ['pending', 'submitted', 'processing']))
                        <button onclick="cancelJob({{ $job->id }})" class="px-4 py-2 bg-yellow-500 hover:bg-yellow-600 text-white rounded-lg transition duration-200">
                            <i class="fas fa-stop-circle mr-2"></i>Cancel
                        </button>
                    @endif
                @endcan
                @can('delete hidrologi')
                    <button onclick="deleteJob({{ $job->id }})" class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition duration-200">
                        <i class="fas fa-trash mr-2"></i>Delete
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <!-- Status Card -->
    <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                @php
                    $statusConfig = [
                        'pending' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-clock', 'ring' => 'ring-gray-300'],
                        'submitted' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-paper-plane', 'ring' => 'ring-blue-300'],
                        'processing' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'fa-spinner fa-spin', 'ring' => 'ring-yellow-300'],
                        'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-check-circle', 'ring' => 'ring-green-300'],
                        'completed_with_warning' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'icon' => 'fa-exclamation-triangle', 'ring' => 'ring-orange-300'],
                        'failed' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'fa-times-circle', 'ring' => 'ring-red-300'],
                        'cancelled' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-ban', 'ring' => 'ring-gray-300']
                    ];
                    $config = $statusConfig[$job->status] ?? $statusConfig['pending'];
                @endphp
                <div class="w-16 h-16 {{ $config['bg'] }} rounded-full flex items-center justify-center ring-4 {{ $config['ring'] }}">
                    <i class="fas {{ $config['icon'] }} text-2xl {{ $config['text'] }}"></i>
                </div>
                <div>
                    <h3 class="text-2xl font-bold {{ $config['text'] }}">{{ ucfirst(str_replace('_', ' ', $job->status)) }}</h3>
                    <p class="text-gray-600">{{ $job->status_message ?? 'Processing...' }}</p>
                </div>
            </div>
            
            <!-- Progress Bar -->
            @if(in_array($job->status, ['pending', 'submitted', 'processing']))
                <div class="w-64">
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Progress</span>
                        <span id="progress-percent">{{ $job->progress }}%</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        <div id="progress-bar" class="bg-blue-600 h-3 rounded-full transition-all duration-500" style="width: {{ $job->progress }}%"></div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Warning/Error Messages -->
        @if($job->warning_message)
            <div class="mt-4 p-4 bg-orange-50 border-l-4 border-orange-500 rounded">
                <div class="flex items-start">
                    <i class="fas fa-exclamation-triangle text-orange-500 mt-1 mr-3"></i>
                    <div>
                        <h4 class="font-semibold text-orange-800">Warning</h4>
                        <p class="text-sm text-orange-700">{{ $job->warning_message }}</p>
                    </div>
                </div>
            </div>
        @endif

        @if($job->error_message)
            <div class="mt-4 p-4 bg-red-50 border-l-4 border-red-500 rounded">
                <div class="flex items-start">
                    <i class="fas fa-times-circle text-red-500 mt-1 mr-3"></i>
                    <div>
                        <h4 class="font-semibold text-red-800">Error</h4>
                        <p class="text-sm text-red-700">{{ $job->error_message }}</p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Job Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Location Info -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                    Location Information
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Location Name</p>
                        <p class="font-medium text-gray-800">{{ $job->location_name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">Coordinates</p>
                        <p class="font-medium text-gray-800">{{ $job->latitude }}, {{ $job->longitude }}</p>
                    </div>
                    @if($job->location_description)
                        <div class="col-span-2">
                            <p class="text-sm text-gray-600">Description</p>
                            <p class="text-gray-800">{{ $job->location_description }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Analysis Period -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                    <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                    Analysis Period
                </h3>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-600">Start Date</p>
                        <p class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($job->start_date)->format('d F Y') }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-600">End Date</p>
                        <p class="font-medium text-gray-800">{{ \Carbon\Carbon::parse($job->end_date)->format('d F Y') }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-sm text-gray-600">Duration</p>
                        <p class="font-medium text-gray-800">
                            {{ \Carbon\Carbon::parse($job->start_date)->diffInDays(\Carbon\Carbon::parse($job->end_date)) + 1 }} days
                        </p>
                    </div>
                </div>
            </div>

            <!-- Analysis Summary - STRUCTURED (Ringkasan Terstruktur) -->
            @if($summary)
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg shadow-md p-6 border border-blue-200">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-xl font-bold text-blue-900 flex items-center">
                            <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                            {{ $summary['title'] ?? '📊 RINGKASAN HASIL ANALISIS HIDROLOGI' }}
                        </h3>
                        <span class="text-xs text-blue-700 bg-blue-100 px-3 py-1 rounded-full">
                            <i class="fas fa-layer-group mr-1"></i>Structured Summary
                        </span>
                    </div>

                    <!-- Job Info -->
                    @if(isset($summary['job_info']))
                        <div class="bg-white rounded-lg p-4 mb-4 shadow-sm border border-gray-200">
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                Informasi Job
                            </h4>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <span class="text-gray-600">Job ID:</span>
                                    <span class="font-mono text-gray-800 ml-2 text-xs">{{ $summary['job_info']['job_id'] ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Status:</span>
                                    <span class="font-semibold text-green-600 ml-2">{{ ucfirst($summary['job_info']['status'] ?? 'N/A') }}</span>
                                </div>
                                @if(isset($summary['job_info']['created_at']))
                                    <div>
                                        <span class="text-gray-600">Created:</span>
                                        <span class="text-gray-800 ml-2 text-xs">{{ $summary['job_info']['created_at'] }}</span>
                                    </div>
                                @endif
                                @if(isset($summary['job_info']['completed_at']))
                                    <div>
                                        <span class="text-gray-600">Completed:</span>
                                        <span class="text-gray-800 ml-2 text-xs">{{ $summary['job_info']['completed_at'] }}</span>
                                    </div>
                                @endif
                                @if(isset($summary['job_info']['files_generated']))
                                    <div class="col-span-2 pt-2 border-t">
                                        <span class="text-gray-600 block mb-2">Files Generated:</span>
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
                                Parameter Input
                            </h4>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <span class="text-gray-600">Longitude:</span>
                                    <span class="font-medium text-gray-800 ml-2">{{ $summary['input_parameters']['longitude'] ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-600">Latitude:</span>
                                    <span class="font-medium text-gray-800 ml-2">{{ $summary['input_parameters']['latitude'] ?? 'N/A' }}</span>
                                </div>
                                <div class="col-span-2">
                                    <span class="text-gray-600">Periode:</span>
                                    <span class="font-medium text-gray-800 ml-2">{{ $summary['input_parameters']['periode_analisis'] ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>
                    @endif

                    <!-- Statistik Data -->
                    @if(isset($summary['statistik_data']))
                        <div class="bg-white rounded-lg p-4 mb-4 shadow-sm border border-gray-200">
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-chart-bar text-green-500 mr-2"></i>
                                Statistik Data
                            </h4>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between items-center pb-2 border-b">
                                    <span class="text-gray-600 font-medium">Total Hari Analisis:</span>
                                    <span class="font-bold text-blue-700 text-lg">{{ $summary['statistik_data']['total_hari'] ?? 'N/A' }} hari</span>
                                </div>
                                
                                @if(isset($summary['statistik_data']['curah_hujan']))
                                    <div class="bg-blue-50 rounded p-3">
                                        <p class="font-medium text-blue-900 mb-2 flex items-center">
                                            <i class="fas fa-cloud-rain text-blue-600 mr-2"></i>
                                            Curah Hujan
                                        </p>
                                        <div class="grid grid-cols-2 gap-2 text-xs">
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">Rata-rata</div>
                                                <div class="font-bold text-blue-700">{{ $summary['statistik_data']['curah_hujan']['rata_rata'] ?? 'N/A' }}</div>
                                            </div>
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">Maksimum</div>
                                                <div class="font-bold text-red-600">{{ $summary['statistik_data']['curah_hujan']['maksimum'] ?? 'N/A' }}</div>
                                            </div>
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">Minimum</div>
                                                <div class="font-bold text-green-600">{{ $summary['statistik_data']['curah_hujan']['minimum'] ?? 'N/A' }}</div>
                                            </div>
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">Total</div>
                                                <div class="font-bold text-purple-600">{{ $summary['statistik_data']['curah_hujan']['total'] ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if(isset($summary['statistik_data']['volume_waduk']))
                                    <div class="bg-cyan-50 rounded p-3">
                                        <p class="font-medium text-cyan-900 mb-2 flex items-center">
                                            <i class="fas fa-water text-cyan-600 mr-2"></i>
                                            Volume Waduk
                                        </p>
                                        <div class="grid grid-cols-2 gap-2 text-xs">
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">Rata-rata</div>
                                                <div class="font-bold text-cyan-700">{{ $summary['statistik_data']['volume_waduk']['rata_rata'] ?? 'N/A' }}</div>
                                            </div>
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">Maksimum</div>
                                                <div class="font-bold text-blue-600">{{ $summary['statistik_data']['volume_waduk']['maksimum'] ?? 'N/A' }}</div>
                                            </div>
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">Minimum</div>
                                                <div class="font-bold text-orange-600">{{ $summary['statistik_data']['volume_waduk']['minimum'] ?? 'N/A' }}</div>
                                            </div>
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">Akhir Periode</div>
                                                <div class="font-bold text-indigo-600">{{ $summary['statistik_data']['volume_waduk']['akhir_periode'] ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if(isset($summary['statistik_data']['keandalan_sistem']))
                                    <div class="bg-green-50 rounded p-3">
                                        <p class="font-medium text-green-900 mb-2 flex items-center">
                                            <i class="fas fa-shield-alt text-green-600 mr-2"></i>
                                            Keandalan Sistem
                                        </p>
                                        <div class="flex justify-between items-center">
                                            <div>
                                                <div class="text-gray-600 text-xs">Rata-rata</div>
                                                <div class="font-bold text-2xl text-green-700">{{ $summary['statistik_data']['keandalan_sistem']['rata_rata'] ?? 'N/A' }}</div>
                                            </div>
                                            <div class="text-right">
                                                <span class="px-4 py-2 rounded-full text-sm font-bold {{ 
                                                    strpos($summary['statistik_data']['keandalan_sistem']['status'] ?? '', 'Sangat Baik') !== false ? 'bg-green-200 text-green-900' : 
                                                    (strpos($summary['statistik_data']['keandalan_sistem']['status'] ?? '', 'Baik') !== false ? 'bg-blue-200 text-blue-900' : 
                                                    'bg-yellow-200 text-yellow-900') 
                                                }}">
                                                    {{ $summary['statistik_data']['keandalan_sistem']['status'] ?? 'N/A' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- Hasil Analisis -->
                    @if(isset($summary['hasil_analisis']))
                        <div class="bg-white rounded-lg p-4 mb-4 shadow-sm border border-gray-200">
                            <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                <i class="fas fa-microscope text-indigo-500 mr-2"></i>
                                Hasil Analisis
                            </h4>
                            <div class="space-y-3 text-sm">
                                @if(isset($summary['hasil_analisis']['pasokan_air']))
                                    <div class="bg-blue-50 rounded p-3">
                                        <p class="font-medium text-blue-900 mb-2 flex items-center">
                                            <i class="fas fa-tint text-blue-600 mr-2"></i>
                                            Pasokan Air
                                        </p>
                                        <div class="grid grid-cols-2 gap-2 text-xs mb-2">
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">Total Supply</div>
                                                <div class="font-bold text-green-700">{{ $summary['hasil_analisis']['pasokan_air']['total_supply'] ?? 'N/A' }}</div>
                                            </div>
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">Total Demand</div>
                                                <div class="font-bold text-orange-700">{{ $summary['hasil_analisis']['pasokan_air']['total_demand'] ?? 'N/A' }}</div>
                                            </div>
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">Defisit</div>
                                                <div class="font-bold text-red-700">{{ $summary['hasil_analisis']['pasokan_air']['defisit'] ?? 'N/A' }}</div>
                                            </div>
                                            <div class="bg-white rounded p-2 flex items-center justify-center">
                                                <span class="px-3 py-1 rounded-full text-xs font-bold {{ 
                                                    strpos($summary['hasil_analisis']['pasokan_air']['status_pasokan'] ?? '', 'Surplus') !== false ? 'bg-green-200 text-green-900' : 
                                                    (strpos($summary['hasil_analisis']['pasokan_air']['status_pasokan'] ?? '', 'Seimbang') !== false ? 'bg-blue-200 text-blue-900' : 
                                                    'bg-red-200 text-red-900') 
                                                }}">
                                                    {{ $summary['hasil_analisis']['pasokan_air']['status_pasokan'] ?? 'N/A' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                @if(isset($summary['hasil_analisis']['risiko']))
                                    <div class="bg-yellow-50 rounded p-3">
                                        <p class="font-medium text-yellow-900 mb-2 flex items-center">
                                            <i class="fas fa-exclamation-triangle text-yellow-600 mr-2"></i>
                                            Analisis Risiko
                                        </p>
                                        <div class="grid grid-cols-2 gap-2 text-xs mb-2">
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">Risiko Banjir</div>
                                                <div class="font-bold text-blue-700">{{ $summary['hasil_analisis']['risiko']['banjir'] ?? 'N/A' }}</div>
                                            </div>
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">Risiko Kekeringan</div>
                                                <div class="font-bold text-orange-700">{{ $summary['hasil_analisis']['risiko']['kekeringan'] ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                        <div class="bg-white rounded p-2 text-center">
                                            <span class="px-3 py-1 rounded-full text-xs font-bold {{ 
                                                strpos($summary['hasil_analisis']['risiko']['kategori_risiko'] ?? '', 'Rendah') !== false ? 'bg-green-200 text-green-900' : 
                                                (strpos($summary['hasil_analisis']['risiko']['kategori_risiko'] ?? '', 'Sedang') !== false ? 'bg-yellow-200 text-yellow-900' : 
                                                'bg-red-200 text-red-900') 
                                            }}">
                                                {{ $summary['hasil_analisis']['risiko']['kategori_risiko'] ?? 'N/A' }}
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                @if(isset($summary['hasil_analisis']['kualitas_air']))
                                    <div class="bg-cyan-50 rounded p-3">
                                        <p class="font-medium text-cyan-900 mb-2 flex items-center">
                                            <i class="fas fa-flask text-cyan-600 mr-2"></i>
                                            Kualitas Air
                                        </p>
                                        <div class="grid grid-cols-3 gap-2 text-xs mb-2">
                                            <div class="bg-white rounded p-2 col-span-2">
                                                <div class="text-gray-600">WQI (Water Quality Index)</div>
                                                <div class="font-bold text-2xl text-cyan-700">{{ $summary['hasil_analisis']['kualitas_air']['WQI_rata_rata'] ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500 mt-1">{{ $summary['hasil_analisis']['kualitas_air']['status'] ?? 'N/A' }}</div>
                                            </div>
                                            <div class="space-y-2">
                                                <div class="bg-white rounded p-2">
                                                    <div class="text-gray-600 text-xs">pH</div>
                                                    <div class="font-bold text-blue-700">{{ $summary['hasil_analisis']['kualitas_air']['pH'] ?? 'N/A' }}</div>
                                                </div>
                                                <div class="bg-white rounded p-2">
                                                    <div class="text-gray-600 text-xs">DO</div>
                                                    <div class="font-bold text-green-700 text-xs">{{ $summary['hasil_analisis']['kualitas_air']['DO'] ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="bg-white rounded p-2">
                                            <div class="text-gray-600 text-xs">TDS (Total Dissolved Solids)</div>
                                            <div class="font-bold text-purple-700">{{ $summary['hasil_analisis']['kualitas_air']['TDS'] ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                @endif

                                @if(isset($summary['hasil_analisis']['kesehatan_ekosistem']))
                                    <div class="bg-green-50 rounded p-3">
                                        <p class="font-medium text-green-900 mb-2 flex items-center">
                                            <i class="fas fa-leaf text-green-600 mr-2"></i>
                                            Kesehatan Ekosistem
                                        </p>
                                        <div class="grid grid-cols-2 gap-2 text-xs mb-2">
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">Health Index</div>
                                                <div class="font-bold text-green-700">{{ $summary['hasil_analisis']['kesehatan_ekosistem']['index'] ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500 mt-1">{{ $summary['hasil_analisis']['kesehatan_ekosistem']['status'] ?? 'N/A' }}</div>
                                            </div>
                                            <div class="bg-white rounded p-2">
                                                <div class="text-gray-600">Habitat Ikan (HSI)</div>
                                                <div class="font-bold text-blue-700">{{ $summary['hasil_analisis']['kesehatan_ekosistem']['habitat_fish'] ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                        <div class="bg-white rounded p-2">
                                            <div class="text-gray-600 text-xs">Habitat Vegetasi</div>
                                            <div class="font-bold text-green-700">{{ $summary['hasil_analisis']['kesehatan_ekosistem']['habitat_vegetation'] ?? 'N/A' }}</div>
                                        </div>
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
                                Water Balance
                            </h4>
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div class="bg-green-50 rounded p-3">
                                    <div class="text-gray-600 text-xs mb-1">Total Input</div>
                                    <div class="font-bold text-lg text-green-700">{{ $summary['water_balance']['total_input'] ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-arrow-down text-green-600"></i> Curah Hujan
                                    </div>
                                </div>
                                <div class="bg-red-50 rounded p-3">
                                    <div class="text-gray-600 text-xs mb-1">Total Output</div>
                                    <div class="font-bold text-lg text-red-700">{{ $summary['water_balance']['total_output'] ?? 'N/A' }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        <i class="fas fa-arrow-up text-red-600"></i> ET + Runoff + ΔS
                                    </div>
                                </div>
                                <div class="bg-blue-50 rounded p-3">
                                    <div class="text-gray-600 text-xs mb-1">Residual</div>
                                    <div class="font-bold text-lg text-blue-700">{{ $summary['water_balance']['residual'] ?? 'N/A' }}</div>
                                </div>
                                <div class="bg-orange-50 rounded p-3">
                                    <div class="text-gray-600 text-xs mb-1">Error</div>
                                    <div class="font-bold text-lg text-orange-700">{{ $summary['water_balance']['error_persen'] ?? 'N/A' }}</div>
                                </div>
                                <div class="col-span-2 mt-2 pt-3 border-t text-center">
                                    <div class="text-gray-600 text-xs mb-2">Status Balance</div>
                                    <span class="px-4 py-2 rounded-full text-sm font-bold {{ 
                                        strpos($summary['water_balance']['status'] ?? '', 'Sangat Baik') !== false ? 'bg-green-200 text-green-900' : 
                                        (strpos($summary['water_balance']['status'] ?? '', 'Baik') !== false ? 'bg-blue-200 text-blue-900' : 
                                        (strpos($summary['water_balance']['status'] ?? '', 'Cukup') !== false ? 'bg-yellow-200 text-yellow-900' : 
                                        'bg-red-200 text-red-900')) 
                                    }}">
                                        {{ $summary['water_balance']['status'] ?? 'N/A' }}
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
                                Kualitas Data
                            </h4>
                            <div class="space-y-3 text-sm">
                                <div class="flex justify-between items-center py-2 border-b">
                                    <span class="text-gray-600 flex items-center">
                                        <i class="fas fa-database text-blue-500 mr-2"></i>
                                        Kelengkapan Data
                                    </span>
                                    <span class="font-semibold text-green-700 text-lg">{{ $summary['kualitas_data']['kelengkapan_data'] ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between items-center py-2 border-b">
                                    <span class="text-gray-600 flex items-center">
                                        <i class="fas fa-calendar-check text-purple-500 mr-2"></i>
                                        Periode Valid
                                    </span>
                                    <span class="font-semibold {{ ($summary['kualitas_data']['periode_valid'] ?? '') == 'Ya' ? 'text-green-700' : 'text-red-700' }}">
                                        {{ ($summary['kualitas_data']['periode_valid'] ?? '') == 'Ya' ? '✅ Ya' : '❌ Tidak' }}
                                    </span>
                                </div>
                                @if(isset($summary['kualitas_data']['file_tersedia']))
                                    <div class="bg-gray-50 rounded p-3">
                                        <p class="text-gray-600 font-medium mb-2 text-xs">File Tersedia:</p>
                                        <div class="space-y-2">
                                            <div class="flex items-center justify-between bg-white rounded p-2">
                                                <div class="flex items-center">
                                                    <i class="fas fa-chart-bar text-blue-500 mr-2"></i>
                                                    <span class="text-gray-700 text-xs">Visualisasi</span>
                                                </div>
                                                <span class="font-bold text-blue-600 text-xs">{{ $summary['kualitas_data']['file_tersedia']['visualisasi'] ?? 'N/A' }}</span>
                                            </div>
                                            <div class="flex items-center justify-between bg-white rounded p-2">
                                                <div class="flex items-center">
                                                    <i class="fas fa-table text-green-500 mr-2"></i>
                                                    <span class="text-gray-700 text-xs">Data CSV</span>
                                                </div>
                                                <span class="font-bold text-green-600 text-xs">{{ $summary['kualitas_data']['file_tersedia']['data_csv'] ?? 'N/A' }}</span>
                                            </div>
                                            <div class="flex items-center justify-between bg-white rounded p-2">
                                                <div class="flex items-center">
                                                    <i class="fas fa-file-code text-orange-500 mr-2"></i>
                                                    <span class="text-gray-700 text-xs">Metadata</span>
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
                                Rekomendasi Pengelolaan ({{ count($summary['rekomendasi']) }})
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
                                                {{ $prioritas }}
                                            </span>
                                        </div>
                                        <p class="text-sm text-gray-700 leading-relaxed pl-8">{{ $rekomendasi['rekomendasi'] ?? 'N/A' }}</p>
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
            @if($fullLogs)
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
            @endif

            <!-- Generated Files -->
            @if($job->files->count() > 0)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-semibold text-gray-800 flex items-center">
                            <i class="fas fa-file-download text-blue-600 mr-2"></i>
                            Generated Files ({{ $job->files->count() }})
                        </h3>
                        <div class="flex space-x-2">
                            <button onclick="filterFiles('all')" class="filter-btn active px-3 py-1 text-xs rounded transition" data-type="all">
                                <i class="fas fa-th mr-1"></i>All
                            </button>
                            <button onclick="filterFiles('png')" class="filter-btn px-3 py-1 text-xs rounded transition" data-type="png">
                                <i class="fas fa-image mr-1"></i>PNG
                            </button>
                            <button onclick="filterFiles('csv')" class="filter-btn px-3 py-1 text-xs rounded transition" data-type="csv">
                                <i class="fas fa-table mr-1"></i>CSV
                            </button>
                            <button onclick="filterFiles('json')" class="filter-btn px-3 py-1 text-xs rounded transition" data-type="json">
                                <i class="fas fa-code mr-1"></i>JSON
                            </button>
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 gap-4">
                        @foreach($job->files->sortBy('display_order') as $file)
                            <div class="file-item border border-gray-200 rounded-lg p-4 hover:shadow-md transition duration-200" data-file-type="{{ strtolower($file->file_type) }}">
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <h4 class="font-medium text-gray-800 mb-1 flex items-center">
                                            @if($file->file_type === 'png')
                                                <i class="fas fa-image text-blue-500 mr-2"></i>
                                            @elseif($file->file_type === 'csv')
                                                <i class="fas fa-table text-green-500 mr-2"></i>
                                            @elseif($file->file_type === 'json')
                                                <i class="fas fa-code text-orange-500 mr-2"></i>
                                            @else
                                                <i class="fas fa-file text-gray-500 mr-2"></i>
                                            @endif
                                            {{ $file->display_name ?? $file->filename }}
                                        </h4>
                                        @if($file->description)
                                            <p class="text-xs text-gray-600 mb-2">{{ $file->description }}</p>
                                        @endif
                                        <div class="flex items-center space-x-4 text-xs text-gray-500">
                                            <span><i class="fas fa-file mr-1"></i>{{ strtoupper($file->file_type) }}</span>
                                            <span><i class="fas fa-weight mr-1"></i>{{ number_format($file->file_size_mb, 2) }} MB</span>
                                            @if($file->created_at)
                                                <span><i class="fas fa-clock mr-1"></i>{{ $file->created_at->format('d M Y, H:i') }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex space-x-2 ml-4">
                                        @if($file->file_type === 'png')
                                            <button onclick="viewImage({{ $file->id }}, '{{ $file->display_name ?? $file->filename }}')" class="px-3 py-2 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition duration-200 text-sm">
                                                <i class="fas fa-eye mr-1"></i>View
                                            </button>
                                        @elseif($file->file_type === 'csv')
                                            <button onclick="viewCSV({{ $file->id }}, '{{ $file->display_name ?? $file->filename }}')" class="px-3 py-2 bg-green-100 text-green-700 rounded hover:bg-green-200 transition duration-200 text-sm">
                                                <i class="fas fa-eye mr-1"></i>View
                                            </button>
                                        @elseif($file->file_type === 'json')
                                            <button onclick="viewJSON({{ $file->id }}, '{{ $file->display_name ?? $file->filename }}')" class="px-3 py-2 bg-orange-100 text-orange-700 rounded hover:bg-orange-200 transition duration-200 text-sm">
                                                <i class="fas fa-eye mr-1"></i>View
                                            </button>
                                        @endif
                                        @can('download hidrologi files')
                                            <a href="{{ route('hidrologi.file.download', $file->id) }}" class="px-3 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200 transition duration-200 text-sm">
                                                <i class="fas fa-download mr-1"></i>Download
                                            </a>
                                        @endcan
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

        <!-- Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Timeline -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Timeline</h3>
                <div class="space-y-4">
                    <div class="flex items-start">
                        <div class="w-2 h-2 bg-blue-600 rounded-full mt-2 mr-3"></div>
                        <div class="flex-1">
                            <p class="text-sm font-medium text-gray-800">Created</p>
                            <p class="text-xs text-gray-600">{{ $job->created_at->format('d M Y, H:i') }}</p>
                        </div>
                    </div>
                    @if($job->submitted_at)
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-blue-600 rounded-full mt-2 mr-3"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">Submitted</p>
                                <p class="text-xs text-gray-600">{{ $job->submitted_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                    @if($job->started_at)
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-yellow-600 rounded-full mt-2 mr-3"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">Started Processing</p>
                                <p class="text-xs text-gray-600">{{ $job->started_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                    @if($job->completed_at)
                        <div class="flex items-start">
                            <div class="w-2 h-2 bg-green-600 rounded-full mt-2 mr-3"></div>
                            <div class="flex-1">
                                <p class="text-sm font-medium text-gray-800">Completed</p>
                                <p class="text-xs text-gray-600">{{ $job->completed_at->format('d M Y, H:i') }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Statistics -->
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-800 mb-4">Statistics</h3>
                <div class="space-y-3">
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">PNG Files</span>
                        <span class="font-medium text-gray-800">{{ $job->png_count }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">CSV Files</span>
                        <span class="font-medium text-gray-800">{{ $job->csv_count }}</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-sm text-gray-600">JSON Files</span>
                        <span class="font-medium text-gray-800">{{ $job->json_count }}</span>
                    </div>
                    <div class="flex justify-between items-center border-t pt-3">
                        <span class="text-sm font-medium text-gray-800">Total Files</span>
                        <span class="font-bold text-blue-600">{{ $job->total_files }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
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
        title: 'Cancel Job?',
        text: "Are you sure you want to cancel this job?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes, cancel it!',
        cancelButtonText: 'No, keep it'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Cancelling...',
                text: 'Please wait',
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
                        title: 'Cancelled!',
                        text: data.message || 'Job has been cancelled successfully.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed!',
                        text: data.message || 'Failed to cancel job'
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while cancelling the job'
                });
            });
        }
    });
}

function deleteJob(jobId) {
    Swal.fire({
        title: 'Delete Job?',
        html: "Are you sure you want to delete this job?<br><strong class='text-red-600'>This action cannot be undone!</strong>",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Deleting...',
                text: 'Please wait',
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
                        title: 'Deleted!',
                        text: data.message || 'Job has been deleted successfully.',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = '/hidrologi';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Failed!',
                        text: data.message || 'Failed to delete job'
                    });
                }
            })
            .catch(function(error) {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: 'An error occurred while deleting the job: ' + error.message
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

// View PNG Image
function viewImage(fileId, fileName) {
    const previewContainer = document.getElementById(`preview-${fileId}`);
    const previewContent = document.getElementById(`preview-content-${fileId}`);
    
    if (previewContainer.classList.contains('hidden')) {
        // Show loading
        previewContent.innerHTML = `
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-blue-600"></div>
                <span class="ml-3 text-gray-600">Loading image...</span>
            </div>
        `;
        previewContainer.classList.remove('hidden');
        
        // Fetch image as blob
        fetch(`/hidrologi/file/${fileId}/preview`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                // Get the final URL after redirect
                return response.blob().then(blob => ({
                    blob: blob,
                    url: response.url
                }));
            })
            .then(data => {
                // Create object URL from blob
                const imageUrl = URL.createObjectURL(data.blob);
                const finalUrl = data.url;
                
                previewContent.innerHTML = `
                    <div class="bg-gray-50 rounded-lg p-4 overflow-auto" style="max-height: 500px;">
                        <img src="${imageUrl}" 
                             alt="${fileName}" 
                             class="max-w-full h-auto mx-auto rounded shadow-lg cursor-pointer hover:opacity-90 transition"
                             style="max-height: 450px;"
                             onclick="window.open('${finalUrl}', '_blank')">
                        <div class="text-center mt-3 space-x-2">
                            <span class="text-xs text-gray-500">
                                <i class="fas fa-info-circle mr-1"></i>
                                Click image to view in full size
                            </span>
                            <button onclick="downloadImageFromBlob('${imageUrl}', '${fileName}')" 
                                    class="text-xs px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition">
                                <i class="fas fa-download mr-1"></i>Save Image
                            </button>
                        </div>
                    </div>
                `;
            })
            .catch(error => {
                console.error('Error loading image:', error);
                previewContent.innerHTML = `
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4 text-center">
                        <i class="fas fa-exclamation-triangle text-red-600 text-3xl mb-2"></i>
                        <p class="text-red-700 font-medium">Failed to load image</p>
                        <p class="text-red-600 text-sm mt-1">${error.message}</p>
                        <div class="mt-3 space-x-2">
                            <button onclick="retryImageLoad(${fileId}, '${fileName}')" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                <i class="fas fa-redo mr-1"></i>Retry
                            </button>
                            <a href="/hidrologi/file/${fileId}/download" 
                               class="inline-block px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 transition">
                                <i class="fas fa-download mr-1"></i>Download Instead
                            </a>
                        </div>
                    </div>
                `;
            });
    } else {
        previewContainer.classList.add('hidden');
    }
}

// Retry image load
function retryImageLoad(fileId, fileName) {
    const previewContainer = document.getElementById(`preview-${fileId}`);
    previewContainer.classList.add('hidden');
    setTimeout(() => viewImage(fileId, fileName), 100);
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

// View CSV File
function viewCSV(fileId, fileName) {
    const previewContainer = document.getElementById(`preview-${fileId}`);
    const previewContent = document.getElementById(`preview-content-${fileId}`);
    
    if (previewContainer.classList.contains('hidden')) {
        // Show loading
        previewContent.innerHTML = `
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-green-600"></div>
                <span class="ml-3 text-gray-600">Loading CSV data...</span>
            </div>
        `;
        previewContainer.classList.remove('hidden');
        
        // Fetch CSV content as blob
        fetch(`/hidrologi/file/${fileId}/preview`)
            .then(response => {
                if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
                return response.text();
            })
            .then(csvText => {
                // Parse CSV
                const lines = csvText.trim().split('\n');
                if (lines.length === 0) {
                    throw new Error('Empty CSV file');
                }
                
                // Get headers and rows
                const headers = lines[0].split(',').map(h => h.trim().replace(/"/g, ''));
                const maxRows = 100; // Limit to first 100 rows
                const dataRows = lines.slice(1, Math.min(lines.length, maxRows + 1));
                
                // Calculate statistics
                const totalRows = lines.length - 1;
                const displayedRows = dataRows.length;
                const fileSize = new Blob([csvText]).size;
                const fileSizeKB = (fileSize / 1024).toFixed(2);
                
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
                                    <a href="/hidrologi/file/${fileId}/download" 
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
                        <p class="text-red-700 font-semibold text-lg mb-2">Failed to load CSV file</p>
                        <p class="text-red-600 text-sm mb-4">${error.message}</p>
                        <div class="space-x-2">
                            <button onclick="retryCSVLoad(${fileId}, '${fileName}')" 
                                    class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 transition">
                                <i class="fas fa-redo mr-1"></i>Retry
                            </button>
                            <a href="/hidrologi/file/${fileId}/download" 
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

// View JSON File
function viewJSON(fileId, fileName) {
    const previewContainer = document.getElementById(`preview-${fileId}`);
    const previewContent = document.getElementById(`preview-content-${fileId}`);
    
    if (previewContainer.classList.contains('hidden')) {
        // Show loading
        previewContent.innerHTML = `
            <div class="flex items-center justify-center py-8">
                <div class="animate-spin rounded-full h-12 w-12 border-b-2 border-orange-600"></div>
                <span class="ml-3 text-gray-600">Loading JSON data...</span>
            </div>
        `;
        previewContainer.classList.remove('hidden');
        
        // Fetch JSON content
        fetch(`/hidrologi/file/${fileId}/preview`)
            .then(response => {
                if (!response.ok) throw new Error('Failed to load JSON');
                return response.json();
            })
            .then(jsonData => {
                const jsonString = JSON.stringify(jsonData, null, 2);
                
                previewContent.innerHTML = `
                    <div class="bg-gray-50 rounded-lg border border-gray-200 overflow-hidden">
                        <div class="bg-gray-100 px-4 py-2 border-b border-gray-200 flex justify-between items-center">
                            <div class="text-sm text-gray-700">
                                <i class="fas fa-code mr-1"></i>
                                JSON Data
                            </div>
                            <button onclick="copyJSONData('${fileId}')" 
                                    class="px-3 py-1 bg-blue-100 text-blue-700 rounded hover:bg-blue-200 transition text-xs">
                                <i class="fas fa-copy mr-1"></i>Copy JSON
                            </button>
                        </div>
                        <div class="overflow-auto p-4" style="max-height: 400px;">
                            <pre class="text-xs text-gray-800 font-mono">${jsonString}</pre>
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
                        <a href="/hidrologi/file/${fileId}/download" 
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

</script>

@endpush
@endsection
