# Design System & UI/UX Specification (Zero-Shadow Edition)
## Smart Room Dryer Hanjeli

Dokumen ini memuat standarisasi desain antarmuka (UI), sistem tipografi, tata letak, komponen visual, palet warna, sistem border/stroke (Zero-Shadow Flat Design), serta ikonografi untuk ekosistem aplikasi Smart Room Dryer Hanjeli (Desa Wisata Hanjeli Waluran & CoE STAS-RG).

---

## 1. Prinsip Utama Desain (Zero-Shadow & Crisp Industrial)

1. **Zero-Shadow Flat Aesthetic**: Seluruh elemen antarmuka (kartu, tombol, popup, modal, dropdown, header, sidebar) **tidak menggunakan efek bayangan (`box-shadow`, `drop-shadow`, `text-shadow`) sama sekali**.
2. **Crisp Stroke & Border Hierarchy**: Kedalaman dan pemisahan antarmuka dicapai melalui garis batas tegas (`1px` hingga `1.5px` border), perbedaan warna permukaan (*surface background tint*), dan kontras warna yang presisi.
3. **Agro-Technology Modern**: Menggabungkan nuansa agrikultur alami tanaman Hanjeli dengan estetika instrumen telemetri IoT industri modern.
4. **Kejelasan Informasi Telemetri**: Menampilkan pembacaan sensor dan status aktuator secara langsung, kontras tinggi, dan mudah dipahami baik oleh operator maupun administrator.
5. **Responsif Multi-Platform**: Tampilan adaptif penuh untuk antarmuka Desktop Workstation dan Mobile Smartphone PWA tanpa ketergantungan pada elevasi bayangan.

---

## 2. Tipografi (Plus Jakarta Sans / Inter)

Aplikasi menggunakan keluarga font **Plus Jakarta Sans** / **Inter** sebagai standar tipografi tunggal untuk seluruh teks, angka metrik, dan label navigasi.

```css
font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
```

### Skala Tipografi & Penerapan

| Level | Ukuran Font | Line Height | Weight | Penerapan |
| :--- | :--- | :--- | :--- | :--- |
| **Display Title** | 28px (1.75rem) | 34px | 800 (Extra Bold) | Judul utama halaman autentikasi & banner hero |
| **Page Heading (H1)** | 22px (1.375rem) | 28px | 700 (Bold) | Header halaman modul dan judul dashboard |
| **Section Title (H2)** | 18px (1.125rem) | 24px | 700 (Bold) | Judul kartu sensor, section analitik, widget |
| **Card Title (H3)** | 15px (0.9375rem) | 20px | 600 (Semi Bold) | Sub-header tabel, judul modal, nama parameter |
| **Metric Large** | 32px (2.0rem) | 36px | 800 (Extra Bold) | Nilai angka suhu, kelembaban, kadar air |
| **Body Default** | 14px (0.875rem) | 20px | 400 / 500 | Teks deskripsi, label input formulir, paragraf |
| **Body Small** | 12.5px (0.781rem) | 17px | 500 (Medium) | Keterangan status, sub-label, hint input |
| **Micro Caption** | 11px (0.6875rem) | 14px | 600 (Semi Bold) | Badge status, timestamp data, label uppercase |

---

## 3. Palet Warna & Token Desain

### A. Warna Utama (Primary Agricultural Green)
- **Primary Dark**: `#0D631B` (Warna utama header, tombol submit utama, sidebar active)
- **Primary Hover**: `#15803D` (State hover tombol utama & link)
- **Primary Base**: `#2E7D32` (Warna brand agrikultur, aksen interaktif)
- **Primary Light**: `#CBFFC2` / `#E8F5E9` (Aksen hijau muda, highlight teks gelap, badge aktif)
- **Primary Subtle Background**: `rgba(46, 125, 50, 0.08)` (Background kartu aktif)
- **Primary Border**: `#86EFAC` / `#0D631B` (Garis tepi kartu status aktif)

### B. Warna Indikator Parameter IoT
- **Suhu & Pemanas (Thermal Orange/Red)**:
  - Base: `#EA580C` / `#B45000`
  - Background: `rgba(234, 88, 12, 0.10)`
  - Alert: `#DC2626` / `rgba(220, 38, 38, 0.10)`
- **Kelembaban & Sirkulasi (Moisture Blue)**:
  - Base: `#0284C7` / `#005DB7`
  - Background: `rgba(2, 132, 199, 0.10)`
  - Track: `#D5ECF8`
- **Kadar Air Biji Hanjeli (Harvest Amber)**:
  - Base: `#D97706` / `#F59E0B`
  - Background: `rgba(217, 119, 6, 0.10)`
- **Kondisi Optimal / Selesai (Success Green)**:
  - Base: `#10B981` / `#16A34A`
  - Background: `#F0FDF4`
  - Border: `#BBF7D0`

