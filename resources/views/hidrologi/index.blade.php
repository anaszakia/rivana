@extends('layouts.app')

@section('title', 'Analisis Hidrologi')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header dengan Gradient Modern -->
    <div class="mb-8">
        <div class="relative overflow-hidden bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 rounded-2xl p-8 shadow-2xl">
            <!-- Background Pattern -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute top-0 right-0 w-64 h-64 bg-white rounded-full -mr-32 -mt-32"></div>
                <div class="absolute bottom-0 left-0 w-48 h-48 bg-white rounded-full -ml-24 -mb-24"></div>
            </div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center md:justify-between">
                <div class="mb-4 md:mb-0">
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                            <i class="fas fa-water text-2xl text-white"></i>
                        </div>
                        <h1 class="text-3xl md:text-4xl font-bold text-white">{{ __('messages.hydrology_analysis') }}</h1>
                    </div>
                    <p class="text-blue-100 text-lg">{{ __('messages.manage_monitor_hydrology_jobs') }}</p>
                    <div class="mt-3 flex items-center text-blue-200">
                        <i class="fas fa-info-circle mr-2"></i>
                        <span class="text-sm">{{ __('messages.total_jobs_recorded', ['count' => $jobs->total()]) }}</span>
                    </div>
                </div>
                    <a href="{{ route('hidrologi.create') }}" class="inline-flex items-center px-6 py-3 bg-white text-blue-700 font-semibold rounded-xl hover:bg-blue-50 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                        <i class="fas fa-plus-circle mr-2 text-lg"></i>
                        {{ __('messages.new_analysis') }}
                    </a>
            </div>
        </div>
    </div>

    <!-- Statistik Cards dengan Animasi Modern -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Total Pekerjaan -->
        <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 overflow-hidden">
            <div class="p-6 relative">
                <div class="absolute top-0 right-0 w-20 h-20 bg-blue-500 opacity-10 rounded-bl-full"></div>
                <div class="flex items-center justify-between relative z-10">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm font-semibold uppercase tracking-wide mb-2">{{ __('messages.total_jobs') }}</p>
                        <p class="text-3xl font-bold text-gray-900 group-hover:text-blue-600 transition-colors">{{ $jobs->total() }}</p>
                        <div class="mt-2 flex items-center text-blue-600">
                            <i class="fas fa-chart-line text-xs mr-1"></i>
                            <span class="text-xs font-medium">{{ __('messages.all_status') }}</span>
                        </div>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-tasks text-white text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="h-1 bg-gradient-to-r from-blue-500 to-blue-600"></div>
        </div>

        <!-- Sedang Diproses -->
        <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 overflow-hidden">
            <div class="p-6 relative">
                <div class="absolute top-0 right-0 w-20 h-20 bg-yellow-500 opacity-10 rounded-bl-full"></div>
                <div class="flex items-center justify-between relative z-10">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm font-semibold uppercase tracking-wide mb-2">{{ __('messages.being_processed') }}</p>
                        <p class="text-3xl font-bold text-gray-900 group-hover:text-yellow-600 transition-colors">{{ $jobs->where('status', 'processing')->count() }}</p>
                        <div class="mt-2 flex items-center text-yellow-600">
                            <div class="w-2 h-2 bg-yellow-400 rounded-full animate-pulse mr-2"></div>
                            <span class="text-xs font-medium">{{ __('messages.active_now') }}</span>
                        </div>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-spinner fa-spin text-white text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="h-1 bg-gradient-to-r from-yellow-500 to-yellow-600"></div>
        </div>

        <!-- Selesai -->
        <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 overflow-hidden">
            <div class="p-6 relative">
                <div class="absolute top-0 right-0 w-20 h-20 bg-green-500 opacity-10 rounded-bl-full"></div>
                <div class="flex items-center justify-between relative z-10">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm font-semibold uppercase tracking-wide mb-2">{{ __('messages.completed') }}</p>
                        <p class="text-3xl font-bold text-gray-900 group-hover:text-green-600 transition-colors">{{ $jobs->whereIn('status', ['completed', 'completed_with_warning'])->count() }}</p>
                        <div class="mt-2 flex items-center text-green-600">
                            <i class="fas fa-check text-xs mr-1"></i>
                            <span class="text-xs font-medium">{{ __('messages.successfully_processed') }}</span>
                        </div>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-green-500 to-green-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-check-circle text-white text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="h-1 bg-gradient-to-r from-green-500 to-green-600"></div>
        </div>

        <!-- Gagal -->
        <div class="group bg-white rounded-2xl shadow-lg hover:shadow-2xl transition-all duration-300 transform hover:-translate-y-1 border border-gray-100 overflow-hidden">
            <div class="p-6 relative">
                <div class="absolute top-0 right-0 w-20 h-20 bg-red-500 opacity-10 rounded-bl-full"></div>
                <div class="flex items-center justify-between relative z-10">
                    <div class="flex-1">
                        <p class="text-gray-600 text-sm font-semibold uppercase tracking-wide mb-2">{{ __('messages.failed') }}</p>
                        <p class="text-3xl font-bold text-gray-900 group-hover:text-red-600 transition-colors">{{ $jobs->where('status', 'failed')->count() }}</p>
                        <div class="mt-2 flex items-center text-red-600">
                            {{-- <i class="fas fa-exclamation text-xs mr-1"></i> --}}
                            <span class="text-xs font-medium">{{ __('messages.needs_attention') }}</span>
                        </div>
                    </div>
                    <div class="w-14 h-14 bg-gradient-to-br from-red-500 to-red-600 rounded-2xl flex items-center justify-center shadow-lg group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-exclamation-circle text-white text-xl"></i>
                    </div>
                </div>
            </div>
            <div class="h-1 bg-gradient-to-r from-red-500 to-red-600"></div>
        </div>
    </div>

    <!-- Tabel Pekerjaan dengan Design Modern -->
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        <!-- Header Tabel -->
        <div class="px-6 py-5 border-b border-gray-200 bg-gradient-to-r from-gray-50 to-white">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold text-gray-900 flex items-center">
                        <i class="fas fa-list-ul text-blue-600 mr-3"></i>
                        {{ __('messages.job_list') }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-1">{{ __('messages.monitor_progress_status') }}</p>
                </div>
                <div class="flex items-center space-x-2">
                   
                        <button id="bulkDeleteBtn" onclick="bulkDelete()" 
                                class="hidden items-center px-4 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition-all duration-200 shadow-md hover:shadow-lg" 
                                title="{{ __('messages.delete_selected') }}">
                            <i class="fas fa-trash-alt mr-2"></i>
                            {{ __('messages.delete_selected') }} (<span id="selectedCount">0</span>)
                        </button>
                    
                    <button onclick="location.reload()" class="p-2 hover:bg-gray-100 rounded-lg transition-colors" title="{{ __('messages.refresh') }}">
                        <i class="fas fa-sync-alt text-gray-400 hover:text-gray-600"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gradient-to-r from-blue-50 to-indigo-50">
                    <tr>
                       
                            <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider w-12">
                                <input type="checkbox" id="selectAll" 
                                       class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer"
                                       onchange="toggleSelectAll(this)">
                            </th>
                      
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('messages.job_id') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('messages.location') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('messages.date_range') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('messages.status') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('messages.progress') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('messages.files') }}</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('messages.created') }}</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-700 uppercase tracking-wider">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($jobs as $job)
                        <tr class="hover:bg-gradient-to-r hover:from-blue-50 hover:to-transparent transition-all duration-200 group">
                            
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <input type="checkbox" 
                                           class="job-checkbox w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 focus:ring-2 cursor-pointer" 
                                           value="{{ $job->id }}"
                                           onchange="updateBulkDeleteButton()">
                                </td>
                            
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                                        <i class="fas fa-hashtag text-blue-600 text-xs"></i>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-900">{{ Str::limit($job->job_id, 12) }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex items-start space-x-2">
                                    <i class="fas fa-map-marker-alt text-red-500 mt-1"></i>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ $job->location_name ?? __('messages.unknown') }}</div>
                                        <div class="text-xs text-gray-500 flex items-center mt-1">
                                            <i class="fas fa-globe text-gray-400 mr-1"></i>
                                            {{ $job->latitude }}, {{ $job->longitude }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-calendar text-blue-500"></i>
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">{{ \Carbon\Carbon::parse($job->start_date)->format('d M Y') }}</div>
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
                                <span class="inline-flex items-center px-3 py-1.5 rounded-xl text-xs font-semibold {{ $config['bg'] }} {{ $config['text'] }} shadow-sm">
                                    <i class="fas {{ $config['icon'] }} mr-2"></i>
                                    {{ $config['label'] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-1">
                                        <div class="w-full bg-gray-200 rounded-full h-2.5 overflow-hidden shadow-inner">
                                            <div class="h-2.5 rounded-full transition-all duration-500 {{ $job->progress == 100 ? 'bg-gradient-to-r from-green-400 to-green-600' : 'bg-gradient-to-r from-blue-400 to-blue-600' }}" style="width: {{ $job->progress }}%"></div>
                                        </div>
                                    </div>
                                    <span class="text-sm font-semibold text-gray-700 min-w-[45px] text-right">{{ $job->progress }}%</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($job->total_files > 0)
                                    <div class="inline-flex items-center space-x-2 px-3 py-1.5 bg-blue-50 rounded-lg">
                                        <i class="fas fa-file-alt text-blue-600"></i>
                                        <span class="text-sm font-semibold text-blue-700">{{ $job->total_files }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-sm">{{ __('messages.no_files') }}</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center space-x-2 text-sm text-gray-600">
                                    <i class="fas fa-clock text-gray-400"></i>
                                    <span>{{ $job->created_at->diffForHumans() }}</span>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right">
                                <div class="flex items-center justify-end space-x-2">
                                    <a href="{{ route('hidrologi.show', $job->id) }}" 
                                       class="inline-flex items-center px-3 py-2 bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-all duration-200 group/btn" 
                                       title="{{ __('messages.detail') }}">
                                        <i class="fas fa-eye mr-1.5 group-hover/btn:scale-110 transition-transform"></i>
                                        <span class="text-xs font-semibold">{{ __('messages.detail') }}</span>
                                    </a>
                                   
                                        @if(in_array($job->status, ['pending', 'submitted', 'processing']))
                                            <button onclick="cancelJob({{ $job->id }})" 
                                                    class="inline-flex items-center px-3 py-2 bg-yellow-100 text-yellow-700 rounded-lg hover:bg-yellow-200 transition-all duration-200 group/btn" 
                                                    title="{{ __('messages.cancel_action') }}">
                                                <i class="fas fa-stop-circle mr-1.5 group-hover/btn:scale-110 transition-transform"></i>
                                                <span class="text-xs font-semibold">{{ __('messages.cancel_action') }}</span>
                                            </button>
                                        @endif
                                    
                                    
                                        <button onclick="deleteJob({{ $job->id }})" 
                                                class="inline-flex items-center px-3 py-2 bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-all duration-200 group/btn" 
                                                title="{{ __('messages.delete_action') }}">
                                            <i class="fas fa-trash mr-1.5 group-hover/btn:scale-110 transition-transform"></i>
                                            <span class="text-xs font-semibold">{{ __('messages.delete_action') }}</span>
                                        </button>
                                   
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-24 h-24 bg-gradient-to-br from-blue-100 to-blue-200 rounded-full flex items-center justify-center mb-6 shadow-lg">
                                        <i class="fas fa-folder-open text-4xl text-blue-400"></i>
                                    </div>
                                    <h3 class="text-xl font-bold text-gray-700 mb-2">{{ __('messages.no_analysis_jobs_yet') }}</h3>
                                    <p class="text-gray-500 mb-6">{{ __('messages.start_first_analysis') }}</p>
                                   
                                        <a href="{{ route('hidrologi.create') }}" 
                                           class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold rounded-xl hover:shadow-lg transform hover:scale-105 transition-all duration-200">
                                            <i class="fas fa-plus-circle mr-2"></i>
                                            {{ __('messages.create_first_analysis') }}
                                        </a>
                                   
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination dengan Style Modern -->
        @if($jobs->hasPages())
            <div class="px-6 py-4 border-t border-gray-200 bg-gray-50">
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
