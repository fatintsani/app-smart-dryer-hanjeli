#include <BH1750.h>
#include <DHT.h>
#include <HTTPClient.h>
#include <LiquidCrystal_I2C.h>
#include <WiFi.h>
#include <Wire.h>

// ==============================================================================
// 1. PIN ESP32 HARDWARE CONFIGURATION
// ==============================================================================
#define DHTPIN 25 // Sensor Suhu & Kelembapan Ruang (DHT22)
#define DHTTYPE DHT22
#define RELAY_CH1 26 // Relay Channel 1: Heater / Pemanas Ruang
#define RELAY_CH2 27 // Relay Channel 2: Heater Cadangan / Exhaust
#define RAIN_PIN 34  // Sensor Hujan (Analog ADC Pin)

// ==============================================================================
// 2. KONFIGURASI KONTROL & PARAMETER KESELAMATAN
// ==============================================================================
const int RAIN_THRESH = 2000; // ADC < 2000 = Terdeteksi Hujan

const float MAX_TEMP_C = 50.0; // Batas Suhu Kritis (Heater Wajib OFF)
const float RESTART_TEMP_C =
    48.0; // Suhu Pemulihan (Heater Boleh Aktif Kembali)

// Ambang Batas Intensitas Cahaya (Lux)
const float INITIAL_NIGHT_MAX_LUX = 50.0;
const float INITIAL_DAY_MIN_LUX = 100.0;

// Hysteresis Perpindahan Fase Cahaya (Mencegah Fluktuasi Status)
const float NIGHT_TO_DAWN_LUX = 80.0;
const float DAWN_TO_NIGHT_LUX = 40.0;
const float DAWN_TO_DAY_LUX = 200.0;
const float DAY_TO_DAWN_LUX = 180.0;

// Hysteresis Kelembapan Khusus Fase Subuh / Transisi
const float DAWN_HUMIDITY_ON = 70.0;
const float DAWN_HUMIDITY_OFF = 65.0;

const byte MAX_DHT_ERRORS = 3;

// Level Logika Relay (Aktif-HIGH)
const uint8_t RELAY_ON_LEVEL = HIGH;
const uint8_t RELAY_OFF_LEVEL = LOW;

// ==============================================================================
// 3. KONFIGURASI NETWORK & SERVER BACKEND WEB
// ==============================================================================
// Pastikan ESP32 terhubung ke Wi-Fi / Hotspot yang SAMA dengan Server Laptop
const char *ssid = "inode-AOO2";     // Nama Wi-Fi / Hotspot
const char *password = "stasrg2024"; // Password Wi-Fi

// Endpoint API Web Server Smart Dryer Hanjeli
const char *serverUrl = "https://hanjeli-dryer.stas-rg.com/api/telemetry";
const char *apiKey = "NCtJiDY-xuz9iPr8LYy4Mb15bXN-tB3EJOcrJ-ei754";
const char *deviceId = "SRD-001";

// ==============================================================================
// 4. INISIALISASI OBJEK HARDWARE
// ==============================================================================
LiquidCrystal_I2C lcd(0x27, 20, 4); // LCD 20x4 Alamat I2C 0x27
DHT dht(DHTPIN, DHTTYPE);
BH1750 lightMeter;

// ==============================================================================
// 5. ENUMERASI FASE CAHAYA
// ==============================================================================
enum DayPhase { PHASE_UNKNOWN, PHASE_NIGHT, PHASE_DAWN, PHASE_DAY };

DayPhase dayPhase = PHASE_UNKNOWN;

// ==============================================================================
// 6. VARIABEL STATUS SENSOR & AKTUATOR
// ==============================================================================
float temp = 0.0;
float hum = 0.0;
float lux = 0.0;

int rainVal = 4095;
bool isRaining = false;

bool heaterState = false;

bool dhtOK = false;
bool bh1750Ready = false;
bool luxReadingValid = false;

bool temperatureLockout = false;
bool dawnHumidityDemand = false;

byte dhtErrorCount = 0;
byte currentPage = 0;

