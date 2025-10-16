@extends('layouts.app')

@section('title', 'Buat Analisis Baru')

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<!-- Leaflet Geocoder CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder@2.4.0/dist/Control.Geocoder.css" crossorigin=""/>
<style>
    #map {
        height: 500px;
        width: 100%;
        border-radius: 1rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
    /* Custom styling for search box */
    .leaflet-control-geocoder {
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px rgba(0,0,0,0.15);
    }
    .leaflet-control-geocoder-form input {
        border-radius: 0.5rem;
        padding: 0.75rem;
        font-size: 14px;
        border: 2px solid #e5e7eb;
    }
    .leaflet-control-geocoder-form input:focus {
        border-color: #3b82f6;
        outline: none;
    }
    /* Modern card hover effects */
    .info-card {
        transition: all 0.3s ease;
    }
    .info-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
    }
</style>
@endpush

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
            
            <div class="relative z-10">
                <!-- Breadcrumb -->
                <div class="flex items-center space-x-2 text-blue-200 mb-4">
                    <a href="{{ route('hidrologi.index') }}" class="hover:text-white transition-colors">
                        <i class="fas fa-water mr-1"></i>
                        Hidrologi
                    </a>
                    <i class="fas fa-chevron-right text-xs"></i>
                    <span class="text-white font-semibold">Buat Analisis Baru</span>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="w-16 h-16 bg-white bg-opacity-20 rounded-2xl flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-plus-circle text-3xl text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Buat Analisis Hidrologi Baru</h1>
                        <p class="text-blue-100 text-lg">Kirimkan pekerjaan baru untuk analisis data hidrologi</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-xl p-8 border border-gray-100">
                <form id="hidrologiForm" action="{{ route('hidrologi.submit') }}" method="POST">
                    @csrf

                    <!-- Location Information -->
                    <div class="mb-8">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-map-marker-alt text-blue-600 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">Informasi Lokasi</h3>
                                <p class="text-sm text-gray-500">Pilih lokasi pada peta atau masukkan koordinat</p>
                            </div>
                        </div>

                        <!-- Interactive Map -->
                        <div class="mb-6">
                            <label class="block text-sm font-semibold text-gray-700 mb-3">
                                <i class="fas fa-map text-blue-600 mr-2"></i>
                                Pilih Lokasi pada Peta
                            </label>
                            <div id="map" class="border-4 border-blue-100"></div>
                            <div class="mt-3 p-3 bg-blue-50 rounded-lg">
                                <p class="text-sm text-blue-800">
                                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                                    <strong>Tips:</strong> Klik pada peta untuk memilih lokasi, atau masukkan koordinat secara manual di bawah. Anda juga bisa menarik marker untuk menyesuaikan posisi.
                                </p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Longitude -->
                            <div>
                                <label for="longitude" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-globe text-blue-500 mr-1"></i>
                                    Longitude <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.000001" name="longitude" id="longitude" 
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                    placeholder="Contoh: 106.845599" required min="-180" max="180">
                                <p class="mt-2 text-xs text-gray-500 flex items-center">
                                    <i class="fas fa-arrow-right text-gray-400 mr-1"></i>
                                    Rentang: -180 hingga 180
                                </p>
                            </div>

                            <!-- Latitude -->
                            <div>
                                <label for="latitude" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-globe text-green-500 mr-1"></i>
                                    Latitude <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.000001" name="latitude" id="latitude"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                    placeholder="Contoh: -6.208763" required min="-90" max="90">
                                <p class="mt-2 text-xs text-gray-500 flex items-center">
                                    <i class="fas fa-arrow-right text-gray-400 mr-1"></i>
                                    Rentang: -90 hingga 90
                                </p>
                            </div>
                        </div>

                        <!-- Location Name -->
                        <div class="mt-6">
                            <label for="location_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-map-pin text-red-500 mr-1"></i>
                                Nama Lokasi
                                <span id="location-loading" class="hidden text-blue-500 text-xs ml-2">
                                    <i class="fas fa-spinner fa-spin"></i> Mengambil nama lokasi...
                                </span>
                            </label>
                            <input type="text" name="location_name" id="location_name"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50"
                                placeholder="Otomatis terisi dari koordinat atau masukkan manual" readonly>
                            <p class="mt-2 text-xs text-gray-500 flex items-center">
                                <i class="fas fa-magic text-blue-400 mr-1"></i>
                                Terisi otomatis ketika Anda memilih lokasi pada peta
                            </p>
                        </div>

                        <!-- Location Description -->
                        <div class="mt-6">
                            <label for="location_description" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-file-alt text-purple-500 mr-1"></i>
                                Deskripsi Lokasi
                            </label>
                            <textarea name="location_description" id="location_description" rows="4"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50"
                                placeholder="Deskripsi detail tentang lokasi akan terisi otomatis dari peta" readonly></textarea>
                            <p class="mt-2 text-xs text-gray-500 flex items-center">
                                <i class="fas fa-info-circle text-blue-400 mr-1"></i>
                                Deskripsi alamat lengkap terisi otomatis dari titik yang dipilih
                            </p>
                        </div>
                    </div>

                    <!-- Date Range -->
                    <div class="mb-8 border-t-2 border-gray-100 pt-8">
                        <div class="flex items-center space-x-3 mb-6">
                            <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                                <i class="fas fa-calendar-alt text-purple-600 text-lg"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">Periode Analisis</h3>
                                <p class="text-sm text-gray-500">Tentukan rentang waktu untuk analisis data</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Start Date -->
                            <div>
                                <label for="start_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-calendar-check text-green-500 mr-1"></i>
                                    Tanggal Mulai <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="start_date" id="start_date"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                    required>
                            </div>

                            <!-- End Date -->
                            <div>
                                <label for="end_date" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <i class="fas fa-calendar-times text-red-500 mr-1"></i>
                                    Tanggal Selesai <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="end_date" id="end_date"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200"
                                    required max="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="mt-4 p-4 bg-yellow-50 rounded-xl border border-yellow-200">
                            <p class="text-sm text-yellow-800 flex items-start">
                                <i class="fas fa-exclamation-triangle text-yellow-600 mr-2 mt-0.5"></i>
                                <span><strong>Penting:</strong> Tanggal selesai harus hari ini atau sebelumnya. Pastikan rentang tanggal sesuai dengan kebutuhan analisis Anda.</span>
                            </p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-between border-t-2 border-gray-100 pt-8">
                        <a href="{{ route('hidrologi.index') }}" 
                           class="inline-flex items-center px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 font-semibold rounded-xl transition-all duration-200 transform hover:scale-105">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Kembali
                        </a>
                        <button type="submit" id="submitBtn"
                            class="inline-flex items-center px-8 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white font-bold rounded-xl transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Buat Analisa
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Cara Kerja Card -->
            <div class="bg-gradient-to-br from-blue-50 to-blue-100 rounded-2xl p-6 shadow-lg info-card border border-blue-200">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-info-circle text-white text-lg"></i>
                    </div>
                    <h3 class="text-xl font-bold text-blue-900">Cara Kerja</h3>
                </div>
                <ol class="space-y-4 text-sm text-blue-900">
                    <li class="flex items-start p-3 bg-white bg-opacity-50 rounded-xl">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 text-white text-sm font-bold mr-3 flex-shrink-0 shadow-md">1</span>
                        <span class="pt-1"><strong>Pilih Lokasi:</strong> Masukkan koordinat geografis dan rentang tanggal untuk analisis Anda</span>
                    </li>
                    <li class="flex items-start p-3 bg-white bg-opacity-50 rounded-xl">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 text-white text-sm font-bold mr-3 flex-shrink-0 shadow-md">2</span>
                        <span class="pt-1"><strong>Kirim Pekerjaan:</strong> Sistem akan mengirim ke API Python untuk diproses</span>
                    </li>
                    <li class="flex items-start p-3 bg-white bg-opacity-50 rounded-xl">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 text-white text-sm font-bold mr-3 flex-shrink-0 shadow-md">3</span>
                        <span class="pt-1"><strong>Pantau Progress:</strong> Lihat kemajuan analisis secara real-time</span>
                    </li>
                    <li class="flex items-start p-3 bg-white bg-opacity-50 rounded-xl">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 text-white text-sm font-bold mr-3 flex-shrink-0 shadow-md">4</span>
                        <span class="pt-1"><strong>Unduh Hasil:</strong> Download file hasil analisis yang telah selesai</span>
                    </li>
                </ol>
            </div>

            <!-- Catatan Penting Card -->
            <div class="bg-gradient-to-br from-yellow-50 to-yellow-100 rounded-2xl p-6 shadow-lg info-card border border-yellow-200">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-yellow-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                    </div>
                    <h3 class="text-xl font-bold text-yellow-900">Catatan Penting</h3>
                </div>
                <ul class="space-y-3 text-sm text-yellow-900">
                    <li class="flex items-start p-3 bg-white bg-opacity-50 rounded-xl">
                        <i class="fas fa-clock text-yellow-600 mr-3 mt-1 flex-shrink-0"></i>
                        <span><strong>Waktu Proses:</strong> Bergantung pada rentang tanggal dan ketersediaan data</span>
                    </li>
                    <li class="flex items-start p-3 bg-white bg-opacity-50 rounded-xl">
                        <i class="fas fa-bell text-yellow-600 mr-3 mt-1 flex-shrink-0"></i>
                        <span><strong>Notifikasi:</strong> Anda akan diberitahu ketika analisis selesai</span>
                    </li>
                    <li class="flex items-start p-3 bg-white bg-opacity-50 rounded-xl">
                        <i class="fas fa-save text-yellow-600 mr-3 mt-1 flex-shrink-0"></i>
                        <span><strong>Penyimpanan:</strong> File tersimpan selama 30 hari setelah dibuat</span>
                    </li>
                </ul>
            </div>

            <!-- Tips Card -->
            <div class="bg-gradient-to-br from-green-50 to-green-100 rounded-2xl p-6 shadow-lg info-card border border-green-200">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="w-10 h-10 bg-green-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-lightbulb text-white text-lg"></i>
                    </div>
                    <h3 class="text-xl font-bold text-green-900">Tips</h3>
                </div>
                <ul class="space-y-3 text-sm text-green-900">
                    <li class="flex items-start p-3 bg-white bg-opacity-50 rounded-xl">
                        <i class="fas fa-map text-green-600 mr-3 mt-1 flex-shrink-0"></i>
                        <span>Gunakan fitur pencarian pada peta untuk menemukan lokasi dengan cepat</span>
                    </li>
                    <li class="flex items-start p-3 bg-white bg-opacity-50 rounded-xl">
                        <i class="fas fa-calendar text-green-600 mr-3 mt-1 flex-shrink-0"></i>
                        <span>Pilih rentang tanggal yang sesuai untuk hasil analisis yang optimal</span>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<!-- Leaflet JS - Load BEFORE our custom script -->
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"
        integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo="
        crossorigin=""></script>
