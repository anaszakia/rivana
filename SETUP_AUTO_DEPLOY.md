# 🚀 Setup Auto Deploy - Langkah Demi Langkah

## ❌ Masalah Saat Ini
Auto-deploy **BELUM BERJALAN** karena GitHub Secrets belum disetup. Anda masih harus manual `git pull` di server.

## ✅ Solusi: Setup GitHub Secrets

---

## 📝 Langkah 1: Generate SSH Key di Server

**SSH ke server Anda:**
```bash
ssh root@103.122.67.145
```

**Generate SSH Key (jika belum ada):**
```bash
# Generate key pair
ssh-keygen -t rsa -b 4096 -C "github-autodeploy" -f ~/.ssh/github_deploy -N ""

# Tampilkan public key
cat ~/.ssh/github_deploy.pub
```

**Tambahkan public key ke authorized_keys:**
```bash
cat ~/.ssh/github_deploy.pub >> ~/.ssh/authorized_keys
chmod 600 ~/.ssh/authorized_keys
```

**Copy PRIVATE KEY untuk GitHub:**
```bash
cat ~/.ssh/github_deploy
```

**Copy seluruh output** (termasuk `-----BEGIN` sampai `-----END`), ini akan dipakai di GitHub Secrets.

---

## 📝 Langkah 2: Setup GitHub Secrets

### 1. Buka Repository Settings
- Buka: https://github.com/anaszakia/rivana/settings/secrets/actions
- (Ganti `anaszakia/rivana` dengan username/repo Anda yang sebenarnya)

### 2. Klik "New repository secret"

### 3. Tambahkan 3 Secrets:

#### **Secret 1: HOST**
- Name: `HOST`
- Value: `103.122.67.145`
- Klik **Add secret**

#### **Secret 2: USER**
- Name: `USER`
- Value: `root`
- Klik **Add secret**

#### **Secret 3: KEY**
- Name: `KEY`
- Value: (Paste seluruh isi private key dari `cat ~/.ssh/github_deploy`)
  ```
  -----BEGIN OPENSSH PRIVATE KEY-----
  b3BlbnNzaC1rZXktdjEAAAAABG5vbmUAAAAEbm9uZQAAAAAAAAABAAACFwAAAA
  ... (banyak baris)
  ... (paste semua)
  -----END OPENSSH PRIVATE KEY-----
  ```
- Klik **Add secret**

---

## 📝 Langkah 3: Test Auto Deploy

### 1. Cek GitHub Secrets sudah terpasang:
- Buka: https://github.com/anaszakia/rivana/settings/secrets/actions
- Pastikan ada 3 secrets: **HOST**, **USER**, **KEY**

### 2. Buat perubahan kecil di lokal:
```bash
# Edit file (contoh: tambah komen di welcome.blade.php)
code resources/views/welcome.blade.php

# Commit & Push
git add .
git commit -m "Test auto deploy"
git push origin main
```

### 3. Monitor di GitHub Actions:
- Buka: https://github.com/anaszakia/rivana/actions
- Lihat workflow **"Deploy to Server"**
- Klik untuk melihat progress real-time

**Status:**
- 🟡 **Yellow (Kuning)** = Sedang berjalan (tunggu 2-5 menit)
- 🟢 **Green (Hijau)** = Berhasil! ✅
- 🔴 **Red (Merah)** = Gagal (klik untuk lihat error)

---

## 🎯 Setelah Setup Berhasil

### Deploy Cukup dengan Push:
```bash
git add .
git commit -m "Update aplikasi"
git push origin main
```

**Deploy otomatis akan berjalan!** Tidak perlu SSH ke server lagi! 🎉

---

## 🐛 Troubleshooting

### ❌ Error: "Permission denied (publickey)"
**Solusi:**
```bash
# Di server, pastikan private key ada
ls -la ~/.ssh/github_deploy

# Test koneksi SSH
ssh -i ~/.ssh/github_deploy root@103.122.67.145
```

Jika berhasil login, berarti key sudah benar. Copy private key lagi dan paste ke GitHub Secret `KEY`.

### ❌ Error: "Host key verification failed"
**Solusi:**
```bash
# Di server, disable strict host checking untuk GitHub Actions
echo "Host *
  StrictHostKeyChecking no
  UserKnownHostsFile=/dev/null" >> ~/.ssh/config

chmod 600 ~/.ssh/config
```

### ❌ Workflow tidak muncul di Actions
**Cek:**
1. File `.github/workflows/deploy.yml` sudah ada di repository?
2. File sudah di-push ke branch `main`?
3. GitHub Actions enabled di repository settings?

**Enable GitHub Actions:**
- Buka: https://github.com/anaszakia/rivana/settings/actions
- Pilih: **"Allow all actions and reusable workflows"**
- Save

---

## 📊 Cara Monitoring Deploy

### Via GitHub Actions (Recommended):
1. **Buka:** https://github.com/anaszakia/rivana/actions
2. **Klik:** Workflow terakhir
3. **Lihat:** Progress real-time setiap step

### Via Server (Manual):
```bash
# SSH ke server
ssh root@103.122.67.145

# Masuk ke directory
cd /usr/share/nginx/rivana.cloud/public_html/rivana

# Cek git log
git log --oneline -5

# Cek file terakhir update
ls -lt | head -20
```

---

## ✅ Checklist Setup

- [ ] SSH key generated di server
- [ ] Private key copied
- [ ] GitHub Secret `HOST` = `103.122.67.145`
- [ ] GitHub Secret `USER` = `root`
- [ ] GitHub Secret `KEY` = (Private key lengkap)
- [ ] GitHub Actions enabled
- [ ] Test push → Cek Actions → Deploy otomatis!

---

## 🎉 Hasil Akhir

**SEBELUM Setup:**
```bash
# Di lokal
git push origin main

# Di server (manual)
ssh root@server
git pull origin main
composer install
php artisan migrate
# dll... 😓
```

**SETELAH Setup:**
```bash
# Di lokal
git push origin main

# Auto-deploy berjalan otomatis! ✅
# Tidak perlu SSH ke server lagi! 🎉
```

---

## 📝 Catatan Penting

1. **Private Key HANYA** untuk GitHub Secrets, jangan share ke orang lain!
2. **Test koneksi SSH** dulu sebelum setup GitHub Secrets
3. **Monitor di Actions** untuk memastikan deploy sukses
4. **File .env** di server tidak akan ke-overwrite (aman)
5. **Backup .env** sebelum setup untuk jaga-jaga

---

**Status:** ⏳ Waiting for GitHub Secrets setup
**Next Step:** Setup 3 GitHub Secrets (HOST, USER, KEY)
**ETA:** 5-10 menit untuk setup