// ==============================================================================
// 7. TIMER INTERVAL (NON-BLOCKING MULTITASKING)
// ==============================================================================
unsigned long prevSensorMillis = 0;
const unsigned long SENSOR_INTERVAL = 2500; // Baca sensor tiap 2.5 detik

unsigned long prevSendMillis = 0;
const unsigned long SEND_INTERVAL = 5000; // Kirim telemetri tiap 5.0 detik

unsigned long prevDisplayMillis = 0;
const unsigned long PAGE_INTERVAL = 4000; // Ganti halaman LCD tiap 4.0 detik

unsigned long lastWifiAttempt = 0;
const unsigned long WIFI_RETRY_INTERVAL =
    15000; // Coba reconnect Wi-Fi tiap 15 detik

bool wifiWasConnected = false;

// ==============================================================================
// 8. DEKLARASI FUNGSI
// ==============================================================================
void readSensors();
void updateDayNight();
void updateHeaterControl();
void applyRelayState();
void maintainWiFi();
void sendTelemetry();
void printToSerial();
void updateLCD();

const char *phaseName();
const char *phaseShortName();

// ==============================================================================
// 9. SETUP HARDWARE
// ==============================================================================
void setup() {
  Serial.begin(115200);

  // Pastikan relay langsung aman (OFF) saat startup
  digitalWrite(RELAY_CH1, RELAY_OFF_LEVEL);
  digitalWrite(RELAY_CH2, RELAY_OFF_LEVEL);

  pinMode(RELAY_CH1, OUTPUT);
  pinMode(RELAY_CH2, OUTPUT);
  pinMode(RAIN_PIN, INPUT);

  digitalWrite(RELAY_CH1, RELAY_OFF_LEVEL);
  digitalWrite(RELAY_CH2, RELAY_OFF_LEVEL);

  // Inisialisasi Bus I2C (SDA=21, SCL=22)
  Wire.begin(21, 22);
  Wire.setClock(100000);

  // Inisialisasi LCD 20x4
  lcd.init();
  lcd.backlight();
  lcd.clear();

  lcd.setCursor(0, 0);
  lcd.print(" SMART ROOM DRYER   ");
  lcd.setCursor(0, 1);
  lcd.print(" Starting System... ");
  lcd.setCursor(0, 2);
  lcd.print(" STAS-RG HANJELI    ");

  // Inisialisasi Sensor DHT22
  dht.begin();

  // Inisialisasi Sensor Cahaya BH1750
  if (lightMeter.begin(BH1750::CONTINUOUS_HIGH_RES_MODE)) {
    bh1750Ready = true;
    Serial.println("BH1750 OK.");
  } else {
    bh1750Ready = false;
    Serial.println("ERROR: BH1750 tidak terdeteksi!");
  }

  // Pengaturan Koneksi Wi-Fi Non-blocking
  WiFi.mode(WIFI_STA);
  WiFi.setSleep(false);
  WiFi.setAutoReconnect(true);
  WiFi.begin(ssid, password);

  lastWifiAttempt = millis();

  Serial.println("==================================================");
  Serial.println("   GREEN HOUSE SMART DRYER - ESP32 FIRMWARE       ");
  Serial.println("==================================================");
  Serial.println("Menghubungkan ke Wi-Fi: " + String(ssid));

  applyRelayState();
}

// ==============================================================================
// 10. LOOP UTAMA
// ==============================================================================
void loop() {
  unsigned long now = millis();

  // 1. Monitor dan jaga kestabilan koneksi Wi-Fi
  maintainWiFi();

  // 2. Baca sensor dan eksekusi logika kontrol lokal
  if (now - prevSensorMillis >= SENSOR_INTERVAL) {
    prevSensorMillis = now;

    readSensors();
    updateHeaterControl();
    applyRelayState();
    printToSerial();
  }

  // 3. Kirim paket telemetri ke API Backend & MQTT Worker
  if (now - prevSendMillis >= SEND_INTERVAL) {
    prevSendMillis = now;
    sendTelemetry();
  }

  // 4. Pergantian halaman tampilan LCD 20x4
  if (now - prevDisplayMillis >= PAGE_INTERVAL) {
    prevDisplayMillis = now;
    currentPage = !currentPage;
    lcd.clear();
  }

  updateLCD();
}