### C. Warna Netral & Tema Gelap (Light & Dark Theme)

| Token Variabel | Light Theme | Dark Theme | Catatan |
| :--- | :--- | :--- | :--- |
| `--color-bg` | `#F4F7F5` | `#0B1911` | Background halaman |
| `--color-card-bg` | `#FFFFFF` | `#13271C` | Permukaan kartu / kontainer |
| `--color-border` | `#CBD5E1` | `#1E3A2B` | Garis tepi kartu & input standar |
| `--color-border-subtle` | `rgba(191, 202, 186, 0.4)` | `rgba(30, 78, 97, 0.5)` | Garis pemisah tabel / item |
| `--color-text-main` | `#071E27` | `#F1F5F9` | Teks judul & metrik utama |
| `--color-text-muted` | `#707A6C` | `#94A3B8` | Subtitle & keterangan |
| `--color-header-bg` | `#FFFFFF` | `#13271C` | Latar header atas & sidebar |
| `--shadow-sm` | `none` | `none` | **Ditiadakan (Zero Shadow)** |
| `--shadow-md` | `none` | `none` | **Ditiadakan (Zero Shadow)** |
| `--shadow-lg` | `none` | `none` | **Ditiadakan (Zero Shadow)** |

---

## 4. Standar Ikonografi (Vector SVG & Font Awesome)

Aplikasi menggunakan **Vector SVG inline & Font Awesome 6 (Solid & Regular)** sebagai sumber ikon standar untuk menjamin ketajaman dan keterbacaan teknis pada semua resolusi layar tanpa efek blur.

---

## 5. Spesifikasi Komponen (Zero-Shadow Rules)

### A. Kartu Metrik Telemetri (Metric Card)
- **Border**: `1px solid var(--color-border)` (Default), `1.5px solid var(--color-primary)` (Hover/Active).
- **Box Shadow**: `none` (Dilarang menggunakan elevasi bayangan).
- **Border Radius**: `12px` hingga `14px`.
- **Background**: `var(--color-card-bg)`.
- **Hover Interaction**: `transform: translateY(-2px); border-color: var(--color-primary);` (Perubahan garis & translasi mikro tegas tanpa shadow).

### B. Tombol Aksi (Action Buttons)
- **Primary Button**: `background: #0D631B; color: #FFFFFF; border: 1px solid #0D631B;`
- **Secondary Button**: `background: transparent; color: var(--color-text-main); border: 1px solid var(--color-border);`
- **Active Press**: `transform: scale(0.97);` (Umpan balik sentuhan mekanis).
- **Box Shadow**: `none`.

### C. Modal Dialog, Sheet & Dropdown Popup
- **Border**: `1.5px solid var(--color-border)` (Light) / `1.5px solid #1E3A2B` (Dark).
- **Backdrop Overlay**: `background: rgba(7, 30, 39, 0.6); backdrop-filter: blur(4px);`
- **Box Shadow**: `none` (Batas modal diperjelas oleh garis border tegas dan kontras backdrop).
- **Border Radius**: `16px` (Desktop Modal), `20px 20px 0 0` (Mobile Sheet).

### D. Formulir & Masukan Data (Input Fields)
- **Tinggi Input**: `42px` (Desktop), `46px` (Mobile).
- **Border**: `1px solid var(--color-border)` (Default), `1.5px solid #0D631B` (Focus).
- **Outline / Ring**: `outline: none;` (Tanpa fuzzy focus ring).
- **Feedback State**: Border merah `#DC2626` saat validasi gagal.

---

## 6. Skala Border Radius & Nol Bayangan

### Skala Border Radius
- **Badge & Status Pill**: `9999px` (Full rounded / Pill)
- **Input Fields & Small Buttons**: `8px` - `10px`
- **Kartu Metrik & Panel Widget**: `12px` - `14px`
- **Modal Dialog & Container Frame**: `16px` - `20px`

### Aturan Nol Bayangan (Zero Shadow Rule)
```css
/* Aturan Global Zero-Shadow */
*, *::before, *::after {
  box-shadow: none !important;
  text-shadow: none !important;
}

:root {
  --shadow-sm: none;
  --shadow-md: none;
  --shadow-lg: none;
  --shadow-xl: none;
}
```

---

## 7. Transisi & Mikro-Interaksi (Non-Shadow Interactions)

1. **Hover State**: Fokus pada perubahan warna border (`border-color`), perubahan kontras background (`background-color`), dan translasi halus (`translateY(-2px)`).
2. **Active State**: Skala penekanan tombol `scale(0.96)`.
3. **Modal & View Transition**: Framer motion style fade & slide halus (`opacity` dan `translateY`).
4. **Status Pulse**: Animasi kedip lembut warna titik dot SVG tanpa efek bayangan melingkar.
