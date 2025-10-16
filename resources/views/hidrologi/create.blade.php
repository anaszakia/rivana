@extends('layouts.app')

@section('title', 'Create New Analysis')

@push('styles')
<!-- Leaflet CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" crossorigin=""/>
<!-- Leaflet Geocoder CSS -->
<link rel="stylesheet" href="https://unpkg.com/leaflet-control-geocoder@2.4.0/dist/Control.Geocoder.css" crossorigin=""/>
<style>
    #map {
        height: 500px;
        width: 100%;
    }
    /* Custom styling for search box */
    .leaflet-control-geocoder {
        border-radius: 0.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .leaflet-control-geocoder-form input {
        border-radius: 0.375rem;
        padding: 0.5rem;
        font-size: 14px;
    }
</style>
@endpush

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex items-center space-x-2 text-sm text-gray-600 mb-2">
            <a href="{{ route('hidrologi.index') }}" class="hover:text-blue-600">Hidrologi</a>
            <i class="fas fa-chevron-right text-xs"></i>
            <span class="text-gray-800">Create New Analysis</span>
        </div>
        <h1 class="text-3xl font-bold text-gray-800">Create New Hydrological Analysis</h1>
        <p class="text-gray-600 mt-1">Submit a new job for hydrological data analysis</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Form -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <form id="hidrologiForm" action="{{ route('hidrologi.submit') }}" method="POST">
                    @csrf

                    <!-- Location Information -->
                    <div class="mb-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-map-marker-alt text-blue-600 mr-2"></i>
                            Location Information
                        </h3>

                        <!-- Interactive Map -->
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Select Location on Map
                            </label>
                            <div id="map"></div>
                            <p class="mt-2 text-xs text-gray-500">
                                <i class="fas fa-info-circle text-blue-500"></i>
                                Click on the map to select a location or enter coordinates manually below
                            </p>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Longitude -->
                            <div>
                                <label for="longitude" class="block text-sm font-medium text-gray-700 mb-2">
                                    Longitude <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.000001" name="longitude" id="longitude" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="e.g., 106.845599" required min="-180" max="180">
                                <p class="mt-1 text-xs text-gray-500">Range: -180 to 180</p>
                            </div>

                            <!-- Latitude -->
                            <div>
                                <label for="latitude" class="block text-sm font-medium text-gray-700 mb-2">
                                    Latitude <span class="text-red-500">*</span>
                                </label>
                                <input type="number" step="0.000001" name="latitude" id="latitude"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    placeholder="e.g., -6.208763" required min="-90" max="90">
                                <p class="mt-1 text-xs text-gray-500">Range: -90 to 90</p>
                            </div>
                        </div>

                        <!-- Location Name -->
                        <div class="mt-4">
                            <label for="location_name" class="block text-sm font-medium text-gray-700 mb-2">
                                Location Name
                                <span id="location-loading" class="hidden text-blue-500 text-xs ml-2">
                                    <i class="fas fa-spinner fa-spin"></i> Getting location name...
                                </span>
                            </label>
                            <input type="text" name="location_name" id="location_name"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Auto-generated from coordinates or enter manually">
                            <p class="mt-1 text-xs text-gray-500">Automatically filled when you select on map</p>
                        </div>

                        <!-- Location Description -->
                        <div class="mt-4">
                            <label for="location_description" class="block text-sm font-medium text-gray-700 mb-2">
                                Location Description
                            </label>
                            <textarea name="location_description" id="location_description" rows="3"
                                class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                placeholder="Optional description about the location"></textarea>
                        </div>
                    </div>

                    <!-- Date Range -->
                    <div class="mb-6 border-t pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4 flex items-center">
                            <i class="fas fa-calendar-alt text-blue-600 mr-2"></i>
                            Analysis Period
                        </h3>

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Start Date -->
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Start Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="start_date" id="start_date"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required>
                            </div>

                            <!-- End Date -->
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    End Date <span class="text-red-500">*</span>
                                </label>
                                <input type="date" name="end_date" id="end_date"
                                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                    required max="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                        <p class="mt-2 text-sm text-gray-600">
                            <i class="fas fa-info-circle text-blue-500"></i>
                            End date must be today or earlier
                        </p>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex items-center justify-between border-t pt-6">
                        <a href="{{ route('hidrologi.index') }}" class="text-gray-600 hover:text-gray-800 font-medium">
                            <i class="fas fa-arrow-left mr-2"></i>
                            Back
                        </a>
                        <button type="submit" id="submitBtn"
                            class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-medium rounded-lg transition duration-200">
                            <i class="fas fa-paper-plane mr-2"></i>
                            Submit Analysis
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Info Sidebar -->
        <div class="lg:col-span-1">
            <div class="bg-blue-50 rounded-lg p-6 mb-4">
                <h3 class="text-lg font-semibold text-blue-900 mb-3 flex items-center">
                    <i class="fas fa-info-circle mr-2"></i>
                    How It Works
                </h3>
                <ol class="space-y-3 text-sm text-blue-800">
                    <li class="flex items-start">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold mr-3 flex-shrink-0">1</span>
                        <span>Enter the geographic coordinates and date range for your analysis</span>
                    </li>
                    <li class="flex items-start">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold mr-3 flex-shrink-0">2</span>
                        <span>Submit the job to our Python API for processing</span>
                    </li>
                    <li class="flex items-start">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold mr-3 flex-shrink-0">3</span>
                        <span>Monitor the progress in real-time</span>
                    </li>
                    <li class="flex items-start">
                        <span class="inline-flex items-center justify-center w-6 h-6 rounded-full bg-blue-600 text-white text-xs font-bold mr-3 flex-shrink-0">4</span>
                        <span>Download the generated analysis files when complete</span>
                    </li>
                </ol>
            </div>

            <div class="bg-yellow-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-yellow-900 mb-3 flex items-center">
                    <i class="fas fa-exclamation-triangle mr-2"></i>
                    Important Notes
                </h3>
                <ul class="space-y-2 text-sm text-yellow-800">
                    <li class="flex items-start">
                        <i class="fas fa-check text-yellow-600 mr-2 mt-1"></i>
                        <span>Processing time depends on the date range and data availability</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-yellow-600 mr-2 mt-1"></i>
                        <span>You will be notified when the analysis is complete</span>
                    </li>
                    <li class="flex items-start">
                        <i class="fas fa-check text-yellow-600 mr-2 mt-1"></i>
                        <span>Files are stored for 30 days after generation</span>
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
                    
                    locationNameInput.value = locationName;
                    
                    const descInput = document.getElementById('location_description');
                    if (!descInput.value) {
                        descInput.value = data.display_name;
                    }
                } else {
                    locationNameInput.value = `Location (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
                }
            })
            .catch(error => {
                console.error('Error fetching location:', error);
                loadingSpan.classList.add('hidden');
                locationNameInput.value = `Location (${lat.toFixed(4)}, ${lng.toFixed(4)})`;
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
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Submitting...';
        
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
                    title: 'Success!',
                    text: data.message || 'Job submitted successfully! Redirecting to job details...',
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
                    title: 'Failed!',
                    html: `<p>${data.message || data.error || 'Failed to submit job'}</p>` +
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
            
            let errorMessage = 'An error occurred while submitting the job';
            
            if (error.name === 'AbortError') {
                errorMessage = 'Request timeout! Python API mungkin sedang memproses atau tidak merespon.';
                console.error('⏱️ TIMEOUT: Request took more than 30 seconds');
            } else if (error.message.includes('JSON')) {
                errorMessage = 'Server returned invalid response. Check Laravel logs.';
            } else if (error.message.includes('NetworkError') || error.message.includes('Failed to fetch')) {
                errorMessage = 'Network error! Check if Laravel server is running.';
            }
            
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                html: `<p>${errorMessage}</p><pre class="text-left text-xs mt-2">${error.message}</pre>`,
                footer: '<p class="text-xs">Check browser console (F12) for details</p>',
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