// ==============================================================================
// 11. PEMBACAAN SENSOR HARDWARE
// ==============================================================================
void readSensors() {
  // A. Sensor Suhu & Kelembapan (DHT22)
  float t = dht.readTemperature();
  float h = dht.readHumidity();

  if (!isnan(t) && !isnan(h) && t >= -40.0 && t <= 80.0 && h >= 0.0 &&
      h <= 100.0) {

    temp = t;
    hum = h;
    dhtOK = true;
    dhtErrorCount = 0;

  } else {
    dhtOK = false;

    if (dhtErrorCount < MAX_DHT_ERRORS) {
      dhtErrorCount++;
    }

    Serial.printf("Warning: DHT22 gagal membaca (%d/%d)\n", dhtErrorCount,
                  MAX_DHT_ERRORS);
  }

  // B. Sensor Intensitas Cahaya (BH1750)
  if (bh1750Ready) {
    float measuredLux = lightMeter.readLightLevel();

    if (!isnan(measuredLux) && measuredLux >= 0.0 && measuredLux <= 65535.0) {
      lux = measuredLux;
      luxReadingValid = true;
      updateDayNight();
    } else {
      luxReadingValid = false;
      Serial.println("Warning: Pembacaan BH1750 tidak valid.");
    }
  } else {
    luxReadingValid = false;
  }

  // C. Sensor Hujan (Analog ADC Pin 34)
  rainVal = analogRead(RAIN_PIN);
  isRaining = (rainVal < RAIN_THRESH);
}

// ==============================================================================
// 12. KLASIFIKASI FASE CAHAYA (HYSTERESIS STATE MACHINE)
// ==============================================================================
void updateDayNight() {
  if (!luxReadingValid) {
    return;
  }

  // Inisialisasi fase awal
  if (dayPhase == PHASE_UNKNOWN) {
    if (lux <= INITIAL_NIGHT_MAX_LUX) {
      dayPhase = PHASE_NIGHT;
    } else if (lux >= INITIAL_DAY_MIN_LUX) {
      dayPhase = PHASE_DAY;
    } else {
      dayPhase = PHASE_DAWN;
    }
    return;
  }

  // Hysteresis transisi untuk meredam kedipan cahaya
  switch (dayPhase) {
  case PHASE_NIGHT:
    if (lux >= DAWN_TO_DAY_LUX) {
      dayPhase = PHASE_DAY;
    } else if (lux >= NIGHT_TO_DAWN_LUX) {
      dayPhase = PHASE_DAWN;
    }
    break;

  case PHASE_DAWN:
    if (lux <= DAWN_TO_NIGHT_LUX) {
      dayPhase = PHASE_NIGHT;
    } else if (lux >= DAWN_TO_DAY_LUX) {
      dayPhase = PHASE_DAY;
    }
    break;

  case PHASE_DAY:
    if (lux <= DAWN_TO_NIGHT_LUX) {
      dayPhase = PHASE_NIGHT;
    } else if (lux <= DAY_TO_DAWN_LUX) {
      dayPhase = PHASE_DAWN;
    }
    break;

  default:
    dayPhase = PHASE_UNKNOWN;
    break;
  }
}

const char *phaseName() {
  switch (dayPhase) {
  case PHASE_NIGHT:
    return "MALAM";
  case PHASE_DAWN:
    return "SUBUH/TRANSISI";
  case PHASE_DAY:
    return "SIANG";
  default:
    return "BELUM DIKETAHUI";
  }
}

const char *phaseShortName() {
  switch (dayPhase) {
  case PHASE_NIGHT:
    return "MALAM";
  case PHASE_DAWN:
    return "SUBUH";
  case PHASE_DAY:
    return "SIANG";
  default:
    return "UNK";
  }
}

