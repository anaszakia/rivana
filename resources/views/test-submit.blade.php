<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quick Submit Test - Laravel Blade Version</title>
    <style>
        body {
            font-family: 'Courier New', monospace;
            background: #1e1e1e;
            color: #d4d4d4;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 900px;
            margin: 0 auto;
            background: #252526;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.5);
        }
        h1 {
            color: #4ec9b0;
            margin-bottom: 10px;
        }
        .subtitle {
            color: #858585;
            margin-bottom: 30px;
        }
        button {
            background: #0e639c;
            color: white;
            border: none;
            padding: 15px 30px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 5px;
            font-weight: bold;
            transition: background 0.3s;
        }
        button:hover {
            background: #1177bb;
        }
        button:disabled {
            background: #555;
            cursor: not-allowed;
        }
        #output {
            margin-top: 30px;
            background: #1e1e1e;
            padding: 20px;
            border-radius: 5px;
            border-left: 4px solid #0e639c;
            min-height: 200px;
            max-height: 600px;
            overflow-y: auto;
        }
        .log-line {
            margin: 5px 0;
            padding: 5px;
            border-radius: 3px;
        }
        .log-success { color: #4ec9b0; }
        .log-error { color: #f48771; background: rgba(244, 135, 113, 0.1); }
        .log-warning { color: #dcdcaa; }
        .log-info { color: #9cdcfe; }
        .separator {
            color: #858585;
            margin: 10px 0;
            font-weight: bold;
        }
        .initial-info {
            background: #2d2d30;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #4ec9b0;
        }
        .initial-info strong {
            color: #4ec9b0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🧪 Quick Submit Test (Laravel Blade)</h1>
        <p class="subtitle">Test form submission dengan CSRF token dari Laravel</p>
        
        <div class="initial-info">
            <strong>✓ CSRF Token:</strong> {{ csrf_token() }}<br>
            <strong>✓ Base URL:</strong> {{ url('/') }}<br>
            <strong>✓ Submit URL:</strong> {{ route('hidrologi.submit') }}<br>
            <strong>✓ Session Driver:</strong> {{ config('session.driver') }}
        </div>

        <button onclick="testSubmit()" id="testBtn">🚀 RUN TEST SUBMIT</button>
        
        <div id="output"></div>
    </div>

    <script>
        const output = document.getElementById('output');
        const testBtn = document.getElementById('testBtn');

        function log(message, type = 'info') {
            const timestamp = new Date().toLocaleTimeString();
            const line = document.createElement('div');
            line.className = `log-line log-${type}`;
            line.textContent = `[${timestamp}] ${message}`;
            output.appendChild(line);
            output.scrollTop = output.scrollHeight;
            console.log(`[${type.toUpperCase()}] ${message}`);
        }

        function logSeparator(text) {
            const line = document.createElement('div');
            line.className = 'log-line separator';
            line.textContent = '='.repeat(60);
            if (text) {
                line.textContent += `\n${text}`;
                line.textContent += '\n' + '='.repeat(60);
            }
            output.appendChild(line);
        }

        async function testSubmit() {
            output.innerHTML = '';
            testBtn.disabled = true;
            
            logSeparator('STARTING QUICK SUBMIT TEST');
            log('Using Laravel Blade with embedded CSRF', 'info');
            
            try {
                // Step 1: CSRF Token (already in page)
                log('Step 1: Getting CSRF token...', 'info');
                const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                log(`  ✓ Token: ${csrfToken.substring(0, 20)}...`, 'success');

                // Step 2: Build FormData
                log('Step 2: Building form data...', 'info');
                const formData = new FormData();
                formData.append('longitude', '106.8456');
                formData.append('latitude', '-6.2088');
                formData.append('location_name', 'Jakarta Test Location');
                formData.append('start_date', '2024-01-01');
                formData.append('end_date', '2024-12-31');
                log('  ✓ Form data prepared:', 'success');
                log('    - Longitude: 106.8456', 'info');
                log('    - Latitude: -6.2088', 'info');
                log('    - Period: 2024-01-01 to 2024-12-31', 'info');

                // Step 3: Submit with timeout
                log('Step 3: Submitting to Laravel...', 'warning');
                log(`  URL: {{ route('hidrologi.submit') }}`, 'info');
                log('  Timeout: 15 seconds', 'info');
                
                const startTime = Date.now();
                
                const controller = new AbortController();
                const timeoutId = setTimeout(() => {
                    controller.abort();
                    log('  ⏱️ Request aborted after 15 seconds', 'error');
                }, 15000);

                const response = await fetch('{{ route("hidrologi.submit") }}', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    signal: controller.signal
                });

                clearTimeout(timeoutId);
                const duration = ((Date.now() - startTime) / 1000).toFixed(2);
                
                log(`  ✓ Response received after ${duration}s`, 'success');
                log(`  Status: ${response.status} ${response.statusText}`, 'info');

                // Step 4: Parse Response
                log('Step 4: Parsing response...', 'info');
                const contentType = response.headers.get('content-type');
                log(`  Content-Type: ${contentType}`, 'info');

                if (!response.ok) {
                    const errorText = await response.text();
                    log(`  ❌ Server returned error ${response.status}`, 'error');
                    log(`  Response: ${errorText.substring(0, 200)}`, 'error');
                    throw new Error(`HTTP ${response.status}: ${errorText.substring(0, 100)}`);
                }

                const data = await response.json();
                log('  ✓ JSON parsed successfully', 'success');

                // Step 5: Display Result
                logSeparator('RESULT');
                if (data.success) {
                    log('✅ SUCCESS!', 'success');
                    log(`Job ID: ${data.job_id}`, 'success');
                    log(`Status: ${data.status}`, 'success');
                    log(`Message: ${data.message}`, 'success');
                    
                    if (data.estimated_time) {
                        log(`Estimated Time: ${data.estimated_time} seconds`, 'info');
                    }
                    
                    logSeparator();
                    log('🎉 Form submission is WORKING!', 'success');
                    log('The "muter-muter" issue is likely due to Python API being slow.', 'warning');
                    log('Recommendation: Increase timeout to 60-120 seconds.', 'warning');
                } else {
                    log('⚠️ Request succeeded but returned success=false', 'warning');
                    log(`Message: ${data.message}`, 'warning');
                }

            } catch (error) {
                logSeparator('ERROR OCCURRED!');
                
                if (error.name === 'AbortError') {
                    log('❌ REQUEST TIMEOUT (15s)', 'error');
                    log('Python API is taking too long to respond.', 'error');
                    log('', 'info');
                    log('SOLUTION:', 'warning');
                    log('1. Check if Python API is running: http://localhost:8000', 'warning');
                    log('2. Increase timeout in create.blade.php from 30s to 120s', 'warning');
                    log('3. Or make Python API async (background processing)', 'warning');
                } else if (error.message.includes('JSON')) {
                    log('❌ INVALID JSON RESPONSE', 'error');
                    log('Laravel returned HTML instead of JSON (likely an error page)', 'error');
                    log('Check storage/logs/laravel.log for errors', 'warning');
                } else {
                    log(`❌ ERROR: ${error.message}`, 'error');
                    if (error.stack) {
                        log(`Stack: ${error.stack}`, 'error');
                    }
                }
            } finally {
                testBtn.disabled = false;
                logSeparator('TEST COMPLETED');
            }
        }
    </script>
</body>
</html>
