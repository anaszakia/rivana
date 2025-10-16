<?php
/**
 * Test Script untuk Koneksi ke Python API
 * Jalankan: php test-api-connection.php
 */

echo "=== TEST KONEKSI KE PYTHON API ===\n\n";

// Test 1: Cek apakah Python API berjalan
echo "1. Testing Python API Connection...\n";
$apiUrl = 'http://localhost:8000';

$ch = curl_init($apiUrl . '/jobs');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_TIMEOUT, 5);
curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

if ($httpCode == 200) {
    echo "   ✓ Python API is running!\n";
    echo "   Response: $response\n\n";
} else {
    echo "   ✗ Python API is NOT running or not accessible!\n";
    echo "   HTTP Code: $httpCode\n";
    echo "   Error: $error\n";
    echo "   \n";
    echo "   SOLUSI:\n";
    echo "   1. Pastikan Python API berjalan di http://localhost:8000\n";
    echo "   2. Jalankan: python api_server.py\n";
    echo "   3. Cek firewall/antivirus yang mungkin memblokir\n\n";
    exit(1);
}

// Test 2: Test submit job
echo "2. Testing Submit Job...\n";
$testData = [
    'longitude' => 106.845599,
    'latitude' => -6.208763,
    'start' => '2024-01-01',
    'end' => '2024-01-31'
];

$ch = curl_init($apiUrl . '/generate');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($testData));
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Content-Type: application/json',
    'Accept: application/json'
]);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$error = curl_error($ch);
curl_close($ch);

echo "   HTTP Code: $httpCode\n";
echo "   Response: $response\n";

if ($httpCode == 200) {
    $data = json_decode($response, true);
    if (isset($data['job_id'])) {
        echo "   ✓ Job submitted successfully!\n";
        echo "   Job ID: {$data['job_id']}\n";
        
        // Test 3: Check status
        echo "\n3. Testing Check Status...\n";
        sleep(2);
        
        $ch = curl_init($apiUrl . '/status/' . $data['job_id']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $statusResponse = curl_exec($ch);
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        echo "   HTTP Code: $statusCode\n";
        echo "   Response: $statusResponse\n";
        
        if ($statusCode == 200) {
            echo "   ✓ Status check working!\n";
        }
    }
} else {
    echo "   ✗ Failed to submit job!\n";
    echo "   Error: $error\n";
}

echo "\n=== TEST SELESAI ===\n";
