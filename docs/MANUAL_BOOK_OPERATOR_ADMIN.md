# 📘 BUKU PANDUAN PENGGUNAAN KHUSUS OPERATOR & ADMINISTRATOR
## Smart Room Dryer Hanjeli — IoT Greenhouse Monitoring & Control System

> **Klasifikasi Dokumen**: User Guide & Operational Manual Khusus Peran **OPERATOR** dan **ADMINISTRATOR**  
> **Versi Dokumen**: v1.0 (Panduan Operasional Lengkap)  
> **Lokasi Implementasi**: Fasilitas Greenhouse Cerdas Desa Wisata Hanjeli, Waluran Mandiri, Kab. Sukabumi, Jawa Barat  
> **Pusat Riset**: Center of Excellence Smart Telecommunication, Automation & System - Research Group (**CoE STAS-RG**)  
> **Akses Sistem**: [https://hanjeli-dryer.stas-rg.com](https://hanjeli-dryer.stas-rg.com) (atau `http://localhost:8000` di workstation lokal)

---

## 📋 DAFTAR ISI PANDUAN

1. [RINGKASAN PERAN, KREDENSIAL & AKSES MASUK](#1-ringkasan-peran-kredensial--akses-masuk)
   - 1.1. [Perbedaan Peran Operator vs Administrator](#11-perbedaan-peran-operator-vs-administrator)
   - 1.2. [Kredensial Akun Bawaan (Default Credentials)](#12-kredensial-akun-bawaan-default-credentials)
   - 1.3. [Panduan Masuk Sistem & Pemulihan Akun](#13-panduan-masuk-sistem--pemulihan-akun)
2. [PETA MENU & NAVIGASI SISTEM](#2-peta-menu--navigasi-sistem)
   - 2.1. [Daftar Menu untuk Operator](#21-daftar-menu-untuk-operator)
   - 2.2. [Daftar Menu Tambahan Khusus Administrator](#22-daftar-menu-tambahan-khusus-administrator)
3. [RINGKASAN FITUR-FITUR UNGGULAN APLIKASI](#3-ringkasan-fitur-fitur-unggulan-aplikasi)
4. [BAGIAN I: PANDUAN LENGKAP PENGGUNAAN UNTUK OPERATOR](#bagian-i-panduan-lengkap-penggunaan-untuk-operator)
   - 4.1. [Menu 1: Dashboard Utama (`/dashboard`)](#41-menu-1-dashboard-utama-dashboard)
   - 4.2. [Menu 2: Live Monitoring Sensor Lingkungan (`/monitoring`)](#42-menu-2-live-monitoring-sensor-lingkungan-monitoring)
   - 4.3. [Menu 3: Memulai Batch Pengeringan Baru (`/drying/new`)](#43-menu-3-memulai-batch-pengeringan-baru-dryingnew)
   - 4.4. [Menu 4: Pengeringan Aktif & AI Predictive ETA (`/drying/active`)](#44-menu-4-pengeringan-aktif--ai-predictive-eta-dryingactive)
   - 4.5. [Pengoperasian Panel Kendali Aktuator (Kipas & Pemanas PTC)](#45-pengoperasian-panel-kendali-aktuator-kipas--pemanas-ptc)
   - 4.6. [Prosedur Jeda, Lanjutkan, dan Selesaikan Batch](#46-prosedur-jeda-lanjutkan-dan-selesaikan-batch)
   - 4.7. [Menu 5: Riwayat Pengeringan & Analisis Mutu (`/history`)](#47-menu-5-riwayat-pengeringan--analisis-mutu-history)
   - 4.8. [Menu 6: Ekspor Laporan Resmi (PDF, Excel, CSV) (`/history/:id`)](#48-menu-6-ekspor-laporan-resmi-pdf-excel-csv-historyid)
   - 4.9. [Menu 7: Cetak Stiker Label QR Code Kemasan](#49-menu-7-cetak-stiker-label-qr-code-kemasan)
   - 4.10. [Menu 8: Panduan Hardware & SOP Pascapanen (`/guide`)](#410-menu-8-panduan-hardware--sop-pascapanen-guide)
   - 4.11. [Menu 9: Pengaturan Sistem Operator (`/settings`)](#411-menu-9-pengaturan-sistem-operator-settings)
   - 4.12. [Penggunaan Asisten Cerdas AI Copilot](#412-penggunaan-asisten-cerdas-ai-copilot)
5. [BAGIAN II: PANDUAN LENGKAP PENGGUNAAN UNTUK ADMINISTRATOR](#bagian-ii-panduan-lengkap-penggunaan-untuk-administrator)
   - 5.1. [Menu 1: Ringkasan Sistem & Health Matrix (`/admin`)](#51-menu-1-ringkasan-sistem--health-matrix-admin)
   - 5.2. [Menu 2: Pusat Armada IoT & 7 Sub-Modul Perangkat (`/admin/devices`)](#52-menu-2-pusat-armada-iot--7-sub-modul-perangkat-admindevices)
     - 5.2.1. [Sub-Modul 1: Otentikasi Token & Ping Node (`security`)](#521-sub-modul-1-otentikasi-token--ping-node-security)
     - 5.2.2. [Sub-Modul 2: Pembaruan Firmware OTA (`ota`)](#522-sub-modul-2-pembaruan-firmware-ota-ota)
     - 5.2.3. [Sub-Modul 3: Pustaka Rilis Biner Firmware (`releases`)](#523-sub-modul-3-pustaka-rilis-biner-firmware-releases)
     - 5.2.4. [Sub-Modul 4: Matriks Kesehatan Sensor & Latensi (`sensors`)](#524-sub-modul-4-matriks-kesehatan-sensor--latensi-sensors)
     - 5.2.5. [Sub-Modul 5: Uji Fisik Sakelar Relai 4 Kanal (`actuators`)](#525-sub-modul-5-uji-fisik-sakelar-relai-4-kanal-actuators)
     - 5.2.6. [Sub-Modul 6: Katalog Hardware & Spesifikasi GPIO (`catalog`)](#526-sub-modul-6-katalog-hardware--spesifikasi-gpio-catalog)
     - 5.2.7. [Sub-Modul 7: Penampil Kode Sumber Arduino ESP32 (`arduino`)](#527-sub-modul-7-penampil-kode-sumber-arduino-esp32-arduino)
   - 5.3. [Menu 3: Manajemen Pengguna & Hak Akses (`/admin/users`)](#53-menu-3-manajemen-pengguna--hak-akses-adminusers)
   - 5.4. [Menu 4: Pengaturan Master Parameter Sistem (`/admin/parameters`)](#54-menu-4-pengaturan-master-parameter-sistem-adminparameters)
   - 5.5. [Menu 5: Log Audit Trail & Downsample Data Telemetri (`/admin/logs`)](#55-menu-5-log-audit-trail--downsample-data-telemetri-adminlogs)
6. [TIPS OPERASIONAL & CHECKLIST HARIAN](#6-tips-operasional--checklist-harian)
7. [PANDUAN TROUBLESHOOTING & PEMULIHAN CEPAT](#7-panduan-troubleshooting--pemulihan-cepat)

---

## 1. RINGKASAN PERAN, KREDENSIAL & AKSES MASUK

### 1.1. Perbedaan Peran Operator vs Administrator

Sistem membedakan tanggung jawab antara tugas operasional rutin greenhouse dengan tugas administrasi teknis:

```
┌──────────────────────────────────────────────┬──────────────────────────────────────────────┐
│             PERAN: OPERATOR                  │            PERAN: ADMINISTRATOR              │
├──────────────────────────────────────────────┼──────────────────────────────────────────────┤
│ • Pengawasan harian suhu, RH, radiasi surya  │ • Memiliki SELURUH hak akses Operator        │
│ • Input batch pengeringan baru & target air  │ • Pengawasan kesehatan daemon server & IoT   │
│ • Kontrol aktuator otomatis & intervensi manual│ • Manajemen akun pengguna & reset sandi    │
│ • Pemantauan AI ETA sisa waktu pengeringan   │ • Flashing firmware OTA jarak jauh via WiFi │
│ • Ekspor laporan PDF A4 bertanda tangan      │ • Pengujian sakelar relai & hardware catalog │
│ • Cetak stiker QR Code traceability kemasan  │ • Pengaturan parameter master sistem & MQTT  │
│ • Kalibrasi sensor & preset pengeringan      │ • Audit log trail & downsampling database    │
└──────────────────────────────────────────────┴──────────────────────────────────────────────┘
```

---

### 1.2. Kredensial Akun Bawaan (Default Credentials)

Gunakan akun berikut untuk masuk ke dalam sistem:

| Peran | Nama Tampilan | Alamat Email | Kata Sandi Bawaan | No. WhatsApp | Halaman Tujuan Awal |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **`OPERATOR`** | Operator Greenhouse | `operator@hanjeli.id` | `operator123` | `089876543210` | `/dashboard` |
| **`ADMIN`** | Admin Smart Dryer | `admin@hanjeli.id` | `admin123` | `081234567890` | `/admin` |
| **`PETANI`** *(Demo)*| Petani Hanjeli Waluran | `petani@hanjeli.id` | `petani123` | `081345678901` | `/dashboard` |

> [!IMPORTANT]
> Begitu berhasil login pertama kali, sangat disarankan segera memperbarui kata sandi Anda melalui menu **Pengaturan** (`/settings`) pada tab **Keamanan & Passkey**.

---

### 1.3. Panduan Masuk Sistem, Pendaftaran Akun & Persetujuan (ACC) Admin

1. **Pendaftaran Akun Mandiri (Registrasi Operator)**:
   - Pada halaman login, klik **Belum punya akun? Daftar Sekarang**.
   - Masukkan Nama Lengkap, Username unik, Alamat Email, Nomor WhatsApp aktif, serta Kata Sandi.
   - Klik **Daftar Akun**.
   - > [!IMPORTANT]
     > **Aturan Keamanan Sistem**: Seluruh akun baru yang mendaftar mandiri otomatis berstatus default sebagai **OPERATOR** dengan status verifikasi **`PENDING` (Menunggu ACC Administrator)**. Akun belum dapat langsung digunakan untuk masuk demi mencegah akses ilegal ke kontrol aktuator greenhouse. Administrator akan memverifikasi identitas Anda dan memberikan persetujuan (ACC).

2. **Masuk Standar (Email & Sandi)**:
   - Akses alamat sistem melalui peramban: `https://hanjeli-dryer.stas-rg.com/login` (atau `http://localhost:8000/login`).
   - Masukkan Email dan Kata Sandi terdaftar.
   - Centang **Ingat Saya** untuk menyimpan sesi di komputer kerja pribadi.
   - Klik **Masuk ke Sistem**.
   - *Catatan*: Jika akun belum di-ACC oleh Admin, sistem akan menampilkan peringatan: *"Akun Anda sedang menunggu persetujuan (ACC) dari Administrator sebelum dapat digunakan. Silakan hubungi admin greenhouse."*

3. **Masuk 1-Klik dengan Akun Google (SSO)**:
   - Pada halaman login, klik tombol **Lanjutkan dengan Google**.
   - Pilih akun Google Anda. Jika akun baru pertama kali menggunakan Google SSO, akun otomatis dibuat sebagai Operator berstatus `PENDING` dan menunggu persetujuan (ACC) dari Administrator.
   - Jika akun Google Anda sudah disetujui sebelumnya, Anda akan langsung dialihkan ke dashboard.

4. **Masuk Biometrik Modern (Passkeys / Sidik Jari / Wajah)**:
   - Jika Anda telah mendaftarkan perangkat di menu `/settings`, cukup klik **Masuk Cepat dengan Passkey / Biometrik**.
   - Sentuh sensor sidik jari perangkat atau hadapkan wajah ke kamera (FaceID/Windows Hello). Sistem akan langsung terotentikasi dalam $< 1\text{ detik}$.
   - *Catatan*: Hanya akun yang telah berstatus `APPROVED` yang diizinkan mengautentikasi Passkey.

5. **Pemulihan Lupa Sandi dengan Kode OTP Email (6 Digit)**:
   - Klik tautan **Lupa Kata Sandi?** di halaman login (`/forgot-password`).
   - Masukkan email akun Anda, lalu klik **Kirim Kode Verifikasi**.
   - Buka inbox email Anda, temukan pesan resmi berisi **6 Digit Kode OTP** (berlaku 10 menit).
   - Masukkan 6 digit kode OTP pada layar, tentukan kata sandi baru Anda, lalu klik **Simpan Kata Sandi Baru**.

---

## 2. PETA MENU & NAVIGASI SISTEM

### 2.1. Daftar Menu untuk Operator

Setelah login sebagai **OPERATOR**, sidebar di desktop atau bottom navigation di smartphone menyediakan menu kerja berikut:

```
NAVIGASI OPERATOR:
├── 🏠 Dashboard Utama          --> /dashboard
├── 📡 Live Monitoring Sensor    --> /monitoring
├── ➕ Mulai Pengeringan Baru    --> /drying/new
├── 🌾 Pengeringan Aktif & ETA   --> /drying/active
├── 📋 Riwayat Pengeringan       --> /history
│   └── 📄 Detail Riwayat & PDF  --> /history/:id
├── 📖 Panduan Hardware & SOP    --> /guide
└── ⚙️ Pengaturan Sistem         --> /settings
    ├── Tab Hardware & Saluran Transmisi
    ├── Tab Parameter Pengeringan
    ├── Tab Saluran Notifikasi (WA/Telegram/Email)
    └── Tab Keamanan Akun & Passkey
```

---

### 2.2. Daftar Menu Tambahan Khusus Administrator

Pengguna dengan peran **ADMIN** memiliki akses ke semua menu Operator di atas, **DITAMBAH** menu panel kontrol administrasi:

```
NAVIGASI ADMINISTRATOR:
├── 📊 Overview & Health Matrix  --> /admin
├── 🛰️ Armada Perangkat IoT      --> /admin/devices
│   ├── 1. Otentikasi Token & Ping Node    (Tab Security)
│   ├── 2. Manajemen Pembaruan OTA          (Tab OTA)
│   ├── 3. Pustaka Rilis Biner Firmware    (Tab Releases)
│   ├── 4. Matriks Kesehatan Sensor         (Tab Sensors)
│   ├── 5. Uji Coba Sakelar Relai 4 Kanal  (Tab Actuators)
│   ├── 6. Katalog Hardware & Pinout GPIO  (Tab Catalog)
│   └── 7. Kode Sumber Arduino & Firmware   (Tab Arduino)
├── 👥 Manajemen Pengguna        --> /admin/users
├── ⚙️ Pengaturan Parameter      --> /admin/parameters
└── 📜 Log Audit & Downsample    --> /admin/logs
```

---

## 3. RINGKASAN FITUR-FITUR UNGGULAN APLIKASI

Aplikasi Smart Room Dryer Hanjeli dilengkapi dengan 12 fitur cerdas:

1. **Telemetri Real-Time Sub-Detik**: Streaming data sensor mikroklimat tanpa refresh halaman via WebSocket native Laravel Reverb (port 8080).
2. **Konektivitas Tri-Mode Transmisi**: Mendukung 3 jalur transmisi fleksibel:
   - **Mode WiFi MQTT**: Transmisi otomatis jarak jauh untuk monitoring cloud.
   - **Mode Bluetooth Low Energy (BLE)**: Konfigurasi jaringan ESP32 langsung dari browser ponsel di area greenhouse tanpa router.
   - **Mode USB Serial Langsung (115200 baud)**: Komunikasi kabel langsung ke PC workstation operator jika jaringan internet mati.
3. **Kendali Aktuator Cerdas (Smart Hysteresis)**: Otomasi cerdas kipas pembuang lembap (*exhaust*), kipas sirkulasi, dan pemanas bantu keramik PTC.
4. **Proteksi Pemutus Darurat (*Safety Interlock Cut-off*)**: Pemutus otomatis arus listrik pemanas jika suhu menyentuh $\ge 50^\circ\text{C} - 55^\circ\text{C}$ untuk mencegah biji terbakar.
5. **AI Predictive Duration & ETA**: Algoritma cerdas yang memprediksi jam dan hari selesainya pengeringan berdasarkan kinetika penguapan dan radiasi matahari.
6. **AI Copilot & Agronomy Assistant**: Asisten pintar berbasis kecerdasan buatan di sisi samping antarmuka untuk konsultasi mikroklimat dan rekomendasi aksi satu-klik.
7. **Sistem Penilaian Mutu Objektif (*Quality Scoring*)**: Evaluasi otomatis mutu hasil pengeringan menjadi **Grade A (Istimewa)**, **Grade B (Standar)**, atau **Grade C**.
8. **Ekspor Laporan Multi-Format**: Ekspor dokumen resmi berformat **PDF A4** bertanda tangan dan kop surat resmi, lembar kerja **Excel (.xlsx)**, dan berkas **CSV**.
9. **Generator Stiker Label Thermal QR Code**: Cetak label kemasan beras hanjeli siap tempel berukuran thermal 100x75mm.
10. **Portal Keterlacakan Publik (*Farm-to-Table Traceability*)**: Portal `/verify/:batchCode` terbuka untuk konsumen melihat kurva pengeringan dan sertifikat keaslian produk.
11. **Pemberitahuan Multi-Saluran**: Notifikasi bahaya dan ringkasan harian (*Daily Digest*) ke WhatsApp (Fonnte/Wablas), Telegram Bot, dan Email SMTP.
12. **Progressive Web App (PWA)**: Aplikasi dapat diinstal di smartphone Android/iOS layaknya aplikasi native dengan dukungan penyimpanan offline (*offline caching*).

---

## BAGIAN I: PANDUAN LENGKAP PENGGUNAAN UNTUK OPERATOR

Bagian ini memandu operator greenhouse dalam menjalankan operasional harian langkah demi langkah.

---

### 4.1. Menu 1: Dashboard Utama (`/dashboard`)

Dashboard adalah halaman utama yang pertama kali dilihat operator saat membuka sistem.

```
┌─────────────────────────────────────────────────────────────────────────────┐
│ 🌤️ CUACA WALURAN: 28°C • Berawan Sebagian • Kelembaban Udara Luar 72%       │
├─────────────────────────────────────────────────────────────────────────────┤
│ 🌾 STATUS BATCH: [ HNJ-20261029-001 ] Varietas: Hanjeli Ketan Sukabumi      │
│    Progres: [▓▓▓▓▓▓▓▓▓▓▓▓▓░░░░░] 65% | ETA: Hari ini 16:15 WIB (Sisa 3.5 jam)│
├─────────────────────────────────────────────────────────────────────────────┤
│ 4 KARTU TELEMETRI UTAMA:                                                    │
│ ┌────────────────┐ ┌────────────────┐ ┌────────────────┐ ┌────────────────┐ │
│ │ 🌡️ SUHU RUANG   │ │ 💧 KELEMBABAN  │ │ ☀️ RADIASI LUX │ │ 🌾 KADAR AIR   │ │
│ │    42.5 °C     │ │    54.2 %RH    │ │    680 W/m²    │ │    13.4 %      │ │
│ │ Optimal (38-48)│ │ Aman (40-60%)  │ │ Sinar Terik    │ │ Target: 12.0%  │ │
│ └────────────────┘ └────────────────┘ └────────────────┘ └────────────────┘ │
├─────────────────────────────────────────────────────────────────────────────┤
│ ⚙️ STATUS AKTUATOR CEPAT:                                                    │
│    • Kipas Exhaust   : [ AKTIF 80% ]        • Kipas Sirkulasi: [ AKTIF 100% ]│
│    • Kipas Intake    : [ NONAKTIF ]         • Pemanas PTC    : [ STANDBY ]   │
└─────────────────────────────────────────────────────────────────────────────┘
```

#### Komponen yang Harus Diperhatikan Operator:
1. **Prakiraan Cuaca Lokal**: Memantau potensi hujan di wilayah Waluran agar operator bersiap mengantisipasi kelembaban tinggi.
2. **Kartu Status Batch**: Menampilkan kode batch yang sedang berjalan beserta persentase capaian kadar air dan sisa waktu pengeringan.
3. **4 Kartu Metrik Utama**:
   - **Suhu Ruang Pengering**: Suhu di dalam ruangan. Zona aman adalah $38^\circ\text{C} - 50^\circ\text{C}$.
   - **Kelembaban Udara (RH)**: Persentase uap air ruang. Jika $> 60\%$, kipas exhaust wajib aktif membuang uap.
   - **Radiasi Surya**: Intensitas sinar matahari yang menembus atap polikarbonat.
   - **Kadar Air Biji Gabah**: Kelembaban internal biji hanjeli. Pengeringan dinyatakan tuntas jika angka ini $\le 12.0\%$.
4. **Ringkasan Aktuator**: Memastikan semua kipas dan pemanas bekerja sesuai instruksi sistem.

---

### 4.2. Menu 2: Live Monitoring Sensor Lingkungan (`/monitoring`)

Gunakan halaman ini untuk inspeksi teknis mendalam:
1. **Interactive Gauges (Dial Pengukur)**:
   - Jarum visual menunjukkan posisi pembacaan terhadap zona ambang:
     - **Hijau (Optimal)**: Parameter berada dalam kondisi ideal pengeringan.
     - **Kuning (Waspada)**: Parameter mendekati batas toleransi.
     - **Merah (Kritis)**: Terjadi anomali (misal suhu $> 55^\circ\text{C}$ atau sensor error).
2. **Grafik Multi-Axis Interaktif**:
   - Menampilkan 4 kurva secara simultan: *Suhu Internal vs Eksternal*, *Kelembaban Internal vs Eksternal*, *Radiasi Cahaya*, dan *Kadar Air Biji*.
   - **Filter Waktu**: Klik tombol **1 Jam**, **6 Jam**, **24 Jam**, atau **7 Hari** untuk melihat perbandingan tren siang dan malam.
   - Arahkan kursor (*hover*) pada grafik untuk melihat pembacaan spesifik per menit.
3. **Pemantauan Susut Bobot Gabah (Load Cell)**:
   - Memantau penurunan berat gabah seiring menguapnya air untuk mengonfirmasi bahwa pengeringan merata.

---

### 4.3. Menu 3: Memulai Batch Pengeringan Baru (`/drying/new`)

Lakukan langkah ini setiap kali gabah basah baru dipanen dan dimasukkan ke greenhouse:

```
LANGKAH MEMULAI BATCH BARU:
1. Buka menu "/drying/new"
2. Sistem otomatis membuat Kode Batch unik (misal: HNJ-20261029-001)
3. Pilih Varietas Hanjeli (contoh: Hanjeli Ketan Sukabumi Grade A)
4. Masukkan Bobot Awal Gabah Basah (contoh: 120 kg)
5. Masukkan Hasil Ukur Kadar Air Awal (contoh: 24.5%)
6. Tentukan Target Kadar Air Akhir (Standar aman: 12.0%)
7. Pilih Posisi Baki Rak (Rak 1 Bawah, Rak 2 Tengah, Rak 3 Atas, atau Semua Rak)
8. Isi Catatan Operasional (contoh: "Panen Blok Barat, cuaca pagi cerah")
9. Klik tombol hijau "Mulai Siklus Pengeringan (Start Drying)"
```

Setelah tombol diklik:
- Status siklus berubah menjadi `ACTIVE`.
- Waktu mulai (*started_at*) terkunci permanen.
- Sistem otomatis mengarahkan operator ke halaman pemantauan batch aktif.

---

### 4.4. Menu 4: Pengeringan Aktif & AI Predictive ETA (`/drying/active`)

Pusat kendali selama gabah berada di dalam ruangan pengering:
- **Indikator Progres**: Menghitung persentase penurunan kadar air dari awal menuju target $12\%$.
- **AI Predictive Duration & ETA**:
  - Algoritma AI menganalisis laju penurunan kadar air per jam dipadukan dengan data radiasi surya.
  - Memberikan estimasi waktu selesai yang akurat (misal: *"Selesai dalam 4 Jam 10 Menit — Pukul 16:40 WIB"*).
- **Log Peristiwa Batch**: Mencatat riwayat setiap kali pemanas menyala, kipas dinyalakan, atau operator melakukan penyesuaian.

---

### 4.5. Pengoperasian Panel Kendali Aktuator (Kipas & Pemanas PTC)

Panel aktuator dapat diakses dari halaman `/dashboard` maupun `/drying/active`:

#### Mode 1: Mode OTOMATIS (Direkomendasikan untuk Operasional Standar)
- Klik tombol saklar pada posisi **OTOMATIS**.
- Sistem menjalankan otomasi histeresis:
  - **Kipas Exhaust**: Otomatis menyala jika kelembaban ruang $> 60\%$ untuk menyedot uap air jenuh ke luar.
  - **Kipas Sirkulasi**: Berputar terus menerus untuk meratakan panas di semua rak baki.
  - **Pemanas Keramik PTC**: Otomatis menyala saat suhu turun di bawah $38^\circ\text{C}$ (misal malam hari atau saat hujan).
  - **Safety Interlock**: Pemanas **otomatis dimatikan seketika** jika suhu ruang mencapai $\ge 50^\circ\text{C} - 55^\circ\text{C}$ demi mencegah kerusakan nutrisi gabah.

#### Mode 2: Mode MANUAL (Khusus Keperluan Uji Coba atau Kondisi Darurat)
- Klik saklar pada posisi **MANUAL**.
- Operator memiliki kendali penuh:
  - Menghidupkan/mematikan tombol Kipas Exhaust dan mengatur slider kecepatannya ($0\% - 100\%$).
  - Menghidupkan/mematikan Kipas Intake dan Kipas Sirkulasi.
  - Menghidupkan/mematikan Pemanas PTC Saluran 1 dan Saluran 2 secara mandiri.
  - *Catatan*: Fitur keselamatan suhu $\ge 55^\circ\text{C}$ tetap aktif sebagai pelindung mutlak perangkat keras.

---

### 4.6. Prosedur Jeda, Lanjutkan, dan Selesaikan Batch

Pada kartu kendali batch aktif, terdapat 3 tombol aksi utama:

1. **Jeda Pengeringan (Tombol *Pause*)**:
   - **Kapan Digunakan**: Gunakan setiap kali operator membuka pintu greenhouse untuk **membalik / mengaduk biji di rak baki** atau saat inspeksi fisik.
   - **Tindakan Sistem**: Sistem menghentikan perhitungan durasi efektif dan mematikan pemanas cadangan demi keamanan operator.
2. **Lanjutkan Pengeringan (Tombol *Resume*)**:
   - **Kapan Digunakan**: Ditekan setelah pembalikan biji selesai dan pintu greenhouse ditutup rapat kembali.
   - **Tindakan Sistem**: Aktuator kipas dan pemanas kembali bekerja sesuai mode yang aktif.
3. **Selesaikan Pengeringan (Tombol *Complete*)**:
   - **Kapan Digunakan**: Ditekan ketika kadar air biji terukur telah mencapai target ($\le 12.0\%$).
   - **Langkah-langkah**:
     1. Klik tombol **Selesaikan Pengeringan**.
     2. Dialog konfirmasi akan muncul. Masukkan **Kadar Air Akhir Riil** (misal `11.8%`) dan **Bobot Akhir Timbangan** (misal `98.5 kg`).
     3. Klik **Konfirmasi Selesai**.
     4. Sistem otomatis mengunci status batch menjadi `COMPLETED`, menghitung skor kualitas (*Quality Score*), menetapkan predikat grade mutu, dan menyimpannya ke menu Riwayat.

---

### 4.7. Menu 5: Riwayat Pengeringan & Analisis Mutu (`/history`)

Menu ini mengarsipkan seluruh batch pengeringan yang telah rampung:
- **Tabel Arsip**: Menampilkan Kode Batch, Tanggal Proses, Varietas, Durasi Pengeringan, Total Susut Massa, dan Predikat Grade Mutu.
- **Pencarian Cepat**: Cari batch berdasarkan kode atau varietas.
- **Formula Penilaian Mutu (Quality Scoring)**:
  $$\text{Skor Mutu} = (S_{\text{kadar air}} \times 45\%) + (S_{\text{stabilitas suhu}} \times 35\%) + (S_{\text{kestabilan RH}} \times 20\%)$$
  - **Grade A (Skor $\ge 85$)**: Kadar air sempurna ($11.5\% - 13.0\%$), suhu stabil tanpa overheat. Sangat direkomendasikan untuk benih unggul bersertifikat dan beras premium kemasan vakum.
  - **Grade B (Skor $70 - 84$)**: Pengeringan baik, kadar air aman simpan ($13.1\% - 14.5\%$). Cocok untuk pasar lokal atau olahan tepung.
  - **Grade C (Skor $< 70$)**: Mengalami fluktuasi cuaca ekstrem atau kadar air $> 14.5\%$. Perlu dikeringkan ulang.

---

### 4.8. Menu 6: Ekspor Laporan Resmi (PDF, Excel, CSV) (`/history/:id`)

Buka salah satu riwayat batch untuk mencetak atau mengunduh dokumen resmi:
1. **Laporan Resmi Berformat PDF (A4)**:
   - Klik tombol **Cetak / Unduh PDF**.
   - Berkas PDF beresolusi tinggi akan di-generate lengkap dengan:
     - Kop surat resmi Desa Wisata Hanjeli & CoE STAS-RG.
     - Ringkasan parameter awal dan akhir gabah.
     - Grafik kinetika pengeringan suhu dan kelembaban.
     - Sertifikat predikat Grade Mutu dan stempel digital.
     - Kolom tanda tangan penanggung jawab greenhouse.
2. **Lembar Data Excel (.xlsx)**: Klik **Unduh Excel** untuk mendapatkan berkas data per menit untuk laporan statistik ke dinas pertanian.
3. **Berkas CSV**: Klik **Unduh CSV** untuk integrasi dengan software pengolahan data lain.

---

### 4.9. Menu 7: Cetak Stiker Label QR Code Kemasan

Untuk mendukung penjualan beras hanjeli kemasan premium dengan jaminan keaslian pangan:
1. Pada halaman detail riwayat batch (`/history/:id`), klik tombol **Cetak Label QR Kemasan**.
2. Jendela pratinjau stiker kemasan akan muncul:
   - Memuat Logo Hanjeli & STAS-RG.
   - Nama Produk & Varietas Hanjeli.
   - Kode Batch Unik & Tanggal Panen.
   - Nilai Mutu (Grade A) & Kadar Air Akhir.
   - **QR Code Terverifikasi**: Tautan publik menuju `/verify/:batchCode`.
3. Pilih ukuran kertas (Stiker Thermal 100x75mm atau kertas biasa).
4. Klik **Cetak / Download Stiker**, lalu tempelkan pada karung goni atau kemasan pouch beras hanjeli.

---

### 4.10. Menu 8: Panduan Hardware & SOP Pascapanen (`/guide`)

Halaman edukasi dan referensi lapangan:
- **Diagram Skematik Pinout ESP32**: Nomor pin sensor DHT22 (GPIO 25), BH1750 (I2C GPIO 21/22), sensor hujan (ADC GPIO 34), dan relay (GPIO 26/27).
- **SOP Pascapanen**: Pedoman lengkap cara pemanenan, perontokan malai, sortasi kotoran, penghamparan di baki rak (maksimal ketebalan $3 - 5\text{ cm}$), pembalikan rutin, hingga penyimpanan di wadah kedap udara.

---

### 4.11. Menu 9: Pengaturan Sistem Operator (`/settings`)

Menu pengaturan terbagi menjadi 4 tab:
1. **Tab Hardware & Transmisi**:
   - Pilih saluran komunikasi sensor: **WiFi MQTT**, **Bluetooth Low Energy (BLE)**, atau **Kabel USB Serial (Web Serial API)**.
   - Masukkan offset kalibrasi suhu (°C) atau kelembaban (% RH) jika alat ukur fisik memiliki sedikit deviasi.
2. **Tab Parameter Pengeringan**:
   - Pilihan preset pengeringan cepat (*Quick Presets*):
     - *Hanjeli Basah Panen*: Target 12.0%, batas suhu 50°C, sirkulasi kontinu.
     - *Hanjeli Setengah Kering*: Target 12.0%, batas suhu 48°C.
     - *Pemeliharaan Stok Gudang*: Target 11.5%, sirkulasi periodik.
3. **Tab Notifikasi**:
   - Atur alamat email, nomor WhatsApp pengelola, atau akun Telegram.
   - Klik **Tes Notifikasi** untuk memastikan pesan terkirim ke ponsel Anda.
   - Klik **Kirim Daily Digest Sekarang** untuk mendistribusikan ringkasan status greenhouse saat itu juga.
4. **Tab Keamanan Akun**:
   - Ganti kata sandi akun Anda.
   - Daftarkan Passkey biometrik (sidik jari / wajah).
   - Tinjau daftar perangkat yang sedang login dan cabut sesi yang tidak dikenal.

---

### 4.12. Penggunaan Asisten Cerdas AI Copilot

Asisten AI siap membantu operator setiap saat:
1. Klik tombol **AI Copilot** (ikon bintang berkilau ✦) di pojok kanan atas header aplikasi.
2. Drawer interaktif akan bergeser terbuka di sisi kanan.
3. Asisten AI otomatis membaca data mikroklimat ruang detik tersebut.
4. **Pilih Tombol Cepat atau Tanyakan Langsung**:
   - *"Apakah kondisi ruang pengering saat ini aman?"*
   - *"Mengapa kelembaban ruangan sulit turun?"*
   - *"Apakah pemanas cadangan perlu dinyalakan saat ini?"*
   - *"Kapan perkiraan batch ini selesai?"*
5. AI Copilot akan menganalisis tren data dan memberikan saran agronomis dalam Bahasa Indonesia yang praktis.
6. Jika AI menyarankan perubahan (contoh: menaikkan putaran exhaust fan), klik tombol **Terapkan Saran** untuk mengeksekusinya secara instan.

---

## BAGIAN II: PANDUAN LENGKAP PENGGUNAAN UNTUK ADMINISTRATOR

Bagian ini memandu Administrator sistem dalam memantau infrastruktur, perangkat IoT, akun pengguna, parameter master, dan basis data.

---

### 5.1. Menu 1: Ringkasan Sistem & Health Matrix (`/admin`)

Halaman pengawasan teknis menyeluruh:

```
┌─────────────────────────────────────────────────────────────────────────────┐
│ MATRIKS KESEHATAN SISTEM MULTI-SERVICE:                                     │
│  🌐 Web Server HTTP / REST API   : [ STATUS: ONLINE | 200 OK | Respon 24ms ] │
│  ⚡ WebSocket Daemon (Reverb)     : [ STATUS: ONLINE | Port 8080 | Latensi 12ms]│
│  📡 MQTT Telemetry Worker Ingestion: [ STATUS: ONLINE | Ingest: 12 paket/mnt ]│
├─────────────────────────────────────────────────────────────────────────────┤
│ 💾 STATISTIK BASIS DATA:                                                    │
│  • Total Baris Telemetri Tersimpan : 142.850 rekaman                        │
│  • Ukuran File Basis Data          : 18.4 MB                                │
│  • Estimasi Retensi Penyimpanan    : 180 Hari                               │
├─────────────────────────────────────────────────────────────────────────────┤
│ ⚠️ PUSAT DETEKSI ANOMALI SENSOR (ANOMALY DETECTION):                        │
│  • Tidak ditemukan anomali kritis dalam 24 jam terakhir.                     │
│  • Deteksi Otomatis: Thermal Spike, Sensor NaN/Disconnect, Thermal Paradox. │
└─────────────────────────────────────────────────────────────────────────────┘
```

#### Tindakan Administrator:
- Jika salah satu badge layanan berwarna **MERAH (OFFLINE)**:
  - Jika Reverb mati: Buka terminal VPS dan jalankan `php artisan reverb:start`.
  - Jika MQTT Worker mati: Jalankan `php artisan greenhouse:mqtt-worker`.
  - Pada server production berbasis Supervisor: Jalankan `sudo supervisorctl restart 'hanjeli-worker:*' hanjeli-reverb hanjeli-mqtt`.

---

### 5.2. Menu 2: Pusat Armada IoT & 7 Sub-Modul Perangkat (`/admin/devices`)

Pusat kendali perangkat keras terpadu mikrokontroler ESP32:

#### 5.2.1. Sub-Modul 1: Otentikasi Token & Ping Node (`security`)
- Mengelola validitas token otentikasi `X-Device-Token` dan tanda tangan HMAC SHA-256.
- Klik tombol **Ping Semua Node** untuk memverifikasi sambungan ESP32 secara nirkabel.
- Klik **Regenerasi Token** untuk memperbarui kunci rahasia jika terdapat indikasi perangkat mikrokontroler lama disusupi.

#### 5.2.2. Sub-Modul 2: Pembaruan Firmware OTA (`ota`)
- Memperbarui firmware mikrokontroler dari jarak jauh melalui jaringan WiFi tanpa perlu menyambungkan kabel USB ke greenhouse.
- Pilih node target (misal `SRD-001`), pilih versi rilis firmware dari pustaka biner, lalu klik **Picukan Pembaruan OTA**.
- Pantau progress bar persentase flashing firmware hingga status berubah menjadi `SUCCESS`.

#### 5.2.3. Sub-Modul 3: Pustaka Rilis Biner Firmware (`releases`)
- Repositori biner berkas firmware berekstensi `.bin`.
- Klik tombol **Unggah Firmware Baru (.bin)** untuk menambahkan versi rilis produksi baru.
- Masukkan nomor versi semantik (misal `v1.2.0`), catatan perubahan rilis (*changelog*), dan periksa kode *checksum* integritas data.

#### 5.2.4. Sub-Modul 4: Matriks Kesehatan Sensor & Latensi (`sensors`)
- Menampilkan kartu diagnostik masing-masing sensor fisik:
  - Sensor DHT22 Suhu & RH Internal (GPIO 25).
  - Sensor Intensitas Cahaya Surya BH1750 Lux (I2C GPIO 21/22).
  - Sensor Deteksi Hujan Analog (ADC GPIO 34).
  - Timbangan Baki Load Cell HX711 (GPIO 32/33).
- Memeriksa waktu detak jantung terakhir (*heartbeat*), rata-rata latensi bolak-balik (ms), dan tingkat kegagalan pembacaan paket data (*packet drop rate*).

#### 5.2.5. Sub-Modul 5: Uji Fisik Sakelar Relai 4 Kanal (`actuators`)
- Panel pengujian teknis sakelar relai 4 kanal langsung dari server:
  - **Relai 1 (GPIO 26)**: Pemanas Keramik PTC Saluran 1.
  - **Relai 2 (GPIO 27)**: Pemanas Keramik PTC Saluran 2.
  - **Relai 3 (GPIO 14)**: Kipas Pembuang Uap Lembap (*Exhaust Fan*).
  - **Relai 4 (GPIO 12)**: Kipas Sirkulasi Ruang Pengering.
- Klik tombol **Uji Sakelar ON / OFF** untuk memverifikasi apakah modul relai fisik di greenhouse berbunyi "klik" dan lampu indikatornya menyala normal.

#### 5.2.6. Sub-Modul 6: Katalog Hardware & Spesifikasi GPIO (`catalog`)
- Lembar data teknis (*datasheet summary*) untuk seluruh komponen hardware di greenhouse.
- Berisi nomor pinout GPIO, protokol komunikasi (I2C/ADC/One-Wire), batas toleransi voltase, dan jadwal pemeliharaan berkala.

#### 5.2.7. Sub-Modul 7: Penampil Kode Sumber Arduino ESP32 (`arduino`)
- Menampilkan kode sumber firmware mikrokontroler C++ langsung di peramban:
  - `esp32_smart_dryer_hardware.ino` (Firmware produksi fisik di greenhouse).
  - `esp32_smart_dryer_simulator.ino` (Firmware simulator untuk pengujian laboratorium).
- Tersedia tombol **Salin Kode** dan **Unduh File .ino** bagi teknisi yang akan mem-flash ESP32 via Arduino IDE atau VS Code PlatformIO.

---

### 5.3. Menu 3: Manajemen Pengguna & Hak Akses (`/admin/users`)

Modul pengelolaan akun staf dan persetujuan (ACC) pendaftaran:
1. **Pemberitahuan & Persetujuan Akun Pendaftar Baru (ACC / Setujui)**:
   - Apabila ada pendaftar baru mandiri atau lewat Google OAuth, banner kuning akan muncul di bagian atas: *"Terdapat X Pendaftar Baru Menunggu Persetujuan (ACC)"*.
   - Tab filter cepat tersedia: **Semua**, **Menunggu ACC (dengan badge merah)**, **Operator**, dan **Admin**.
   - Pada baris pendaftar berstatus `⏳ Menunggu ACC`:
     - Klik tombol hijau **`ACC / Setujui`** untuk menyetujui akun secara instan dengan 1-klik. Akun langsung aktif (`APPROVED`) dan pengguna dapat login.
     - Klik tombol silang merah **`✕`** jika ingin menolak pendaftaran akun tersebut.
   - Jika akun berstatus `✕ Ditolak (REJECTED)`, Admin dapat memulihkannya sewaktu-waktu dengan tombol **`Pulihkan (ACC)`**.
2. **Melihat & Mencari Daftar Pengguna**: Tabel daftar seluruh pengguna, avatar, username, email, nomor WhatsApp, jumlah riwayat batch, tanggal terdaftar, status persetujuan, dan peran aktif (`ADMIN` atau `OPERATOR`).
3. **Tambah Pengguna Baru Secara Manual**:
   - Klik tombol **Tambah Pengguna**.
   - Masukkan Nama Lengkap, Username, Email, Nomor WhatsApp, Kata Sandi Sementara, dan tentukan Peran (`OPERATOR` atau `ADMIN`).
   - Akun yang ditambahkan langsung oleh Admin otomatis berstatus **`APPROVED` (Langsung Aktif)** tanpa perlu di-ACC lagi.
4. **Ubah Data & Status Pengguna**:
   - Klik ikon pensil (Edit) pada baris pengguna.
   - Admin dapat memperbarui nama, username, email, nomor telepon, kata sandi baru, peran, serta status persetujuan (`APPROVED`, `PENDING`, `REJECTED`).
5. **Ubah Peran Cepat (*Role Promotion/Demotion*)**:
   - Melalui dropdown peran pada baris akun yang telah disetujui, Administrator dapat menaikkan hak akses operator menjadi administrator atau sebaliknya kapan saja.
6. **Hapus Akun Pengguna**:
   - Klik tombol ikon tempat sampah merah pada baris pengguna yang ingin dihapus. Sistem akan memunculkan dialog konfirmasi demi mencegah ketidaksengajaan. Akun yang sedang digunakan login oleh admin yang bersangkutan terlindungi dari penghapusan mandiri (*self-delete protection*).

---

### 5.4. Menu 4: Pengaturan Master Parameter Sistem (`/admin/parameters`)

Pengaturan nilai batas toleransi yang mengatur seluruh otomatisasi sistem:
- **Ambang Suhu Aman Maksimum (`max_safe_temp`)**: Default `50.0°C - 55.0°C`. Jika sensor membaca suhu melebihi nilai ini, sistem secara paksa memutus arus pemanas PTC (*Safety Lockout*) dan membunyikan alarm bahaya.
- **Ambang Suhu Minimum Operasional (`min_safe_temp`)**: Default `35.0°C`. Batas pemicu pemanas aktif saat siang hari mendung atau malam hari.
- **Target Kadar Air Default (`target_moisture_default`)**: Default `12.0%`.
- **Interval Pengambilan Sampel Sensor (`sampling_interval_seconds`)**: Default `5 detik`.
- **Pengaturan Broker MQTT**: Host broker (`broker.hivemq.com` atau broker lokal IP), Port (`1883`), dan root topic telemetri.
- **Pengaturan Notifikasi WhatsApp Gateway**: URL endpoint API provider (Fonnte/Wablas), API Token, dan nomor telepon grup petani.
- **Pengaturan Notifikasi Bot Telegram**: Token Bot Telegram dan Chat ID channel penerima alert.

---

### 5.5. Menu 5: Log Audit Trail & Downsample Data Telemetri (`/admin/logs`)

- **Audit Trail Aktivitas**: Merekam jejak waktu setiap kali ada pengguna yang login, menyalakan pemanas secara manual, mengubah batas suhu aman, atau memicu flashing OTA.
- **Fitur Downsampling Data Lama**:
  - Perekaman telemetri per 5 detik menghasilkan ribuan baris data per hari.
  - Untuk mencegah database server melambat setelah berbulan-bulan, Administrator cukup mengklik tombol **Jalankan Downsample Data Lama**.
  - Sistem akan merangkum rekaman data yang lebih tua dari 30 hari menjadi rata-rata periodik per 10 menit, memangkas ukuran database hingga **$90\%$** tanpa menghilangkan kurva historis pengeringan.

---

## 6. TIPS OPERASIONAL & CHECKLIST HARIAN

### Checklist Operasional Operator Greenhouse:

| Waktu | Tindakan yang Harus Dilakukan |
| :--- | :--- |
| **07:30 - Pagi Hari** | 1. Buka dashboard di smartphone atau komputer, pastikan status **ONLINE**.<br>2. Periksa kebersihan atap polikarbonat greenhouse dari dedaunan rontok.<br>3. Bersihkan permukaan sensor cahaya BH1750 dan sensor hujan.<br>4. Lakukan pembalikan gabah pertama di baki rak. |
| **12:00 - Siang Hari** | 1. Periksa suhu ruang di `/monitoring`. Pastikan suhu stabil di rentang $40^\circ\text{C} - 48^\circ\text{C}$.<br>2. Pastikan Kipas Exhaust berputar normal membuang uap air panas.<br>3. Lakukan pembalikan gabah kedua. |
| **16:30 - Sore Hari** | 1. Periksa estimasi sisa waktu pengeringan pada kartu AI Predictive ETA.<br>2. Pastikan pintu greenhouse tertutup rapat.<br>3. Periksa kesiapan pemanas PTC untuk menjaga suhu malam hari.<br>4. Lakukan pembalikan gabah ketiga. |
| **Malam Hari** | Sistem beroperasi otomatis menjaga suhu $\ge 38^\circ\text{C}$. Peringatan darurat otomatis dikirim ke WhatsApp jika terjadi anomali suhu. |

---

## 7. PANDUAN TROUBLESHOOTING & PEMULIHAN CEPAT

| Gejala Masalah | Penyebab Utama | Solusi Tindakan Cepat |
| :--- | :--- | :--- |
| **Data suhu & kelembaban di dashboard membeku / tidak berubah** | Daemon MQTT Ingestion Worker di server terhenti. | **Untuk Operator**: Laporkan ke Admin.<br>**Untuk Admin**: Buka terminal dan jalankan `php artisan greenhouse:mqtt-worker` atau `sudo supervisorctl restart hanjeli-mqtt`. |
| **Status koneksi WebSocket bertuliskan "Disconnected" (Merah)** | Server WebSocket Reverb mati atau port 8080 terblokir. | **Untuk Admin**: Jalankan `php artisan reverb:start` atau periksa firewall port 8080. |
| **Pemanas PTC tidak mau menyala sama sekali** | Suhu terdeteksi $\ge 50^\circ\text{C}$ (*safety lockout*) atau kabel sensor suhu DHT22 terlepas (nilai `0` atau `NaN`). | 1. Periksa suhu ruang. Jika $\ge 50^\circ\text{C}$, pemanas sengaja dikunci mati demi keselamatan.<br>2. Jika pembacaan suhu `0` atau `NaN`, periksa koneksi fisik kabel pin GPIO 25. |
| **Kipas Exhaust tidak menyala saat kelembaban tinggi** | Saklar aktuator berada di mode **MANUAL** posisi OFF, atau sekring kipas putus. | 1. Buka panel aktuator di dashboard, ubah mode ke **OTOMATIS**.<br>2. Periksa lampu LED indikator modul relai di panel box greenhouse. |
| **Pindai Bluetooth (BLE) tidak menemukan ESP32** | Bluetooth perangkat belum aktif, jarak $> 5\text{ m}$, atau peramban bukan Chrome/Edge. | Aktifkan Bluetooth dan GPS/Lokasi ponsel. Dekatkan ponsel ke box kontrol ESP32. Gunakan Google Chrome atau Edge. |
| **Lupa sandi dan kode OTP email tidak masuk** | Konfigurasi SMTP email server bermasalah atau pesan masuk ke folder Spam. | 1. Cek folder Spam / Promosi pada email.<br>2. Hubungi Administrator untuk mereset kata sandi Anda secara manual melalui menu `/admin/users`. |

---

*Buku Panduan Penggunaan ini disusun secara khusus oleh Tim Peneliti CoE STAS-RG bersama Pengelola Smart Room Dryer Desa Wisata Hanjeli Waluran. Seluruh hak cipta dilindungi undang-undang.*

