@extends('layouts.app')

@section('title', __('messages.create_new_analysis'))

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
<div class="container mx-auto px-3 sm:px-4 lg:px-6 py-4 sm:py-6">
    <!-- Modern Header Banner -->
    <div class="mb-6">
        <div class="relative overflow-hidden bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-3xl shadow-2xl">
            <!-- Animated Background Circles -->
            <div class="absolute inset-0 opacity-10">
                <div class="absolute w-96 h-96 -top-48 -right-48 bg-white rounded-full animate-pulse"></div>
                <div class="absolute w-64 h-64 -bottom-32 -left-32 bg-white rounded-full animate-pulse" style="animation-delay: 1s;"></div>
                <div class="absolute w-40 h-40 top-1/2 left-1/3 bg-white rounded-full animate-pulse" style="animation-delay: 2s;"></div>
            </div>
            
            <div class="relative z-10 p-6 sm:p-8">
                <!-- Breadcrumb -->
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
                        <i class="fas fa-plus-circle text-2xl sm:text-3xl text-white"></i>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold text-white mb-1 tracking-tight">{{ __('messages.create_new_hydrology_analysis') }}</h1>
                        <p class="text-blue-100 text-sm sm:text-base lg:text-lg">{{ __('messages.submit_new_job_for_analysis') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 sm:gap-6">
        <!-- Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-xl p-5 sm:p-6 lg:p-8 border border-gray-100">
                <form id="hidrologiForm" action="{{ route('hidrologi.submit') }}" method="POST">
                    @csrf

                    <!-- Location Information -->
                    <div class="mb-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-11 h-11 sm:w-12 sm:h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-map-marker-alt text-white text-lg sm:text-xl"></i>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-lg sm:text-xl font-extrabold text-gray-900">{{ __('messages.location_information') }}</h3>
                                <p class="text-xs sm:text-sm text-gray-600">{{ __('messages.select_location_on_map_or_enter_coordinates') }}</p>
                            </div>
                        </div>

                        <!-- Interactive Map -->
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-gray-800 mb-3 flex items-center gap-2">
                                <div class="w-6 h-6 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <i class="fas fa-map text-blue-600 text-xs"></i>
                                </div>
                                {{ __('messages.select_location_on_map') }}
                            </label>
                            <div id="map" class="border-4 border-blue-200 shadow-lg"></div>
                            <div class="mt-4 p-4 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl border-2 border-blue-200">
                                <p class="text-sm text-blue-900 flex items-start gap-2">
                                    <i class="fas fa-info-circle text-blue-600 mt-0.5 shrink-0"></i>
                                    <span>{!! __('messages.map_tips') !!}</span>
                                </p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Longitude -->
                            <div>
                                <label for="longitude" class="block text-sm font-bold text-gray-800 mb-2 flex items-center gap-2">
                                    <div class="w-5 h-5 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-globe text-blue-600 text-xs"></i>
                                    </div>
                                    {{ __('messages.longitude') }} <span class="text-red-600">*</span>
                                </label>
                                <input type="number" step="0.000001" name="longitude" id="longitude" 
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 font-medium"
                                    placeholder="{{ __('messages.example') }}: 106.845599" required min="-180" max="180">
                                <p class="mt-2 text-xs text-gray-600 flex items-center gap-1">
                                    <i class="fas fa-arrow-right text-gray-400"></i>
                                    {{ __('messages.range') }}: -180 {{ __('messages.to_lowercase') }} 180
                                </p>
                            </div>

                            <!-- Latitude -->
                            <div>
                                <label for="latitude" class="block text-sm font-bold text-gray-800 mb-2 flex items-center gap-2">
                                    <div class="w-5 h-5 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-globe text-green-600 text-xs"></i>
                                    </div>
                                    {{ __('messages.latitude') }} <span class="text-red-600">*</span>
                                </label>
                                <input type="number" step="0.000001" name="latitude" id="latitude"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 font-medium"
                                    placeholder="{{ __('messages.example') }}: -6.208763" required min="-90" max="90">
                                <p class="mt-2 text-xs text-gray-600 flex items-center gap-1">
                                    <i class="fas fa-arrow-right text-gray-400"></i>
                                    {{ __('messages.range') }}: -90 {{ __('messages.to_lowercase') }} 90
                                </p>
                            </div>
                        </div>

                        <!-- Location Name -->
                        <div class="mt-6">
                            <label for="location_name" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-map-pin text-red-500 mr-1"></i>
                                {{ __('messages.location_name') }}
                                <span id="location-loading" class="hidden text-blue-500 text-xs ml-2">
                                    <i class="fas fa-spinner fa-spin"></i> {{ __('messages.fetching_location_name') }}
                                </span>
                            </label>
                            <input type="text" name="location_name" id="location_name"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50"
                                placeholder="{{ __('messages.auto_filled_from_coordinates') }}" readonly>
                            <p class="mt-2 text-xs text-gray-500 flex items-center">
                                <i class="fas fa-magic text-blue-400 mr-1"></i>
                                {{ __('messages.auto_filled_when_select_location') }}
                            </p>
                        </div>

                        <!-- Location Description -->
                        <div class="mt-6">
                            <label for="location_description" class="block text-sm font-semibold text-gray-700 mb-2">
                                <i class="fas fa-file-alt text-purple-500 mr-1"></i>
                                {{ __('messages.location_description') }}
                            </label>
                            <textarea name="location_description" id="location_description" rows="4"
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 bg-gray-50"
                                placeholder="{{ __('messages.location_description_placeholder') }}" readonly></textarea>
                            <p class="mt-2 text-xs text-gray-500 flex items-center">
                                <i class="fas fa-info-circle text-blue-400 mr-1"></i>
                                {{ __('messages.full_address_auto_filled') }}
                            </p>
                        </div>
                    </div>

                    <!-- Date Range -->
                    <div class="mb-8 border-t-2 border-gray-200 pt-8">
                        <div class="flex items-center gap-3 mb-6">
                            <div class="w-11 h-11 sm:w-12 sm:h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl flex items-center justify-center shadow-lg">
                                <i class="fas fa-calendar-alt text-white text-lg sm:text-xl"></i>
                            </div>
                            <div class="min-w-0">
                                <h3 class="text-lg sm:text-xl font-extrabold text-gray-900">{{ __('messages.analysis_period') }}</h3>
                                <p class="text-xs sm:text-sm text-gray-600">{{ __('messages.determine_time_range_for_analysis') }}</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <!-- Start Date -->
                            <div>
                                <label for="start_date" class="flex items-center gap-2 text-sm font-bold text-gray-800 mb-2">
                                    <div class="w-5 h-5 bg-green-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-calendar-check text-green-600 text-xs"></i>
                                    </div>
                                    {{ __('messages.start_date') }} <span class="text-red-600">*</span>
                                </label>
                                <input type="date" name="start_date" id="start_date"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 font-medium"
                                    required>
                            </div>

                            <!-- End Date -->
                            <div>
                                <label for="end_date" class="flex items-center gap-2 text-sm font-bold text-gray-800 mb-2">
                                    <div class="w-5 h-5 bg-red-100 rounded-lg flex items-center justify-center">
                                        <i class="fas fa-calendar-times text-red-600 text-xs"></i>
                                    </div>
                                    {{ __('messages.end_date') }} <span class="text-red-600">*</span>
                                </label>
                                <input type="date" name="end_date" id="end_date"
                                    class="w-full px-4 py-3 border-2 border-gray-200 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-200 font-medium"
                                    required max="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <div class="mt-5 p-4 bg-gradient-to-r from-yellow-50 to-orange-50 rounded-2xl border-2 border-yellow-200">
                            <p class="text-sm text-yellow-900 flex items-start gap-2 font-medium">
                                <i class="fas fa-exclamation-triangle text-yellow-600 mt-0.5 shrink-0"></i>
                                <span>{!! __('messages.date_validation_important') !!}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3 border-t-2 border-gray-200 pt-8">
                        <a href="{{ route('hidrologi.index') }}" 
                           class="inline-flex items-center justify-center gap-2 px-6 py-3 text-gray-700 bg-gray-100 hover:bg-gray-200 font-bold rounded-2xl transition-all duration-200 transform hover:scale-105">
                            <i class="fas fa-arrow-left"></i>
                            <span>{{ __('messages.back') }}</span>
                        </a>
                        <button type="submit" id="submitBtn"
                            class="inline-flex items-center justify-center gap-2 px-8 py-3 bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-600 hover:from-blue-700 hover:via-blue-800 hover:to-indigo-700 text-white font-extrabold rounded-2xl transition-all duration-200 transform hover:scale-105 shadow-xl hover:shadow-2xl">
                            <i class="fas fa-paper-plane"></i>
                            <span>{{ __('messages.create_analysis') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="lg:col-span-1 space-y-5 sm:space-y-6">
            <!-- Cara Kerja Card -->
            <div class="bg-gradient-to-br from-blue-50 to-indigo-100 rounded-3xl p-5 sm:p-6 shadow-xl info-card border-2 border-blue-200">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-info-circle text-white text-lg"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-extrabold text-blue-900">{{ __('messages.how_it_works') }}</h3>
                </div>
                <ol class="space-y-3 text-sm text-blue-900">
                    <li class="flex items-start p-4 bg-white rounded-2xl shadow-sm hover:shadow-md transition-all">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 text-white text-sm font-extrabold mr-3 shrink-0 shadow-md">1</span>
                        <span class="pt-1 font-medium">{!! __('messages.step_select_location') !!}</span>
                    </li>
                    <li class="flex items-start p-4 bg-white rounded-2xl shadow-sm hover:shadow-md transition-all">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 text-white text-sm font-extrabold mr-3 shrink-0 shadow-md">2</span>
                        <span class="pt-1 font-medium">{!! __('messages.step_submit_job') !!}</span>
                    </li>
                    <li class="flex items-start p-4 bg-white rounded-2xl shadow-sm hover:shadow-md transition-all">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 text-white text-sm font-extrabold mr-3 shrink-0 shadow-md">3</span>
                        <span class="pt-1 font-medium">{!! __('messages.step_monitor_progress') !!}</span>
                    </li>
                    <li class="flex items-start p-4 bg-white rounded-2xl shadow-sm hover:shadow-md transition-all">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-gradient-to-br from-blue-600 to-blue-700 text-white text-sm font-extrabold mr-3 shrink-0 shadow-md">4</span>
                        <span class="pt-1 font-medium">{!! __('messages.step_download_results') !!}</span>
                    </li>
                </ol>
            </div>

            <!-- Catatan Penting Card -->
            <div class="bg-gradient-to-br from-yellow-50 to-orange-100 rounded-3xl p-5 sm:p-6 shadow-xl info-card border-2 border-yellow-200">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 bg-gradient-to-br from-yellow-600 to-orange-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-exclamation-triangle text-white text-lg"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-extrabold text-yellow-900">{{ __('messages.important_notes') }}</h3>
                </div>
                <ul class="space-y-3 text-sm text-yellow-900">
                    <li class="flex items-start p-4 bg-white rounded-2xl shadow-sm hover:shadow-md transition-all">
                        <i class="fas fa-clock text-yellow-600 mr-3 mt-1 shrink-0"></i>
                        <span class="font-medium">{!! __('messages.processing_time') !!}</span>
                    </li>
                    <li class="flex items-start p-4 bg-white rounded-2xl shadow-sm hover:shadow-md transition-all">
                        <i class="fas fa-bell text-yellow-600 mr-3 mt-1 shrink-0"></i>
                        <span class="font-medium">{!! __('messages.notification') !!}</span>
                    </li>
                    <li class="flex items-start p-4 bg-white rounded-2xl shadow-sm hover:shadow-md transition-all">
                        <i class="fas fa-save text-yellow-600 mr-3 mt-1 shrink-0"></i>
                        <span class="font-medium">{!! __('messages.storage') !!}</span>
                    </li>
                </ul>
            </div>

            <!-- Tips Card -->
            <div class="bg-gradient-to-br from-green-50 to-emerald-100 rounded-3xl p-5 sm:p-6 shadow-xl info-card border-2 border-green-200">
                <div class="flex items-center gap-3 mb-5">
                    <div class="w-11 h-11 bg-gradient-to-br from-green-600 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg">
                        <i class="fas fa-lightbulb text-white text-lg"></i>
                    </div>
                    <h3 class="text-lg sm:text-xl font-extrabold text-green-900">{{ __('messages.tips') }}</h3>
                </div>
                <ul class="space-y-3 text-sm text-green-900">
                    <li class="flex items-start p-4 bg-white rounded-2xl shadow-sm hover:shadow-md transition-all">
                        <i class="fas fa-map text-green-600 mr-3 mt-1 shrink-0"></i>
                        <span class="font-medium">{{ __('messages.use_search_feature') }}</span>
                    </li>
                    <li class="flex items-start p-4 bg-white rounded-2xl shadow-sm hover:shadow-md transition-all">
                        <i class="fas fa-calendar text-green-600 mr-3 mt-1 shrink-0"></i>
                        <span class="font-medium">{{ __('messages.choose_appropriate_date_range') }}</span>
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