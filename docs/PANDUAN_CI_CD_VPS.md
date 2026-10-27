# 🚀 Panduan CI/CD & Deployment Otomatis VPS (GitHub Actions)
### Proyek: Smart Room Dryer Hanjeli (Laravel 12/13 + Vue 3 + Reverb + MQTT)

Dokumen ini menjelaskan alur, konfigurasi **GitHub Secrets**, persiapan server **VPS (Ubuntu/Debian)**, manajemen worker background (Reverb, MQTT, Queue), keamanan kredensial, serta prosedur **backup dan rollback otomatis**.

---

## 📑 Daftar Isi
1. [Arsitektur Pipeline CI/CD](#1-arsitektur-pipeline-cicd)
2. [Konfigurasi GitHub Secrets](#2-konfigurasi-github-secrets)
3. [Persiapan Server VPS (Step-by-Step)](#3-persiapan-server-vps-step-by-step)
4. [Konfigurasi Daemon Background (Supervisor / Systemd)](#4-konfigurasi-daemon-background-supervisor--systemd)
5. [Konfigurasi Cron Job Scheduler](#5-konfigurasi-cron-job-scheduler)
6. [Mekanisme Keamanan Data & Lingkungan (.env)](#6-mekanisme-keamanan-data--lingkungan-env)
7. [Prosedur Backup & Rollback](#7-prosedur-backup--rollback)
8. [Uji Coba & Troubleshooting](#8-uji-coba--troubleshooting)

---

## 1. Arsitektur Pipeline CI/CD

Workflow didefinisikan pada file [`.github/workflows/deploy.yml`](file:///.github/workflows/deploy.yml) dan terbagi menjadi 2 tahapan (Jobs):

```
┌────────────────────────────────────────────────────────┐
│ GitHub Actions Trigger: Push ke branch 'main'          │
└───────────────────────────┬────────────────────────────┘
                            │
                            ▼
┌────────────────────────────────────────────────────────┐
│ JOB 1: Test & Build Assets (CI - GitHub Hosted Runner) │
├────────────────────────────────────────────────────────┤
│ • Checkout repo & Setup PHP 8.3 + Node.js 20           │
│ • Install Composer & jalankan 'php artisan test'       │
│ • Install NPM & kompilasi frontend ('npm run build')   │
└───────────────────────────┬────────────────────────────┘
                            │ (Hanya lanjut jika CI SUKSES)
                            ▼
┌────────────────────────────────────────────────────────┐
│ JOB 2: Deploy to VPS (CD - via SSH)                    │
├────────────────────────────────────────────────────────┤
│ • Koneksi ke VPS via Private Key SSH aman              │
│ • Backup snapshot .env & database SQLite otomatis       │
│ • Aktifkan Maintenance Mode ('artisan down')           │
│ • 'git fetch' & 'git reset --hard origin/main'         │
│ • 'composer install --no-dev --optimize-autoloader'   │
│ • 'npm install' & 'npm run build' di VPS               │
│ • 'php artisan migrate --force'                        │
│ • 'php artisan optimize' & cache warmup                │
│ • Restart workers (Reverb, MQTT Worker, Queue)         │
│ • Matikan Maintenance Mode ('artisan up')              │
│ • ⚠️ Trap Rollback otomatis jika langkah mana pun gagal│
└────────────────────────────────────────────────────────┘
```

---

## 2. Konfigurasi GitHub Secrets

Buka repositori Anda di GitHub: **Settings > Secrets and variables > Actions > New repository secret**, lalu tambahkan variabel rahasia berikut:

| Secret Name | Contoh Nilai | Deskripsi |
| :--- | :--- | :--- |
| `SSH_HOST` | `103.xxx.xxx.xxx` atau `domain-vps.com` | IP Address publik atau domain server VPS Anda. |
| `SSH_USER` | `deployer` atau `ubuntu` atau `root` | Username SSH untuk masuk ke server. |
| `SSH_KEY` | `-----BEGIN OPENSSH PRIVATE KEY----- ...` | Konten **Private Key SSH** (bukan public key!). |
| `SSH_PORT` | `22` | Port SSH server VPS (default: 22). |
| `DEPLOY_PATH` | `/var/www/app-smart-dryer-hanjeli` | Path absolut folder instalasi proyek di VPS. |

> [!IMPORTANT]
> Jangan pernah membagikan nilai `SSH_KEY` atau meletakkannya di dalam kode repository.

---

## 3. Persiapan Server VPS (Step-by-Step)

### A. Buat User Khusus Deployment (Disarankan)
Demi keamanan, sebaiknya gunakan user non-root (misal: `deployer`):
```bash
# Buat user baru
sudo adduser deployer

# Tambahkan ke grup www-data dan sudo
sudo usermod -aG sudo,www-data deployer
```

### B. Setup SSH Key Authentication
Di komputer lokal Anda atau di server, buat pasangan kunci SSH khusus:
```bash
# Generate key ed25519
ssh-keygen -t ed25519 -C "github-actions-deploy" -f ~/.ssh/hanjeli_deploy_key
```
1. Masukkan isi `hanjeli_deploy_key.pub` ke file `~/.ssh/authorized_keys` milik user di VPS.
2. Masukkan isi `hanjeli_deploy_key` (private key) ke **GitHub Secrets (`SSH_KEY`)**.

Beri izin restart layanan (Nginx / Supervisor / PHP-FPM) tanpa prompt password:
```bash
sudo visudo
```
Tambahkan baris berikut di baris paling bawah:
```sudoers
deployer ALL=(ALL) NOPASSWD: /usr/bin/supervisorctl, /usr/bin/systemctl reload php8.3-fpm, /usr/bin/systemctl restart hanjeli-reverb, /usr/bin/systemctl restart hanjeli-mqtt, /usr/bin/systemctl restart nginx
```

### C. Clone Repository Pertama Kali di VPS
```bash
# Buat direktori aplikasi
sudo mkdir -p /var/www/app-smart-dryer-hanjeli
sudo chown -R deployer:www-data /var/www/app-smart-dryer-hanjeli

# Clone repository
cd /var/www
git clone https://github.com/fatintsani/app-smart-dryer-hanjeli.git /var/www/app-smart-dryer-hanjeli
cd /var/www/app-smart-dryer-hanjeli

# Setup awal .env
cp .env.example .env
nano .env   # Sesuaikan APP_ENV=production, APP_DEBUG=false, DB, MQTT, REVERB, dsb.

# Install dependensi awal
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Buat database & link storage
touch database/database.sqlite  # jika menggunakan sqlite
php artisan key:generate
php artisan migrate --seed --force
php artisan storage:link

# Atur permission
chmod -R 775 storage bootstrap/cache database
chown -R deployer:www-data storage bootstrap/cache database
```

---

## 4. Konfigurasi Daemon Background (Supervisor / Systemd)

Proyek ini memiliki 3 service background utama:
1. **Laravel Reverb (WebSockets)** (`php artisan reverb:start`)
2. **MQTT Telemetry Worker** (`php artisan greenhouse:mqtt-worker`)
3. **Queue Worker** (`php artisan queue:work`)

### Rekomendasi: Menggunakan Supervisor

Pasang Supervisor:
```bash
sudo apt update && sudo apt install supervisor -y
```

Buat konfigurasi file `/etc/supervisor/conf.d/hanjeli.conf`:
```ini
# 1. Laravel Queue Worker
[program:hanjeli-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /var/www/app-smart-dryer-hanjeli/artisan queue:work --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=deployer
numprocs=1
redirect_stderr=true
stdout_logfile=/var/www/app-smart-dryer-hanjeli/storage/logs/worker.log
stopwaitsecs=3600

# 2. Laravel Reverb WebSocket Server
[program:hanjeli-reverb]
command=php /var/www/app-smart-dryer-hanjeli/artisan reverb:start --host=0.0.0.0 --port=8080
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=deployer
redirect_stderr=true
stdout_logfile=/var/www/app-smart-dryer-hanjeli/storage/logs/reverb.log

# 3. MQTT ESP32 Telemetry Ingestion Worker
[program:hanjeli-mqtt]
command=php /var/www/app-smart-dryer-hanjeli/artisan greenhouse:mqtt-worker
autostart=true
autorestart=true
stopasgroup=true
killasgroup=true
user=deployer
redirect_stderr=true
stdout_logfile=/var/www/app-smart-dryer-hanjeli/storage/logs/mqtt.log
```

Aktifkan konfigurasi Supervisor:
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start all
sudo supervisorctl status
```

---

## 5. Konfigurasi Cron Job Scheduler

Sistem ini menjalankan downsampling data sensor setiap malam pukul 02:00 WIB via Laravel Scheduler. Tambahkan cron job di VPS:

```bash
crontab -e -u deployer
```

Tambahkan baris berikut:
```cron
* * * * * cd /var/www/app-smart-dryer-hanjeli && php artisan schedule:run >> /dev/null 2>&1
```

---

## 6. Mekanisme Keamanan Data & Lingkungan (.env)

Untuk menjaga data produksi dan kredensial tetap aman:
1. **File `.env` tidak pernah ditimpa**:
   - File `.env` berada di `.gitignore` sehingga tidak pernah tersimpan di GitHub.
   - Script deployment otomatis mem-backup `.env` ke folder terpisah sebelum melakukan `git reset --hard`.
2. **Database Production Aman**:
   - Jika menggunakan SQLite (`database.sqlite`), file tersebut dibackup sebelum migrasi dijalankan.
   - Migrasi dijalankan dengan opsi `--force` tanpa me-reset data (`artisan migrate --force`).
3. **Folder Upload & Media Tetap Utuh**:
   - Direktori `storage/app/public` tidak disentuh oleh git update karena berada di luar git tracking dan dihubungkan via symlink (`php artisan storage:link`).

---

## 7. Prosedur Backup & Rollback

### A. Backup Otomatis
Setiap kali deployment berjalan, sistem membuat folder backup:
```
/var/www/backups_hanjeli/backup_YYYYMMDD_HHMMSS/
├── .env
└── database.sqlite (jika ada)
```
*Script otomatis merotasi dan mempertahankan 5 snapshot backup terakhir untuk menghemat kapasitas disk.*

### B. Rollback Otomatis
Jika terjadi kegagalan pada tahap apa pun di server (misalnya: gagal migrasi database, error kompilasi Vite, atau dependensi Composer bermasalah):
1. **Trap `rollback()`** langsung terpicu seketika.
2. Git me-reset kode ke commit stabil sebelumnya (`git reset --hard $PREVIOUS_COMMIT`).
3. File `.env` dan `database.sqlite` dipulihkan dari folder backup snapshot.
4. Cache dibersihkan (`php artisan optimize:clear`).
5. Aplikasi langsung dikeluarkan dari maintenance mode (`php artisan up`).
6. Worker background di-restart ulang.
7. Job GitHub Actions ditandai **FAILED** beserta laporan error lengkap di log konsol.

### C. Rollback Manual (Jika Diperlukan)
Jika ingin mengembalikan aplikasi secara manual ke versi sebelumnya:
```bash
cd /var/www/app-smart-dryer-hanjeli

# 1. Aktifkan maintenance mode
php artisan down

# 2. Kembalikan kode ke commit sebelumnya
git log -n 5 --oneline
git reset --hard <COMMIT_HASH_SEBELUMNYA>

# 3. Rebuild dependensi & cache
composer install --no-dev --optimize-autoloader
npm run build
php artisan optimize:clear
php artisan optimize

# 4. Aktifkan kembali
php artisan up
sudo supervisorctl restart all
```

---

## 8. Uji Coba & Troubleshooting

### Memverifikasi Deployment
1. Lakukan perubahan kecil (misal update dokumentasi atau komentar) pada branch `main`.
2. Push ke GitHub: `git push origin main`.
3. Buka tab **Actions** di GitHub repository Anda:
   - Pantau progress job `Test & Build Assets`.
   - Pantau eksekusi job `Deploy to Production VPS`.
4. Periksa log detail di setiap step untuk memastikan semua tahap bertanda centang hijau ✅.

### Masalah Umum & Solusi:
- **`Permission denied (publickey)`**: Periksa apakah private key di GitHub Secret `SSH_KEY` cocok dengan `authorized_keys` di VPS dan memiliki newline lengkap di akhir key.
- **`sudo: a password is required`**: Pastikan konfigurasi `sudoers` untuk `supervisorctl` atau `systemctl` sudah ditambahkan dengan opsi `NOPASSWD`.
- **`Vite build out of memory` di VPS spesifikasi RAM 1GB**:
  Tambahkan swap file 2GB di VPS:
  ```bash
  sudo fallocate -l 2G /swapfile
  sudo chmod 600 /swapfile
  sudo mkswap /swapfile
  sudo swapon /swapfile
  echo '/swapfile none swap sw 0 0' | sudo tee -a /etc/fstab
  ```
