#!/bin/bash

# Script untuk menampilkan private key yang perlu di-copy ke GitHub Secret KEY

echo "╔════════════════════════════════════════════════════════════════╗"
echo "║  🔑 GET PRIVATE KEY FOR GITHUB SECRET                          ║"
echo "╚════════════════════════════════════════════════════════════════╝"
echo ""

KEY_FILE="/root/.ssh/github_deploy"

if [ ! -f "$KEY_FILE" ]; then
    echo "❌ Error: Private key tidak ditemukan!"
    echo "File: $KEY_FILE"
    echo ""
    echo "Jalankan dulu: bash setup-deploy.sh"
    exit 1
fi

echo "✅ Private key ditemukan!"
echo ""
echo "════════════════════════════════════════════════════════════════"
echo "  📋 COPY SELURUH OUTPUT DIBAWAH INI KE GITHUB SECRET 'KEY'"
echo "════════════════════════════════════════════════════════════════"
echo ""

cat "$KEY_FILE"

echo ""
echo "════════════════════════════════════════════════════════════════"
echo "  ⚠️  PENTING: Copy dari '-----BEGIN' sampai '-----END'"
echo "════════════════════════════════════════════════════════════════"
echo ""
echo "📝 Langkah selanjutnya:"
echo ""
echo "1. Select dan copy SEMUA text diatas (Ctrl+Shift+C)"
echo "2. Buka: https://github.com/YOUR_USERNAME/YOUR_REPO/settings/secrets/actions"
echo "3. Klik 'New repository secret'"
echo "4. Name: KEY"
echo "5. Value: (Paste private key yang sudah di-copy)"
echo "6. Klik 'Add secret'"
echo ""