// ==============================================================================
// 13. LOGIKA KONTROL HEATER & SAFETY INTERLOCK
// ==============================================================================
void updateHeaterControl() {
  // Penguncian Suhu Darurat (Lockout)
  // OFF jika suhu >= 50°C, kembali ON jika suhu <= 48°C
  if (dhtOK) {
    if (temp >= MAX_TEMP_C) {
      temperatureLockout = true;
    } else if (temp <= RESTART_TEMP_C) {
      temperatureLockout = false;
    }

    // Permintaan kelembapan subuh
    if (hum >= DAWN_HUMIDITY_ON) {
      dawnHumidityDemand = true;
    } else if (hum <= DAWN_HUMIDITY_OFF) {
      dawnHumidityDemand = false;
    }
  }

  // Interlock Keamanan Wajib
  if (!dhtOK || !bh1750Ready || !luxReadingValid || dayPhase == PHASE_UNKNOWN ||
      temperatureLockout) {
    heaterState = false;
    return;
  }

  // Logika Berdasarkan Fase
  switch (dayPhase) {
  case PHASE_NIGHT:
    // Malam: Pemanas ON terus selama suhu aman
    heaterState = true;
    break;

  case PHASE_DAWN:
    // Subuh: ON jika hujan atau kelembapan tinggi
    heaterState = isRaining || dawnHumidityDemand;
    break;

  case PHASE_DAY:
    // Siang: ON hanya saat mendung/hujan
    heaterState = isRaining;
    break;

  default:
    heaterState = false;
    break;
  }
}

// ==============================================================================
// 14. TERAPKAN KONDISI RELAY AKTIF
// ==============================================================================
void applyRelayState() {
  uint8_t relayLevel = heaterState ? RELAY_ON_LEVEL : RELAY_OFF_LEVEL;
  digitalWrite(RELAY_CH1, relayLevel);
  digitalWrite(RELAY_CH2, relayLevel);
}

// ==============================================================================
// 15. MANAJEMEN KONEKSI WIFI
// ==============================================================================
void maintainWiFi() {
  wl_status_t status = WiFi.status();

  if (status == WL_CONNECTED) {
    if (!wifiWasConnected) {
      Serial.println("\n[WiFi] Terhubung!");
      Serial.print("[WiFi] IP Address ESP32: ");
      Serial.println(WiFi.localIP());
    }
    wifiWasConnected = true;
    return;
  }

  if (wifiWasConnected) {
    Serial.println("\n[WiFi] Koneksi terputus. Kontrol lokal tetap aktif.");
  }
  wifiWasConnected = false;

  if ((status == WL_CONNECT_FAILED || status == WL_NO_SSID_AVAIL) &&
      millis() - lastWifiAttempt >= WIFI_RETRY_INTERVAL) {
    lastWifiAttempt = millis();
    Serial.println("[WiFi] Mencoba koneksi kembali...");
    WiFi.begin(ssid, password);
  }
}

// ==============================================================================
// 16. KIRIM TELEMETRI KE API SERVER & MQTT WORKER
// ==============================================================================
void sendTelemetry() {
  if (WiFi.status() != WL_CONNECTED) {
    Serial.println("[HTTP] Wi-Fi offline. Pembacaan lokal berjalan normal.");
    return;
  }

  HTTPClient http;
  http.setTimeout(3000); // Batas waktu koneksi 3 detik

  if (!http.begin(serverUrl)) {
    Serial.println("[HTTP] Gagal menginisialisasi koneksi HTTP.");
    return;
  }

  // Header Autentikasi Hardware Resmi
  http.addHeader("Content-Type", "application/json");
  http.addHeader("X-Device-Key", apiKey);

  // Buat Payload JSON
  String json = "{";
  json += "\"device_id\":\"" + String(deviceId) + "\"";
  json += ",\"temperature\":" + String(temp, 1);
  json += ",\"humidity\":" + String(hum, 1);
  json += ",\"lux\":" + String(lux, 1);
  json += ",\"rain_adc\":" + String(rainVal);
  json += ",\"is_raining\":" + String(isRaining ? "true" : "false");
  json += ",\"dht_ok\":" + String(dhtOK ? "true" : "false");
  json += ",\"bh1750_ok\":" +
          String(bh1750Ready && luxReadingValid ? "true" : "false");
  json += ",\"heater_on\":" + String(heaterState ? "true" : "false");
  json += "}";

  int responseCode = http.POST(json);

  Serial.printf("[HTTP] POST %s -> Response Code: %d\n", serverUrl,
                responseCode);

  if (responseCode > 0) {
    String response = http.getString();
    Serial.printf("[HTTP] Balasan: %s\n", response.c_str());
  } else {
    Serial.printf("[HTTP] Error: %s\n",
                  http.errorToString(responseCode).c_str());
  }

  http.end();
}

