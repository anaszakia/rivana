<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard')</title>
    <link rel="icon" type="image/jpeg" href="/images/logo2.jpg">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.12.3/dist/cdn.min.js"></script>
    
    @stack('styles')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.store('modal', {
                open: false
            });
        });
        
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'catalyst-gray': {
                            50: '#f9fafb',
                            100: '#f3f4f6',
                            200: '#e5e7eb',
                            300: '#d1d5db',
                            400: '#9ca3af',
                            500: '#6b7280',
                            600: '#4b5563',
                            700: '#374151',
                            800: '#1f2937',
                            900: '#111827',
                        }
                    }
                }
            }
        }
    </script>
    <style>
        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
        
        /* Alpine.js transitions */
        [x-cloak] { 
            display: none !important; 
        }
        
        .modal-transition {
            transition-property: opacity, transform;
            transition-duration: 300ms;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .modal-enter-start, .modal-leave-end {
            opacity: 0;
            transform: scale(0.95);
        }
        
        .modal-enter-end, .modal-leave-start {
            opacity: 1;
            transform: scale(1);
        }
        
        .backdrop-transition {
            transition-property: opacity;
            transition-duration: 200ms;
        }
        
        .backdrop-enter-start, .backdrop-leave-end {
            opacity: 0;
        }
        
        .backdrop-enter-end, .backdrop-leave-start {
            opacity: 1;
        }

        /* Custom animations for floating button */
        .floating-button {
            animation: float 6s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-5px); }
        }

        .tooltip {
            visibility: hidden;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .tooltip-parent:hover .tooltip {
            visibility: visible;
            opacity: 1;
        }

        /* Header dropdown animations */
        .dropdown-enter {
            animation: dropdownEnter 0.2s ease-out forwards;
        }

        @keyframes dropdownEnter {
            from {
                opacity: 0;
                transform: translateY(-10px) scale(0.95);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900">
    <div class="min-h-screen flex flex-col" x-data="{ mobileMenuOpen: false }">
        <!-- Navigation Header -->
        @include('partials.navbar')
        
        <!-- Main Content Area -->
        <main class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6">
            <!-- Page Header (Optional) -->
            @hasSection('page-header')
                <div class="mb-6">
                    @yield('page-header')
                </div>
            @endif
            
            <!-- Content -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 min-h-[600px] p-6">
                @yield('content')
            </div>
        </main>
        
        <!-- Footer -->
        @include('partials.footer')
    </div>

   <script>
        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            // Close any open dropdowns when clicking outside
            if (!event.target.closest('.relative')) {
                const dropdowns = document.querySelectorAll('[x-show]');
                dropdowns.forEach(dropdown => {
                    if (dropdown.style.display !== 'none') {
                        dropdown.style.display = 'none';
                    }
                });
            }
        });

        // Close dropdowns when pressing Escape
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                const dropdowns = document.querySelectorAll('[x-show]');
                dropdowns.forEach(dropdown => {
                    dropdown.style.display = 'none';
                });
            }
        });
    </script>

    {{-- SweetAlert2 library --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Script untuk menampilkan notifikasi sukses --}}
    @if(session('success'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: '{{ session('success') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        });
    </script>
    @endif

    {{-- Script untuk menampilkan notifikasi error --}}
    @if(session('error'))
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: 'error',
                title: 'Gagal',
                text: '{{ session('error') }}',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        });
    </script>
    @endif

    {{-- Script untuk menampilkan error validasi --}}
    @if($errors->any())
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            let errorMessages = '';
            @foreach($errors->all() as $error)
                errorMessages += '{{ $error }}\n';
            @endforeach

            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: errorMessages,
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 5000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.addEventListener('mouseenter', Swal.stopTimer)
                    toast.addEventListener('mouseleave', Swal.resumeTimer)
                }
            });
        });
    </script>
    @endif

    {{-- Utility function untuk konfirmasi SweetAlert --}}
    <script>
        function confirmDelete(formElement, itemName = 'item') {
            event.preventDefault();
            Swal.fire({
                title: 'Hapus Item?',
                text: `Anda yakin ingin menghapus ${itemName}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6b7280',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then(res => {
                if (res.isConfirmed) formElement.submit();
            });
        }
    </script>
    
    @stack('scripts')
    @yield('scripts')
</body>
</html>