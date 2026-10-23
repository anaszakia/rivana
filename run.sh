#!/bin/bash

# =========================================================
# RIVANA FULLSTACK RUNNER (Laravel + Python API)
# =========================================================

PROJECT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
RIVANA_DIR="$PROJECT_DIR/rivana"
PY_RIVANA_DIR="$PROJECT_DIR/py_rivana"
PYTHON_BIN="$PY_RIVANA_DIR/venv/bin/python"

echo "========================================================="
echo "  🚀 Starting RIVANA (Laravel Web + Python ML API)"
echo "========================================================="

# 1. Bersihkan port jika ada proses sebelumnya yang masih nyangkut
cleanup_ports() {
    local p8001=$(lsof -ti :8001 2>/dev/null)
    local p8000=$(lsof -ti :8000 2>/dev/null)
    if [ -n "$p8001" ]; then
        kill -9 $p8001 2>/dev/null
    fi
    if [ -n "$p8000" ]; then
        kill -9 $p8000 2>/dev/null
    fi
}

cleanup_ports

# Trap Ctrl+C (SIGINT/SIGTERM) untuk mematikan kedua server sekaligus
cleanup() {
    echo ""
    echo "========================================================="
    echo "  🛑 Shutting down all servers..."
    echo "========================================================="
    if [ -n "$PID_PYTHON" ]; then
        kill $PID_PYTHON 2>/dev/null
    fi
    if [ -n "$PID_LARAVEL" ]; then
        kill $PID_LARAVEL 2>/dev/null
    fi
    cleanup_ports
    echo "  ✅ All servers stopped cleanly."
    exit 0
}

trap cleanup INT TERM EXIT

# 2. Jalankan Python ML API Server (Port 8001)
echo "  [1/2] Starting Python ML API (Port 8001)..."
cd "$PY_RIVANA_DIR/project_hidrologi_ml"
"$PYTHON_BIN" api_server.py > /dev/null 2>&1 &
PID_PYTHON=$!

# Tunggu sebentar memastikan Python API up
sleep 1.5

if ps -p $PID_PYTHON > /dev/null; then
    echo "  -> Python ML API running at: http://127.0.0.1:8001 [PID: $PID_PYTHON]"
else
    echo "  ❌ Failed to start Python ML API. Check python venv or logs."
    exit 1
fi

# 3. Jalankan Laravel Web Server (Port 8000)
echo "  [2/2] Starting Laravel Web Server (Port 8000)..."
cd "$RIVANA_DIR"
echo "  -> Laravel Web running at:   http://127.0.0.1:8000"
echo "---------------------------------------------------------"
echo "  👉 Superadmin Login: superadmin@gmail.com / password123"
echo "  👉 Press [Ctrl + C] to stop both servers."
echo "========================================================="

php artisan serve --port=8000 &
PID_LARAVEL=$!

# Tunggu child processes
wait $PID_LARAVEL $PID_PYTHON