// ==============================================================================
// 17. SERIAL MONITOR
// ==============================================================================
void printToSerial() {
  Serial.println("----------------------------------------------");
  if (dhtOK) {
    Serial.printf("Suhu: %.1f C | Kelembapan: %.1f %%\n", temp, hum);
  } else {
    Serial.println("DHT22: SENSOR FAULT / ERROR");
  }

  if (luxReadingValid) {
    Serial.printf("Cahaya: %.1f lux | Fase: %s\n", lux, phaseName());
  } else {
    Serial.println("BH1750: SENSOR FAULT / ERROR");
  }

  Serial.printf("Sensor Hujan: %s (ADC: %d)\n", isRaining ? "HUJAN" : "KERING",
                rainVal);
  Serial.printf("Status Heater: %s | Lockout: %s\n",
                heaterState ? "AKTIF (ON)" : "MATI (OFF)",
                temperatureLockout ? "LOCKOUT (SUHU TINGGI)" : "NORMAL");
  Serial.println("----------------------------------------------");
}

// ==============================================================================
// 18. TAMPILAN LCD 20x4
// ==============================================================================
void updateLCD() {
  if (currentPage == 0) {
    // Halaman 1: Monitoring Real-Time Sensor & Aktuator
    lcd.setCursor(0, 0);
    lcd.print(" SMART ROOM DRYER   ");

    lcd.setCursor(0, 1);
    if (dhtOK) {
      lcd.print("T:");
      lcd.print(temp, 1);
      lcd.print((char)223);
      lcd.print("C H:");
      lcd.print(hum, 1);
      lcd.print("%   ");
    } else {
      lcd.print("T:ERR! H:ERR!       ");
    }

    lcd.setCursor(0, 2);
    lcd.print("L:");
    if (luxReadingValid) {
      lcd.print((int)lux);
      lcd.print("lx       ");
    } else {
      lcd.print("ERR lx      ");
    }

    lcd.setCursor(10, 2);
    lcd.print("Rain:");
    lcd.print(isRaining ? "YES" : "NO ");

    lcd.setCursor(0, 3);
    lcd.print("H1:");
    lcd.print(heaterState ? "ON " : "OFF");
    lcd.print(" H2:");
    lcd.print(heaterState ? "ON " : "OFF");
    lcd.print(" ");
    lcd.print(phaseShortName());

  } else {
    // Halaman 2: Diagnostik Perangkat & IP ESP32
    lcd.setCursor(0, 0);
    lcd.print("[SYSTEM DIAGNOSTIC] ");

    lcd.setCursor(0, 1);
    lcd.print("DHT:");
    lcd.print(dhtOK ? "OK " : "ERR");
    lcd.print(" BH:");
    lcd.print((bh1750Ready && luxReadingValid) ? "OK " : "ERR");
    lcd.print(" P:");
    lcd.print(phaseShortName());

    lcd.setCursor(0, 2);
    lcd.print("Rain ADC: ");
    lcd.print(rainVal);
    lcd.print("    ");

    lcd.setCursor(0, 3);
    if (WiFi.status() == WL_CONNECTED) {
      lcd.print("IP:");
      lcd.print(WiFi.localIP().toString().substring(0, 17));
    } else {
      lcd.print("WiFi: DISCONNECTED  ");
    }
  }
}