<!-- Leaflet Geocoder Plugin -->
<script src="https://unpkg.com/leaflet-control-geocoder@2.4.0/dist/Control.Geocoder.js" crossorigin=""></script>

<script>
console.log('=== LEAFLET INITIALIZATION ===');
console.log('Leaflet loaded:', typeof L !== 'undefined');

// Wait for page to be fully loaded
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeMap);
} else {
    initializeMap();
}

function initializeMap() {
    console.log('Initializing map...');
    
    // Check if Leaflet is loaded
    if (typeof L === 'undefined') {
        console.error('❌ Leaflet library not loaded!');
        return;
    }

    // Check if map container exists
    const mapContainer = document.getElementById('map');
    if (!mapContainer) {
        console.error('❌ Map container not found!');
        return;
    }

    // Initialize map variables
    let map;
    let marker;
    let currentLat = -6.208763; // Default: Jakarta
    let currentLng = 106.845599;

    try {
        // Create map - EXACTLY like test file
        map = L.map('map').setView([currentLat, currentLng], 10);
        console.log('✓ Map object created');

        // Add OpenStreetMap tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            maxZoom: 19
        }).addTo(map);
        console.log('✓ Tile layer added');

        // Add Search/Geocoder Control
        const geocoder = L.Control.geocoder({
            defaultMarkGeocode: false,
            placeholder: 'Search location...',
            errorMessage: 'Location not found',
            geocoder: L.Control.Geocoder.nominatim({
                geocodingQueryParams: {
                    countrycodes: 'id' // Prioritas Indonesia, hapus jika ingin global
                }
            })
        })
        .on('markgeocode', function(e) {
            const latlng = e.geocode.center;
            updateLocation(latlng.lat, latlng.lng);
            map.setView(latlng, 14);
        })
        .addTo(map);
        console.log('✓ Geocoder control added');

        // Add marker
        marker = L.marker([currentLat, currentLng], {
            draggable: true
        }).addTo(map);
        console.log('✓ Marker added');

        marker.bindPopup("<b>Selected Location</b><br>Drag me or click on map!").openPopup();

        // Map click event
        map.on('click', function(e) {
            updateLocation(e.latlng.lat, e.latlng.lng);
        });

        // Marker drag event
        marker.on('dragend', function(e) {
            const position = e.target.getLatLng();
            updateLocation(position.lat, position.lng);
        });

        // Set initial values
        document.getElementById('latitude').value = currentLat.toFixed(6);
        document.getElementById('longitude').value = currentLng.toFixed(6);
        getLocationName(currentLat, currentLng);

        console.log('✅ Leaflet map initialized successfully!');

    } catch (error) {
        console.error('❌ Error initializing map:', error);
    }

    // Update location function
    function updateLocation(lat, lng) {
        currentLat = lat;
        currentLng = lng;

        marker.setLatLng([lat, lng]);
        marker.bindPopup("<b>Selected Location</b><br>Lat: " + lat.toFixed(6) + "<br>Lng: " + lng.toFixed(6)).openPopup();

        document.getElementById('latitude').value = lat.toFixed(6);
        document.getElementById('longitude').value = lng.toFixed(6);

        getLocationName(lat, lng);
    }

    // Reverse geocoding using Nominatim
    function getLocationName(lat, lng) {
        const locationNameInput = document.getElementById('location_name');
        const locationDescInput = document.getElementById('location_description');
        const loadingSpan = document.getElementById('location-loading');

        loadingSpan.classList.remove('hidden');

        fetch(`https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=14&addressdetails=1`)
            .then(response => response.json())
            .then(data => {
                loadingSpan.classList.add('hidden');
                
                if (data && data.display_name) {
                    let locationName = '';
                    
                    if (data.address) {
                        const addr = data.address;
                        locationName = addr.village || addr.suburb || addr.city || 
                                     addr.town || addr.county || addr.state || 
                                     addr.country || data.display_name;
                    } else {
                        locationName = data.display_name;
                    }
                    
                    // Set location name
                    locationNameInput.value = locationName;
                    
                    // Auto-fill location description with full address
                    locationDescInput.value = data.display_name;
                } else {
                    locationNameInput.value = `Lokasi (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
                    locationDescInput.value = `Koordinat: Latitude ${lat.toFixed(6)}, Longitude ${lng.toFixed(6)}`;
                }
            })
            .catch(error => {
                console.error('Error fetching location:', error);
                loadingSpan.classList.add('hidden');
                locationNameInput.value = `Lokasi (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
                locationDescInput.value = `Koordinat: Latitude ${lat.toFixed(6)}, Longitude ${lng.toFixed(6)}`;
            });
    }

    // Update map when coordinates manually entered
    document.getElementById('latitude').addEventListener('change', function() {
        const lat = parseFloat(this.value);
        const lng = parseFloat(document.getElementById('longitude').value);
        
        if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
            updateLocation(lat, lng);
            map.setView([lat, lng], 12);
        }
    });

    document.getElementById('longitude').addEventListener('change', function() {
        const lat = parseFloat(document.getElementById('latitude').value);
        const lng = parseFloat(this.value);
        
        if (!isNaN(lat) && !isNaN(lng) && lat >= -90 && lat <= 90 && lng >= -180 && lng <= 180) {
            updateLocation(lat, lng);
            map.setView([lat, lng], 12);
        }
    });

    // Form submission with timeout and better error handling
    document.getElementById('hidrologiForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        console.log('=== FORM SUBMIT DEBUG ===');
        console.log('Form action:', this.action);
        console.log('Timestamp:', new Date().toISOString());
        
        const submitBtn = document.getElementById('submitBtn');
        const originalText = submitBtn.innerHTML;
        
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Mengirim...';
        
        const formData = new FormData(this);
        
        // Debug: Log form data
        console.log('Form Data:');
        for (let [key, value] of formData.entries()) {
            console.log(`  ${key}: ${value}`);
        }
        
        // Create abort controller for timeout
        const controller = new AbortController();
        const timeoutId = setTimeout(() => {
            controller.abort();
            console.error('❌ Request timeout after 30 seconds');
        }, 30000); // 30 second timeout
        
        console.log('📤 Sending request...');
        const startTime = Date.now();
        
        fetch(this.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            signal: controller.signal
        })
        .then(response => {
            clearTimeout(timeoutId);
            const duration = ((Date.now() - startTime) / 1000).toFixed(2);
            console.log(`✓ Response received after ${duration}s`);
            console.log('Response status:', response.status);
            console.log('Response ok:', response.ok);
            console.log('Response type:', response.type);
            
            // Check if response is JSON
            const contentType = response.headers.get('content-type');
            console.log('Content-Type:', contentType);
            
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error(`Expected JSON but got ${contentType}`);
            }
            
            // Clone response to read it twice if needed
            return response.clone().text().then(text => {
                console.log('Raw response body:', text.substring(0, 500)); // First 500 chars
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('❌ Failed to parse JSON:', e);
                    throw new Error('Invalid JSON response: ' + text.substring(0, 100));
                }
            });
        })
        .then(data => {
            console.log('✓ Parsed response data:', data);
            console.log('✓ Success status:', data.success);
            console.log('✓ Job ID:', data.job_id);
            console.log('✓ Message:', data.message);
            
            // Check for success (handle both true boolean and truthy values)
            if (data.success === true || (data.job_id && !data.error)) {
                console.log('✅ SUCCESS! Job created with ID:', data.job_id);
                
                // Hide loading spinner IMMEDIATELY
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil!',
                    text: data.message || 'Pekerjaan berhasil dikirim! Mengalihkan ke detail pekerjaan...',
                    showConfirmButton: false,
                    timer: 2000,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                    didOpen: () => {
                        console.log('✓ SweetAlert opened, will redirect in 2s');
                    }
                }).then(() => {
                    const redirectUrl = `{{ url('hidrologi/show') }}/${data.job_id}`;
                    console.log('🔄 Redirecting to:', redirectUrl);
                    window.location.href = redirectUrl;
                });
            } else {
                console.error('❌ Server returned error:', data);
                
                // Re-enable button
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal!',
                    html: `<p>${data.message || data.error || 'Gagal mengirim pekerjaan'}</p>` +
                          (data.debug ? `<pre class="text-left text-xs mt-2">${JSON.stringify(data.debug, null, 2)}</pre>` : ''),
                    width: '600px'
                });
            }
        })
        .catch(error => {
            clearTimeout(timeoutId);
            console.error('❌ Fetch error:', error);
            console.error('Error name:', error.name);
            console.error('Error message:', error.message);
            
            let errorMessage = 'Terjadi kesalahan saat mengirim pekerjaan';
            
            if (error.name === 'AbortError') {
                errorMessage = 'Waktu permintaan habis! Python API mungkin sedang memproses atau tidak merespon.';
                console.error('⏱️ TIMEOUT: Request took more than 30 seconds');
            } else if (error.message.includes('JSON')) {
                errorMessage = 'Server mengembalikan respon yang tidak valid. Periksa log Laravel.';
            } else if (error.message.includes('NetworkError') || error.message.includes('Failed to fetch')) {
                errorMessage = 'Kesalahan jaringan! Periksa apakah server Laravel berjalan.';
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Kesalahan!',
                html: `<p>${errorMessage}</p><pre class="text-left text-xs mt-2">${error.message}</pre>`,
                footer: '<p class="text-xs">Periksa konsol browser (F12) untuk detail</p>',
                width: '600px'
            });
            
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        });
    });

    // Date validation
    document.getElementById('start_date').addEventListener('change', function() {
        const startDate = new Date(this.value);
        const endDateInput = document.getElementById('end_date');
        endDateInput.min = this.value;
        
        if (endDateInput.value && new Date(endDateInput.value) < startDate) {
            endDateInput.value = '';
        }
    });
}
</script>
@endpush