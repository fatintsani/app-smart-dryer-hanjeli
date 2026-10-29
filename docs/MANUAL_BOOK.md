# 📖 BUKU PANDUAN PENGGUNAAN & TEKNIS SISTEM (MANUAL BOOK)
## Smart Room Dryer Hanjeli — IoT Greenhouse Monitoring & Control System

> **Identitas Dokumen**: Master User Manual & Technical Reference Guide  
> **Versi Rilis**: v1.0.0 (Production Stable)  
> **Tanggal Publikasi**: Oktober 2026  
> **Lokasi Implementasi**: Fasilitas Greenhouse Cerdas Desa Wisata Hanjeli, Waluran Mandiri, Kecamatan Waluran, Kabupaten Sukabumi, Jawa Barat  
> **Pengembang & Peneliti**: Center of Excellence Smart Telecommunication, Automation & System - Research Group (**CoE STAS-RG**)  
> **Domain Sistem**: [https://hanjeli-dryer.stas-rg.com](https://hanjeli-dryer.stas-rg.com)

---

## 📑 DAFTAR ISI

1. [INFORMASI UMUM & IDENTITAS SISTEM](#1-informasi-umum--identitas-sistem)
2. [BAB 1: PENJELASAN WEBSITE & ARSITEKTUR SISTEM](#bab-1-penjelasan-website--arsitektur-sistem)
   - 1.1. [Latar Belakang & Urgensi Solusi](#11-latar-belakang--urgensi-solusi)
   - 1.2. [Tujuan & Nilai Strategis Sistem](#12-tujuan--nilai-strategis-sistem)
   - 1.3. [Arsitektur Sistem Menyeluruh (End-to-End)](#13-arsitektur-sistem-menyeluruh-end-to-end)
   - 1.4. [Tumpukan Teknologi (Tech Stack)](#14-tumpukan-teknologi-tech-stack)
   - 1.5. [Filosofi Desain UI/UX (Zero-Shadow Industrial Aesthetic)](#15-filosofi-desain-uiux-zero-shadow-industrial-aesthetic)
3. [BAB 2: MANAJEMEN PERAN (RBAC) & KREDENSIAL AKUN](#bab-2-manajemen-peran-rbac--kredensial-akun)
   - 2.1. [Matriks Hak Akses Pengguna (Role-Based Access Control)](#21-matriks-hak-akses-pengguna-role-based-access-control)
   - 2.2. [Akun Bawaan Sistem (Default Demo Credentials)](#22-akun-bawaan-sistem-default-demo-credentials)
   - 2.3. [Prosedur Pengamanan Akun Pasca-Deployment](#23-prosedur-pengamanan-akun-pasca-deployment)
4. [BAB 3: ALUR AUTENTIKASI & KEAMANAN AKUN](#bab-3-alur-autentikasi--keamanan-akun)
   - 3.1. [Masuk Menggunakan Email & Kata Sandi](#31-masuk-menggunakan-email--kata-sandi)
   - 3.2. [Masuk Cepat Google OAuth 2.0](#32-masuk-cepat-google-oauth-20)
   - 3.3. [Masuk Biometrik & Passkeys (WebAuthn / FIDO2)](#33-masuk-biometrik--passkeys-webauthn--fido2)
   - 3.4. [Pemulihan Kata Sandi dengan Kode OTP Email (6 Digit)](#34-pemulihan-kata-sandi-dengan-kode-otp-email-6-digit)
   - 3.5. [Manajemen Sesi Aktif & Keamanan Token](#35-manajemen-sesi-aktif--keamanan-token)
5. [BAB 4: PANDUAN HALAMAN PUBLIK & TRACEABILITY MUTU](#bab-4-panduan-halaman-publik--traceability-mutu)
   - 4.1. [Landing Page Utama (`/`)](#41-landing-page-utama-)
   - 4.2. [Portal Sertifikasi & Verifikasi Mutu Farm-to-Table (`/verify`)](#42-portal-sertifikasi--verifikasi-mutu-farm-to-table-verify)
6. [BAB 5: PANDUAN MODUL OPERATOR GREENHOUSE](#bab-5-panduan-modul-operator-greenhouse)
   - 5.1. [Dashboard Utama Operator (`/dashboard`)](#51-dashboard-utama-operator-dashboard)
   - 5.2. [Live Monitoring Sensor Lingkungan (`/monitoring`)](#52-live-monitoring-sensor-lingkungan-monitoring)
   - 5.3. [Memulai Batch Pengeringan Baru (`/drying/new`)](#53-memulai-batch-pengeringan-baru-dryingnew)
   - 5.4. [Pemantauan Batch Aktif & Predictive ETA (`/drying/active`)](#54-pemantauan-batch-aktif--predictive-eta-dryingactive)
   - 5.5. [Panel Kendali Aktuator Cerdas (Kipas & Pemanas PTC)](#55-panel-kendali-aktuator-cerdas-kipas--pemanas-ptc)
   - 5.6. [Riwayat Pengeringan & Evaluasi Kualitas (`/history`)](#56-riwayat-pengeringan--evaluasi-kualitas-history)
   - 5.7. [Ekspor Laporan (PDF Resmi, Excel, CSV)](#57-ekspor-laporan-pdf-resmi-excel-csv)
   - 5.8. [Cetak Label Kemasan & Sertifikat QR Code](#58-cetak-label-kemasan--sertifikat-qr-code)
   - 5.9. [Panduan Hardware & SOP Pascapanen (`/guide`)](#59-panduan-hardware--sop-pascapanen-guide)
   - 5.10. [Pengaturan Sistem & Saluran Transmisi (`/settings`)](#510-pengaturan-sistem--saluran-transmisi-settings)
7. [BAB 6: PANDUAN MODUL ADMINISTRATOR SISTEM](#bab-6-panduan-modul-administrator-sistem)
   - 6.1. [Overview Administrator & Health Matrix (`/admin`)](#61-overview-administrator--health-matrix-admin)
   - 6.2. [Manajemen Armada Perangkat IoT 7 Sub-Modul (`/admin/devices`)](#62-manajemen-armada-perangkat-iot-7-sub-modul-admindevices)
   - 6.3. [Manajemen Pengguna & Pengaturan Peran (`/admin/users`)](#63-manajemen-pengguna--pengaturan-peran-adminusers)
   - 6.4. [Pengaturan Master Parameter Sistem (`/admin/parameters`)](#64-pengaturan-master-parameter-sistem-adminparameters)
   - 6.5. [Log Audit & Agregasi Data Telemetri (`/admin/logs`)](#65-log-audit--agregasi-data-telemetri-adminlogs)
8. [BAB 7: KONEKTIVITAS TINGKAT LANJUT & FITUR INOVASI](#bab-7-konektivitas-tingkat-lanjut--fitur-inovasi)
   - 7.1. [Web Bluetooth Low Energy (BLE) Provisioning](#71-web-bluetooth-low-energy-ble-provisioning)
   - 7.2. [Komunikasi Kabel Langsung via Web Serial API (USB 115200 baud)](#72-komunikasi-kabel-langsung-via-web-serial-api-usb-115200-baud)
   - 7.3. [Progressive Web App (PWA) — Instalasi Mobile & Offline Caching](#73-progressive-web-app-pwa--instalasi-mobile--offline-caching)
   - 7.4. [Asisten Cerdas AI Copilot & Agronomy Assistant](#74-asisten-cerdas-ai-copilot--agronomy-assistant)
9. [BAB 8: STANDAR OPERASIONAL PROSEDUR (SOP) & FORMULA MUTU](#bab-8-standar-operasional-prosedur-sop--formula-mutu)
   - 8.1. [Alur Operasional Pascapanen Biji Hanjeli](#81-alur-operasional-pascapanen-biji-hanjeli)
   - 8.2. [Checklist Siklus Harian Operator](#82-checklist-siklus-harian-operator)
   - 8.3. [Formula Matematis Penilaian Mutu (Quality Scoring)](#83-formula-matematis-penilaian-mutu-quality-scoring)
   - 8.4. [Prosedur Tanggap Darurat & Situasi Khusus](#84-prosedur-tanggap-darurat--situasi-khusus)
10. [BAB 9: PANDUAN TROUBLESHOOTING, FAQ & BANTUAN TEKNIS](#bab-9-panduan-troubleshooting-faq--bantuan-teknis)
    - 9.1. [Matriks Diagnosa Masalah & Solusi Teknis](#91-matriks-diagnosa-masalah--solusi-teknis)
    - 9.2. [Pertanyaan yang Sering Diajukan (FAQ)](#92-pertanyaan-yang-sering-diajukan-faq)
    - 9.3. [Kontak Dukungan & Saluran Kolaborasi](#93-kontak-dukungan--saluran-kolaborasi)

---

## 1. INFORMASI UMUM & IDENTITAS SISTEM

Dokumen **Manual Book** ini disusun sebagai pedoman operasional terpadu dan referensi teknis komprehensif bagi seluruh pemangku kepentingan aplikasi **Smart Room Dryer Hanjeli**. Dokumen mencakup tata kelola operasional lapangan bagi Operator Greenhouse dan Kelompok Tani, tata kelola infrastruktur bagi Administrator Sistem, serta panduan verifikasi bagi konsumen dan penguji mutu pangan.

```
Nama Produk     : Smart Room Dryer Hanjeli
Kategori        : Precision Agriculture & IoT Microclimate Control System
Platform        : Progressive Web Application (PWA) / Single Page Application (SPA)
Arsitektur      : Laravel 12 Backend + Vue.js 3.5 Frontend + ESP32 Hardware Firmware
Mitra Lapangan  : Kelompok Tani & Pengelola Desa Wisata Hanjeli, Waluran Mandiri, Sukabumi
Pusat Riset     : Center of Excellence Smart Telecommunication, Automation & System (CoE STAS-RG)
Hak Cipta       : © 2026 CoE STAS-RG & Desa Wisata Hanjeli. All Rights Reserved.
```

---

## BAB 1: PENJELASAN WEBSITE & ARSITEKTUR SISTEM

### 1.1. Latar Belakang & Urgensi Solusi

Desa Wisata Hanjeli yang berlokasi di Waluran Mandiri, Kabupaten Sukabumi, berada di dalam kawasan UNESCO Global Geopark Ciletuh-Palabuhanratu. Komoditas unggulan desa ini adalah tanaman **Hanjeli** (*Coix lacryma-jobi* L.), tanaman pangan fungsional berkhasiat tinggi yang kaya serat, bebas gluten, dan memiliki indeks glikemik rendah.

Dalam proses pascapanen, biji hanjeli memiliki tantangan spesifik:
1. **Struktur Kulit Luar Keras**: Gabah hanjeli memiliki cangkang keras berbobot tebal yang menahan kelembaban internal, sehingga proses penurunan kadar air dari panen ($\approx 24\% - 28\%$) ke batas simpan aman ($\le 12\% - 14\%$) memerlukan sirkulasi udara stabil dan pemanasan terkontrol.
2. **Ketergantungan Penjemuran Konvensional (*Sun Drying*)**: Metode tradisional penjemuran di atas terpal di pinggir jalan sangat rentan terhadap hujan mendadak, kontaminasi kotoran debu, serangan serangga/unggas, serta kelembaban tinggi malam hari yang memicu pertumbuhan cendawan penghasil mikotoksin (*Aflatoksin*).
3. **Fluktuasi Kualitas Gabah**: Panas matahari yang tidak terkontrol atau terlalu menyengat ($> 55^\circ\text{C}$) menyebabkan biji retak dan merusak kandungan nutrisi mikro serta daya berkecambah biji jika dijadikan benih.

**Smart Room Dryer Hanjeli** hadir sebagai solusi berbasis teknologi pertanian presisi (*precision agriculture*). Melalui fasilitas *greenhouse* tertutup beratap polikarbonat yang dilengkapi sistem sensor mikroiklim, aktuator sirkulasi udara, pemanas bantu elektrik keramik PTC, serta integrasi digital berbasis web real-time, pengeringan dapat berlangsung higienis, terukur, hemat energi, dan tidak bergantung pada cuaca luar.

---

### 1.2. Tujuan & Nilai Strategis Sistem

- **Presisi Mutu Gabah**: Mempertahankan kestabilan suhu ruangan pada rentang optimal $38^\circ\text{C} - 50^\circ\text{C}$ dan kelembaban relatif $\le 60\%$, menghasilkan beras hanjeli utuh (*whole grain*) berkadar air seragam $\le 12\%$.
- **Efisiensi Waktu & Energi**: Mempercepat durasi pengeringan dari 5-7 hari konvensional menjadi hanya 24-36 jam dengan memanfaatkan efek rumah kaca surya di siang hari dan pemanas PTC cerdas di malam hari / cuaca hujan.
- **Keterlacakan Pangan (*Farm-to-Table Traceability*)**: Setiap karung dan kemasan hanjeli memiliki QR Code unik yang menghubungkan pembeli langsung ke portal verifikasi mutu untuk melihat riwayat kurva suhu pengeringan dan sertifikat grade mutu.
- **Kemudahan Monitoring Lapangan**: Operator dan petani dapat mengawasi suhu dan progres kadar air kapan saja dari ponsel pintar tanpa perlu berada terus-menerus di dalam ruang pengering.

---

### 1.3. Arsitektur Sistem Menyeluruh (End-to-End)

Sistem Smart Room Dryer Hanjeli dibangun di atas arsitektur 4 lapis (*4-tier architecture*):

```
┌─────────────────────────────────────────────────────────────────────────────┐
│ 1. LAYER FISIK & SENSOR (GREENHOUSE WALURAN)                                │
│   • DHT22 Internal (Pin 25)        • BH1750 Surya Lux (I2C Pin 21/22)       │
│   • DHT22 Eksternal (Pin 16)       • Sensor Hujan Analog ADC (Pin 34)       │
│   • Load Cell HX711 (Massa Rak)    • LCD 20x4 I2C (Status Lokal Pin 21/22)  │
│   • 4-Ch Relay (Pemanas PTC 1 & 2 [Pin 26/27], Exhaust [Pin 14], Sirkulasi) │
└──────────────────────────────────────┬──────────────────────────────────────┘
                                       │ Protokol: WiFi MQTT / HTTP / BLE / Serial
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ 2. LAYER INGESTION & JARINGAN                                               │
│   • Broker MQTT (HiveMQ / Eclipse Mosquitto - Port 1883)                    │
│   • Topic: hanjeli/greenhouse/telemetry                                     │
│   • Otentikasi: X-Device-Token & HMAC SHA-256 Signatures                    │
└──────────────────────────────────────┬──────────────────────────────────────┘
                                       │ Worker: php artisan greenhouse:mqtt-worker
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ 3. LAYER BACKEND & BUSINESS LOGIC (LARAVEL 12/13)                           │
│   • Ingestion Service & Data Validator (TelemetryController / MQTT Worker)  │
│   • Real-Time WebSocket Daemon (Laravel Reverb - Port 8080)                 │
│   • Evaluator Otomasi Histeresis, Anomaly Detection & AI ETA Engine         │
│   • Quality Scoring Engine (Grade A/B/C) & PDF Certificate Generator        │
│   • Notification Gateway (WhatsApp Fonnte/Wablas, Telegram Bot, SMTP Email)│
└──────────────────────────────────────┬──────────────────────────────────────┘
                                       │ Channel: Reverb WebSockets & REST JSON API
                                       ▼
┌─────────────────────────────────────────────────────────────────────────────┐
│ 4. LAYER PRESENTASI & PENGGUNA (VUE.JS 3.5 SPA / PWA)                       │
│   • Operator Dashboard, Live Multi-Axis Charts, & Actuator Controls         │
│   • Admin Devices Center (OTA Firmware, Token, Relay Test, Arduino Source)  │
│   • Public Traceability Verification Portal (/verify/:batchCode)            │
│   • AI Copilot Drawer & Web Bluetooth (BLE) / Web Serial Direct Hub         │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

### 1.4. Tumpukan Teknologi (Tech Stack)

| Lapisan Sistem | Teknologi / Pustaka | Peran & Deskripsi |
| :--- | :--- | :--- |
| **Frontend Framework** | **Vue.js 3.5** (SFC `<script setup>`) | Antarmuka pengguna reaktif, modular, dan cepat |
| **State Management** | **Pinia Store** | Pengelolaan state terpusat (telemetri, auth, preferensi) |
| **Routing** | **Vue Router 4** | Navigasi halaman SPA berbasis Route Guard & RBAC |
| **Grafik & Visualisasi** | **Chart.js** & Native SVG Gauges | Visualisasi grafik multi-axis mikroiklim dan dial meter |
| **Realtime Client** | **Laravel Echo** + Pusher JS | Penerima event WebSocket instan sub-detik |
| **Protokol Fisik Browser** | **Web Bluetooth API & Web Serial API** | Konfigurasi nirkabel BLE dan streaming USB langsung |
| **Backend Framework** | **Laravel 12 / 13** | Kerangka kerja PHP modern untuk API dan orkestrasi |
| **WebSocket Engine** | **Laravel Reverb** (Port 8080) | Server WebSocket mandiri berperforma tinggi berlatensi < 100ms |
| **IoT Ingestion Daemon** | **php-mqtt/client** | Worker latar belakang konsumsi topik MQTT non-stop |
| **Basis Data** | **SQLite / MySQL 8.0** | Penyimpanan batch, telemetri per menit, parameter, dan user |
| **Otentikasi Kriptografis**| **WebAuthn (Passkeys)** & OAuth 2.0 | Login biometrik sidik jari / wajah & Google sign-in |
| **Mikrokontroler** | **ESP32 Dual-Core Tensilica 240MHz** | Pemrosesan sensor lokal, LCD, kontrol relay, dan transmisi data |

---

### 1.5. Filosofi Desain UI/UX (Zero-Shadow Industrial Aesthetic)

Antarmuka *Smart Room Dryer Hanjeli* dibangun dengan filosofi **Zero-Shadow Industrial Aesthetic**:
- **Kontras Tinggi & Garis Tegas (Border 1px)**: Menghindari bayangan blur lembut (*soft shadows*) yang sulit dibaca di luar ruangan. Setiap kartu informasi dibatasi garis presisi 1px dengan kontras warna terkalibrasi.
- **Kenyamanan Lapangan**:
  - **Tema Terang (Light Mode)**: Menggunakan warna dasar abu-abu kehijauan lembut (`#F4F7F5`) yang meredam pantulan silau cahaya matahari di area perkebunan.
  - **Tema Gelap (Dark Mode)**: Menggunakan obsidian hijau tua (`#0B1911`) dengan tipografi kontras tinggi, menghemat baterai layar OLED ponsel operator serta nyaman dipantau di malam hari.
- **Dwibahasa (Bilingual ID/EN)**: Seluruh label tombol, metrik sensor, panduan, dan pesan peringatan dapat dialihkan secara instan antara Bahasa Indonesia (🇮🇩) dan Bahasa Inggris (🇬🇧).
- **Responsif Multi-Perangkat**: Tata letak desktop menggunakan sidebar kokoh, sedangkan tata letak mobile smartphone menggunakan header dinamis dan **Bottom Navigation Bar** 5 tombol yang ergonomis bagi jempol operator.

---

## BAB 2: MANAJEMEN PERAN (RBAC) & KREDENSIAL AKUN

### 2.1. Matriks Hak Akses Pengguna (Role-Based Access Control)

Sistem menerapkan pembatasan hak akses berbasis peran (*Role-Based Access Control*) untuk menjamin integritas data operasional dan keamanan perangkat keras di greenhouse:

| Modul / Fitur Website | Tamu / Publik | Peran `PETANI` | Peran `OPERATOR` | Peran `ADMIN` |
| :--- | :---: | :---: | :---: | :---: |
| **Landing Page Publik (`/`)** | ✅ Ya | ✅ Ya | ✅ Ya | ✅ Ya |
| **Portal Traceability & Verifikasi Mutu (`/verify`)** | ✅ Ya | ✅ Ya | ✅ Ya | ✅ Ya |
| **Halaman Masuk (`/login`) & Lupa Sandi (`/forgot-password`)** | ✅ Ya | ✅ Ya | ✅ Ya | ✅ Ya |
| **Dashboard Utama Operator (`/dashboard`)** | ❌ Tidak | ✅ Ya (Lihat) | ✅ Ya (Penuh) | ✅ Ya (Penuh) |
| **Live Monitoring Sensor Lingkungan (`/monitoring`)** | ❌ Tidak | ✅ Ya (Lihat) | ✅ Ya (Penuh) | ✅ Ya (Penuh) |
| **Memulai Batch Pengeringan Baru (`/drying/new`)** | ❌ Tidak | ❌ Tidak | ✅ Ya | ✅ Ya |
| **Pengeringan Aktif & Kendali Aktuator (`/drying/active`)** | ❌ Tidak | ✅ Ya (Lihat) | ✅ Ya (Kontrol) | ✅ Ya (Kontrol) |
| **Riwayat Pengeringan (`/history`) & Ekspor PDF/Excel** | ❌ Tidak | ✅ Ya (Lihat) | ✅ Ya | ✅ Ya |
| **Cetak Label Kemasan & Sertifikat QR Code** | ❌ Tidak | ❌ Tidak | ✅ Ya | ✅ Ya |
| **Panduan Hardware & SOP (`/guide`)** | ❌ Tidak | ✅ Ya | ✅ Ya | ✅ Ya |
| **Pengaturan Sistem & Saluran Transmisi (`/settings`)** | ❌ Tidak | ❌ Tidak | ✅ Ya | ✅ Ya |
| **Overview Administrator & Health Matrix (`/admin`)** | ❌ Tidak | ❌ Tidak | ❌ Tidak | ✅ Ya |
| **Manajemen Armada IoT 7 Sub-Modul (`/admin/devices`)** | ❌ Tidak | ❌ Tidak | ❌ Tidak | ✅ Ya |
| **Manajemen Pengguna & Hak Akses (`/admin/users`)** | ❌ Tidak | ❌ Tidak | ❌ Tidak | ✅ Ya |
| **Pengaturan Master Parameter Sistem (`/admin/parameters`)** | ❌ Tidak | ❌ Tidak | ❌ Tidak | ✅ Ya |
| **Log Audit, Anomali & Downsample Data (`/admin/logs`)** | ❌ Tidak | ❌ Tidak | ❌ Tidak | ✅ Ya |

---

### 2.2. Akun Bawaan Sistem (Default Demo Credentials)

Untuk keperluan instalasi baru, demonstrasi, dan pengujian lapangan, sistem menyediakan 3 akun default yang telah terdaftar di database:

| No | Peran (*Role*) | Nama Pengguna | Alamat Email | Kata Sandi Default | Nomor WhatsApp | Rute Pengalihan Masuk |
| :-: | :--- | :--- | :--- | :--- | :--- | :--- |
| 1 | **`ADMIN`** | Admin Smart Dryer | `admin@hanjeli.id` | `admin123` | `081234567890` | `/admin` |
| 2 | **`OPERATOR`** | Operator Greenhouse | `operator@hanjeli.id` | `operator123` | `089876543210` | `/dashboard` |
| 3 | **`PETANI`** *(Demo)* | Petani Hanjeli Waluran | `petani@hanjeli.id` | `petani123` | `081345678901` | `/dashboard` |

---

### 2.3. Prosedur Pengamanan Akun Pasca-Deployment

> [!WARNING]
> Demi keamanan fasilitas fisik greenhouse dan kerahasiaan konfigurasi token IoT, segera lakukan langkah mitigasi berikut setelah server aplikasi terpasang di jaringan publik atau VPS:

1. **Perbarui Kata Sandi Default**: Masuk menggunakan akun `admin@hanjeli.id`, buka menu **Pengaturan** (`/settings` $\rightarrow$ tab *Keamanan & Passkey*), lalu ganti kata sandi dengan kombinasi minimal 10 karakter yang mengandung huruf besar, huruf kecil, angka, dan simbol.
2. **Daftarkan Kunci Biometrik (Passkey)**: Tambahkan perangkat laptop/smartphone kerja Anda ke daftar Passkey terpercaya agar terhindar dari bahaya pembajakan kata sandi (*credential sniffing*).
3. **Perbarui Token Perangkat IoT (`X-Device-Token`)**: Buka menu `/admin/devices` pada sub-modul *Otentikasi Token*, lalu lakukan regenerasi token API untuk memastikan mikrokontroler lama yang tidak terotorisasi tidak dapat menyuntikkan data palsu.

---

## BAB 3: ALUR AUTENTIKASI & KEAMANAN AKUN

### 3.1. Masuk Menggunakan Email & Kata Sandi

1. Buka peramban di komputer atau ponsel pintar Anda, lalu akses alamat website: `https://hanjeli-dryer.stas-rg.com` (atau `http://localhost:8000` pada server lokal).
2. Klik tombol **Dashboard** atau **Masuk** pada navigation bar di halaman landing, atau langsung tuju rute `/login`.
3. Masukkan **Alamat Email** dan **Kata Sandi** akun Anda.
4. Beri tanda centang pada opsi **Ingat Saya (Remember Me)** jika Anda menggunakan perangkat pribadi untuk menjaga sesi tetap aktif selama 30 hari.
5. Klik tombol hijau **Masuk ke Sistem**.
6. Sistem akan memvalidasi data dan mengarahkan Anda ke rute yang sesuai:
   - Pengguna dengan peran `ADMIN` dialihkan ke `/admin`.
   - Pengguna dengan peran `OPERATOR` dan `PETANI` dialihkan ke `/dashboard`.

---

### 3.2. Masuk Cepat Google OAuth 2.0

Sistem mendukung integrasi Single Sign-On (SSO) Google untuk kemudahan akses:
1. Pada halaman `/login`, klik tombol **Masuk dengan Google** (ikon logo Google).
2. Dialog otorisasi akun Google akan terbuka. Pilih akun Google resmi Anda.
3. Google akan mengautentikasi identitas Anda secara aman. Jika email Google terdaftar pada database sistem, Anda akan langsung dialihkan ke dashboard tanpa perlu mengetikkan kata sandi secara manual.

---

### 3.3. Masuk Biometrik & Passkeys (WebAuthn / FIDO2)

Sistem dilengkapi teknologi kriptografi modern WebAuthn (*Passwordless Biometric Login*):
1. **Pendaftaran Awal**:
   - Masuk terlebih dahulu menggunakan email dan kata sandi.
   - Buka menu **Pengaturan** (`/settings`), pilih tab **Keamanan & Passkey**.
   - Klik tombol **Daftarkan Kunci Biometrik / Passkey Baru**.
   - Berikan izin pada peramban untuk memindai sidik jari (*TouchID / Fingerprint*), wajah (*FaceID / Windows Hello*), atau menyentuh Kunci Fisik USB FIDO2 (YubiKey).
   - Beri nama perangkat (misalnya: *"MacBook Operator 01"* atau *"Samsung S24 Lapangan"*).
2. **Cara Masuk dengan Passkey**:
   - Di halaman `/login`, klik tombol bertuliskan **Masuk Cepat dengan Passkey / Biometrik**.
   - Sentuh sensor sidik jari perangkat Anda atau hadapkan wajah ke kamera.
   - Sesi terotentikasi dalam hitungan kurang dari 1 detik tanpa transmisi kata sandi di jaringan.

---

### 3.4. Pemulihan Kata Sandi dengan Kode OTP Email (6 Digit)

Jika pengguna melupakan kata sandi:
1. Pada halaman `/login`, klik tautan **Lupa Kata Sandi?** (mengarah ke `/forgot-password`).
2. Masukkan alamat email terdaftar akun Anda, lalu klik **Kirim Kode Verifikasi**.
3. Sistem akan mengirimkan pesan email resmi berisikan **6 Digit Kode OTP** ke kotak masuk Anda.
4. Buka email Anda, salin kode OTP tersebut (kode berlaku selama 10 menit demi alasan keamanan).
5. Masukkan 6 digit kode OTP pada kotak input verifikasi di layar.
6. Masukkan **Kata Sandi Baru** Anda beserta konfirmasi kata sandi baru.
7. Klik tombol **Simpan Kata Sandi Baru**. Anda kini dapat langsung masuk menggunakan kredensial yang baru diperbarui.

---

### 3.5. Manajemen Sesi Aktif & Keamanan Token

Pengguna dapat memantau dan mengaudit sesi yang sedang login:
- Buka menu **Pengaturan** (`/settings` $\rightarrow$ tab *Keamanan & Passkey*).
- Pada bagian **Sesi Perangkat Aktif**, sistem menampilkan daftar perangkat yang sedang terhubung, mencakup: *Jenis Peramban*, *Sistem Operasi*, *Alamat IP*, dan *Waktu Terakhir Aktif*.
- Jika terdapat perangkat asing yang mencurigakan, klik tombol merah **Cabut Akses Sesi Ini** untuk memutuskan koneksi perangkat tersebut secara seketika.

---

## BAB 4: PANDUAN HALAMAN PUBLIK & TRACEABILITY MUTU

### 4.1. Landing Page Utama (`/`)

Landing Page dirancang untuk memperkenalkan fasilitas teknologi pengeringan pintar kepada tamu, konsumen pangan sehat, dinas pertanian, dan wisatawan Desa Wisata Hanjeli:

```
┌─────────────────────────────────────────────────────────────────────────────┐
│ NAVBAR: [Logo Hanjeli] Tentang | Hardware | Fitur | Panduan | Verifikasi  [Masuk] │
├─────────────────────────────────────────────────────────────────────────────┤
│ HERO SECTION:                                                               │
│   "Solusi Cerdas Pengeringan Biji Hanjeli Berbasis IoT"                     │
│   • Subheading: Optimalkan mutu panen dengan kendali otomatis suhu & RH.    │
│   • Tombol: [Masuk ke Sistem Monitoring ->]   [Pelajari Hardware & Fitur]   │
│   • Badges: Target 40°C Suhu Aman | Histeresis Pintar | Sensor DHT22 Presisi│
│   • Ilustrasi 3D: Mesin Smart Greenhouse beratap surya & biji hanjeli emas │
├─────────────────────────────────────────────────────────────────────────────┤
│ ARSITEKTUR HARDWARE: Grid 6 Modul Sensor, Relai, dan Mikrokontroler ESP32  │
├─────────────────────────────────────────────────────────────────────────────┤
│ FITUR UNGGULAN: Tri-Mode Transmisi, AI Duration ETA, Quality Scoring A/B/C │
├─────────────────────────────────────────────────────────────────────────────┤
│ ALUR SOP PASCAPANEN: 5 Tahapan dari Panen Sawah hingga Beras Kemasan Vakum │
├─────────────────────────────────────────────────────────────────────────────┤
│ PREVIEW TRACEABILITY: Contoh Sertifikat Verifikasi QR Code Mutu Konsumen    │
└─────────────────────────────────────────────────────────────────────────────┘
```

#### Navigasi Header Ramah Ponsel (Responsive Mobile Menu)
- **Tampilan Desktop**: Menu horizontal dengan tautan langsung ke masing-masing *anchor section*.
- **Tampilan Smartphone**: Tombol ikon *hamburger* di pojok kanan atas. Ketika ditekan, menu *drawer* geser akan muncul dengan latar belakang transparan beraksen *backdrop blur*, memungkinkan navigasi cepat tanpa menutup pandangan layar secara penuh.

---

### 4.2. Portal Sertifikasi & Verifikasi Mutu Farm-to-Table (`/verify`)

Portal Verifikasi Mutu (`/verify` atau `/verify/:batchCode`) adalah inovasi keterbukaan mutu pangan yang memberikan kepastian kualitas gabah dan beras hanjeli kepada konsumen, pembeli industri, maupun pengawas sertifikasi pangan organik.

#### Cara Melakukan Verifikasi:
1. **Metode Pindai QR Code**: Arahkan kamera smartphone ke stiker label QR Code pada kemasan beras hanjeli. Ponsel akan otomatis membuka URL verifikasi spesifik (contoh: `https://hanjeli-dryer.stas-rg.com/verify/HNJ-20261026-001`).
2. **Metode Input Manual**: Buka menu **Verifikasi** pada landing page, masukkan kode batch pada bilah pencarian, lalu tekan Enter.

#### Informasi Transparan yang Disajikan:
- **Badge Sertifikat Resmi**: Memuat cap keaslian riset **Center of Excellence STAS-RG** dan kelembagaan **Desa Wisata Hanjeli**.
- **Grade Mutu & Skor Kualitas**:
  - Menampilkan badge **Grade A (Istimewa)**, **Grade B (Standar)**, atau **Grade C**.
  - Persentase kadar air akhir terukur ($\le 12.0\%$).
- **Tabel Fakta Nutrisi Pangan Fungsional (per 100 gram)**:
  - Protein Nabati: **$14.1\text{ g}$**
  - Serat Pangan Alami: **$8.4\text{ g}$**
  - Karbohidrat Kompleks Low-GI: **$65.3\text{ g}$**
  - Lemak Nabati Sehat: **$4.2\text{ g}$**
  - Kalsium & Zat Besi Tinggi: Mendukung pemulihan kesehatan dan pencegahan stunting.
- **Grafik Interaktif Kinetika Pengeringan**: Konsumen dapat melihat riwayat stabilitas suhu ruang dan penurunan kelembaban udara selama gabah dikeringkan di greenhouse.
- **Asal Usul Bahan Baku & Waktu Panen**: Menampilkan nama blok lahan tani di Waluran Mandiri, tanggal panen sawah, dan tanggal pengemasan.
- **Tombol Unduh Sertifikat PDF**: Konsumen dapat mengunduh dokumen sertifikat mutu digital resmi beresolusi tinggi untuk keperluan pengadaan industri kuliner sehat atau audit ekspor.

---

## BAB 5: PANDUAN MODUL OPERATOR GREENHOUSE

### 5.1. Dashboard Utama Operator (`/dashboard`)

Halaman kerja harian bagi operator greenhouse untuk memantau kondisi ruangan secara sekilas:

```
┌─────────────────────────────────────────────────────────────────────────────┐
│ KARTU CUACA: Desa Waluran, Sukabumi • 28°C Berawan • Kelembaban Luar 72%    │
├─────────────────────────────────────────────────────────────────────────────┤
│ STATUS BATCH AKTIF: [ BATCH HNJ-20261028-001 ]  Varietas: Hanjeli Ketan A   │
│ Progres: [▓▓▓▓▓▓▓▓▓▓▓▓▓▓▓░░░░] 68% • Sisa Waktu (ETA): 3 Jam 15 Menit       │
├─────────────────────────────────────────────────────────────────────────────┤
│ 4 KARTU TELEMETRI UTAMA:                                                    │
│  🌡️ Suhu Ruangan: 42.8°C     💧 Kelembaban (RH): 52.4%                      │
│  ☀️ Radiasi Surya: 680 W/m²   🌾 Kadar Air Gabah: 13.8%                      │
├─────────────────────────────────────────────────────────────────────────────┤
│ RINGKASAN AKTUATOR:                                                         │
│  • Kipas Exhaust: [ AKTIF 80% ]     • Kipas Sirkulasi: [ AKTIF 100% ]       │
│  • Kipas Intake : [ NONAKTIF ]      • Pemanas PTC    : [ STANDBY ]          │
└─────────────────────────────────────────────────────────────────────────────┘
```

---

### 5.2. Live Monitoring Sensor Lingkungan (`/monitoring`)

Halaman pemantauan instrumen presisi dengan penyegaran data sub-detik melalui WebSocket Reverb:
1. **Interactive SVG Gauges**:
   - Menggambarkan posisi jarum indikator pada busur warna:
     - **Zona Hijau (Optimal)**: $38^\circ\text{C} - 48^\circ\text{C}$ (Suhu) / $40\% - 60\%$ (Kelembaban).
     - **Zona Kuning (Waspada)**: $48^\circ\text{C} - 52^\circ\text{C}$ / $60\% - 70\%$.
     - **Zona Merah (Kritis)**: $> 55^\circ\text{C}$ (Pemanas otomatis diputus) / $> 80\%$ (Kipas exhaust dipacu maksimal).
2. **Grafik Multi-Axis Mikroiklim Interaktif**:
   - Membandingkan 4 kurva secara simultan: *Suhu Internal vs Suhu Eksternal*, *Kelembaban Internal vs Eksternal*, *Intensitas Sinar Surya (Lux)*, dan *Kadar Air Gabah (%)*.
   - **Tombol Filter Rentang**: Pilih **1 Jam**, **6 Jam**, **24 Jam**, atau **7 Hari** untuk melihat fluktuasi siang dan malam.
3. **Kartu Pemantauan Susut Bobot Massa (Load Cell HX711)**:
   - Menghitung penurunan massa air total dalam rak pengering untuk mengonfirmasi bahwa pengurangan kadar air terjadi secara merata.

---

### 5.3. Memulai Batch Pengeringan Baru (`/drying/new`)

Ketika gabah basah yang baru dirontokkan masuk ke ruang greenhouse:
1. Buka menu navigasi **Mulai Batch Baru** (`/drying/new`).
2. Isi parameter pada formulir registrasi batch:
   - **Kode Batch**: Dihasilkan secara otomatis oleh sistem dengan format standar `HNJ-YYYYMMDD-XXX` (misal: `HNJ-20261029-001`). Kode ini dapat disesuaikan manual bila kelompok tani memiliki standar penomoran tersendiri.
   - **Varietas Hanjeli**: Pilih jenis hanjeli yang dikeringkan (contoh: *Hanjeli Ketan Sukabumi Unggul*, *Hanjeli Batu*, atau *Hanjeli Putih Lokal*).
   - **Bobot Awal Gabah (kg)**: Masukkan berat total gabah basah yang diletakkan pada rak (misal: `120 kg`).
   - **Kadar Air Awal (%)**: Masukkan hasil pengukuran alat moisture tester portabel (umumnya berkisar antara $22.0\% - 28.0\%$).
   - **Target Kadar Air Akhir (%)**: Standar mutu nasional untuk penyimpanan aman adalah `12.0%` (maksimal $13.5\%$).
   - **Posisi Rak (Tray Level)**: Pilih penempatan rak: *Rak Bawah*, *Rak Tengah*, *Rak Atas*, atau *Semua Rak*.
   - **Catatan Operasional**: Masukkan informasi tambahan (misal: *Lahan Blok Cimandiri, dipanen saat pagi cerah*).
3. Klik tombol hijau **Mulai Siklus Pengeringan (Start Drying)**.
4. Sistem akan membuat entitas batch berstatus `ACTIVE`, mencatat waktu mulai, dan langsung mengarahkan Anda ke ruang kendali batch aktif.

---

### 5.4. Pemantauan Batch Aktif & Predictive ETA (`/drying/active`)

Halaman pengawasan utama selama proses pengeringan berlangsung:
- **Progress Bar Persentase**: Menghitung secara dinamis seberapa jauh kadar air telah turun mendekati target $12.0\%$.
- **AI Predictive Duration & ETA**:
  - Algoritma prediktif menghitung laju penguapan rata-rata ($\Delta \text{Moisture} / \text{Jam}$) yang dikorelasikan dengan akumulasi radiasi surya jam berikutnya.
  - Menampilkan prediksi waktu selesai yang akurat (contoh: *"Perkiraan Selesai: Hari Ini pukul 16:30 WIB (Sisa 4 Jam 20 Menit)"*).
- **Tombol Kendali Siklus Batch**:
  1. **Tombol Jeda (*Pause*)**: Digunakan saat operator membuka pintu greenhouse untuk membalik/mengaduk gabah di rak. Sistem akan menghentikan perhitungan durasi efektif dan mematikan pemanas demi keselamatan operator.
  2. **Tombol Lanjutkan (*Resume*)**: Mengaktifkan kembali siklus pengeringan setelah pintu ditutup rapat.
  3. **Tombol Selesaikan (*Complete*)**: Ditekan saat kadar air gabah terukur telah mencapai target ($\le 12\%$). Sistem akan memunculkan dialog verifikasi berat akhir timbangan dan kadar air akhir, lalu mengunci data secara permanen.

---

### 5.5. Panel Kendali Aktuator Cerdas (Kipas & Pemanas PTC)

Panel kendali aktuator dapat dioperasikan dalam dua mode utama:

#### 1. Mode Otomatis (Algoritma Histeresis Cerdas - Direkomendasikan)
- **Kipas Pembuang Lembap (Exhaust Fan)**: Otomatis menyala saat kelembaban udara ruang $> 60\%$ untuk mengeluarkan uap air jenuh hasil penguapan biji ke luar ruangan.
- **Kipas Sirkulasi Ruang (Circulation Fan)**: Berputar kontinu untuk meratakan distribusi udara panas dari atap polikarbonat ke seluruh rak baki biji.
- **Pemanas Bantu Keramik PTC (Auxiliary Heater)**:
  - Menyala otomatis jika suhu ruangan turun di bawah $38^\circ\text{C}$ (misal saat hujan lebat atau malam hari).
  - **Fitur Proteksi Suhu Kritis (*Safety Interlock Cut-off*)**: Jika suhu ruang menyentuh $\ge 50^\circ\text{C} - 55^\circ\text{C}$, sistem server dan mikrokontroler ESP32 secara instan memutus aliran listrik ke pemanas PTC dan membuka kipas exhaust secara penuh untuk mencegah kerusakan gabah.

#### 2. Mode Manual (Intervensi Teknisi / Operator)
- Klik toggle saklar ke posisi **MANUAL**.
- Operator dapat menyalakan atau mematikan saklar relai masing-masing kipas secara individual.
- Operator dapat menggeser *slider* kecepatan kipas berbasis modulasi lebar pulsa (*PWM $0\% - 100\%$*).
- Operator dapat mengaktifkan Saluran Pemanas 1 atau Saluran Pemanas 2 sesuai kebutuhan pengujian fisik.

---

### 5.6. Riwayat Pengeringan & Evaluasi Kualitas (`/history`)

Seluruh data pengeringan yang telah selesai akan diarsipkan di halaman Riwayat:
- **Tabel Arsip Terpadu**: Menampilkan Kode Batch, Tanggal, Varietas, Durasi Pengeringan, Total Susut Massa Gabah, dan Badge Skor Mutu.
- **Formula Evaluasi Mutu (*Quality Scoring Formula*)**:
  Sistem mengevaluasi mutu hasil pengeringan secara objektif menggunakan persamaan komposit berbobot:
  $$\text{Skor Mutu} = (S_{\text{kadar air}} \times 45\%) + (S_{\text{stabilitas suhu}} \times 35\%) + (S_{\text{kestabilan RH}} \times 20\%)$$
  - **Grade A (Skor $\ge 85$)**: Kadar air optimal ($11.5\% - 13.0\%$) tanpa ada insiden *overheating*. Cocok untuk benih bersertifikat dan beras premium kemasan vakum.
  - **Grade B (Skor $70 - 84$)**: Pengeringan baik ($13.1\% - 14.5\%$). Sangat baik untuk konsumsi pangan lokal atau bahan baku tepung hanjeli.
  - **Grade C (Skor $< 70$)**: Terdapat fluktuasi ekstrem atau kadar air $> 14.5\%$. Disarankan untuk dikeringkan ulang sebelum disimpan.

---

### 5.7. Ekspor Laporan (PDF Resmi, Excel, CSV)

Pada halaman rincian riwayat pengeringan (`/history/:id`), operator dapat mengekspor dokumen resmi:
1. **Laporan Resmi Berformat PDF (A4)**:
   - Didesain profesional lengkap dengan kop surat resmi **Desa Wisata Hanjeli** dan **CoE STAS-RG**.
   - Dilengkapi tabel statistik mikroiklim rata-rata, grafik kurva suhu/kelembaban, sertifikat grade mutu, stempel digital, serta kolom tanda tangan penanggung jawab greenhouse.
2. **Lembar Kerja Excel (.xlsx)**:
   - Menyimpan seluruh baris log telemetri per menit lengkap untuk keperluan olah data statistik peneliti atau arsip dinas pertanian.
3. **Berkas CSV (.csv)**:
   - Berkas data mentah terpisah tanda koma (*comma-separated*) untuk integrasi dengan perangkat lunak analitik pihak ketiga.

---

### 5.8. Cetak Label Kemasan & Sertifikat QR Code

Untuk mendukung pemasaran beras kemasan bernilai jual tinggi:
1. Pada detail riwayat batch yang telah selesai, klik tombol **Cetak Label Kemasan**.
2. Sistem akan memunculkan pratinjau stiker label kemasan siap cetak:
   - Logo Desa Wisata Hanjeli & STAS-RG.
   - Nama Produk & Varietas Hanjeli.
   - Kode Batch Unik, Tanggal Panen, dan Lokasi Kebun Waluran.
   - Badge Grade Mutu Resmi & Kadar Air Akhir.
   - **QR Code Terenkripsi**: Mengarah langsung ke URL publik `/verify/:batchCode`.
3. Pilih format cetak (Stiker Label Thermal 100x75mm atau kertas A4 multi-label).
4. Klik **Cetak Label**, lalu tempelkan stiker pada karung goni atau kemasan pouch beras hanjeli.

---

### 5.9. Panduan Hardware & SOP Pascapanen (`/guide`)

Halaman referensi interaktif yang dapat diakses langsung oleh operator:
- **Skematik Wiring & Pinout ESP32**: Diagram pengkabelan sensor DHT22, BH1750, modul relai 4 kanal, dan layar LCD 20x4.
- **SOP Pascapanen Interaktif**: Panduan langkah demi langkah pemrosesan hanjeli dari panen sawah, perontokan, pembersihan kotoran gabah, penataan baki di greenhouse, hingga penyimpanan dalam wadah kedap udara.

---

### 5.10. Pengaturan Sistem & Saluran Transmisi (`/settings`)

Menu pengaturan komprehensif yang terbagi dalam 4 tab:
1. **Tab Hardware & Transmisi (`hardware`)**:
   - Memilih jalur komunikasi utama: **Wi-Fi MQTT**, **Web Bluetooth (BLE)**, atau **Kabel USB Serial**.
   - Input nilai offset kalibrasi sensor suhu (°C) dan kelembaban (% RH).
   - Pengaturan SSID, password WiFi greenhouse, dan host broker MQTT.
2. **Tab Parameter Pengeringan (`drying`)**:
   - Pengaturan ambang batas suhu maksimum aman (default $50^\circ\text{C} - 55^\circ\text{C}$).
   - Pengaturan target kadar air default (default $12.0\%$).
   - Pilihan preset pengeringan cepat (*Quick Presets*).
3. **Tab Saluran Notifikasi (`notifications`)**:
   - Konfigurasi email SMTP peringatan darurat.
   - Pengaturan token API WhatsApp Gateway (Fonnte/Wablas) dan nomor grup pengelola.
   - Pengaturan Bot Telegram & Chat ID.
   - Tombol **Kirim Pesan Uji Coba** dan tombol **Kirim Daily Digest Sekarang**.
4. **Tab Keamanan & Passkey (`security`)**:
   - Pembaruan kata sandi akun secara mandiri.
   - Manajemen pendaftaran Passkey biometrik (WebAuthn).
   - Peninjauan daftar sesi login aktif.

---

## BAB 6: PANDUAN MODUL ADMINISTRATOR SISTEM

Modul Administrator (`/admin`) merupakan pusat kendali teknis tingkat tinggi yang hanya dapat diakses oleh akun dengan peran `ADMIN`.

### 6.1. Overview Administrator & Health Matrix (`/admin`)

Halaman pengawasan kesehatan infrastruktur secara real-time:
- **Matriks Kesehatan Layanan (Multi-Service Status Matrix)**:
  - 🌐 **Web Server HTTP / REST API**: Status respon server web.
  - ⚡ **WebSocket Daemon (Laravel Reverb)**: Status broadcasting event real-time pada port 8080.
  - 📡 **MQTT Telemetry Worker**: Status proses latar belakang pengonsumsi data sensor dari broker.
- **Statistik Basis Data**: Jumlah total baris telemetri yang tersimpan, ukuran berkas basis data, dan estimasi waktu retensi.
- **Deteksi Anomali Sensor (Anomaly Detection Center)**: Mendeteksi secara otomatis sinyal anomali seperti lonjakan suhu abnormal (*Thermal Spike*), sensor terputus (*Sensor Dropped/NaN*), atau fenomena pemanas aktif namun suhu anjlok (*Thermal Paradox*).

---

### 6.2. Manajemen Armada Perangkat IoT 7 Sub-Modul (`/admin/devices`)

Pusat kendali perangkat keras terpadu yang memadukan 7 sub-modul teknis dalam satu antarmuka:

```
┌─────────────────────────────────────────────────────────────────────────────┐
│ PUSAT KENDALI ARMADA IOT: [ Node Utama: SRD-001 | Status: ONLINE | Ping: 42ms ] │
├─────────────────────────────────────────────────────────────────────────────┤
│ TAB SUB-MODUL:                                                              │
│ [1. Security] [2. OTA] [3. Releases] [4. Sensors] [5. Relays] [6. Catalog] [7. Code] │
└─────────────────────────────────────────────────────────────────────────────┘
```

1. **Sub-Modul 1: Otentikasi Token & Keamanan API (`security`)**:
   - Mengelola validitas token `X-Device-Token` dan kunci enkripsi HMAC SHA-256 untuk memastikan hanya mikrokontroler resmi yang diizinkan mengirimkan data.
   - Fitur **Ping Semua Node** untuk memeriksa konektivitas seluruh unit di greenhouse.
   - Fitur **Regenerasi Token** untuk merotasi kunci rahasia secara berkala.
2. **Sub-Modul 2: Manajemen Firmware OTA (Over-The-Air) (`ota`)**:
   - Melakukan pembaruan program mikrokontroler jarak jauh melalui jaringan WiFi tanpa perlu menyambungkan kabel fisik ke greenhouse.
   - Pilih node target, pilih file biner firmware, lalu klik **Picukan Pembaruan OTA**. Progres flashing akan dipantau real-time hingga status `SUCCESS`.
3. **Sub-Modul 3: Pustaka Rilis Biner Firmware (`releases`)**:
   - Repositori penyimpanan berkas biner firmware berekstensi `.bin`.
   - Administrator dapat mengunggah versi rilis baru, mengisi catatan perubahan (*changelog*), dan memverifikasi kode *checksum* integritas data.
4. **Sub-Modul 4: Matriks Kesehatan Sensor & Latensi (`sensors`)**:
   - Memantau kondisi individual setiap sensor fisik (DHT22 Internal, DHT22 Eksternal, BH1750 Lux, Sensor Hujan, Load Cell).
   - Memeriksa waktu detak jantung terakhir (*heartbeat*), tingkat kehilangan paket data (*packet drop rate*), dan latensi komunikasi bolak-balik (ms).
5. **Sub-Modul 5: Kontrol Sakelar Relai & Pengujian Aktuator Fisik (`actuators`)**:
   - Panel uji coba sakelar relai 4 kanal langsung dari server:
     - Relai 1 (GPIO 26): Pemanas Keramik PTC Saluran 1
     - Relai 2 (GPIO 27): Pemanas Keramik PTC Saluran 2
     - Relai 3 (GPIO 14): Kipas Pembuang Uap Lembap (Exhaust Fan)
     - Relai 4 (GPIO 12): Kipas Sirkulasi Ruang Pengering
6. **Sub-Modul 6: Katalog Hardware & Sensor Greenhouse (`catalog`)**:
   - Lembar data teknis (*datasheet summary*) untuk seluruh sensor yang terpasang: nomor pinout GPIO, protokol bus data (I2C / One-Wire / ADC Analog), rentang batas ukur, serta jadwal pemeliharaan rutin.
7. **Sub-Modul 7: Kode Sumber Arduino & Firmware ESP32 (`arduino`)**:
   - Penampil kode sumber (*source code viewer*) lengkap untuk berkas program mikrokontroler:
     - `esp32_smart_dryer_hardware.ino` (Firmware produksi greenhouse).
     - `esp32_smart_dryer_simulator.ino` (Firmware simulator untuk pengujian laboratorium).
   - Tersedia tombol **Salin Kode** dan **Unduh File .ino** untuk mempermudah teknisi melakukan flashing langsung melalui Arduino IDE atau VS Code PlatformIO.

---

### 6.3. Manajemen Pengguna & Pengaturan Peran (`/admin/users`)

Modul pengelolaan akun dan hak akses seluruh personil greenhouse:
- **Daftar Pengguna**: Tabel komprehensif yang menampilkan Nama Lengkap, Username, Email, Nomor WhatsApp, Status Verifikasi, dan Peran Aktif (`ADMIN` atau `OPERATOR`).
- **Tambah Pengguna Baru**: Administrator dapat menambahkan akun operator baru dengan mengisi identitas dan kata sandi sementara.
- **Ubah Peran (*Role Promotion/Demotion*)**: Mengubah hak akses pengguna sewaktu-waktu sesuai penugasan lapangan.
- **Reset Kata Sandi Pengguna**: Mereset kata sandi operator yang lupa kredensial secara langsung tanpa harus menunggu email OTP.
- **Blokir / Hapus Pengguna**: Menonaktifkan akses akun staf yang sudah tidak bertugas di fasilitas pengeringan.

---

### 6.4. Pengaturan Master Parameter Sistem (`/admin/parameters`)

Pusat kendali konstanta dan ambang batas yang mengatur seluruh logika otomatisasi:
- **Ambang Suhu Kritis Maksimum (`max_safe_temp`)**: Default `50.0°C - 55.0°C`. Suhu batas darurat pemutusan pemanas otomatis.
- **Ambang Suhu Minimum Operasional (`min_safe_temp`)**: Default `35.0°C`.
- **Target Kadar Air Default (`target_moisture_default`)**: Default `12.0%`.
- **Interval Pengambilan Sampel Sensor (`sampling_interval_seconds`)**: Default `5 detik`.
- **Konfigurasi MQTT Broker**: Alamat host broker (`broker.hivemq.com` atau broker lokal), Port (`1883`), dan root topic.
- **Konfigurasi WhatsApp Gateway**: URL API provider (Fonnte/Wablas), Token Otorisasi, dan nomor tujuan grup notifikasi pengelola.
- **Konfigurasi Bot Telegram**: Token bot Telegram dan Chat ID channel alert.

---

### 6.5. Log Audit & Agregasi Data Telemetri (`/admin/logs`)

- **Audit Trail Aktivitas**: Merekam setiap intervensi penting pengguna, seperti waktu login, perubahan status aktuator manual, inisiasi batch baru, hingga pengubahan parameter sistem.
- **Fitur Downsampling Data Lama**:
  - Untuk mencegah basis data membengkak setelah berbulan-bulan beroperasi, sistem menyediakan tombol **Jalankan Downsample Data Lama**.
  - Fitur ini akan merangkum rekaman telemetri per 5 detik yang lebih tua dari 30 hari menjadi rata-rata periodik per 10 menit, menghemat hingga $90\%$ ruang penyimpanan tanpa kehilangan tren historis pengeringan.

---

## BAB 7: KONEKTIVITAS TINGKAT LANJUT & FITUR INOVASI

### 7.1. Web Bluetooth Low Energy (BLE) Provisioning

Fitur konfigurasi nirkabel jarak dekat langsung dari peramban tanpa bantuan router:
1. Pastikan Bluetooth dan Lokasi/GPS aktif pada smartphone atau laptop Anda.
2. Buka aplikasi di peramban berbasis Chromium (Google Chrome atau Microsoft Edge).
3. Buka menu **Pengaturan** (`/settings` tab *Hardware*), pilih mode **Bluetooth Low Energy (BLE)**.
4. Dekati panel box kontrol ESP32 di greenhouse (jarak $< 5\text{ meter}$).
5. Klik tombol **Pindai Perangkat BLE**.
6. Pilih perangkat bernama **`SMART-DRYER-PROVISION`** lalu klik **Sandingkan (Pair)**.
7. Masukkan nama Wi-Fi (SSID) dan kata sandi baru untuk greenhouse, lalu klik **Kirim Konfigurasi**. ESP32 akan menyimpan kredensial ke memori internal EEPROM/NVS dan tersambung ke jaringan lokal seketika.

---

### 7.2. Komunikasi Kabel Langsung via Web Serial API (USB 115200 baud)

Sangat bermanfaat ketika workstation operator terhubung langsung menggunakan kabel ke mikrokontroler di dalam greenhouse atau saat area perkebunan mengalami gangguan jaringan internet/WiFi:
1. Sambungkan kabel data USB dari port laptop/komputer stasiun kerja ke port USB board ESP32.
2. Buka menu **Pengaturan** (`/settings` tab *Hardware*), pilih mode **Kabel USB Langsung (Web Serial)**.
3. Klik tombol **Hubungkan USB Serial**.
4. Peramban akan memunculkan daftar port serial yang terdeteksi (contoh: `COM3`, `COM4` pada Windows, atau `/dev/ttyUSB0` pada Linux/macOS).
5. Pilih port ESP32 dan klik **Connect**.
6. Peramban membuka komunikasi serial berkecepatan **115200 baud**. Data telemetri sensor akan mengalir lancar langsung ke dashboard secara *offline-ready*.

---

### 7.3. Progressive Web App (PWA) — Instalasi Mobile & Offline Caching

Aplikasi memenuhi standar PWA modern dan dapat diinstal layaknya aplikasi native Android atau iOS tanpa melalui toko aplikasi:
- **Pada Ponsel Android (Google Chrome)**:
  1. Buka tautan website aplikasi di Google Chrome.
  2. Klik banner **"Tambahkan Smart Dryer ke Layar Utama"** yang muncul di bagian bawah, atau klik ikon titik tiga (⋮) di pojok kanan atas lalu pilih **Instal Aplikasi**.
  3. Ikon aplikasi *Smart Room Dryer Hanjeli* akan terpasang di daftar aplikasi ponsel Anda.
- **Pada iPhone / iPad (Safari)**:
  1. Buka tautan website aplikasi di browser Safari.
  2. Klik tombol **Share** (ikon kotak dengan panah ke atas di bilah bawah).
  3. Gulir ke bawah dan klik opsi **Tambah ke Layar Utama (Add to Home Screen)**.
  4. Klik **Tambah** di pojok kanan atas.
- **Kelebihan Mode PWA**: Tampilan layar penuh (*standalone*) tanpa bilah URL peramban, loading secepat kilat berkat *Service Worker Asset Caching*, dan hemat kuota internet.

---

### 7.4. Asisten Cerdas AI Copilot & Agronomy Assistant

Sistem dilengkapi asisten kecerdasan buatan terintegrasi yang memahami data telemetri mikroiklim secara kontekstual:
1. Klik tombol **AI Copilot** (ikon bintang berkilau ✦) di pojok kanan atas header aplikasi.
2. Drawer interaktif akan meluncur terbuka dari sisi kanan layar.
3. AI Copilot secara otomatis menyerap konteks sensor terkini: suhu ruang, kelembaban udara, status kipas, dan persentase kadar air gabah saat ini.
4. **Pilih Tombol Cepat (*Quick Actions*) atau Ajukan Pertanyaan**:
   - *"Bagaimana kondisi mikroklimat ruang saat ini? Apakah aman?"*
   - *"Kenapa kelembaban udara sulit turun di bawah 60%?"*
   - *"Apakah pemanas PTC perlu dinyalakan pada kondisi cuaca saat ini?"*
   - *"Berapa estimasi waktu pengeringan varietas Hanjeli Ketan ini?"*
5. AI Copilot akan menganalisis tren data dan memberikan saran agronomis praktis berbahasa Indonesia.
6. Jika AI Copilot menyarankan tindakan intervensi (contoh: *naikkan kecepatan kipas exhaust ke 90%*), tersedia tombol **Terapkan Saran** untuk mengeksekusi rekomendasi tersebut dengan satu klik.

---

## BAB 8: STANDAR OPERASIONAL PROSEDUR (SOP) & FORMULA MUTU

### 8.1. Alur Operasional Pascapanen Biji Hanjeli

```
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────┐
│ 1. PANEN SAWAH  │ ──> │ 2. PERONTOKAN    │ ──> │ 3. SORTASI AWAL │
│ (Kadar 24%-28%) │     │ (Threshing Biji) │     │ (Pilah Kotoran) │
└─────────────────┘     └──────────────────┘     └─────────────────┘
                                                           │
                                                           ▼
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────┐
│ 6. DISTRIBUSI & │ <── │ 5. UJI MUTU &    │ <── │ 4. PENGERINGAN  │
│ KEMASAN VAKUM   │     │ CETAK QR LABEL   │     │ GREENHOUSE IOT  │
│ (Traceability)  │     │ (Kadar <= 12%)   │     │ (Suhu 38°-50°C) │
└─────────────────┘     └──────────────────┘     └─────────────────┘
```

1. **Tahap 1 - Panen**: Pemanenan biji hanjeli dilakukan saat malai telah menguning/kelabu kecokelatan secara merata (kadar air awal berkisar $24\% - 28\%$).
2. **Tahap 2 - Perontokan**: Biji dipisahkan dari tangkai malai menggunakan mesin perontok mekanis atau perontokan manual di atas terpal bersih.
3. **Tahap 3 - Sortasi Awal**: Biji diayak (*winnowing*) untuk memisahkan biji hampa, daun kering, tangkai, dan kerikil tanah.
4. **Tahap 4 - Pengeringan Greenhouse IoT**:
   - Gabah dihamparkan merata di atas baki rak kawat kasa (*drying trays*) dengan ketebalan maksimal $3 - 5\text{ cm}$ agar aliran udara panas dapat menembus seluruh lapisan.
   - Operator mendaftarkan batch baru di sistem web (`/drying/new`).
   - Sirkulasi udara dan pemanas cadangan bekerja otomatis menjaga kelembaban rendah hingga kadar air menyentuh target $\le 12.0\%$.
   - Operator membalik gabah setiap 3-4 jam sekali untuk memastikan pengeringan homogen.
5. **Tahap 5 - Uji Mutu & Cetak Label QR**: Batch diselesaikan di sistem (`/drying/active`), skor mutu dihitung otomatis, dan stiker label thermal QR Code dicetak.
6. **Tahap 6 - Pengemasan & Penyimpanan**: Gabah kering digiling menjadi beras hanjeli sosoh/pecah kulit, dikemas dalam plastik kedap udara (*vacuum pouch*), ditempel stiker QR Code traceability, dan disimpan di gudang bersuhu sejuk.

---

### 8.2. Checklist Siklus Harian Operator

| Waktu Pelaksanaan | Komponen yang Diperiksa | Tindakan yang Harus Dilakukan |
| :--- | :--- | :--- |
| **Pagi Hari (07:30 - 08:30 WIB)** | Fasilitas Greenhouse & Sensor | 1. Buka dashboard operator dan periksa status koneksi (*ONLINE*).<br>2. Periksa kebersihan kaca/atap polikarbonat dari debu atau dedaunan jatuh.<br>3. Bersihkan permukaan sensor surya BH1750 dan pelat sensor hujan.<br>4. Lakukan pembalikan biji pertama pada rak baki. |
| **Siang Hari (12:00 - 13:00 WIB)** | Suhu Maksimum & Ventilasi | 1. Pantau suhu ruang di menu `/monitoring`. Pastikan suhu tidak melewati batas aman $50^\circ\text{C}$.<br>2. Pastikan Kipas Exhaust berputar lancar membuang uap air panas.<br>3. Lakukan pembalikan biji kedua. |
| **Sore Hari (16:30 - 17:30 WIB)** | Transisi Senja & Penutupan | 1. Periksa prakiraan cuaca malam hari.<br>2. Pastikan pintu greenhouse tertutup rapat.<br>3. Periksa kesiapan elemen pemanas PTC untuk menjaga suhu malam hari.<br>4. Lakukan pembalikan biji ketiga. |
| **Malam Hari (Periodik Otomatis)** | Pemanas PTC & Pengawasan Jarak Jauh | 1. Sistem otomatis mengontrol pemanas PTC dan sirkulasi udara.<br>2. Operator dapat memantau status melalui aplikasi smartphone dari rumah.<br>3. Peringatan darurat akan dikirim otomatis ke WhatsApp jika terjadi anomali suhu. |

---

### 8.3. Formula Matematis Penilaian Mutu (Quality Scoring)

Sistem menetapkan nilai mutu secara transparan dan terstandarisasi:

$$\text{Skor Mutu Total} = (S_{\text{kadar air}} \times 0.45) + (S_{\text{stabilitas suhu}} \times 0.35) + (S_{\text{kestabilan RH}} \times 0.20)$$

#### Komponen Penilaian:
1. **Sub-Skor Kadar Air ($S_{\text{kadar air}}$)**:
   - Kadar air akhir $11.5\% - 12.5\% \rightarrow \text{Skor } 100$
   - Kadar air akhir $12.6\% - 13.5\% \rightarrow \text{Skor } 85$
   - Kadar air akhir $13.6\% - 14.5\% \rightarrow \text{Skor } 70$
   - Kadar air akhir $> 14.5\% \rightarrow \text{Skor } 50$ (Belum aman simpan)
2. **Sub-Skor Stabilitas Suhu ($S_{\text{stabilitas suhu}}$)**:
   - Rata-rata suhu di rentang $38^\circ\text{C} - 48^\circ\text{C}$ dengan simpangan baku $\sigma < 3.0^\circ\text{C} \rightarrow \text{Skor } 100$
   - Terdapat lonjakan suhu $> 52^\circ\text{C} \rightarrow \text{Pengurangan } 15 \text{ poin}$
   - Terdapat insiden *overheat* $> 55^\circ\text{C} \rightarrow \text{Pengurangan } 30 \text{ poin}$
3. **Sub-Skor Kestabilan Kelembaban Udara ($S_{\text{kestabilan RH}}$)**:
   - Rata-rata RH ruang pengering $\le 55\% \rightarrow \text{Skor } 100$
   - Rata-rata RH ruang pengering $56\% - 65\% \rightarrow \text{Skor } 80$
   - Rata-rata RH ruang pengering $> 65\% \rightarrow \text{Skor } 60$

#### Kategori Kelulusan Grade:
- **GRADE A (Skor $\ge 85$)**: Mutu Istimewa / Unggul (*Export Quality*).
- **GRADE B (Skor $70 - 84$)**: Mutu Baik / Standar Konsumsi Nasional.
- **GRADE C (Skor $< 70$)**: Mutu Kurang / Perlu Pengeringan Tambahan.

---

### 8.4. Prosedur Tanggap Darurat & Situasi Khusus

1. **Kondisi Hujan Lebat / Mendung Panjang**:
   - Sensor hujan analog (Pin 34) akan mendeteksi kebasahan ($ADC < 2000$).
   - Kipas intake udara luar otomatis dinonaktifkan agar udara lembap luar tidak terhisap ke dalam greenhouse.
   - Pemanas cadangan PTC otomatis diaktifkan untuk menjaga temperatur ruang pengering tetap di atas $38^\circ\text{C}$.
2. **Kondisi Suhu Ruangan Kritis ($> 50^\circ\text{C} - 55^\circ\text{C}$)**:
   - Logika *safety interlock* hardware dan software seketika memutus daya relai pemanas PTC.
   - Kipas Exhaust otomatis dipacu pada kecepatan maksimal $100\%$ untuk membuang akumulasi panas berlebih.
   - Sistem mengirimkan pesan peringatan bahaya (*CRITICAL ALERT*) ke grup WhatsApp pengelola.
3. **Kondisi Pemadaman Listrik PLN**:
   - Jika greenhouse dilengkapi UPS atau sistem baterai surya (*Solar PV*), sistem IoT akan tetap beroperasi dalam mode hemat daya (*power-saving mode*).
   - Jika listrik padam total, mikrokontroler ESP32 akan menyimpan status batch terakhir di memori flash lokal. Begitu listrik menyala kembali, sistem akan otomatis melanjutkan pencatatan telemetri (*auto-resume*) tanpa kehilangan data historis.

---

## BAB 9: PANDUAN TROUBLESHOOTING, FAQ & BANTUAN TEKNIS

### 9.1. Matriks Diagnosa Masalah & Solusi Teknis

| No | Gejala Permasalahan | Kemungkinan Penyebab | Tindakan Solusi Perbaikan |
| :-: | :--- | :--- | :--- |
| 1 | **Data sensor di dashboard membeku / tidak diperbarui** | Daemon MQTT Ingestion Worker di server berhenti beroperasi. | Buka terminal server dan jalankan perintah: `php artisan greenhouse:mqtt-worker`. Jika di VPS production, jalankan: `sudo supervisorctl restart hanjeli-mqtt`. |
| 2 | **Status WebSocket menampilkan badge merah "Disconnected"** | Daemon Laravel Reverb mati atau port 8080 belum dibuka pada firewall. | Jalankan `php artisan reverb:start`. Pastikan nilai `REVERB_HOST` dan `REVERB_PORT=8080` pada berkas `.env` telah sesuai serta port 8080 diizinkan pada security group VPS. |
| 3 | **Pemanas PTC tidak mau menyala sama sekali** | Sensor mendeteksi suhu sudah $\ge 50^\circ\text{C}$ (*safety cut-off*) atau kabel sensor suhu DHT22 terlepas. | Periksa pembacaan suhu di dashboard. Jika pembacaan suhu bernilai `0` atau `NaN`, periksa koneksi kabel DHT22 di GPIO 25. Pemanas dirancang terkunci mati jika sensor suhu gagal dibaca demi mencegah kebakaran. |
| 4 | **Pindai Bluetooth (BLE) tidak menemukan ESP32** | Bluetooth perangkat belum aktif atau peramban bukan Google Chrome / Edge. | Aktifkan Bluetooth dan GPS/Lokasi perangkat. Gunakan peramban berbasis Chromium. Pastikan jarak perangkat dengan box kontrol tidak melebihi 5 meter. |
| 5 | **Kipas Exhaust tidak berputar meski kelembaban tinggi** | Saklar aktuator berada dalam mode MANUAL dengan posisi OFF, atau sekring kipas putus. | 1. Buka dashboard dan alihkan mode aktuator ke **OTOMATIS**.<br>2. Periksa lampu LED indikator pada modul relai di panel box greenhouse. Jika LED menyala namun kipas mati, periksa jalur kabel power supply motor 12V/220V. |
| 6 | **Pesan peringatan "Thermal Paradox Detected" muncul** | Pemanas PTC terdeteksi menyala, tetapi grafik suhu ruang justru turun tajam. | 1. Periksa apakah pintu greenhouse terbuka lebar saat udara luar dingin.<br>2. Periksa apakah elemen pemanas PTC rusak atau kabel daya pemanas terlepas. |
| 7 | **Email kode OTP reset password tidak kunjung masuk** | Konfigurasi SMTP email pada `.env` belum lengkap atau email masuk ke folder Spam. | 1. Periksa folder *Spam / Promosi* pada akun email Anda.<br>2. Minta bantuan Administrator untuk mereset kata sandi Anda secara langsung melalui menu `/admin/users`. |

---

### 9.2. Pertanyaan yang Sering Diajukan (FAQ)

**Q1: Berapa lama waktu yang dibutuhkan untuk mengeringkan biji hanjeli hingga mencapai kadar air 12%?**  
> *Jawaban*: Pada kondisi cuaca cerah di Desa Waluran dengan bantuan efek rumah kaca greenhouse dan sirkulasi otomatis, pengeringan rata-rata selesai dalam **24 hingga 36 jam** (jauh lebih cepat dibanding penjemuran konvensional yang memakan waktu 5 hingga 7 hari).

**Q2: Apakah sistem web ini tetap dapat berjalan jika jaringan internet di perkebunan terputus?**  
> *Jawaban*: **Ya**. Sistem memiliki kapabilitas multi-mode:
> 1. Mikrokontroler ESP32 memiliki logika kontrol otonom lokal di memori internalnya, sehingga kipas dan pemanas tetap beroperasi normal menjaga suhu aman meski tanpa internet.
> 2. Operator dapat menghubungkan laptop langsung ke mikrokontroler menggunakan kabel USB via menu **Web Serial API** untuk memantau data tanpa koneksi internet sama sekali.
> 3. Tampilan web telah berstandar PWA dengan *offline caching*, sehingga antarmuka tetap dapat dibuka di layar ponsel.

**Q3: Siapa yang dapat melihat sertifikat verifikasi traceability pada label QR Code kemasan?**  
> *Jawaban*: Siapa saja. Halaman `/verify/:batchCode` bersifat publik dan tidak memerlukan login akun. Konsumen, distributor, atau mitra restoran dapat langsung memindai QR Code menggunakan kamera ponsel untuk melihat sertifikat mutu, data nutrisi, dan kurva kestabilan suhu pengeringan.

**Q4: Apakah data telemetri historis akan hilang jika mikrokontroler dimatikan?**  
> *Jawaban*: **Tidak**. Seluruh riwayat telemetri, log peristiwa batch, dan sertifikat mutu tersimpan secara permanen di basis data server backend dan diarsipkan di menu `/history`.

---

### 9.3. Kontak Dukungan & Saluran Kolaborasi

Jika Anda mengalami kendala operasional yang tidak tercantum dalam panduan ini, membutuhkan kalibrasi sensor fisik, atau ingin menjalin kerjasama riset agroteknologi, silakan menghubungi:

```
Pusat Riset & Pengembang Sistem:
Center of Excellence Smart Telecommunication, Automation & System (CoE STAS-RG)
Situs Web Resmi : https://hanjeli-dryer.stas-rg.com
Email Dukungan  : admin@hanjeli.id / support@stas-rg.com
Alamat Lab      : Research Center STAS-RG, Sukabumi, Jawa Barat

Pengelola Lapangan Greenhouse:
Sekretariat Desa Wisata Hanjeli
Alamat          : Desa Waluran Mandiri, Kec. Waluran, Kab. Sukabumi, Jawa Barat
Kawasan         : UNESCO Global Geopark Ciletuh-Palabuhanratu
Kontak Operator : 0898-7654-3210 (Operator Greenhouse)
WhatsApp Admin  : 0812-3456-7890 (Administrator Sistem)
```

---

*Dokumen ini diterbitkan sebagai panduan resmi sistem per Oktober 2026. Seluruh hak cipta dilindungi oleh CoE STAS-RG dan Pengelola Fasilitas Smart Room Dryer Desa Wisata Hanjeli Waluran.*
