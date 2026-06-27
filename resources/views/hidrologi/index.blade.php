@extends('layouts.app')

@section('title', __('messages.hydrology_analysis'))

@push('styles')
<style>
    /* ── Design tokens (selaras dengan halaman create) ── */
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

    /* ── Card dasar (sama dengan step-card di halaman create) ── */
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

    /* ── Tombol utama ── */
    .btn-submit {
        display: inline-flex; align-items: center; justify-content: center; gap: 0.5rem;
        padding: 0.75rem 1.375rem;
        background: var(--c-teal);
        color: #fff;
        font-weight: 800;
        font-size: 0.85rem;
        border-radius: 0.75rem;
        border: none;
        cursor: pointer;
        transition: all 0.15s;
        box-shadow: 0 4px 14px rgba(13,148,136,.3);
        text-decoration: none;
    }
    .btn-submit:hover { background: var(--c-teal-dk); box-shadow: 0 6px 20px rgba(13,148,136,.4); transform: translateY(-1px); color: #fff; }

    /* Tombol hapus terpilih — display diatur via utility hidden/flex Tailwind, bukan di sini */
    .btn-danger {
        padding: 0.625rem 1.125rem;
        background: #dc2626;
        color: #fff;
        font-weight: 700;
        font-size: 0.8rem;
        border-radius: 0.625rem;
        border: none;
        cursor: pointer;
        transition: background .15s;
    }
    .btn-danger:hover { background: #b91c1c; }

    /* ── Tombol ikon (aksi tabel & refresh) ── */
    .icon-btn {
        width: 2.25rem; height: 2.25rem;
        display: flex; align-items: center; justify-content: center;
        border-radius: 0.625rem;
        border: 1.5px solid var(--c-border);
        background: var(--c-white);
        color: var(--c-muted);
        transition: all .15s;
        cursor: pointer;
        flex-shrink: 0;
    }
    .icon-btn:hover { background: var(--c-surface); border-color: #94a3b8; color: var(--c-slate); }
    .icon-btn-view:hover { border-color: var(--c-teal); color: var(--c-teal-dk); background: #f0fdfa; }
    .icon-btn-warn:hover { border-color: #f59e0b; color: #b45309; background: #fffbeb; }
    .icon-btn-danger:hover { border-color: #ef4444; color: #b91c1c; background: #fef2f2; }

    /* ── Kartu statistik ── */
    .stat-card {
        background: var(--c-white);
        border: 1.5px solid var(--c-border);
        border-radius: var(--radius-card);
        box-shadow: var(--shadow-card);
        padding: 1.125rem 1.25rem;
        display: flex; align-items: center; gap: 1rem;
    }
    .stat-icon {
        width: 2.75rem; height: 2.75rem;
        border-radius: 0.75rem;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0; font-size: 1.05rem; color: #fff;
    }
    .stat-value { font-size: 1.625rem; font-weight: 800; color: var(--c-slate); line-height: 1; }
    .stat-label { font-size: 0.68rem; font-weight: 700; color: var(--c-muted); text-transform: uppercase; letter-spacing: .04em; margin-top: 0.2rem; }

    /* ── Tabel ── */
    .job-table th {
        font-size: 0.68rem; font-weight: 800; text-transform: uppercase; letter-spacing: .04em;
        color: var(--c-muted); text-align: left; padding: 0.875rem 1.25rem;
        border-bottom: 1.5px solid var(--c-border);
        background: var(--c-surface);
        white-space: nowrap;
    }
    .job-table td { padding: 0.875rem 1.25rem; border-bottom: 1px solid var(--c-border); vertical-align: middle; }
    .job-table tbody tr:last-child td { border-bottom: none; }
    .job-table tbody tr:hover td { background: var(--c-surface); }

    .status-pill {
        display: inline-flex; align-items: center; gap: 0.375rem;
        padding: 0.3rem 0.75rem; border-radius: 9999px;
        font-size: 0.72rem; font-weight: 700; white-space: nowrap;
    }

    .progress-track { width: 100%; height: 0.5rem; background: var(--c-border); border-radius: 9999px; overflow: hidden; }
    .progress-fill { height: 100%; border-radius: 9999px; background: var(--c-teal); transition: width .4s; }

    .files-pill {
        display: inline-flex; align-items: center; gap: 0.375rem;
        padding: 0.3rem 0.7rem; border-radius: 0.5rem;
        background: var(--c-teal-lt); color: var(--c-teal-dk);
        font-size: 0.78rem; font-weight: 700; white-space: nowrap;
    }

    input.chk { width: 1.05rem; height: 1.05rem; accent-color: var(--c-teal); border-radius: 0.3rem; cursor: pointer; }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 sm:px-5 lg:px-6 py-6 max-w-6xl">

    {{-- ── Header halaman ── --}}
    <div class="mb-5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <p class="text-xs text-gray-400 font-medium mb-0.5">{{ __('messages.hydrology') }}</p>
            <h1 class="text-xl font-extrabold text-gray-900 leading-tight">{{ __('messages.hydrology_analysis') }}</h1>
            <p class="text-xs text-gray-400 mt-1">{{ __('messages.manage_monitor_hydrology_jobs') }}</p>
        </div>
        <a href="{{ route('hidrologi.create') }}" class="btn-submit">
            <i class="fas fa-plus" style="font-size:0.75rem"></i>
            {{ __('messages.new_analysis') }}
        </a>
    </div>

    {{-- ── Statistik ringkas ── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 mb-5">
        <div class="stat-card">
            <div class="stat-icon" style="background: var(--c-teal);"><i class="fas fa-tasks"></i></div>
            <div>
                <div class="stat-value">{{ $jobs->total() }}</div>
                <div class="stat-label">{{ __('messages.total_jobs') }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#d97706;"><i class="fas fa-spinner fa-spin"></i></div>
            <div>
                <div class="stat-value">{{ $jobs->where('status', 'processing')->count() }}</div>
                <div class="stat-label">{{ __('messages.being_processed') }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#10b981;"><i class="fas fa-check-circle"></i></div>
            <div>
                <div class="stat-value">{{ $jobs->whereIn('status', ['completed', 'completed_with_warning'])->count() }}</div>
                <div class="stat-label">{{ __('messages.completed') }}</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon" style="background:#ef4444;"><i class="fas fa-exclamation-circle"></i></div>
            <div>
                <div class="stat-value">{{ $jobs->where('status', 'failed')->count() }}</div>
                <div class="stat-label">{{ __('messages.failed') }}</div>
            </div>
        </div>
    </div>

    {{-- ── Kartu daftar pekerjaan ── --}}
    <div class="step-card" style="overflow: hidden;">
        <div class="step-header">
            <div class="step-badge"><i class="fas fa-list-ul" style="font-size:0.8rem"></i></div>
            <div class="flex-1 min-w-0">
                <h3 class="text-sm font-extrabold text-gray-900">{{ __('messages.job_list') }}</h3>
                <p class="text-xs text-gray-400 mt-0.5">{{ __('messages.monitor_progress_status') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <button id="bulkDeleteBtn" onclick="bulkDelete()"
                        class="hidden items-center gap-2 btn-danger"
                        title="{{ __('messages.delete_selected') }}">
                    <i class="fas fa-trash-alt"></i>
                    <span>{{ __('messages.delete_selected') }} (<span id="selectedCount">0</span>)</span>
                </button>
                <button onclick="location.reload()" class="icon-btn" title="{{ __('messages.refresh') }}">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full job-table">
                <thead>
                    <tr>
                        <th class="w-10">
                            <input type="checkbox" id="selectAll" class="chk" onchange="toggleSelectAll(this)">
                        </th>
                        <th>{{ __('messages.job_id') }}</th>
                        <th>{{ __('messages.location') }}</th>
                        <th>{{ __('messages.date_range') }}</th>
                        <th>{{ __('messages.status') }}</th>
                        <th>{{ __('messages.progress') }}</th>
                        <th>{{ __('messages.files') }}</th>
                        <th>{{ __('messages.created') }}</th>
                        <th class="text-right">{{ __('messages.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobs as $job)
                        <tr>
                            <td>
                                <input type="checkbox" class="job-checkbox chk" value="{{ $job->id }}" onchange="updateBulkDeleteButton()">
                            </td>

                            <td class="whitespace-nowrap">
                                <span class="text-sm font-bold text-gray-900">
                                    <i class="fas fa-hashtag text-gray-400" style="font-size:0.65rem"></i>
                                    {{ Str::limit($job->job_id, 12) }}
                                </span>
                            </td>

                            <td>
                                <div class="text-sm font-bold text-gray-900">
                                    <i class="fas fa-map-marker-alt text-red-400" style="font-size:0.7rem"></i>
                                    {{ $job->location_name ?? __('messages.unknown') }}
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ $job->latitude }}, {{ $job->longitude }}</div>
                            </td>

                            <td class="whitespace-nowrap">
                                <div class="text-sm font-semibold text-gray-700">
                                    <i class="fas fa-calendar text-gray-400" style="font-size:0.7rem"></i>
                                    {{ \Carbon\Carbon::parse($job->start_date)->format('d M Y') }}
                                </div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ __('messages.to') }} {{ \Carbon\Carbon::parse($job->end_date)->format('d M Y') }}</div>
                            </td>

                            <td class="whitespace-nowrap">
                                @php
                                    $statusConfig = [
                                        'pending' => ['bg' => '#f1f5f9', 'text' => '#475569', 'icon' => 'fa-clock', 'label' => __('messages.waiting')],
                                        'submitted' => ['bg' => '#e0f2fe', 'text' => '#0369a1', 'icon' => 'fa-paper-plane', 'label' => __('messages.sent')],
                                        'processing' => ['bg' => '#fef3c7', 'text' => '#b45309', 'icon' => 'fa-spinner fa-spin', 'label' => __('messages.processed')],
                                        'completed' => ['bg' => '#d1fae5', 'text' => '#047857', 'icon' => 'fa-check-circle', 'label' => __('messages.completed')],
                                        'completed_with_warning' => ['bg' => '#ffedd5', 'text' => '#c2410c', 'icon' => 'fa-exclamation-triangle', 'label' => __('messages.completed_with_warning')],
                                        'failed' => ['bg' => '#fee2e2', 'text' => '#b91c1c', 'icon' => 'fa-times-circle', 'label' => __('messages.failed')],
                                        'cancelled' => ['bg' => '#f1f5f9', 'text' => '#475569', 'icon' => 'fa-ban', 'label' => __('messages.cancelled')],
                                    ];
                                    $config = $statusConfig[$job->status] ?? $statusConfig['pending'];
                                @endphp
                                <span class="status-pill" style="background: {{ $config['bg'] }}; color: {{ $config['text'] }};">
                                    <i class="fas {{ $config['icon'] }}" style="font-size:0.65rem"></i>
                                    {{ $config['label'] }}
                                </span>
                            </td>

                            <td class="whitespace-nowrap" style="min-width:140px;">
                                <div class="flex items-center gap-2">
                                    <div class="progress-track flex-1">
                                        <div class="progress-fill" style="width: {{ $job->progress }}%; {{ $job->progress == 100 ? 'background:#10b981;' : '' }}"></div>
                                    </div>
                                    <span class="text-xs font-bold text-gray-600" style="min-width:2.2rem; text-align:right;">{{ $job->progress }}%</span>
                                </div>
                            </td>

                            <td class="whitespace-nowrap">
                                @if($job->total_files > 0)
                                    <span class="files-pill">
                                        <i class="fas fa-file-alt" style="font-size:0.65rem"></i>
                                        {{ $job->total_files }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">{{ __('messages.no_files') }}</span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap">
                                <span class="text-xs text-gray-500">{{ $job->created_at->diffForHumans() }}</span>
                            </td>

                            <td class="text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('hidrologi.show', $job->id) }}" class="icon-btn icon-btn-view" title="{{ __('messages.detail') }}">
                                        <i class="fas fa-eye" style="font-size:0.8rem"></i>
                                    </a>

                                    @if(in_array($job->status, ['pending', 'submitted', 'processing']))
                                        <button onclick="cancelJob({{ $job->id }})" class="icon-btn icon-btn-warn" title="{{ __('messages.cancel_action') }}">
                                            <i class="fas fa-stop-circle" style="font-size:0.8rem"></i>
                                        </button>
                                    @endif

                                    <button onclick="deleteJob({{ $job->id }})" class="icon-btn icon-btn-danger" title="{{ __('messages.delete_action') }}">
                                        <i class="fas fa-trash" style="font-size:0.8rem"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center" style="padding: 4rem 1.5rem;">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 rounded-2xl flex items-center justify-center mb-4" style="background: var(--c-teal-lt);">
                                        <i class="fas fa-folder-open text-2xl" style="color: var(--c-teal);"></i>
                                    </div>
                                    <h3 class="text-base font-extrabold text-gray-800 mb-1">{{ __('messages.no_analysis_jobs_yet') }}</h3>
                                    <p class="text-sm text-gray-400 mb-5 max-w-sm">{{ __('messages.start_first_analysis') }}</p>
                                    <a href="{{ route('hidrologi.create') }}" class="btn-submit">
                                        <i class="fas fa-plus" style="font-size:0.75rem"></i>
                                        {{ __('messages.create_first_analysis') }}
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jobs->hasPages())
            <div class="px-5 py-4" style="border-top: 1.5px solid var(--c-border); background: var(--c-surface);">
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
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus Semua!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
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
        confirmButtonColor: '#0d9488',
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Batalkan!',
        cancelButtonText: 'Tidak, Biarkan'
    }).then((result) => {
        if (result.isConfirmed) {
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
        cancelButtonColor: '#64748b',
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
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