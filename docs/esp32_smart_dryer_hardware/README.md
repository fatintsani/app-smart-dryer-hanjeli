# Dokumentasi Firmware Hardware ESP32 - Smart Room Dryer Hanjeli

Firmware resmi untuk mikrokontroler **ESP32** pada sistem Smart Room Dryer Green House Hanjeli Waluran. Firmware ini mengintegrasikan sensor lingkungan, logika kontrol pemanas otomatis (otomasi fase cahaya & suhu batas aman), tampilan LCD 20x4 I2C, serta transmisi telemetri ke server web dan MQTT Worker secara real-time.

---

## 1. Spesifikasi Pin Hardware (Pinout)

| Komponen | Tipe / Protokol | Pin ESP32 | Keterangan |
|---|---|---|---|
| **DHT22** | Digital 1-Wire | `GPIO 25` | Sensor Suhu & Kelembapan Udara Internal |
| **BH1750** | I2C (SDA) | `GPIO 21` | Sensor Intensitas Cahaya Matahari (Lux) |
| **BH1750** | I2C (SCL) | `GPIO 22` | Clock I2C |
| **LCD 20x4 I2C** | I2C (SDA) | `GPIO 21` | Tampilan Layar Utama (Alamat: `0x27`) |
| **LCD 20x4 I2C** | I2C (SCL) | `GPIO 22` | Clock I2C |
| **Sensor Hujan** | Analog Input (ADC) | `GPIO 34` | Deteksi Hujan (ADC < 2000 = Hujan) |
| **Relay CH1** | Digital Output | `GPIO 26` | Kontrol Pemanas Utama (*Heater*) |
| **Relay CH2** | Digital Output | `GPIO 27` | Kontrol Pemanas Bantu / Cadangan |

---

## 2. Konfigurasi Jaringan, NTP & MQTT

Di dalam file `esp32_smart_dryer_hardware.ino`, konfigurasi jaringan dan server telah dikombinasikan secara lengkap:

```cpp
// Wi-Fi
const char* ssid        = "inode-AOO2";
const char* password    = "stasrg2024";

// Endpoint Backend Web (HTTP Ingestion)
const char* serverUrl   = "https://hanjeli-dryer.stas-rg.com/api/telemetry";
const char* apiKey      = "NCtJiDY-xuz9iPr8LYy4Mb15bXN-tB3EJOcrJ-ei754";
const char* deviceId    = "SRD-001";

// Waktu NTP WIB (UTC+7) & Jadwal Operasional Pemanas
const uint8_t HEATER_START_HOUR = 18;  // Mulai 18:00 WIB
const uint8_t HEATER_STOP_HOUR  = 5;   // Berakhir 05:00 WIB

// MQTT Broker HiveMQ
const char*    MQTT_BROKER      = "broker.hivemq.com";
const uint16_t MQTT_PORT        = 1883;
const char*    TOPIC_TELEMETRY  = "hanjeli/greenhouse/telemetry";
const char*    TOPIC_CONTROL    = "hanjeli/greenhouse/control";
```

---

## 3. Logika Kontrol Pemanas & Keamanan (Safety Interlock)

1. **Batas Suhu Kritis (Temperature Lockout):**
   * Pemanas **wajib MATI (OFF)** jika suhu $\ge 50^\circ\text{C}$.
   * Pemanas baru diizinkan menyala kembali jika suhu telah turun $\le 48^\circ\text{C}$.
2. **Jadwal Operasional Waktu Nyata (NTP WIB):**
   * Pemanas hanya aktif pada jam kerja malam: **18:00 hingga sebelum 05:00 WIB**.
   * Di luar jadwal tersebut atau sebelum waktu NTP tersinkronisasi, pemanas dimatikan secara aman (*fail-safe*).
3. **State Machine Fase Cahaya (Di Dalam Jadwal):**
   * **Malam ($\le 50\text{ lux}$):** Pemanas menyala (*ON*) untuk menjaga suhu pengeringan stabil.
   * **Subuh / Transisi ($40 - 200\text{ lux}$):** Pemanas menyala jika terdeteksi hujan atau kelembapan udara $\ge 70\%$.
   * **Siang ($\ge 200\text{ lux}$):** Pemanas menyala hanya jika kondisi mendung gelap atau hujan.
4. **Kendali Jarak Jauh (Remote Override via Web / MQTT & HTTP Fallback):**
   * Operator dan Admin dapat mengatur relay pemanas langsung dari antarmuka Web (Halaman Monitoring & Dashboard).
   * **Dual-Path Control**: Perintah diteruskan secara instan melalui MQTT (`hanjeli/greenhouse/control`) dan disinkronkan otomatis lewat respons HTTP Ingestion backend.
   * **Mode Manual vs Otomatis**:
     * Saat diatur ke **ON** atau **OFF** lewat web, relay langsung berpindah status (override manual aktif).
     * Saat diatur ke **Mode Otomatis (AUTO)**, ESP32 kembali mengikuti jadwal histeresis cahaya, hujan, dan waktu NTP WIB (18:00 - 05:00 WIB).
   * **Prioritas Keselamatan Absolut (Overheat Lockout)**: Jika suhu ruangan $\ge 50^\circ\text{C}$, sistem keamanan fisik mikrokontroler akan secara mutlak mematikan relay pemanas (fail-safe over-temperature protection).
5. **Fail-Safe Offline:**
   * Jika Wi-Fi atau server backend terputus, ESP32 tetap menjalankan seluruh pembacaan sensor dan kontrol relay secara lokal tanpa kendala (*non-blocking*).

---

## 4. Format Payload Telemetri ke Backend Web

Dikirimkan via HTTP POST setiap 5 detik ke `https://hanjeli-dryer.stas-rg.com/api/telemetry`:

```json
{
  "device_id": "SRD-001",
  "temperature": 43.5,
  "humidity": 54.0,
  "lux": 760.0,
  "rain_adc": 4095,
  "is_raining": false,
  "dht_ok": true,
  "bh1750_ok": true,
  "heater_on": false
}
```

Backend Laravel secara otomatis:
1. Menyimpan data telemetri ke database MySQL.
2. Memancarkan event WebSocket ke dashboard web secara live.
3. Meneruskan (*mirror*) paket ke MQTT Broker HiveMQ (`hanjeli/greenhouse/telemetry`), sehingga **`php artisan greenhouse:mqtt-worker`** langsung membaca dan menampilkan log di terminal.
