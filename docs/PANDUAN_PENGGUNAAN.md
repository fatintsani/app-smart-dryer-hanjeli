# 📘 Panduan Penggunaan Sistem (User Manual)
## Smart Room Dryer Hanjeli — IoT Greenhouse Monitoring & Control System

> **Versi Dokumen**: v1.0.0 (Stable Release)  
> **Sasaran Pengguna**: Operator Greenhouse, Petani Biji Hanjeli, Teknisi Lapangan, dan Administrator Sistem  
> **Lokasi Implementasi**: Fasilitas Greenhouse Desa Wisata Hanjeli, Waluran Mandiri, Waluran, Kab. Sukabumi, Jawa Barat  
> **Kolaborasi**: Center of Excellence Smart Telecommunication, Automation & System - Research Group (**CoE STAS-RG**)

---

## 📋 Daftar Isi

1. [Pengenalan Aplikasi](#1-pengenalan-aplikasi)
2. [Hak Akses & Akun Default](#2-hak-akses--akun-default)
3. [Panduan Masuk & Otentikasi Sistem](#3-panduan-masuk--otentikasi-sistem)
   - 3.1. [Login Email & Password](#31-login-email--password)
   - 3.2. [Login Google OAuth 2.0](#32-login-google-oauth-20)
   - 3.3. [Login Biometrik & Passkeys (WebAuthn)](#33-login-biometrik--passkeys-webauthn)
   - 3.4. [Pemulihan Kata Sandi dengan OTP Email](#34-pemulihan-kata-sandi-dengan-otp-email)
4. [Navigasi & Tata Letak Antarmuka](#4-navigasi--tata-letak-antarmuka)
   - 4.1. [Antarmuka Desktop & Sidebar](#41-antarmuka-desktop--sidebar)
   - 4.2. [Antarmuka Mobile & Bottom Navigation (PWA)](#42-antarmuka-mobile--bottom-navigation-pwa)
   - 4.3. [Beralih Tema Terang / Gelap (Light / Dark Mode)](#43-beralih-tema-terang--gelap-light--dark-mode)
   - 4.4. [Beralih Bahasa (Bahasa Indonesia / English)](#44-beralih-bahasa-bahasa-indonesia--english)
5. [Panduan Modul Operator](#5-panduan-modul-operator)
   - 5.1. [Dashboard Utama](#51-dashboard-utama)
   - 5.2. [Live Monitoring Sensor Lingkungan](#52-live-monitoring-sensor-lingkungan)
   - 5.3. [Kendali Aktuator Cerdas (Kipas & Pemanas PTC)](#53-kendali-aktuator-cerdas-kipas--pemanas-ptc)
   - 5.4. [Memulai Batch Pengeringan Baru](#54-memulai-batch-pengeringan-baru)
   - 5.5. [Pemantauan Batch Pengeringan Aktif & Predictive ETA](#55-pemantauan-batch-pengeringan-aktif--predictive-eta)
   - 5.6. [Menjeda, Melanjutkan, dan Menyelesaikan Batch](#56-menjeda-melanjutkan-dan-menyelesaikan-batch)
   - 5.7. [Riwayat Pengeringan (History) & Penilaian Mutu (Grade A/B/C)](#57-riwayat-pengeringan-history--penilaian-mutu-grade-abc)
   - 5.8. [Ekspor Laporan (PDF, Excel, CSV)](#58-ekspor-laporan-pdf-excel-csv)
   - 5.9. [Cetak Label Kemasan & Sertifikasi Traceability (QR Code)](#59-cetak-label-kemasan--sertifikasi-traceability-qr-code)
   - 5.10. [Pengaturan Sistem & Saluran Transmisi Tri-Mode (/settings)](#510-pengaturan-sistem--saluran-transmisi-tri-mode-settings)
6. [Penggunaan Asisten AI (AI Copilot & Agronomy Assistant)](#6-penggunaan-asisten-ai-ai-copilot--agronomy-assistant)
7. [Panduan Modul Administrator](#7-panduan-modul-administrator)
   - 7.1. [Overview Administrator & Health Matrix](#71-overview-administrator--health-matrix)
   - 7.2. [Manajemen Armada Perangkat IoT & 7 Sub-Modul (/admin/devices)](#72-manajemen-armada-perangkat-iot--7-sub-modul-admindevices)
   - 7.3. [Integrasi Alat Fisik & Uji Operasional ESP32](#73-integrasi-alat-fisik--uji-operasional-esp32)
   - 7.4. [Manajemen Pengguna & Pengaturan Peran (RBAC)](#74-manajemen-pengguna--pengaturan-peran-rbac)
   - 7.5. [Pengaturan Master Parameter & Ambang Batas](#75-pengaturan-master-parameter--ambang-batas)
   - 7.6. [Log Audit & Notifikasi Multi-Saluran (WA/Telegram/Email)](#76-log-audit--notifikasi-multi-saluran-watelegramemail)
8. [Fitur Konektivitas Langsung: Web Bluetooth (BLE) & Web Serial USB](#8-fitur-konektivitas-langsung-web-bluetooth-ble--web-serial-usb)
9. [Portal Sertifikasi & Verifikasi Mutu Publik (/verify)](#9-portal-sertifikasi--verifikasi-mutu-publik-verify)
10. [Panduan Instalasi PWA (Mobile Android & iOS)](#10-panduan-instalasi-pwa-mobile-android--ios)
11. [Troubleshooting & Pemecahan Masalah Operasional](#11-troubleshooting--pemecahan-masalah-operasional)

---

## 1. Pengenalan Aplikasi

Aplikasi **Smart Room Dryer Hanjeli** adalah platform web cerdas yang menghubungkan mikrokontroler IoT pada *greenhouse* pengeringan biji hanjeli dengan antarmuka digital yang reaktif, modern, dan mudah digunakan.

Aplikasi ini didesain menggunakan filosofi antarmuka **Zero-Shadow Industrial Aesthetic** (desain tegas tanpa bayangan berlebihan, mengutamakan kontras permukaan dan batas garis 1px presisi) untuk memastikan kenyamanan pembacaan data di bawah sinar matahari langsung di area perkebunan maupun di workstation laboratorium.

### Nilai Utama Sistem:
- **Presisi Pengeringan**: Membantu menurunkan kadar air biji hanjeli dari kondisi basah panen ($\approx 24\% - 28\%$) ke ambang batas aman simpan nasional ($\le 12\% - 14\%$) secara konsisten tanpa merusak kandungan gizi mikro.
- **Otomasi Aktuator**: Mengatur otomatis pembuangan uap lembap (*Exhaust Fan*), sirkulasi udara dalam (*Circulation Fan*), pemasukan udara segar (*Intake Fan*), dan pemanas cadangan keramik PTC (*Auxiliary Heater*).
- **Transparansi Mutu (Traceability)**: Setiap karung/kemasan hasil panen memiliki QR Code unik yang dapat dipindai oleh konsumen untuk memverifikasi kurva suhu pengeringan dan sertifikat grade mutu.

---

## 2. Hak Akses & Akun Default

Sistem menerapkan prinsip *Role-Based Access Control* (RBAC) dengan pemisahan tugas yang jelas:

| Peran (Role) | Pengguna Target | Cakupan Hak Akses Utama |
| :--- | :--- | :--- |
| **`ADMIN`** | Ketua Tim, Manajer Sistem, Teknisi IoT | Akses penuh: manajemen akun pengguna, pengaturan sensor/hardware fisik, manajemen firmware OTA, monitoring telemetri live ESP32, konfigurasi parameter master (suhu min/max, interval sampling, integrasi Telegram/WhatsApp), dan log audit. |
| **`OPERATOR`** | Operator Greenhouse, Staf Pengeringan | Pemantauan sensor real-time, kontrol manual/auto aktuator relay, inisiasi batch pengeringan, jeda/lanjutkan/selesaikan batch, cetak label QR, dan ekspor laporan. |
| **`PETANI`** | Anggota Kelompok Tani | Pemantauan progres kadar air, melihat panduan pengeringan, dan memeriksa status kelompok batch hanjeli yang sedang diproses. |

### Akun Bawaan (Default Demo Credentials)

Gunakan akun berikut saat pertama kali menjalankan sistem:

| Peran | Alamat Email | Password Standar | URL Masuk |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@hanjeli.id` | `admin123` | `/login` $\rightarrow$ dialihkan ke `/admin` |
| **Operator** | `operator@hanjeli.id` | `operator123` | `/login` $\rightarrow$ dialihkan ke `/dashboard` |
| **Petani Demo** | `petani@hanjeli.id` | `petani123` | `/login` $\rightarrow$ dialihkan ke `/dashboard` |

> [!WARNING]
> Sangat disarankan untuk segera mengubah kata sandi default administrator melalui menu **Pengaturan Akun** (`/settings`) setelah proses deployment pertama kali!

---

## 3. Panduan Masuk & Otentikasi Sistem

### 3.1. Login Email & Password
1. Buka peramban (Google Chrome, Microsoft Edge, atau Safari) lalu akses URL aplikasi (misalnya: `http://localhost:8000` atau domain resmi).
2. Klik tombol **Masuk / Login** pada header landing page atau langsung buka rute `/login`.
3. Masukkan alamat email terdaftar dan kata sandi.
4. Centang **Ingat Saya (Remember Me)** jika menggunakan perangkat pribadi agar sesi login tidak cepat berakhir.
5. Klik tombol **Masuk ke Dashboard**. Sistem akan memverifikasi kredensial dan mengarahkan Anda ke dasbor sesuai peran (*role*).

### 3.2. Login Google OAuth 2.0
1. Pada halaman login, klik tombol **Lanjutkan dengan Google** (ikon Google).
2. Popup otorisasi Google akan muncul. Pilih akun Google institusi/kerja Anda.
3. Jika email Google cocok dengan data pengguna terdaftar, Anda akan langsung dialihkan ke dalam sistem tanpa perlu memasukkan password manual.

### 3.3. Login Biometrik & Passkeys (WebAuthn)
Sistem mendukung login modern tanpa kata sandi menggunakan sensor sidik jari (*Fingerprint/TouchID*), pengenalan wajah (*FaceID/Windows Hello*), atau kunci keamanan hardware USB:
1. Pastikan Anda telah mendaftarkan Passkey perangkat Anda sebelumnya di menu **Pengaturan Akun** (`/settings` $\rightarrow$ tab *Keamanan & Passkey*).
2. Pada halaman login, klik opsi **Masuk dengan Passkey / Sidik Jari**.
3. Sentuh sensor sidik jari atau hadapkan wajah ke kamera sesuai instruksi peramban.
4. Sistem mengenali kriptografi biometrik dan login berhasil seketika (< 1 detik).

### 3.4. Pemulihan Kata Sandi dengan OTP Email
Jika Anda lupa kata sandi:
1. Pada halaman login, klik tautan **Lupa Kata Sandi?** (`/forgot-password`).
2. Masukkan alamat email akun Anda, lalu klik **Kirim Kode Verifikasi**.
3. Periksa kotak masuk email Anda (atau folder spam). Anda akan menerima email resmi dari *Smart Room Dryer Hanjeli* berisi **6 Digit Kode OTP** yang berlaku selama 10 menit.
4. Masukkan 6 digit kode OTP pada layar verifikasi.
5. Masukkan kata sandi baru (minimal 8 karakter) dan konfirmasi kata sandi.
6. Klik **Simpan Sandi Baru**. Anda kini dapat login kembali menggunakan kata sandi yang baru diperbarui.

---

## 4. Navigasi & Tata Letak Antarmuka

Antarmuka aplikasi dibangun dengan tata letak adaptif yang nyaman digunakan pada monitor meja kerja maupun layar ponsel berorientasi potret di lapangan.

### 4.1. Antarmuka Desktop & Sidebar
Pada layar komputer/laptop ($\ge 1024\text{px}$):
- **Desktop Sidebar (Kiri)**: Berisi identitas sistem, logo Desa Wisata Hanjeli, navigasi modul utama, serta indikator peran akun aktif.
- **Desktop Header (Atas)**: Berisi pencarian cepat, status latensi jaringan (*Network Health Card*), tombol AI Copilot, toggle bahasa, toggle tema (gelap/terang), notifikasi lonceng (*Alerts Bell*), dan menu profil pengguna.
- **Area Kerja (Tengah)**: Konten reaktif dinamis yang berganti tanpa *full-page reload*.

### 4.2. Antarmuka Mobile & Bottom Navigation (PWA)
Saat diakses dari smartphone atau tablet:
- Sidebar desktop otomatis disembunyikan.
- **Mobile Header (Atas)**: Menampilkan nama modul aktif, status batch ringkas, lonceng notifikasi, dan tombol drawer menu.
- **Mobile Bottom Navigation (Bawah)**: Navigasi cepat 5 tombol yang mudah dijangkau ibu jari:
  1. 🏠 **Beranda**: Ringkasan dashboard & status pengeringan.
  2. 📡 **Monitor**: Grafik live sensor mikroiklim.
  3. 🌾 **Proses**: Detail batch yang sedang aktif atau form batch baru.
  4. 📋 **Riwayat**: Arsip pengeringan yang telah selesai.
  5. ⚙️ **Setelan**: Pengaturan akun & sistem.

### 4.3. Beralih Tema Terang / Gelap (Light / Dark Mode)
- Klik ikon **Matahari / Bulan** di header aplikasi.
- **Tema Terang (Light Mode)**: Menggunakan latar abu-abu kehijauan lembut (`#F4F7F5`) yang sangat nyaman untuk lingkungan luar ruangan dengan intensitas cahaya tinggi.
- **Tema Gelap (Dark Mode)**: Menggunakan latar obsidian hijau tua (`#0B1911`) dengan kontras teks terang, meminimalkan kelelahan mata saat pemantauan malam hari dan menghemat baterai perangkat OLED.

### 4.4. Beralih Bahasa (Bahasa Indonesia / English)
- Klik tombol pemilih bahasa di header (ikon bendera Indonesia 🇮🇩 atau Inggris 🇬🇧).
- Seluruh teks antarmuka, satuan metrik, notifikasi peringatan, dan panduan tombol akan seketika beralih bahasa tanpa memutus koneksi telemetri yang sedang berlangsung.

---

## 5. Panduan Modul Operator

Modul Operator dirancang untuk operasional harian fasilitas greenhouse pengeringan.

### 5.1. Dashboard Utama (`/dashboard`)
Dashboard memberikan gambaran menyeluruh kondisi greenhouse dalam satu pandangan:
1. **Header Cuaca & Lokasi**: Menampilkan prakiraan cuaca real-time di Waluran, Sukabumi (suhu luar, kelembaban, estimasi hujan dari sensor internal & satelit).
2. **Kartu Status Batch Berjalan**: Jika ada batch pengeringan aktif, kartu utama akan menampilkan kode batch, varietas hanjeli, durasi berjalan, persentase progres penurunan kadar air, serta estimasi waktu selesai (*Predictive ETA*).
3. **4 Kartu Metrik Telemetri Utama**:
   - 🌡️ **Suhu Ruang Pengering**: Suhu internal greenhouse (°C).
   - 💧 **Kelembaban Ruang (RH)**: Persentase kelembaban udara ruang (%).
   - ☀️ **Radiasi Sinar Matahari**: Intensitas radiasi surya yang menembus atap polikarbonat ($W/m^2$ atau Lux).
   - 🌾 **Kadar Air Biji Gabah**: Pembacaan probe kelembaban internal biji hanjeli (%).
4. **Ringkasan Cepat Aktuator**: Status aktif/mati Kipas Exhaust, Kipas Intake, Kipas Sirkulasi, dan Pemanas PTC.

---

### 5.2. Live Monitoring Sensor Lingkungan (`/monitoring`)
Halaman pemantauan teknis dengan pembacaan sensor sub-detik:
1. **Interactive Metric Gauges**: Dial visual yang memperlihatkan posisi nilai sensor terhadap zona ambang aman (Zona Hijau: Optimal, Kuning: Waspada, Merah: Kritis).
2. **Multi-Axis Interactive Chart**:
   - Menampilkan grafik gabungan: *Suhu Internal vs Suhu Eksternal*, *Kelembaban Internal vs Eksternal*, *Radiasi Matahari*, dan *Kurva Kadar Air Biji*.
   - **Filter Rentang Waktu**: Pilih tombol **1 Jam**, **6 Jam**, **24 Jam**, atau **7 Hari** untuk menganalisis tren fluktuasi mikroklimat.
   - **Tooltip Rinci**: Arahkan kursor pada titik grafik untuk membaca data spesifik per menit.
3. **Penyusutan Bobot Gabah (Massa)**: Memantau penurunan berat gabah secara bertahap seiring menguapnya kadar air.

---

### 5.3. Kendali Aktuator Cerdas (Kipas & Pemanas PTC)
Komponen kendali aktuator dapat diakses dari dashboard maupun halaman monitoring:

```
┌────────────────────────────────────────────────────────┐
│               PANEL KENDALI AKTUATOR                   │
├────────────────────────────────────────────────────────┤
│  Mode Operasi: [ ● OTOMATIS ]   [   MANUAL  ]          │
│                                                        │
│  1. Kipas Pembuang Lembap (Exhaust Fan)                │
│     Status: [ ON ]     Kecepatan PWM: [ 80% ]          │
│                                                        │
│  2. Kipas Udara Segar (Intake Fan)                     │
│     Status: [ OFF ]    Kecepatan PWM: [ 0% ]           │
│                                                        │
│  3. Kipas Sirkulasi Ruang (Circulation Fan)            │
│     Status: [ ON ]     Kecepatan PWM: [ 100% ]         │
│                                                        │
│  4. Pemanas Bantu Keramik PTC (Auxiliary Heater)       │
│     Saluran 1: [ OFF ]   Saluran 2: [ OFF ]            │
│     Suhu Ruang Saat Ini: 48.5°C (Ambang Cutoff: 55°C)  │
└────────────────────────────────────────────────────────┘
```

#### Cara Mengoperasikan:
1. **Mode OTOMATIS (Direkomendasikan)**:
   - Sistem mikrokontroler & server secara otomatis menyalakan Kipas Exhaust saat kelembaban ruang $> 65\%$ untuk mengeluarkan uap air jenuh.
   - Pemanas PTC otomatis menyala jika suhu ruang turun di bawah $40^\circ\text{C}$ (misal saat hujan atau malam hari).
   - Fitur keamanan (*safety interlock*): Pemanas otomatis diputus (*OFF*) seketika jika suhu menembus $55.0^\circ\text{C} - 60.0^\circ\text{C}$ untuk mencegah biji gosong atau retak.
2. **Mode MANUAL (Override Operator)**:
   - Klik tombol **MANUAL**.
   - Operator dapat menyalakan/mematikan saklar relay masing-masing kipas dan mengatur slider kecepatan kipas ($0\% - 100\%$).
   - Operator dapat mengaktifkan Pemanas Keramik PTC secara mandiri jika diperlukan pemanasan intensif.

---

### 5.4. Memulai Batch Pengeringan Baru (`/drying/new`)
Saat biji hanjeli yang baru dipanen tiba di greenhouse:
1. Buka menu **Mulai Pengeringan** (`/drying/new`).
2. Isi formulir parameter pengeringan:
   - **Kode Batch**: Terisi otomatis oleh sistem dengan pola tanggal dan nomor seri (misal: `HNJ-20261026-001`), namun dapat disesuaikan jika memiliki format penomoran sendiri.
   - **Varietas Hanjeli**: Pilih varietas yang dikeringkan (contoh: *Hanjeli Ketan Sukabumi Grade A*, *Hanjeli Batu*, dll).
   - **Bobot Awal Gabah (kg)**: Masukkan berat total biji basah yang dimasukkan ke rak (misal: `100 kg`).
   - **Kadar Air Awal (%)**: Masukkan hasil pengukuran awal kadar air menggunakan probe kelembaban (biasanya berkisar antara $22\% - 28\%$).
   - **Target Kadar Air Akhir (%)**: Standar optimal adalah `12.0%` (aman simpan jangka panjang dan giling).
   - **Tingkat Rak (Tray Level)**: Pilih lokasi rak pengeringan (Rak 1 Bawah, Rak 2 Tengah, Rak 3 Atas, atau Semua Rak).
   - **Catatan Operasional**: Catatan tambahan (misalnya kondisi cuaca saat panen, asal blok kebun petani).
3. Klik tombol **Mulai Siklus Pengeringan (Start Drying)**.
4. Sistem akan mengunci parameter batch, mengubah status menjadi `ACTIVE`, mencatat stempel waktu mulai (*started_at*), dan mengarahkan Anda ke halaman pemantauan batch aktif.

---

### 5.5. Pemantauan Batch Pengeringan Aktif & Predictive ETA (`/drying/active`)
Halaman ini adalah pusat kerja utama selama pengeringan berlangsung:
- **Indikator Progres Persentase**: Menampilkan persentase pencapaian dari kadar air awal menuju target $12\%$.
- **Estimasi Susut Bobot**: Menghitung secara otomatis berat air yang telah berhasil diuapkan dan proyeksi bobot akhir kering simpan.
- **Prediksi Waktu Selesai (AI Predictive ETA)**:
  - Model algoritma menganalisis laju penurunan kelembaban per jam dipadukan dengan prakiraan radiasi surya jam-jam berikutnya.
  - Memberikan estimasi jam dan hari pengeringan akan rampung (contoh: *"Diperkirakan selesai dalam 5 Jam 20 Menit (Pukul 16:45 WIB)"*).
- **Log Kejadian Batch**: Riwayat perubahan status aktuator dan intervensi operator yang tercatat otomatis selama siklus batch berlangsung.

---

### 5.6. Menjeda, Melanjutkan, dan Menyelesaikan Batch
Pada kartu kontrol batch aktif, tersedia tombol tindakan:
1. **Jeda Pengeringan (Pause)**:
   - Gunakan jika rak sedang dibalik (*turning over/mixing* biji) atau jika pintu greenhouse dibuka untuk inspeksi fisik.
   - Sistem akan menghentikan sementara penghitungan durasi aktif dan mematikan pemanas demi keselamatan operator.
2. **Lanjutkan Pengeringan (Resume)**:
   - Klik setelah proses inspeksi atau pembalikan biji selesai. Kipas dan pemanas akan kembali beroperasi sesuai mode sebelumnya.
3. **Selesaikan Pengeringan (Complete)**:
   - Klik ketika kadar air telah mencapai target ($\le 12\% - 14\%$).
   - Sistem akan meminta konfirmasi kadar air akhir terukur dan bobot akhir riil timbangan.
   - Sistem mengunci data batch, menghitung skor kualitas (*Quality Scoring*), menetapkan grade mutu, dan menyimpan laporan permanen ke database riwayat.

---

### 5.7. Riwayat Pengeringan (History) & Penilaian Mutu (Grade A/B/C) (`/history`)
Semua batch yang telah selesai tersimpan rapi pada modul Riwayat:
- **Tabel Arsip**: Menampilkan kode batch, tanggal proses, varietas, durasi total pengeringan, susut bobot, dan badge grade mutu.
- **Algoritma Penilaian Mutu (Quality Scoring)**:
  Sistem mengevaluasi mutu pengeringan secara objektif dengan rumus komposit:
  $$\text{Skor Mutu} = (S_{\text{kadar air}} \times 45\%) + (S_{\text{stabilitas suhu}} \times 35\%) + (S_{\text{kestabilan RH}} \times 20\%)$$
  - **Grade A (Skor $\ge 85$)**: Kadar air sempurna ($11.5\% - 13.0\%$), suhu stabil tanpa overheat. Sangat direkomendasikan untuk beras hanjeli premium kemasan vakum dan benih unggul.
  - **Grade B (Skor $70 - 84$)**: Pengeringan baik, kadar air aman ($13.1\% - 14.5\%$). Cocok untuk konsumsi pasar lokal atau olahan tepung hanjeli.
  - **Grade C (Skor $< 70$)**: Terdapat fluktuasi ekstrem atau kadar air melebihi batas ideal ($> 14.5\%$). Perlu pengeringan ulang atau segera diproses.

---

### 5.8. Ekspor Laporan (PDF, Excel, CSV)
Pada halaman detail riwayat batch (`/history/:id`):
1. Klik tombol **Ekspor Laporan** di pojok kanan atas.
2. Pilih format dokumen yang diinginkan:
   - 📄 **Cetak / Unduh PDF**: Laporan resmi berformat A4 lengkap dengan kop surat Desa Wisata Hanjeli & CoE STAS-RG, ringkasan batch, grafik kurva telemetri, tabel statistik rata-rata, sertifikat grade mutu, dan kolom tanda tangan penanggung jawab.
   - 📊 **Unduh Excel (.xlsx)**: Seluruh baris telemetri per menit lengkap untuk keperluan olah data statistik atau laporan riset lanjutan.
   - 📑 **Unduh CSV**: Data mentah terpisah koma untuk integrasi dengan software pihak ketiga.

---

### 5.9. Cetak Label Kemasan & Sertifikasi Traceability (QR Code)
Sistem dilengkapi modul Farm-to-Table Certification:
1. Pada detail batch yang telah selesai, klik tombol **Cetak Label QR Kemasan**.
2. Modal pratinjau label stiker kemasan akan terbuka.
3. Label memuat:
   - Logo Hanjeli & STAS-RG
   - Nama Produk & Varietas
   - Kode Unik Batch & Tanggal Panen/Pengeringan
   - Nilai Mutu (Grade A) & Kadar Air Akhir
   - **QR Code Terverifikasi**: Tautan langsung ke laman publik `/verify/:batchCode`.
4. Pilih ukuran kertas (Stiker Label Thermal 100x75mm, A4 Multi-label, dll).
5. Klik **Cetak / Download Stiker**. Tempelkan label pada karung goni atau kemasan pouch beras hanjeli siap jual.

---

### 5.10. Pengaturan Sistem & Saluran Transmisi Tri-Mode (`/settings`)
Halaman Pengaturan menyediakan konfigurasi komprehensif bagi Operator dan Administrator melalui 4 tab navigasi:

#### 1. Tab Perangkat & Transmisi Sensor (`hardware`):
- **Saluran Transmisi Sensor Utama**:
  - **Wi-Fi (MQTT + WebSockets)**: Mode nirkabel utama untuk pengoperasian otomatis jarak jauh via broker cloud / lokal.
  - **Bluetooth Low Energy (BLE)**: Mode nirkabel jarak dekat langsung dari browser ke ESP32 tanpa jaringan WiFi.
  - **Kabel USB Langsung (Web Serial API)**: Mode kabel fisik berkecepatan tinggi (115200 baud) yang terhubung langsung ke port USB komputer kerja greenhouse. Sangat andal saat area perkebunan mengalami gangguan sinyal nirkabel.
- **Kalibrasi Sensor**: Atur offset koreksi suhu internal/eksternal (°C) dan kelembaban relatif (% RH) langsung ke memori mikrokontroler.
- **Konfigurasi Wi-Fi & Broker ESP32**: Perbarui SSID, password hotspot, host broker MQTT, port, serta Device ID.

#### 2. Tab Parameter Pengeringan (`drying`):
- **Batas Toleransi Suhu**: Atur batas suhu maksimum aman (default `55.0°C`) dan batas suhu minimum (default `35.0°C`).
- **Target Kadar Air Standar**: Nilai target default penurunan kelembaban biji (default `12.0%`).
- **Interval Sampling**: Frekuensi perekaman telemetri per detik.
- **Preset Pengeringan Cepat Hanjeli**:
  - *Hanjeli Basah Panen*: Target 12%, Ambang Panas 50°C, Sirkulasi 100%.
  - *Hanjeli Setengah Kering*: Target 12%, Ambang Panas 48°C, Sirkulasi 80%.
  - *Pemeliharaan Stok Gudang*: Target 11.5%, Sirkulasi Periodik.

#### 3. Tab Saluran Notifikasi & Digest (`notifications`):
- **Notifikasi Email SMTP**: Masukkan alamat email penerima peringatan darurat dan laporan berkala.
- **WhatsApp Gateway**: Masukkan token API WhatsApp (Fonnte / Wablas) dan nomor grup pengelola.
- **Bot Telegram**: Masukkan Bot Token dan Chat ID channel alert greenhouse.
- **Tombol Uji Notifikasi**: Klik *Tes Email*, *Tes WhatsApp*, atau *Tes Telegram* untuk memverifikasi sambungan API.
- **Kirim Daily Digest Instan**: Klik tombol *Kirim Ringkasan Harian Sekarang* untuk mendistribusikan rekapitulasi status greenhouse seketika ke seluruh saluran komunikasi.

#### 4. Tab Keamanan Akun & Biometrik (`security`):
- **Ganti Kata Sandi**: Perbarui password akun Anda secara mandiri.
- **Passkeys & Autentikasi Biometrik (WebAuthn)**: Daftarkan sidik jari (*Fingerprint / TouchID*) atau sensor wajah (*FaceID / Windows Hello*) laptop/ponsel Anda untuk login instan tanpa kata sandi.
- **Sesi Aktif**: Tinjau perangkat yang sedang mengakses akun Anda dan cabut sesi yang mencurigakan.

---

## 6. Penggunaan Asisten AI (AI Copilot & Agronomy Assistant)

Aplikasi memiliki asisten cerdas berbasis kecerdasan buatan (*AI Copilot*) yang memahami konteks mikroklimat greenhouse secara *real-time*:

### Cara Menggunakan AI Copilot:
1. Klik tombol **AI Copilot** (ikon bintang berkilau ✦) pada header kanan atas.
2. Drawer interaktif akan bergeser terbuka dari sisi kanan layar.
3. AI Copilot secara otomatis membaca:
   - Kondisi suhu & kelembaban detik ini.
   - Status kipas dan pemanas yang sedang menyala.
   - Tahapan pengeringan batch yang sedang berlangsung.
4. **Ketik Pertanyaan atau Pilih Tombol Rekomendasi Cepat (Quick Actions)**:
   - *"Bagaimana kondisi ruang pengering saat ini? Apakah aman?"*
   - *"Kenapa kelembaban udara sulit turun di bawah 60%?"*
   - *"Apakah pemanas PTC perlu dinyalakan saat ini?"*
   - *"Berapa lama lagi perkiraan pengeringan varietas Hanjeli Ketan ini selesai?"*
5. Asisten AI akan menganalisis data telemetri dan memberikan penjelasan agronomis praktis dalam bahasa yang mudah dimengerti petani.
6. Tersedia tombol aksi cepat (*quick apply*) jika AI menyarankan tindakan korektif (misal: meningkatkan kecepatan exhaust fan).

---

## 7. Panduan Modul Administrator

Modul Administrator khusus diperuntukkan bagi pengguna dengan peran `ADMIN` untuk mengelola infrastruktur, pengguna, dan konfigurasi inti.

### 7.1. Overview Administrator & Health Matrix (`/admin`)
Halaman pengawasan teknis tingkat tinggi:
- **Status Layanan Multi-Service**: Memantau apakah *Web Server (HTTP)*, *WebSocket Daemon (Reverb)*, dan *MQTT Telemetry Ingestion Worker* sedang dalam keadaan aktif (*ONLINE*) atau terputus (*OFFLINE*).
- **Statistik Database**: Jumlah total log telemetri yang tersimpan, ukuran file database, dan utilisasi memori.
- **Log Peringatan Terbaru (Anomaly Detection)**: Daftar deteksi anomali sensor (misal lonjakan suhu tiba-tiba atau sensor terputus).

---

### 7.2. Manajemen Armada Perangkat IoT & 7 Sub-Modul (`/admin/devices`)
Modul terpadu pengawasan keamanan, firmware, sensor, dan aktuator mikrokontroler ESP32 melalui 7 sub-modul:

1. **Sub-Modul 1: Otentikasi Token & Keamanan API (`security`)**:
   - Memantau status validitas header otentikasi `X-Device-Token` dan enkripsi HMAC untuk mencegah spoofing data oleh perangkat tidak dikenal.
   - Klik **Ping Semua Node** untuk menguji ketersediaan seluruh mikrokontroler.
   - Klik **Regenerasi Token** untuk memperbarui kunci rahasia mikrokontroler secara instan.

2. **Sub-Modul 2: Manajemen Firmware OTA (Over-The-Air) (`ota`)**:
   - Melakukan pembaruan firmware mikrokontroler jarak jauh tanpa menyentuh fisik unit di greenhouse.
   - Pilih target node, tentukan versi rilis biner, dan klik **Picukan Pembaruan OTA**.
   - Pantau progress bar persentase flashing firmware secara real-time hingga status `SUCCESS`.

3. **Sub-Modul 3: Pustaka Rilis Firmware (`releases`)**:
   - Repositori biner firmware berekstensi `.bin`.
   - Klik **Unggah Firmware Baru (.bin)** untuk menambahkan versi rilis produksi baru beserta catatan pembaruan (*release changelog*) dan kode checksum SHA-256.

4. **Sub-Modul 4: Matriks Kesehatan Sensor & Latensi (`sensors`)**:
   - Memantau kesehatan masing-masing sensor (DHT22, BH1750, Rain Sensor, Moisture Probe).
   - Memeriksa waktu detak jantung terakhir (*last heartbeat*), rata-rata latensi pulang-pergi (ms), dan jumlah kegagalan pembacaan paket data.

5. **Sub-Modul 5: Kontrol Sakelar Relai & Aktuator Hardware (`actuators`)**:
   - Panel pengujian teknis sakelar relai 4 kanal langsung dari hak akses administrator:
     - Relai 1: Pemanas PTC Saluran 1
     - Relai 2: Pemanas PTC Saluran 2
     - Relai 3: Kipas Pembuang Uap Lembap (Exhaust Fan)
     - Relai 4: Kipas Sirkulasi Ruang (Circulation Fan)

6. **Sub-Modul 6: Katalog Sensor & Hardware Greenhouse (6) (`catalog`)**:
   - Lembar data teknis (*datasheet summary*) untuk 6 perangkat utama di greenhouse: pinout GPIO, protokol bus (I2C/One-Wire/ADC), rentang akurasi ukur, dan rekomendasi pemeliharaan.

7. **Sub-Modul 7: Program Arduino / Source Code ESP32 (2) (`arduino`)**:
   - Menyediakan tampilan kode sumber firmware C++ mikrokontroler secara langsung di peramban:
     - `esp32_smart_dryer_dual_mode.ino`: Firmware utama produksi greenhouse.
     - `esp32_smart_dryer_simulator.ino`: Firmware pengujian dan simulasi laboratorium.
   - Tersedia tombol **Salin Kode** dan **Unduh File .ino** untuk mempermudah teknisi saat flashing via Arduino IDE atau PlatformIO.

---

### 7.3. Integrasi Alat Fisik & Uji Operasional ESP32
Sistem mengadopsi integrasi multi-saluran terpadu:
1. **Verifikasi Sambungan Fisik**: Pastikan unit mikrokontroler ESP32 terhubung ke catu daya 5V/2A dan antena terpasang baik di greenhouse.
2. **Koneksi Telemetri**: Data dibroadcast oleh ESP32 setiap interval sampling ke broker MQTT, atau dialirkan melalui kabel USB Serial jika workstation operator terhubung langsung.
3. **Pemeriksaan Status Aktuator**: Uji relay kipas inlet, exhaust, sirkulator, dan pemanas tambahan langsung melalui kartu kontrol aktuator di Dashboard Operator atau sub-modul aktuator di Admin Devices.
4. **Kalibrasi Sensor**: Pastikan pembacaan sensor SHT31, DS18B20, load cell HX711, dan pyranometer berada dalam rentang toleransi fisik sebelum memulai batch produksi.

---

### 7.4. Manajemen Pengguna & Pengaturan Peran (RBAC) (`/admin/users`)
1. **Melihat Pengguna**: Tabel daftar seluruh pengguna terdaftar, nomor kontak WhatsApp, status verifikasi email, dan peran aktif.
2. **Tambah Pengguna Baru**:
   - Klik **Tambah Pengguna**.
   - Masukkan Nama Lengkap, Username, Email, Nomor WhatsApp, Kata Sandi Sementara, dan Peran (`ADMIN` atau `OPERATOR`).
   - Klik **Simpan Pengguna**.
3. **Ubah Peran (Role)**: Administrator dapat menaikkan atau menurunkan hak akses operator sewaktu-waktu.
4. **Hapus / Nonaktifkan Pengguna**: Blokir akses akun yang sudah tidak bertugas di fasilitas pengeringan.

---

### 7.5. Pengaturan Master Parameter & Ambang Batas (`/admin/parameters` atau `/settings`)
Pengaturan nilai batas toleransi yang mengatur seluruh otomatisasi sistem:
- **Ambang Suhu Aman Maksimum (`max_safe_temp`)**: Default `55.0°C`. Jika suhu melewati angka ini, sistem otomatis mematikan pemanas PTC dan menyalakan kipas exhaust $100\%$ serta mengirim notifikasi bahaya.
- **Ambang Suhu Minimum (`min_safe_temp`)**: Default `35.0°C`. Jika suhu di bawah batas ini saat siang hari, pemanas tambahan dapat dipicu aktif.
- **Target Kadar Air Default (`target_moisture_default`)**: Default `12.0%`.
- **Interval Pengambilan Sampel (`sampling_interval_seconds`)**: Default `5 detik`.
- **Konfigurasi MQTT Broker**: Host (`broker.hivemq.com` atau broker lokal), Port (`1883`), dan root topik.
- **Konfigurasi Notifikasi WhatsApp**: API URL provider (Fonnte/Twilio), API Key, dan nomor tujuan grup petani.
- **Konfigurasi Notifikasi Telegram**: Token Bot Telegram dan Chat ID channel alert.
- **Kirim Uji Coba (Test Notification)**: Klik tombol uji coba untuk memastikan pesan tes terkirim ke Email, WhatsApp, atau Telegram Anda.

---

### 7.6. Log Audit & Notifikasi Multi-Saluran (WA/Telegram/Email) (`/admin/logs`)
- Memeriksa catatan riwayat login, intervensi kendali manual aktuator oleh operator, dan deteksi anomali sensor.
- Fitur **Downsampling Telemetri**: Untuk mencegah database membengkak setelah berbulan-bulan beroperasi, klik tombol **Jalankan Downsample Data Lama** untuk merangkum data rekaman per 5 detik menjadi rata-rata per 10 menit untuk data yang lebih tua dari 30 hari.

---

## 8. Fitur Konektivitas Langsung: Web Bluetooth (BLE) & Web Serial USB

Sistem Smart Room Dryer Hanjeli dilengkapi dengan kemampuan komunikasi hardware langsung dari browser tanpa perantara server luar:

### 8.1. Provisioning Nirkabel via Bluetooth (BLE):
Gunakan ketika mengkonfigurasi ESP32 di lokasi greenhouse menggunakan smartphone atau tablet:
1. Buka peramban berbasis Chromium (Google Chrome / Microsoft Edge) pada perangkat yang memiliki konektivitas Bluetooth aktif.
2. Buka menu **Panduan Hardware** (`/guide`) atau menu **Pengaturan** (`/settings` tab Hardware).
3. Dekati box panel kontrol ESP32 di greenhouse (jarak $< 5\text{ meter}$).
4. Klik tombol **Pindai Perangkat BLE**.
5. Pilih perangkat dengan nama **`SMART-DRYER-PROVISION`** dan pasangkan (*Pair*).
6. Masukkan nama SSID dan kata sandi WiFi greenhouse, lalu klik **Kirim Konfigurasi**. ESP32 akan menyimpan kredensial ke memori internal dan langsung tersambung ke jaringan lokal.

### 8.2. Komunikasi Kabel Langsung via Web Serial API (USB):
Gunakan ketika laptop/komputer stasiun kerja terhubung langsung dengan kabel USB ke mikrokontroler ESP32:
1. Sambungkan kabel data USB dari komputer ke port micro-USB / Type-C board ESP32.
2. Buka menu **Pengaturan** (`/settings` tab Hardware), lalu pilih opsi **Kabel USB Langsung (Web Serial)**.
3. Klik tombol **Hubungkan USB Serial**.
4. Dialog peramban akan memunculkan daftar port serial (misalnya `COM3`, `COM4` pada Windows atau `/dev/ttyUSB0` pada Linux/Mac).
5. Pilih port ESP32 dan klik **Connect**.
6. Sistem akan membuka komunikasi serial berkecepatan **115200 baud**. Data telemetri sensor akan langsung mengalir ke aplikasi secara real-time meskipun greenhouse tidak memiliki sinyal internet sama sekali.

---

## 9. Portal Sertifikasi & Verifikasi Mutu Publik (`/verify`)

Halaman verifikasi publik (`/verify` atau `/verify/:batchCode`) adalah portal keterbukaan informasi mutu produk bagi konsumen, mitra bisnis kuliner, dan auditor mutu pangan:

### 1. Cara Mengakses Portal:
- **Metode QR Code**: Pindai QR Code pada stiker kemasan beras hanjeli menggunakan kamera ponsel pintar. Halaman detail batch terverifikasi akan langsung terbuka seketika.
- **Metode Pencarian Manual**: Akses URL `/verify`, masukkan kode batch (contoh: `HNJ-20261026-001`) pada bilah pencarian, lalu tekan Enter.

### 2. Informasi yang Diverifikasi:
- 🛡️ **Sertifikat Keaslian Farm-to-Table**: Dilengkapi cap dan tanda tangan digital resmi dari **Center of Excellence STAS-RG** dan **Desa Wisata Hanjeli**.
- 🏆 **Penilaian Mutu Objektif (Grade A / B / C)**:
  - Skor komposit mutu (0 - 100).
  - Kesesuaian kadar air akhir terhadap standar aman simpan SNI ($\le 12\% - 14\%$).
- 🥗 **Rincian Fakta Nutrisi Pangan Fungsional**:
  - Kandungan Protein Nabati (~14.1 g per 100g).
  - Kandungan Serat Pangan Alami (~8.4 g per 100g).
  - Karbohidrat Kompleks Indeks Glikemik Rendah (~65.3 g per 100g).
  - Zat Besi & Mineral Esensial.
- 📈 **Kurva Interaktif Kinetika Pengeringan**:
  - Menampilkan riwayat suhu ruang, kelembaban udara, dan radiasi matahari per jam selama hanjeli berada di dalam greenhouse.
  - Membuktikan bahwa biji dikeringkan secara higienis dalam ruang tertutup tanpa polusi jalanan.
- 📍 **Asal Usul Bahan Baku & Petani**:
  - Lokasi blok kebun di Desa Waluran Mandiri, Sukabumi.
  - Tanggal panen dan tanggal penyelesaian pengeringan.

### 3. Cetak & Unduh Dokumen Resmi:
Konsumen atau auditor dapat menekan tombol **Unduh Sertifikat Mutu (PDF)** untuk menyimpan arsip sertifikat resmi berkualitas tinggi untuk keperluan verifikasi pengadaan dan ekspor.

---

## 10. Panduan Instalasi PWA (Mobile Android & iOS)

Aplikasi ini berstandar **Progressive Web App (PWA)** dan dapat diinstal langsung menjadi aplikasi mandiri di layar utama ponsel tanpa melalui Google Play Store atau Apple App Store:

### Pada Android (Google Chrome):
1. Buka URL aplikasi di Google Chrome.
2. Banner notifikasi **"Tambahkan Smart Dryer ke Layar Utama"** akan muncul di bagian bawah layar. Klik **Instal**.
3. Jika banner tidak muncul, klik ikon titik tiga (⋮) di pojok kanan atas Chrome, lalu pilih **Instal Aplikasi (Add to Home screen)**.
4. Ikon aplikasi *Smart Dryer Hanjeli* akan muncul di daftar aplikasi smartphone Anda. Aplikasi kini dapat dibuka dalam mode layar penuh (*standalone*) tanpa bilah navigasi peramban.

### Pada iOS / iPhone (Safari):
1. Buka URL aplikasi menggunakan peramban **Safari**.
2. Klik tombol **Share** (ikon kotak dengan panah mengarah ke atas di bagian bawah).
3. Gulir ke bawah dan pilih **Add to Home Screen (Tambah ke Layar Utama)**.
4. Beri nama aplikasi lalu klik **Add (Tambah)** di pojok kanan atas.

---

## 11. Troubleshooting & Pemecahan Masalah Operasional

| Gejala Masalah | Kemungkinan Penyebab | Langkah Solusi Perbaikan |
| :--- | :--- | :--- |
| **Nilai sensor di dashboard tidak bergerak / data statis** | Daemon MQTT Ingestion Worker di server sedang berhenti. | Buka terminal server dan jalankan: `php artisan greenhouse:mqtt-worker`. Periksa apakah terminal menampilkan log data masuk dari ESP32. |
| **Status koneksi WebSocket menampilkan "Disconnected"** | Server Laravel Reverb belum berjalan atau port 8080 terblokir firewall. | Jalankan `php artisan reverb:start`. Pastikan konfigurasi `REVERB_HOST` dan `REVERB_PORT=8080` pada file `.env` telah sesuai. |
| **Kipas Exhaust tidak menyala meski kelembaban tinggi** | Sistem berada dalam mode **MANUAL** dengan saklar mati, atau kabel relay longgar. | 1. Periksa panel aktuator di dashboard, pastikan mode berada di **OTOMATIS**.<br>2. Cek fisik lampu indikator LED pada modul relay di panel box greenhouse. Jika LED relay menyala tapi kipas mati, periksa sekring (*fuse*) atau sumber daya motor 12V/220V. |
| **Pemanas PTC tidak mau menyala sama sekali** | Sensor mendeteksi suhu ruang sudah $\ge 55^\circ\text{C}$ (*safety cut-off*) atau sensor DHT22 mengalami error pembacaan. | Pemanas memiliki proteksi otomatis. Cek apakah sensor DHT22 membaca suhu secara normal. Jika pembacaan `NaN` atau `0°C`, ganti kabel sensor DHT22. |
| **Pindai Bluetooth (BLE) tidak menemukan perangkat ESP32** | 1. Bluetooth ponsel belum diaktifkan.<br>2. Jarak terlalu jauh ($> 5\text{ meter}$).<br>3. Peramban bukan Google Chrome / Edge. | Aktifkan Bluetooth dan GPS/Lokasi di ponsel. Dekatkan ponsel ke box ESP32. Pastikan menggunakan browser berbasis Chromium (Web Bluetooth API saat ini belum didukung penuh oleh Mozilla Firefox). |
| **Lupa kata sandi dan email OTP tidak kunjung masuk** | Konfigurasi SMTP email pada `.env` belum diatur atau masuk ke folder Spam. | 1. Cek folder *Spam / Promosi* di aplikasi email Anda.<br>2. Hubungi Administrator sistem untuk mereset kata sandi Anda secara manual melalui menu `/admin/users`. |
| **Muncul pesan peringatan "Thermal Paradox Detected"** | Pemanas PTC terdeteksi aktif menyala, namun suhu ruang pengering justru anjlok drastis. | 1. Periksa apakah pintu greenhouse terbuka lebar saat cuaca dingin/hujan.<br>2. Periksa apakah elemen pemanas PTC rusak atau kabel daya pemanas terputus. |

---

*Dokumentasi ini disusun oleh Tim CoE STAS-RG bersama Pengelola Fasilitas Smart Room Dryer Hanjeli. Jika membutuhkan asistensi teknis lebih lanjut, hubungi kontak administrator di `admin@hanjeli.id`.*
