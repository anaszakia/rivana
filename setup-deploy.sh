#!/bin/bash

# 🚀 RIVANA Auto Deploy Setup Script
# Jalankan script ini di SERVER untuk setup SSH key

set -e

echo "=================================================="
echo "🚀 RIVANA Auto Deploy Setup Script"
echo "=================================================="
echo ""

# Color codes
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Check if running as root
if [ "$EUID" -ne 0 ]; then 
    echo -e "${RED}❌ Error: Script ini harus dijalankan sebagai root${NC}"
    echo "Gunakan: sudo bash setup-deploy.sh"
    exit 1
fi

echo -e "${BLUE}📝 Step 1: Generate SSH Key${NC}"
echo "=================================================="

# SSH directory
SSH_DIR="/root/.ssh"
KEY_FILE="$SSH_DIR/github_deploy"

# Create .ssh directory if not exists
if [ ! -d "$SSH_DIR" ]; then
    mkdir -p "$SSH_DIR"
    chmod 700 "$SSH_DIR"
    echo -e "${GREEN}✅ Directory .ssh created${NC}"
fi

# Generate SSH key if not exists
if [ -f "$KEY_FILE" ]; then
    echo -e "${YELLOW}⚠️  SSH key sudah ada: $KEY_FILE${NC}"
    read -p "Generate ulang? (y/N): " -n 1 -r
    echo
    if [[ ! $REPLY =~ ^[Yy]$ ]]; then
        echo -e "${BLUE}Menggunakan key yang sudah ada...${NC}"
    else
        rm -f "$KEY_FILE" "$KEY_FILE.pub"
        ssh-keygen -t rsa -b 4096 -C "github-autodeploy" -f "$KEY_FILE" -N ""
        echo -e "${GREEN}✅ SSH key baru berhasil di-generate${NC}"
    fi
else
    ssh-keygen -t rsa -b 4096 -C "github-autodeploy" -f "$KEY_FILE" -N ""
    echo -e "${GREEN}✅ SSH key berhasil di-generate${NC}"
fi

echo ""
echo -e "${BLUE}📝 Step 2: Add Public Key to authorized_keys${NC}"
echo "=================================================="

# Add public key to authorized_keys
if [ -f "$KEY_FILE.pub" ]; then
    cat "$KEY_FILE.pub" >> "$SSH_DIR/authorized_keys"
    chmod 600 "$SSH_DIR/authorized_keys"
    echo -e "${GREEN}✅ Public key berhasil ditambahkan${NC}"
else
    echo -e "${RED}❌ Error: Public key tidak ditemukan${NC}"
    exit 1
fi

echo ""
echo -e "${BLUE}📝 Step 3: Configure SSH${NC}"
echo "=================================================="

# Create SSH config
cat > "$SSH_DIR/config" << 'EOF'
Host *
  StrictHostKeyChecking no
  UserKnownHostsFile=/dev/null
EOF

chmod 600 "$SSH_DIR/config"
echo -e "${GREEN}✅ SSH config berhasil dibuat${NC}"

echo ""
echo -e "${BLUE}📝 Step 4: Set Permissions${NC}"
echo "=================================================="

# Set permissions
chmod 600 "$KEY_FILE"
chmod 644 "$KEY_FILE.pub"
chmod 700 "$SSH_DIR"

echo -e "${GREEN}✅ Permissions berhasil di-set${NC}"

echo ""
echo "=================================================="
echo -e "${GREEN}✅ Setup SSH Key Selesai!${NC}"
echo "=================================================="
echo ""

# Get server info
SERVER_IP=$(hostname -I | awk '{print $1}')
SERVER_USER=$(whoami)

echo -e "${YELLOW}📋 INFORMASI UNTUK GITHUB SECRETS:${NC}"
echo "=================================================="
echo ""
echo -e "${BLUE}Secret 1: HOST${NC}"
echo "Value: $SERVER_IP"
echo ""
echo -e "${BLUE}Secret 2: USER${NC}"
echo "Value: $SERVER_USER"
echo ""
echo -e "${BLUE}Secret 3: KEY${NC}"
echo -e "${RED}COPY seluruh output dibawah ini (termasuk BEGIN dan END):${NC}"
echo "---BEGIN PRIVATE KEY---"
cat "$KEY_FILE"
echo "---END PRIVATE KEY---"
echo ""

echo "=================================================="
echo -e "${YELLOW}📝 LANGKAH SELANJUTNYA:${NC}"
echo "=================================================="
echo ""
echo "1. Buka GitHub Repository Settings:"
echo "   https://github.com/YOUR_USERNAME/YOUR_REPO/settings/secrets/actions"
echo ""
echo "2. Klik 'New repository secret'"
echo ""
echo "3. Tambahkan 3 secrets:"
echo "   - Name: HOST"
echo "     Value: $SERVER_IP"
echo ""
echo "   - Name: USER"
echo "     Value: $SERVER_USER"
echo ""
echo "   - Name: KEY"
echo "     Value: (Copy dari output diatas)"
echo ""
echo "4. Test dengan push ke GitHub:"
echo "   git add ."
echo "   git commit -m 'Test auto deploy'"
echo "   git push origin main"
echo ""
echo "5. Monitor di GitHub Actions:"
echo "   https://github.com/YOUR_USERNAME/YOUR_REPO/actions"
echo ""
echo "=================================================="
echo -e "${GREEN}✅ Script selesai!${NC}"
echo "=================================================="
echo ""

# Test SSH connection
echo -e "${BLUE}🔍 Testing SSH connection...${NC}"
ssh -i "$KEY_FILE" -o BatchMode=yes -o ConnectTimeout=5 "$SERVER_USER@$SERVER_IP" "echo 'SSH connection successful!'" 2>/dev/null

if [ $? -eq 0 ]; then
    echo -e "${GREEN}✅ SSH connection test: SUCCESS${NC}"
else
    echo -e "${RED}⚠️  SSH connection test: Please verify manually${NC}"
fi

echo ""
