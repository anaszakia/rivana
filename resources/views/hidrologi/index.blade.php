@extends('layouts.app')

@section('title', 'Analisis Hidrologi')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Modern Header Banner -->
    <div class="mb-6">
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
                                    {{ __('messages.hydrology_analysis') }}
                                </h1>
                                <p class="text-blue-100 text-sm mt-1">{{ __('messages.manage_monitor_hydrology_jobs') }}</p>
                            </div>
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-4 text-sm">
                            <div class="flex items-center gap-2 bg-white/10 backdrop-blur-sm px-4 py-2 rounded-xl">
                                <i class="fas fa-database text-blue-200"></i>
                                <span class="text-white font-medium">{{ __('messages.total_jobs_recorded', ['count' => $jobs->total()]) }}</span>
                            </div>
                            <div class="flex items-center gap-2 bg-green-500/20 backdrop-blur-sm px-4 py-2 rounded-xl border border-green-300/30">
                                <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                                <span class="text-white font-medium">Live Monitoring</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="flex items-center">
                        <a href="{{ route('hidrologi.create') }}" class="inline-flex items-center gap-3 px-6 py-3.5 bg-white hover:bg-blue-50 text-blue-700 font-bold rounded-2xl shadow-xl hover:shadow-2xl transition-all duration-300 hover:scale-105 group">
                            <div class="w-8 h-8 bg-blue-100 rounded-xl flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                <i class="fas fa-plus text-blue-600"></i>
                            </div>
                            <span>{{ __('messages.new_analysis') }}</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modern Stats Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5 mb-6">
        <!-- Total Jobs Card -->
        <div class="group relative bg-white rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100">
            <!-- Decorative Gradient Circle -->
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full opacity-10 group-hover:scale-125 transition-transform duration-500"></div>
            
            <div class="p-6 relative z-10">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-2">{{ __('messages.total_jobs') }}</p>
                        <h3 class="text-4xl font-extrabold text-gray-900 group-hover:text-blue-600 transition-colors">
                            {{ $jobs->total() }}
                        </h3>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-xl group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <i class="fas fa-tasks text-white text-2xl"></i>
                    </div>
                </div>
                
                <div class="flex items-center gap-2 text-blue-600 bg-blue-50 px-3 py-2 rounded-xl">
                    <i class="fas fa-chart-line text-sm"></i>
                    <span class="text-xs font-bold">{{ __('messages.all_status') }}</span>
                </div>
            </div>
        </div>

        <!-- Processing Card -->
        <div class="group relative bg-white rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100">
            <!-- Decorative Gradient Circle -->
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-gradient-to-br from-yellow-400 to-orange-600 rounded-full opacity-10 group-hover:scale-125 transition-transform duration-500"></div>
            
            <div class="p-6 relative z-10">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-2">{{ __('messages.being_processed') }}</p>
                        <h3 class="text-4xl font-extrabold text-gray-900 group-hover:text-yellow-600 transition-colors">
                            {{ $jobs->where('status', 'processing')->count() }}
                        </h3>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-yellow-500 to-orange-600 rounded-2xl flex items-center justify-center shadow-xl group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <i class="fas fa-spinner fa-spin text-white text-2xl"></i>
                    </div>
                </div>
                
                <div class="flex items-center gap-2 text-yellow-600 bg-yellow-50 px-3 py-2 rounded-xl">
                    <div class="w-2 h-2 bg-yellow-500 rounded-full animate-pulse"></div>
                    <span class="text-xs font-bold">{{ __('messages.active_now') }}</span>
                </div>
            </div>
        </div>

        <!-- Completed Card -->
        <div class="group relative bg-white rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100">
            <!-- Decorative Gradient Circle -->
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-gradient-to-br from-green-400 to-emerald-600 rounded-full opacity-10 group-hover:scale-125 transition-transform duration-500"></div>
            
            <div class="p-6 relative z-10">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-2">{{ __('messages.completed') }}</p>
                        <h3 class="text-4xl font-extrabold text-gray-900 group-hover:text-green-600 transition-colors">
                            {{ $jobs->whereIn('status', ['completed', 'completed_with_warning'])->count() }}
                        </h3>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-xl group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <i class="fas fa-check-circle text-white text-2xl"></i>
                    </div>
                </div>
                
                <div class="flex items-center gap-2 text-green-600 bg-green-50 px-3 py-2 rounded-xl">
                    <i class="fas fa-check text-sm"></i>
                    <span class="text-xs font-bold">{{ __('messages.successfully_processed') }}</span>
                </div>
            </div>
        </div>

        <!-- Failed Card -->
        <div class="group relative bg-white rounded-3xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden border border-gray-100">
            <!-- Decorative Gradient Circle -->
            <div class="absolute -top-12 -right-12 w-32 h-32 bg-gradient-to-br from-red-400 to-rose-600 rounded-full opacity-10 group-hover:scale-125 transition-transform duration-500"></div>
            
            <div class="p-6 relative z-10">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <p class="text-gray-500 text-xs font-bold uppercase tracking-wider mb-2">{{ __('messages.failed') }}</p>
                        <h3 class="text-4xl font-extrabold text-gray-900 group-hover:text-red-600 transition-colors">
                            {{ $jobs->where('status', 'failed')->count() }}
                        </h3>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-rose-600 rounded-2xl flex items-center justify-center shadow-xl group-hover:scale-110 group-hover:rotate-6 transition-all duration-300">
                        <i class="fas fa-exclamation-circle text-white text-2xl"></i>
                    </div>
                </div>
                
                <div class="flex items-center gap-2 text-red-600 bg-red-50 px-3 py-2 rounded-xl">
                    <i class="fas fa-exclamation-triangle text-sm"></i>
                    <span class="text-xs font-bold">{{ __('messages.needs_attention') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Modern Jobs Table -->
    <div class="bg-white rounded-3xl shadow-xl overflow-hidden border border-gray-100">
        <!-- Table Header -->
        <div class="bg-gradient-to-r from-blue-50 via-indigo-50 to-purple-50 px-6 py-5 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-list-ul text-white"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-bold text-gray-900">
                            {{ __('messages.job_list') }}
                        </h3>
                        <p class="text-sm text-gray-500">{{ __('messages.monitor_progress_status') }}</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button id="bulkDeleteBtn" onclick="bulkDelete()" 
                            class="hidden items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-red-600 to-red-700 text-white font-bold rounded-xl hover:shadow-xl transition-all duration-300 hover:scale-105" 
                            title="{{ __('messages.delete_selected') }}">
                        <i class="fas fa-trash-alt"></i>
                        <span>{{ __('messages.delete_selected') }} (<span id="selectedCount">0</span>)</span>
                    </button>
                    
                    <button onclick="location.reload()" class="p-2.5 hover:bg-white rounded-xl transition-all duration-200 group" title="{{ __('messages.refresh') }}">
                        <i class="fas fa-sync-alt text-gray-400 group-hover:text-blue-600 group-hover:rotate-180 transition-all duration-300"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-gray-50 to-blue-50">
                    <tr>
                        <th class="px-6 py-4 text-left w-12">
                            <input type="checkbox" id="selectAll" 
                                   class="w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded-lg focus:ring-blue-500 focus:ring-2 cursor-pointer transition-all"
                                   onchange="toggleSelectAll(this)">
                        </th>
                        <th class="px-6 py-4 text-left text-xs font-extrabold text-gray-700 uppercase tracking-wider">{{ __('messages.job_id') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-extrabold text-gray-700 uppercase tracking-wider">{{ __('messages.location') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-extrabold text-gray-700 uppercase tracking-wider">{{ __('messages.date_range') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-extrabold text-gray-700 uppercase tracking-wider">{{ __('messages.status') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-extrabold text-gray-700 uppercase tracking-wider">{{ __('messages.progress') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-extrabold text-gray-700 uppercase tracking-wider">{{ __('messages.files') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-extrabold text-gray-700 uppercase tracking-wider">{{ __('messages.created') }}</th>
                        <th class="px-6 py-4 text-right text-xs font-extrabold text-gray-700 uppercase tracking-wider">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-100">
                    @forelse($jobs as $job)
                        <tr class="hover:bg-blue-50/50 transition-all duration-200 group">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <input type="checkbox" 
                                       class="job-checkbox w-5 h-5 text-blue-600 bg-gray-100 border-gray-300 rounded-lg focus:ring-blue-500 focus:ring-2 cursor-pointer transition-all" 
                                       value="{{ $job->id }}"
                                       onchange="updateBulkDeleteButton()">
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 bg-gradient-to-br from-blue-100 to-blue-200 rounded-xl flex items-center justify-center group-hover:shadow-md transition-all">
                                        <i class="fas fa-hashtag text-blue-600"></i>
                                    </div>
                                    <span class="text-sm font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ Str::limit($job->job_id, 12) }}</span>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4">
                                <div class="flex items-start gap-3">
                                    <div class="w-9 h-9 bg-red-100 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:shadow-md transition-all">
                                        <i class="fas fa-map-marker-alt text-red-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">{{ $job->location_name ?? __('messages.unknown') }}</div>
                                        <div class="text-xs text-gray-500 flex items-center gap-1 mt-1">
                                            <i class="fas fa-globe text-gray-400"></i>
                                            <span>{{ $job->latitude }}, {{ $job->longitude }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-9 h-9 bg-purple-100 rounded-xl flex items-center justify-center group-hover:shadow-md transition-all">
                                        <i class="fas fa-calendar text-purple-600"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900">{{ \Carbon\Carbon::parse($job->start_date)->format('d M Y') }}</div>
                                        <div class="text-xs text-gray-500">{{ __('messages.to') }} {{ \Carbon\Carbon::parse($job->end_date)->format('d M Y') }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusConfig = [
                                        'pending' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-clock', 'label' => __('messages.waiting')],
                                        'submitted' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'icon' => 'fa-paper-plane', 'label' => __('messages.sent')],
                                        'processing' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'icon' => 'fa-spinner fa-spin', 'label' => __('messages.processed')],
                                        'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'icon' => 'fa-check-circle', 'label' => __('messages.completed')],
                                        'completed_with_warning' => ['bg' => 'bg-orange-100', 'text' => 'text-orange-800', 'icon' => 'fa-exclamation-triangle', 'label' => __('messages.completed_with_warning')],
                                        'failed' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'icon' => 'fa-times-circle', 'label' => __('messages.failed')],
                                        'cancelled' => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'icon' => 'fa-ban', 'label' => __('messages.cancelled')]
                                    ];
                                    $config = $statusConfig[$job->status] ?? $statusConfig['pending'];
                                @endphp
                                <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold {{ $config['bg'] }} {{ $config['text'] }} shadow-sm border-2 border-transparent group-hover:border-current transition-all">
                                    <i class="fas {{ $config['icon'] }}"></i>
                                    <span>{{ $config['label'] }}</span>
                                </span>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="flex-1 min-w-[120px]">
                                        <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden shadow-inner">
                                            <div class="h-3 rounded-full transition-all duration-500 {{ $job->progress == 100 ? 'bg-gradient-to-r from-green-400 to-emerald-600' : 'bg-gradient-to-r from-blue-400 to-indigo-600' }}" style="width: {{ $job->progress }}%"></div>
                                        </div>
                                    </div>
                                    <span class="text-sm font-extrabold text-gray-700 min-w-[50px] text-right">{{ $job->progress }}%</span>
                                </div>
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($job->total_files > 0)
                                    <div class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl border-2 border-blue-100">
                                        <div class="w-7 h-7 bg-blue-500 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-file-alt text-white text-xs"></i>
                                        </div>
                                        <span class="text-sm font-extrabold text-blue-700">{{ $job->total_files }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm font-medium">{{ __('messages.no_files') }}</span>
                                @endif
                            </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-clock text-gray-500 text-xs"></i>
                                    </div>
                                    <span class="text-sm font-medium text-gray-600">{{ $job->created_at->diffForHumans() }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('hidrologi.show', $job->id) }}" 
                                       class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-blue-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 hover:scale-105 group/btn" 
                                       title="{{ __('messages.detail') }}">
                                        <i class="fas fa-eye group-hover/btn:scale-110 transition-transform"></i>
                                        <span class="text-xs font-bold">{{ __('messages.detail') }}</span>
                                    </a>
                                   
                                    @if(in_array($job->status, ['pending', 'submitted', 'processing']))
                                        <button onclick="cancelJob({{ $job->id }})" 
                                                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-yellow-500 to-orange-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 hover:scale-105 group/btn" 
                                                title="{{ __('messages.cancel_action') }}">
                                            <i class="fas fa-stop-circle group-hover/btn:scale-110 transition-transform"></i>
                                            <span class="text-xs font-bold">{{ __('messages.cancel_action') }}</span>
                                        </button>
                                    @endif
                                    
                                    <button onclick="deleteJob({{ $job->id }})" 
                                            class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:shadow-lg transition-all duration-300 hover:scale-105 group/btn" 
                                            title="{{ __('messages.delete_action') }}">
                                        <i class="fas fa-trash group-hover/btn:scale-110 transition-transform"></i>
                                        <span class="text-xs font-bold">{{ __('messages.delete_action') }}</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-20 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="relative mb-8">
                                        <div class="w-32 h-32 bg-gradient-to-br from-blue-100 via-blue-200 to-indigo-200 rounded-full flex items-center justify-center shadow-2xl">
                                            <i class="fas fa-folder-open text-5xl text-blue-400"></i>
                                        </div>
                                        <div class="absolute -top-2 -right-2 w-8 h-8 bg-yellow-400 rounded-full flex items-center justify-center shadow-lg animate-bounce">
                                            <i class="fas fa-plus text-white text-sm"></i>
                                        </div>
                                    </div>
                                    
                                    <h3 class="text-2xl font-extrabold text-gray-800 mb-2">{{ __('messages.no_analysis_jobs_yet') }}</h3>
                                    <p class="text-gray-500 mb-8 max-w-md">{{ __('messages.start_first_analysis') }}</p>
                                   
                                    <a href="{{ route('hidrologi.create') }}" 
                                       class="inline-flex items-center gap-3 px-8 py-4 bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-600 text-white font-bold rounded-2xl hover:shadow-2xl transform hover:scale-105 transition-all duration-300 group">
                                        <div class="w-8 h-8 bg-white/20 rounded-xl flex items-center justify-center group-hover:rotate-90 transition-transform duration-300">
                                            <i class="fas fa-plus text-white"></i>
                                        </div>
                                        <span>{{ __('messages.create_first_analysis') }}</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Modern Pagination -->
        @if($jobs->hasPages())
            <div class="px-6 py-5 border-t border-gray-200 bg-gradient-to-r from-gray-50 to-blue-50">
                {{ $jobs->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
// Toggle Select All Checkbox
function toggleSelectAll(checkbox) {
    const checkboxes = document.querySelectorAll('.job-checkbox');
    checkboxes.forEach(cb => {
        cb.checked = checkbox.checked;
    });
    updateBulkDeleteButton();
}

// Update Bulk Delete Button visibility and count
function updateBulkDeleteButton() {
    const checkedBoxes = document.querySelectorAll('.job-checkbox:checked');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const selectedCount = document.getElementById('selectedCount');
    const selectAll = document.getElementById('selectAll');
    
    if (checkedBoxes.length > 0) {
        bulkDeleteBtn.classList.remove('hidden');
        bulkDeleteBtn.classList.add('flex');
        selectedCount.textContent = checkedBoxes.length;
    } else {
        bulkDeleteBtn.classList.add('hidden');
        bulkDeleteBtn.classList.remove('flex');
    }
    
    // Update select all checkbox state
    const allCheckboxes = document.querySelectorAll('.job-checkbox');
    if (selectAll) {
        selectAll.checked = checkedBoxes.length === allCheckboxes.length && allCheckboxes.length > 0;
    }
}

// Bulk Delete Function
function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.job-checkbox:checked');
    const jobIds = Array.from(checkedBoxes).map(cb => cb.value);
    
    if (jobIds.length === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Tidak Ada Data Terpilih',
            text: 'Pilih minimal satu pekerjaan untuk dihapus'
        });
        return;
    }
    
    Swal.fire({
        title: 'Hapus Data Terpilih?',
        html: `Apakah Anda yakin ingin menghapus <strong>${jobIds.length}</strong> pekerjaan?<br><strong class='text-red-600'>Tindakan ini tidak dapat dibatalkan!</strong>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc2626',
        cancelButtonColor: '#6b7280',
        confirmButtonText: 'Ya, Hapus Semua!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: 'Menghapus Data...',
                html: `Menghapus ${jobIds.length} pekerjaan, mohon tunggu...`,
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('/hidrologi/bulk-delete', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    job_ids: jobIds
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Berhasil!',
                        html: data.message || `${data.deleted_count || jobIds.length} pekerjaan berhasil dihapus.`,
                        showConfirmButton: false,
                        timer: 2000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Gagal menghapus pekerjaan'
                    });
                }
            })
            .catch(error => {
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
                    throw new Error(`HTTP error! status: ${response.status}`);
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
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Gagal menghapus pekerjaan'
                    });
                }
            })
            .catch(error => {
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
</script>
@endpush
@endsection
