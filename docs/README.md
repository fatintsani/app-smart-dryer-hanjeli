# 📖 Indeks Dokumentasi — Smart Room Dryer Hanjeli

Selamat datang di repositori dokumentasi resmi sistem **Smart Room Dryer Hanjeli**. Seluruh dokumen disusun secara komprehensif untuk memudahkan pemahaman, instalasi, pengoperasian, pemeliharaan, serta pengembangan sistem baik di tingkat perangkat keras (*IoT Hardware*) maupun perangkat lunak (*Web Platform & Cloud*).

---

## 🗂️ Daftar Dokumen Dokumentasi

| Dokumen | Deskripsi & Cakupan | Sasaran Pembaca |
| :--- | :--- | :--- |
| [📖 Buku Panduan Lengkap (Manual Book)](./MANUAL_BOOK.md) | **Master Manual Book** terpadu dan lengkap mencakup penjelasan website, urgensi komoditas hanjeli Waluran, arsitektur sistem IoT, matriks hak akses peran (RBAC), kredensial akun bawaan, panduan lengkap setiap menu & fitur (Landing Page, Traceability QR, Dashboard, Monitoring, Mulai Batch, Kontrol Aktuator, History, Settings, Admin Devices 7 Sub-Modul), integrasi Web Bluetooth BLE, Web Serial USB, PWA, SOP pascapanen, formula Quality Scoring, serta troubleshooting. | **Semua Pengguna (Operator, Petani, Admin, Penguji Mutu)** |
| [📘 Panduan Penggunaan Sistem (User Manual)](./PANDUAN_PENGGUNAAN.md) | Panduan langkah-demi-langkah pengoperasian aplikasi web untuk Operator, Administrator, dan Petani. Mencakup alur login & Passkeys, pemantauan sensor real-time, manajemen batch pengeringan & kinetika ETA, kontrol aktuator cerdas, konfigurasi transmisi tri-mode (Wi-Fi, BLE, USB Serial), manajemen armada IoT & OTA di `/admin/devices`, verifikasi QR Code Traceability Farm-to-Table publik, serta troubleshooting operasional. | **Operator Greenhouse, Petani, Admin Lapangan** |
| [⚙️ Dokumentasi Arsitektur & Teknis Sistem](./DOKUMENTASI_SISTEM.md) | Dokumentasi teknis menyeluruh mencakup arsitektur sistem end-to-end, spesifikasi perangkat keras ESP32 & pinout sensor, protokol transmisi data tri-mode (MQTT, Laravel Reverb WebSockets, Web Bluetooth API, Web Serial API), skema database ERD, katalog lengkap endpoint REST API, algoritma prediksi ETA, sistem penilaian mutu (Quality Scoring Grade A/B/C), deteksi anomali sensor, serta panduan deployment produksi. | **Software Engineer, IoT Engineer, System Administrator** |
| [🚀 Panduan CI/CD & Deployment Otomatis VPS](./PANDUAN_CI_CD_VPS.md) | Panduan komprehensif setup CI/CD GitHub Actions, konfigurasi GitHub Secrets, persiapan VPS Ubuntu/Debian, manajemen background workers (Supervisor/Systemd untuk Reverb, MQTT Worker, dan Queue), keamanan `.env`, cron job, serta mekanisme backup dan rollback otomatis saat deployment gagal. | **DevOps, System Administrator, Developer** |
| [🎨 Standar Design System (Zero-Shadow Edition)](./DESIGN.md) | Pedoman desain antarmuka industri Zero-Shadow, palet warna agrikultur, skala tipografi Plus Jakarta Sans / Inter, hierarki border, dan aturan komponen UI responsif. | **UI/UX Designer, Frontend Engineer** |
| [📡 Panduan Koneksi Web ke Hardware IoT](./PANDUAN_KONEKSI_HARDWARE.md) | Panduan langkah-demi-langkah menghubungkan website ke perangkat keras fisik ESP32, diagram pinout & wiring sensor DHT22/Relay, source code firmware lengkap siap upload, konfigurasi MQTT Worker daemon di Laravel, panduan Web Bluetooth & Web Serial USB, serta troubleshooting lapangan. | **Teknisi Hardware, IoT Engineer, Mahasiswa/Intern, Operator** |
| [🔌 Firmware ESP32 Dual-Mode (Source Code)](./esp32_smart_dryer_dual_mode/esp32_smart_dryer_dual_mode.ino) | Source code firmware mikrokontroler ESP32 produksi dengan dukungan transmisi Wi-Fi MQTT, Web Bluetooth BLE provisioning, komunikasi kabel Serial 115200 baud, sensor DHT22, BH1750, Rain Sensor, relay 4-channel, dan LCD 20x4 I2C. | **Embedded Engineer, Teknisi Hardware** |
| [🧪 Firmware ESP32 Simulator (Source Code)](./esp32_smart_dryer_simulator/esp32_smart_dryer_simulator.ino) | Source code firmware simulator untuk pengujian lingkungan tanpa mikrokontroler fisik atau untuk unit testing telemetri. | **Tester, Pengembang Software** |

---

## 📍 Profil Singkat Proyek

- **Nama Platform**: Smart Room Dryer Hanjeli (*IoT Greenhouse Monitoring & Control System*)
- **Komoditas**: Biji Hanjeli (*Coix lacryma-jobi*) — Pangan fungsional khas Sukabumi
- **Lokasi Implementasi**: Desa Wisata Hanjeli, Jl. Pamoyan, Waluran Mandiri, Kec. Waluran, Kabupaten Sukabumi, Jawa Barat 43175, Indonesia
- **Kolaborasi Pengembangan**: Center of Excellence Smart Telecommunication, Automation & System - Research Group (**CoE STAS-RG**) & Program Pengabdian kepada Masyarakat
- **Pengembang**: [@fatintsani](https://github.com/fatintsani)

---

## 🚀 Ringkasan Menjalankan Sistem Secara Cepat

```bash
# 1. Jalankan Web Server Laravel & Vite Compiler (Concurrent)
npm run start

# 2. Jalankan WebSocket Reverb Server (Terminal Terpisah)
php artisan reverb:start

# 3. Jalankan MQTT Telemetry Worker Daemon (Terminal Terpisah)
php artisan greenhouse:mqtt-worker
```

Akses sistem melalui browser di: **`http://localhost:8000`**

