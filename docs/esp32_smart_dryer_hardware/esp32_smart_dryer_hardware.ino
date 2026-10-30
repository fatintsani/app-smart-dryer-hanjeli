#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <DHT.h>
#include <BH1750.h>
#include <WiFi.h>
#include <WiFiClientSecure.h>
#include <HTTPClient.h>
#include <PubSubClient.h>
#include <ArduinoJson.h>
#include <time.h>   // NTP dan waktu lokal WIB

// ==============================================================================
// 1. PIN ESP32 HARDWARE CONFIGURATION
// ==============================================================================
#define DHTPIN        25     // Sensor Suhu & Kelembapan Ruang (DHT22)
#define DHTTYPE       DHT22  
#define RELAY_CH1     26     // Relay Channel 1: Heater / Pemanas Ruang (PTC 1)
#define RELAY_CH2     27     // Relay Channel 2: Heater Cadangan / Exhaust (PTC 2)
#define RAIN_PIN      34     // Sensor Hujan (Analog ADC Pin)

// ==============================================================================
// 2. KONFIGURASI KONTROL & PARAMETER KESELAMATAN
// ==============================================================================
const int RAIN_THRESH = 2000;          // ADC < 2000 = Terdeteksi Hujan

const float MAX_TEMP_C     = 50.0;     // Batas Suhu Kritis (Heater Wajib OFF / Lockout)
const float RESTART_TEMP_C = 48.0;     // Suhu Pemulihan (Heater Boleh Aktif Kembali)

// Ambang Batas Intensitas Cahaya (Lux)
const float INITIAL_NIGHT_MAX_LUX = 50.0;
const float INITIAL_DAY_MIN_LUX   = 100.0;

// Hysteresis Perpindahan Fase Cahaya (Mencegah Fluktuasi Status)
const float NIGHT_TO_DAWN_LUX = 80.0;
const float DAWN_TO_NIGHT_LUX = 40.0;
const float DAWN_TO_DAY_LUX   = 200.0;
const float DAY_TO_DAWN_LUX   = 180.0;

// Hysteresis Kelembapan Khusus Fase Subuh / Transisi
const float DAWN_HUMIDITY_ON  = 70.0;
const float DAWN_HUMIDITY_OFF = 65.0;

const byte MAX_DHT_ERRORS     = 3;

// Level Logika Modul Relay Fisik:
// false = aktif-HIGH (HIGH menyalakan relay - default perangkat Smart Dryer)
// true  = aktif-LOW  (LOW menyalakan relay)
const bool RELAY_ACTIVE_LOW   = false;
const uint8_t RELAY_ON_LEVEL  = RELAY_ACTIVE_LOW ? LOW : HIGH;
const uint8_t RELAY_OFF_LEVEL = RELAY_ACTIVE_LOW ? HIGH : LOW;

// ==============================================================================
// 3. KONFIGURASI NETWORK & SERVER BACKEND WEB (TETAP & COMBINE)
// ==============================================================================
// A. Kredensial Wi-Fi / Hotspot (Mendukung Hotspot Utama & Cadangan Otomatis)
const char* ssidPrimary       = "inode-AOO2";     // Nama Wi-Fi / Hotspot HP Anda
const char* passwordPrimary   = "stasrg2024";     // Password Hotspot HP Anda

const char* ssidSecondary     = "tes2";           // Nama Hotspot Cadangan
const char* passwordSecondary = "12345678";       // Password Hotspot Cadangan

// Variabel aktif untuk koneksi Wi-Fi
const char* currentSsid       = ssidPrimary;
const char* currentPassword   = passwordPrimary;
bool usingSecondaryWifi       = false;
uint8_t wifiFailRetries       = 0;

// B. Endpoint API HTTP Web Server Smart Dryer Hanjeli
const char* serverUrl   = "https://hanjeli-dryer.stas-rg.com/api/telemetry";
const char* apiKey      = "NCtJiDY-xuz9iPr8LYy4Mb15bXN-tB3EJOcrJ-ei754";
const char* deviceId    = "inode-AOO2";        // Identitas Perangkat ESP32 (Sinkron dengan Sistem Web)

// C. Sinkronisasi Waktu NTP Indonesia Barat (WIB = UTC+7, tanpa DST)
const long GMT_OFFSET_SEC      = 7L * 60L * 60L;
const int  DAYLIGHT_OFFSET_SEC = 0;
const char* NTP_SERVER_1       = "id.pool.ntp.org";
const char* NTP_SERVER_2       = "pool.ntp.org";
const char* NTP_SERVER_3       = "time.google.com";

// Jadwal Operasional Pemanas (Heater) Berdasarkan Waktu NTP WIB
// Aktif dari pukul 18:00 malam hingga sebelum 05:00 subuh WIB. Di luar jadwal, heater OFF.
const uint8_t HEATER_START_HOUR = 18;
const uint8_t HEATER_STOP_HOUR  = 5;

// D. Konfigurasi Protokol Broker MQTT (HiveMQ)
const char*    MQTT_BROKER      = "broker.hivemq.com";
const uint16_t MQTT_PORT        = 1883;
const char*    MQTT_DEVICE_ID   = "inode-AOO2"; // Harmonisasi dengan deviceId backend

const char* TOPIC_TELEMETRY     = "hanjeli/greenhouse/telemetry";
const char* TOPIC_CONTROL       = "hanjeli/greenhouse/control";

// Opsi Transmisi Ganda (Dual Telemetry: MQTT + HTTP Ingestion)
const bool ENABLE_MQTT_TELEMETRY = true;
const bool ENABLE_HTTP_TELEMETRY = true;

// Menerima kendali jarak jauh (remote control / override) dari website & MQTT
const bool ENABLE_REMOTE_CONTROL = true;

// ==============================================================================
// 4. INISIALISASI OBJEK HARDWARE
// ==============================================================================
LiquidCrystal_I2C lcd(0x27, 20, 4); // LCD 20x4 Alamat I2C 0x27
DHT dht(DHTPIN, DHTTYPE);
BH1750 lightMeter; 
WiFiClient mqttNetworkClient;
PubSubClient mqttClient(mqttNetworkClient);

// ==============================================================================
// 5. ENUMERASI FASE CAHAYA
// ==============================================================================
enum DayPhase {
  PHASE_UNKNOWN,
  PHASE_NIGHT,
  PHASE_DAWN,
  PHASE_DAY
};

DayPhase dayPhase = PHASE_UNKNOWN;

// ==============================================================================
// 6. VARIABEL STATUS SENSOR & AKTUATOR
// ==============================================================================
float temp = 0.0;
float hum  = 0.0;
float lux  = 0.0;

int  rainVal   = 4095;
bool isRaining = false;

bool heaterState = false;

bool dhtOK                  = false;
bool bh1750Ready            = false;
bool luxReadingValid        = false;
bool hasCompletedSensorRead = false; // Telemetri baru dikirim setelah pembacaan sensor pertama selesai

bool temperatureLockout = false;
bool dawnHumidityDemand = false;

// Override kendali heater jarak jauh (Remote Control via MQTT/Web)
bool remoteHeaterOverrideActive = false;
bool remoteHeaterRequest        = false;

byte dhtErrorCount = 0;
byte currentPage   = 0;

// ==============================================================================
// 7. TIMER INTERVAL (NON-BLOCKING MULTITASKING)
// ==============================================================================
unsigned long prevSensorMillis     = 0;
const unsigned long SENSOR_INTERVAL = 2500;  // Baca sensor tiap 2.5 detik

unsigned long prevSendMillis       = 0;
const unsigned long SEND_INTERVAL   = 5000;  // Kirim telemetri berkala tiap 5.0 detik

unsigned long prevDisplayMillis    = 0;
const unsigned long PAGE_INTERVAL   = 4000;  // Ganti halaman LCD tiap 4.0 detik

unsigned long prevLCDRefreshMillis = 0;
const unsigned long LCD_REFRESH_INTERVAL = 300; // Refresh LCD tiap 300 ms

unsigned long lastWifiAttempt      = 0;
const unsigned long WIFI_RETRY_INTERVAL = 8000; // Evaluasi koneksi Wi-Fi tiap 8 detik

unsigned long lastMqttAttempt      = 0;
const unsigned long MQTT_RETRY_INTERVAL = 5000; // Coba reconnect MQTT tiap 5 detik

bool wifiWasConnected = false;

// Status sinkronisasi waktu NTP
bool ntpTimeSynced = false;
bool heaterScheduleActive = false;
unsigned long prevNtpCheckMillis = 0;
const unsigned long NTP_CHECK_INTERVAL = 1000;

// Status telemetri terakhir yang berhasil dipublikasikan
bool hasPublishedHeaterState  = false;
bool lastPublishedHeaterState = false;

// Status pengiriman data untuk monitoring Serial & LCD
bool hasMqttPublishResult     = false;
bool lastMqttPublishOk        = false;
unsigned long lastMqttPublishMillis = 0;

bool hasHttpPublishResult     = false;
bool lastHttpPublishOk        = false;
int  lastHttpResponseCode     = 0;
unsigned long lastHttpPublishMillis = 0;

// ==============================================================================
// 8. DEKLARASI FUNGSI
// ==============================================================================
void readSensors();
void updateDayNight();
void updateHeaterControl();
void applyRelayState();
void maintainWiFi();
void updateNtpStatus();
bool isWithinHeaterSchedule();
void maintainMQTT();
void mqttCallback(char* topic, byte* payload, unsigned int length);
bool publishMqttTelemetry();
bool sendHttpTelemetry();
bool sendTelemetry();
void printFriendlyDashboard();
void updateLCD();
void printLCDLine(uint8_t row, String line);

const char* phaseName();
const char* phaseShortName();
const char* heaterReasonName();

// ==============================================================================
// 9. SETUP HARDWARE
// ==============================================================================
void setup() {
  Serial.begin(115200);
  delay(500);

  // Inisialisasi pin relay dan tetapkan kondisi awal aman (OFF)
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

  printLCDLine(0, " SMART ROOM DRYER   ");
  printLCDLine(1, " Starting System... ");
  printLCDLine(2, " STAS-RG HANJELI    ");
  printLCDLine(3, " Menghubungkan WiFi ");

  // Inisialisasi Sensor DHT22
  dht.begin();

  // Inisialisasi Sensor Cahaya BH1750
  if (lightMeter.begin(BH1750::CONTINUOUS_HIGH_RES_MODE)) {
    bh1750Ready = true;
    Serial.println("[BH1750] Sensor cahaya BH1750 siap digunakan (I2C 0x23).");
  } else {
    bh1750Ready = false;
    Serial.println("[BH1750] PERINGATAN: Sensor cahaya tidak terdeteksi. Periksa pengkabelan I2C.");
  }

  // Pengaturan Koneksi Wi-Fi Non-blocking
  WiFi.mode(WIFI_STA);
  WiFi.setSleep(false);
  WiFi.setAutoReconnect(true);

  Serial.println();
  Serial.println("==================================================");
  Serial.println("   GREEN HOUSE SMART DRYER - ESP32 FIRMWARE       ");
  Serial.println("   Desa Wisata Hanjeli - Waluran, Kab. Sukabumi   ");
  Serial.println("==================================================");
  Serial.printf("Target Perangkat : %s\n", deviceId);
  Serial.printf("Hotspot Utama    : %s\n", ssidPrimary);
  Serial.printf("Hotspot Cadangan : %s\n", ssidSecondary);
  Serial.printf("Server Hosting   : %s\n", serverUrl);
  Serial.printf("Broker MQTT      : %s:%d\n", MQTT_BROKER, MQTT_PORT);
  Serial.printf("Jadwal Heater    : %02d:00 - %02d:00 WIB\n", HEATER_START_HOUR, HEATER_STOP_HOUR);
  Serial.println("==================================================");

  Serial.printf("[WiFi] Memulai inisialisasi koneksi ke: \"%s\"...\n", currentSsid);
  WiFi.begin(currentSsid, currentPassword);
  lastWifiAttempt = millis();

  // Konfigurasi MQTT Client HiveMQ
  mqttClient.setServer(MQTT_BROKER, MQTT_PORT);
  mqttClient.setCallback(mqttCallback);
  mqttClient.setBufferSize(1536);
  mqttClient.setKeepAlive(30);
  mqttClient.setSocketTimeout(5);

  applyRelayState();
}

// ==============================================================================
// 10. LOOP UTAMA
// ==============================================================================
void loop() {
  unsigned long now = millis();

  // 1. Monitor Wi-Fi, waktu NTP WIB, dan koneksi MQTT
  maintainWiFi();
  updateNtpStatus();
  maintainMQTT();

  // 2. Baca sensor dan eksekusi logika kontrol lokal
  if (now - prevSensorMillis >= SENSOR_INTERVAL) {
    prevSensorMillis = now;

    readSensors();
    updateHeaterControl();
    applyRelayState();
  }

  // 3. Kirim telemetri berkala (Dual Ingestion: MQTT + HTTP)
  // Responsif terhadap perubahan status heater (kirim segera jika heater berubah, jeda min 1s)
  const bool heaterStateChanged =
      hasPublishedHeaterState && (heaterState != lastPublishedHeaterState);
  const bool firstPublishDue =
      !hasPublishedHeaterState && hasCompletedSensorRead &&
      (WiFi.status() == WL_CONNECTED) && (now - prevSendMillis >= 1000UL);

  if ((now - prevSendMillis >= SEND_INTERVAL) ||
      firstPublishDue ||
      (heaterStateChanged && (WiFi.status() == WL_CONNECTED) &&
       (now - prevSendMillis >= 1000UL))) {
    prevSendMillis = now;

    if (sendTelemetry()) {
      lastPublishedHeaterState = heaterState;
      hasPublishedHeaterState = true;
    }

    // Tampilkan dashboard Serial Monitor setelah pengiriman
    printFriendlyDashboard();
  }

  // 4. Pergantian halaman tampilan LCD 20x4
  if (now - prevDisplayMillis >= PAGE_INTERVAL) {
    prevDisplayMillis = now;
    currentPage = !currentPage;
  }

  // 5. Refresh LCD berkala tanpa membebani lalu lintas I2C
  if (now - prevLCDRefreshMillis >= LCD_REFRESH_INTERVAL) {
    prevLCDRefreshMillis = now;
    updateLCD();
  }
}

// ==============================================================================
// 11. PEMBACAAN SENSOR HARDWARE
// ==============================================================================
void readSensors() {
  // A. Sensor Suhu & Kelembapan (DHT22)
  float t = dht.readTemperature();
  float h = dht.readHumidity();

  if (!isnan(t) && !isnan(h) &&
      t >= -40.0 && t <= 80.0 &&
      h >= 0.0 && h <= 100.0) {

    temp = t;
    hum = h;
    dhtOK = true;
    dhtErrorCount = 0;

  } else {
    dhtOK = false;

    if (dhtErrorCount < MAX_DHT_ERRORS) {
      dhtErrorCount++;
    }
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
    }
  } else {
    luxReadingValid = false;
  }

  // C. Sensor Hujan (Analog ADC Pin 34)
  rainVal = analogRead(RAIN_PIN);
  isRaining = (rainVal < RAIN_THRESH);

  // Menandakan siklus pembacaan pertama sudah selesai
  hasCompletedSensorRead = true;
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

const char* phaseName() {
  switch (dayPhase) {
    case PHASE_NIGHT: return "MALAM";
    case PHASE_DAWN:  return "SUBUH/TRANSISI";
    case PHASE_DAY:   return "SIANG";
    default:          return "BELUM DIKETAHUI";
  }
}

const char* phaseShortName() {
  switch (dayPhase) {
    case PHASE_NIGHT: return "MLM";
    case PHASE_DAWN:  return "SBH";
    case PHASE_DAY:   return "SNG";
    default:          return "UNK";
  }
}

// Alasan status heater dikirim bersama telemetri agar mudah dianalisis
const char* heaterReasonName() {
  if (temperatureLockout || (dhtOK && temp >= MAX_TEMP_C)) {
    return "OVER_TEMPERATURE";
  }
  if (ENABLE_REMOTE_CONTROL && remoteHeaterOverrideActive) {
    return remoteHeaterRequest ? "REMOTE_ON_OVERRIDE" : "REMOTE_OFF_OVERRIDE";
  }
  if (!dhtOK) {
    return "DHT22_ERROR";
  }
  if (!bh1750Ready || !luxReadingValid) {
    return "BH1750_ERROR";
  }
  if (dayPhase == PHASE_UNKNOWN) {
    return "PHASE_UNKNOWN";
  }
  if (!ntpTimeSynced) {
    return "WAITING_FOR_NTP_TIME";
  }
  if (!heaterScheduleActive) {
    return "OUTSIDE_HEATER_SCHEDULE";
  }

  switch (dayPhase) {
    case PHASE_NIGHT:
      return heaterState ? "NIGHT_AUTO_ON" : "NIGHT_STATE_OFF";
    case PHASE_DAWN:
      if (isRaining) return "DAWN_RAIN";
      if (dawnHumidityDemand) return "DAWN_HUMIDITY";
      return "DAWN_NO_DEMAND";
    case PHASE_DAY:
      return isRaining ? "DAY_RAIN" : "DAY_NO_RAIN";
    default:
      return "UNKNOWN";
  }
}

// ==============================================================================
// 13. LOGIKA KONTROL HEATER & SAFETY INTERLOCK (COMBINE)
// ==============================================================================
void updateHeaterControl() {
  // Evaluasi jadwal operasional NTP WIB (18:00 - 05:00 WIB)
  heaterScheduleActive = isWithinHeaterSchedule();

  // 1. Penguncian Suhu Darurat (Safety Lockout):
  //    OFF jika suhu >= 50°C, kembali diizinkan ON jika suhu <= 48°C
  if (dhtOK) {
    if (temp >= MAX_TEMP_C) {
      temperatureLockout = true;
    } else if (temp <= RESTART_TEMP_C) {
      temperatureLockout = false;
    }

    // Permintaan pemanasan kelembapan subuh
    if (hum >= DAWN_HUMIDITY_ON) {
      dawnHumidityDemand = true;
    } else if (hum <= DAWN_HUMIDITY_OFF) {
      dawnHumidityDemand = false;
    }
  }

  // Interlock Keselamatan Mutlak Suhu Kritis (≥ 50°C): Wajib mematikan heater
  if (temperatureLockout || (dhtOK && temp >= MAX_TEMP_C)) {
    heaterState = false;
    return;
  }

  // 2. KENDALI MANUAL DARI WEBSITE / MQTT (REMOTE OVERRIDE):
  //    Jika operator mengubah status heater lewat website/MQTT, prioritaskan kendali web!
  if (ENABLE_REMOTE_CONTROL && remoteHeaterOverrideActive) {
    heaterState = remoteHeaterRequest;
    return;
  }

  // 3. LOGIKA OTOMATIS (Bila tidak ada override manual dari web)
  // Syarat sensor valid:
  if (!dhtOK || !bh1750Ready || !luxReadingValid || dayPhase == PHASE_UNKNOWN) {
    heaterState = false;
    return;
  }

  // Syarat sinkronisasi waktu NTP:
  if (!ntpTimeSynced) {
    heaterState = false;
    return;
  }

  // Syarat jam operasional (18:00 - 05:00 WIB):
  if (!heaterScheduleActive) {
    heaterState = false;
    return;
  }

  // Siklus Otomatis Histeresis Berdasarkan Fase Cahaya:
  switch (dayPhase) {
    case PHASE_NIGHT:
      // Malam: Pemanas ON terus menerus selama suhu dalam batas aman
      heaterState = true;
      break;

    case PHASE_DAWN:
      // Subuh: ON jika hujan atau kelembapan udara tinggi (>= 70%)
      heaterState = isRaining || dawnHumidityDemand;
      break;

    case PHASE_DAY:
      // Siang: ON hanya saat mendung gelap atau hujan
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
// 15. KONEKSI & KONTROL MQTT
// ==============================================================================
void maintainMQTT() {
  if (WiFi.status() != WL_CONNECTED) {
    lastMqttPublishOk = false;
    return;
  }

  if (mqttClient.connected()) {
    mqttClient.loop();
    return;
  }

  const unsigned long now = millis();
  if (now - lastMqttAttempt < MQTT_RETRY_INTERVAL) {
    return;
  }
  lastMqttAttempt = now;

  String mac = WiFi.macAddress();
  mac.replace(":", "");
  String clientId = String("ESP32-") + deviceId + "-" + mac.substring(mac.length() - 4);

  Serial.printf("[MQTT] Menghubungkan ke Broker %s:%d (Client ID: %s)... ",
                MQTT_BROKER, MQTT_PORT, clientId.c_str());

  if (mqttClient.connect(clientId.c_str())) {
    Serial.println("SUKSES!");
    Serial.println("[MQTT SUKSES] Terhubung ke Broker HiveMQ!");
    if (ENABLE_REMOTE_CONTROL) {
      mqttClient.subscribe(TOPIC_CONTROL);
      Serial.printf("[MQTT SUKSES] Berlangganan topik kontrol website: %s\n", TOPIC_CONTROL);
    }
  } else {
    Serial.printf("GAGAL (RC: %d). Akan mencoba ulang otomatis.\n", mqttClient.state());
  }
}

void mqttCallback(char* topic, byte* payload, unsigned int length) {
  if (!ENABLE_REMOTE_CONTROL) {
    return;
  }

  if (String(topic) != TOPIC_CONTROL) {
    return;
  }

  StaticJsonDocument<768> command;
  DeserializationError error = deserializeJson(command, payload, length);
  if (error) {
    Serial.println("[MQTT] Perintah kontrol tidak dapat di-parse JSON.");
    return;
  }

  // Format 1: Perintah kembali ke Mode Otomatis {"mode":"AUTO"} atau {"data":{"overrideMode":"AUTO"}}
  bool setAutoMode = false;
  if ((command["mode"].is<const char*>() && String(command["mode"].as<const char*>()) == "AUTO") ||
      (command["overrideMode"].is<const char*>() && String(command["overrideMode"].as<const char*>()) == "AUTO") ||
      (command["data"]["overrideMode"].is<const char*>() && String(command["data"]["overrideMode"].as<const char*>()) == "AUTO") ||
      (command.containsKey("isOverrideActive") && !command["isOverrideActive"].as<bool>()) ||
      (command["data"].containsKey("isOverrideActive") && !command["data"]["isOverrideActive"].as<bool>())) {
    setAutoMode = true;
  }

  if (setAutoMode) {
    remoteHeaterOverrideActive = false;
    remoteHeaterRequest = false;
    updateHeaterControl();
    applyRelayState();
    Serial.println("[MQTT] Perintah Web Diterima -> Mode AUTO Aktif (Kembali ke histeresis otomatis).");
    return;
  }

  // Format 2: Perintah Langsung ON / OFF Relay Heater dari Web
  bool requestedState = false;
  bool hasState = false;

  if (command["data"].containsKey("auxHeaterStatus")) {
    requestedState = command["data"]["auxHeaterStatus"].as<bool>();
    hasState = true;
  } else if (command.containsKey("auxHeaterStatus")) {
    requestedState = command["auxHeaterStatus"].as<bool>();
    hasState = true;
  } else if (command["data"].containsKey("heaterStatus")) {
    requestedState = command["data"]["heaterStatus"].as<bool>();
    hasState = true;
  } else if (command.containsKey("heaterStatus")) {
    requestedState = command["heaterStatus"].as<bool>();
    hasState = true;
  } else if (command["data"].containsKey("heater_status")) {
    requestedState = command["data"]["heater_status"].as<bool>();
    hasState = true;
  } else if (command.containsKey("heater_status")) {
    requestedState = command["heater_status"].as<bool>();
    hasState = true;
  } else if (command["data"].containsKey("heater")) {
    requestedState = command["data"]["heater"].as<bool>();
    hasState = true;
  } else if (command.containsKey("heater")) {
    requestedState = command["heater"].as<bool>();
    hasState = true;
  } else if (command.containsKey("target") && String(command["target"].as<const char*>()) == "AUX_HEATER") {
    if (command.containsKey("state")) {
      requestedState = command["state"].as<bool>();
      hasState = true;
    }
  }

  if (!hasState) {
    return;
  }

  // Interlock Keselamatan Mutlak: Tolak menyalakan heater jika suhu kritis >= 50°C
  if (requestedState && (temperatureLockout || (dhtOK && temp >= MAX_TEMP_C))) {
    Serial.println("[MQTT] DITOLAK: Suhu >= 50°C (Overheat Lockout). Heater tetap OFF demi keselamatan.");
    heaterState = false;
    remoteHeaterOverrideActive = true;
    remoteHeaterRequest = false;
    applyRelayState();
    return;
  }

  // Terapkan override manual relay dari website
  remoteHeaterOverrideActive = true;
  remoteHeaterRequest = requestedState;
  heaterState = requestedState;
  applyRelayState();

  Serial.printf("[MQTT] Perintah Web Diterapkan -> RELAY HEATER: %s (Pin 26 & 27)\n",
                requestedState ? "ON (MENYALA)" : "OFF (MATI)");
}

// ==============================================================================
// 16. PUBLIKASI TELEMETRI (MQTT & HTTP DUAL TRANSMISSION)
// ==============================================================================
bool publishMqttTelemetry() {
  if (!ENABLE_MQTT_TELEMETRY || !hasCompletedSensorRead || !mqttClient.connected()) {
    lastMqttPublishOk = false;
    hasMqttPublishResult = true;
    lastMqttPublishMillis = millis();
    return false;
  }

  StaticJsonDocument<1536> doc;
  doc["deviceId"]  = deviceId;
  doc["device_id"] = deviceId;

  // Sensor Suhu & Kelembapan
  if (dhtOK) {
    doc["tempInternal"]     = round(temp * 10.0) / 10.0;
    doc["humidityInternal"] = round(hum * 10.0) / 10.0;
  } else {
    doc["tempInternal"]     = nullptr;
    doc["humidityInternal"] = nullptr;
  }

  // Sensor Intensitas Cahaya
  if (bh1750Ready && luxReadingValid) {
    doc["lux"]            = round(lux * 10.0) / 10.0;
    doc["solarRadiation"] = round(lux * 10.0) / 10.0;
  } else {
    doc["lux"]            = nullptr;
    doc["solarRadiation"] = nullptr;
  }

  // Sensor Hujan & Status Lingkungan
  doc["rainAdc"]    = rainVal;
  doc["isRaining"]  = isRaining;
  doc["rainStatus"] = isRaining ? "HUJAN" : "KERING";
  doc["dhtOk"]      = dhtOK;
  doc["bh1750Ok"]   = bh1750Ready && luxReadingValid;
  doc["dayPhase"]   = phaseName();

  // Status Aktuator Heater & Relay
  doc["auxHeater"]            = heaterState;
  doc["auxHeaterStatus"]      = heaterState;
  doc["heaterState"]          = heaterState;
  doc["heaterOn"]             = heaterState;
  doc["heaterStatus"]         = heaterState;
  doc["relayCh1"]             = heaterState;
  doc["relayCh2"]             = heaterState;
  doc["heaterStatusText"]     = heaterState ? "ON" : "OFF";
  doc["relayStatus"]          = heaterState ? "ON" : "OFF";
  doc["heaterReason"]         = heaterReasonName();
  doc["relayActiveLow"]       = RELAY_ACTIVE_LOW;
  doc["heaterScheduleStart"]  = "18:00";
  doc["heaterScheduleEnd"]    = "05:00";
  doc["heaterScheduleActive"] = heaterScheduleActive;
  doc["heaterScheduleStatus"] = !ntpTimeSynced
      ? "WAITING_FOR_TIME"
      : (heaterScheduleActive ? "ACTIVE" : "OUTSIDE_SCHEDULE");

  doc["heaterMode"]           = (ENABLE_REMOTE_CONTROL && remoteHeaterOverrideActive) ? "MANUAL" : "AUTO";
  doc["temperatureLockout"]   = temperatureLockout;
  doc["sensorSafe"]           = dhtOK && bh1750Ready && luxReadingValid &&
                                dayPhase != PHASE_UNKNOWN && !temperatureLockout &&
                                temp < MAX_TEMP_C;
  doc["uptimeMs"]             = millis();

  // Waktu Lokal Sinkron NTP WIB
  doc["ntpSynced"] = ntpTimeSynced;
  doc["timeZone"]  = "WIB";
  if (ntpTimeSynced) {
    struct tm timeInfo;
    if (getLocalTime(&timeInfo, 10)) {
      char dateBuffer[11];
      char timeBuffer[9];
      char dateTimeBuffer[20];
      strftime(dateBuffer, sizeof(dateBuffer), "%Y-%m-%d", &timeInfo);
      strftime(timeBuffer, sizeof(timeBuffer), "%H:%M:%S", &timeInfo);
      strftime(dateTimeBuffer, sizeof(dateTimeBuffer), "%Y-%m-%d %H:%M:%S", &timeInfo);
      doc["localDate"]     = dateBuffer;
      doc["localTime"]     = timeBuffer;
      doc["localDateTime"] = dateTimeBuffer;
    } else {
      doc["ntpSynced"]     = false;
      doc["localDate"]     = nullptr;
      doc["localTime"]     = nullptr;
      doc["localDateTime"] = nullptr;
    }
  } else {
    doc["localDate"]     = nullptr;
    doc["localTime"]     = nullptr;
    doc["localDateTime"] = nullptr;
  }

  char payload[1536];
  const size_t bytes = serializeJson(doc, payload, sizeof(payload));
  if (bytes == 0 || bytes >= sizeof(payload)) {
    lastMqttPublishOk = false;
    hasMqttPublishResult = true;
    lastMqttPublishMillis = millis();
    return false;
  }

  const bool ok = mqttClient.publish(TOPIC_TELEMETRY, payload);
  lastMqttPublishOk = ok;
  hasMqttPublishResult = true;
  lastMqttPublishMillis = millis();

  if (ok) {
    Serial.println("[MQTT SUKSES] Paket telemetri terkirim ke topic: " + String(TOPIC_TELEMETRY));
  }
  return ok;
}

bool sendHttpTelemetry() {
  if (!ENABLE_HTTP_TELEMETRY || !hasCompletedSensorRead || WiFi.status() != WL_CONNECTED) {
    lastHttpPublishOk = false;
    hasHttpPublishResult = true;
    lastHttpPublishMillis = millis();
    return false;
  }

  HTTPClient http;
  http.setTimeout(4500);

  WiFiClientSecure secureClient;
  WiFiClient standardClient;
  bool beginOk = false;

  // Mendukung HTTPS (port 443) dengan bypass validasi SSL root CA, atau HTTP biasa (port 80)
  if (String(serverUrl).startsWith("https://")) {
    secureClient.setInsecure(); // Kompatibel dengan semua core ESP32 tanpa sertifikat root CA lokal
    beginOk = http.begin(secureClient, serverUrl);
  } else {
    beginOk = http.begin(standardClient, serverUrl);
  }

  if (!beginOk) {
    Serial.println("[HTTP ERROR] Inisialisasi koneksi HTTP gagal.");
    lastHttpPublishOk = false;
    hasHttpPublishResult = true;
    lastHttpPublishMillis = millis();
    return false;
  }

  http.addHeader("Content-Type", "application/json");
  http.addHeader("X-Device-Key", apiKey);
  http.addHeader("X-Device-Token", apiKey);

  // Buat Payload JSON HTTP kompatibel dengan TelemetryController Laravel
  StaticJsonDocument<768> httpDoc;
  httpDoc["deviceId"]         = deviceId;
  httpDoc["device_id"]        = deviceId;
  httpDoc["temperature"]      = dhtOK ? (round(temp * 10.0) / 10.0) : 0.0;
  httpDoc["tempInternal"]     = dhtOK ? (round(temp * 10.0) / 10.0) : 0.0;
  httpDoc["humidity"]         = dhtOK ? (round(hum * 10.0) / 10.0) : 0.0;
  httpDoc["humidityInternal"] = dhtOK ? (round(hum * 10.0) / 10.0) : 0.0;
  httpDoc["lux"]              = (bh1750Ready && luxReadingValid) ? (round(lux * 10.0) / 10.0) : 0.0;
  httpDoc["solarRadiation"]   = (bh1750Ready && luxReadingValid) ? (round(lux * 10.0) / 10.0) : 0.0;
  httpDoc["rain_adc"]         = rainVal;
  httpDoc["is_raining"]       = isRaining;
  httpDoc["dht_ok"]           = dhtOK;
  httpDoc["bh1750_ok"]        = (bh1750Ready && luxReadingValid);
  httpDoc["heater_on"]        = heaterState;
  httpDoc["heaterStatus"]     = heaterState;
  httpDoc["heaterReason"]     = heaterReasonName();
  httpDoc["dayPhase"]         = phaseName();

  String jsonPayload;
  serializeJson(httpDoc, jsonPayload);

  Serial.println("[HTTP POST] Mengirim data telemetri ke Website Hosting...");
  int responseCode = http.POST(jsonPayload);
  lastHttpResponseCode = responseCode;
  lastHttpPublishOk = (responseCode >= 200 && responseCode < 300);
  hasHttpPublishResult = true;
  lastHttpPublishMillis = millis();

  if (lastHttpPublishOk) {
    Serial.printf("[HTTP %d SUKSES] Data DITERIMA oleh Website Hosting!\n", responseCode);
  } else {
    Serial.printf("[HTTP %d INFO] Website Hosting respon kode %d (Data disalurkan via HiveMQ)\n",
                  responseCode, responseCode);
  }

  // Sinkronisasi status aktuator dari respons HTTP backend website
  if (lastHttpPublishOk && ENABLE_REMOTE_CONTROL) {
    String responseBody = http.getString();
    if (responseBody.length() > 0) {
      StaticJsonDocument<512> respDoc;
      DeserializationError respErr = deserializeJson(respDoc, responseBody);
      if (!respErr && respDoc.containsKey("actuators")) {
        JsonObject acts = respDoc["actuators"];
        bool isOverride = acts["isOverrideActive"].as<bool>();
        const char* mode = acts["overrideMode"].as<const char*>();

        if (isOverride && mode && String(mode) == "MANUAL") {
          bool reqState = acts["auxHeaterStatus"].as<bool>();
          if (reqState && (temperatureLockout || (dhtOK && temp >= MAX_TEMP_C))) {
            reqState = false;
          }
          if (!remoteHeaterOverrideActive || remoteHeaterRequest != reqState) {
            remoteHeaterOverrideActive = true;
            remoteHeaterRequest = reqState;
            heaterState = reqState;
            applyRelayState();
            Serial.printf("[HTTP Sync] Relay Heater DISINKRONKAN dari Web: %s\n",
                          reqState ? "ON (MENYALA)" : "OFF (MATI)");
          }
        } else if (!isOverride || (mode && String(mode) == "AUTO")) {
          if (remoteHeaterOverrideActive) {
            remoteHeaterOverrideActive = false;
            remoteHeaterRequest = false;
            updateHeaterControl();
            applyRelayState();
            Serial.println("[HTTP Sync] Mode AUTO disinkronkan dari Web.");
          }
        }
      }
    }
  }

  http.end();
  return lastHttpPublishOk;
}

bool sendTelemetry() {
  if (WiFi.status() != WL_CONNECTED) {
    lastMqttPublishOk = false;
    lastHttpPublishOk = false;
    hasMqttPublishResult = true;
    hasHttpPublishResult = true;
    return false;
  }

  bool mqttOk = false;
  bool httpOk = false;

  if (ENABLE_MQTT_TELEMETRY) {
    mqttOk = publishMqttTelemetry();
  }
  if (ENABLE_HTTP_TELEMETRY) {
    httpOk = sendHttpTelemetry();
  }

  return (mqttOk || httpOk);
}

// ==============================================================================
// 17. MANAJEMEN KONEKSI WIFI & NTP
// ==============================================================================
void maintainWiFi() {
  wl_status_t status = WiFi.status();

  if (status == WL_CONNECTED) {
    if (!wifiWasConnected) {
      wifiWasConnected = true;
      wifiFailRetries = 0;
      Serial.println();
      Serial.println("==================================================");
      Serial.println("[WiFi SUKSES] ESP32 BERHASIL TERHUBUNG KE JARINGAN!");
      Serial.printf("[WiFi] SSID Terhubung: %s\n", currentSsid);
      Serial.printf("[WiFi] IP Address    : %s\n", WiFi.localIP().toString().c_str());
      Serial.printf("[WiFi] Kuat Sinyal   : %d dBm\n", WiFi.RSSI());
      Serial.println("==================================================");

      // Sinkronkan waktu WIB via NTP
      configTime(GMT_OFFSET_SEC, DAYLIGHT_OFFSET_SEC,
                 NTP_SERVER_1, NTP_SERVER_2, NTP_SERVER_3);
      prevNtpCheckMillis = 0;
      Serial.println("[NTP] Memulai sinkronisasi waktu WIB (UTC+7)...");
    }
    return;
  }

  if (wifiWasConnected) {
    Serial.println("\n[WiFi] Koneksi terputus! Mencoba menyambungkan kembali...");
  }
  wifiWasConnected = false;

  const unsigned long now = millis();
  if (now - lastWifiAttempt >= WIFI_RETRY_INTERVAL) {
    lastWifiAttempt = now;
    wifiFailRetries++;

    // Jika gagal terhubung 3x berturut-turut pada SSID utama, beralih ke cadangan (atau sebaliknya)
    if (wifiFailRetries >= 3) {
      wifiFailRetries = 0;
      if (!usingSecondaryWifi && strlen(ssidSecondary) > 0) {
        usingSecondaryWifi = true;
        currentSsid = ssidSecondary;
        currentPassword = passwordSecondary;
        Serial.printf("\n[WiFi SWITCH] Beralih ke Hotspot Cadangan: \"%s\"\n", currentSsid);
      } else {
        usingSecondaryWifi = false;
        currentSsid = ssidPrimary;
        currentPassword = passwordPrimary;
        Serial.printf("\n[WiFi SWITCH] Kembali mencoba Hotspot Utama: \"%s\"\n", currentSsid);
      }
    }

    Serial.printf("[WiFi] Menghubungkan ke \"%s\" (Percobaan %d)... ", currentSsid, wifiFailRetries + 1);
    WiFi.disconnect();
    WiFi.begin(currentSsid, currentPassword);

    if (wifiFailRetries == 2) {
      Serial.println("\n--------------------------------------------------");
      Serial.println("[WiFi TIPS PENGGUNAAN]:");
      Serial.println("1. Pastikan Personal Hotspot di HP Anda AKTIF.");
      Serial.println("2. Pastikan Hotspot diatur pada frekuensi 2.4 GHz (ESP32 tidak mendukung 5 GHz).");
      Serial.printf("3. Pastikan Nama Hotspot di HP persis: \"%s\"\n", currentSsid);
      Serial.printf("4. Pastikan Password Hotspot di HP persis: \"%s\"\n", currentPassword);
      Serial.println("--------------------------------------------------");
    }
  }
}

void updateNtpStatus() {
  const unsigned long now = millis();
  if (now - prevNtpCheckMillis < NTP_CHECK_INTERVAL) {
    return;
  }
  prevNtpCheckMillis = now;

  struct tm timeInfo;
  const bool timeValid = getLocalTime(&timeInfo, 10) &&
                         (timeInfo.tm_year >= (2024 - 1900));
  if (timeValid) {
    ntpTimeSynced = true;
  }
}

bool isWithinHeaterSchedule() {
  if (!ntpTimeSynced) {
    return false;
  }

  struct tm timeInfo;
  if (!getLocalTime(&timeInfo, 10) || timeInfo.tm_year < (2024 - 1900)) {
    return false;
  }

  const int hourNow = timeInfo.tm_hour;

  // Jadwal melewati tengah malam: 18:00 hingga sebelum 05:00 WIB
  if (HEATER_START_HOUR > HEATER_STOP_HOUR) {
    return (hourNow >= HEATER_START_HOUR || hourNow < HEATER_STOP_HOUR);
  }

  return (hourNow >= HEATER_START_HOUR && hourNow < HEATER_STOP_HOUR);
}

// ==============================================================================
// 18. SERIAL MONITOR — DASHBOARD MONITORING
// ==============================================================================
void printFriendlyDashboard() {
  Serial.println();
  Serial.println("==================================================");
  Serial.println("       SMART ROOM DRYER - DESA WISATA HANJELI     ");
  Serial.println("==================================================");

  Serial.printf("Kondisi Cahaya  : %s\n", phaseName());

  if (dhtOK) {
    Serial.printf("Suhu Ruangan    : %.1f C\n", temp);
    Serial.printf("Kelembapan Udara: %.1f %%\n", hum);
  } else {
    Serial.printf("Suhu / RH Udara : SENSOR ERROR (%d/%d)\n", dhtErrorCount, MAX_DHT_ERRORS);
  }

  if (bh1750Ready && luxReadingValid) {
    Serial.printf("Intensitas Lux  : %.1f lux\n", lux);
  } else {
    Serial.println("Intensitas Lux  : BH1750 ERROR / TIDAK TERBACA");
  }

  Serial.printf("Sensor Hujan    : %s (ADC: %d)\n",
                isRaining ? "HUJAN TERDETEKSI" : "TIDAK HUJAN", rainVal);

  Serial.printf("Jadwal Heater   : %s\n",
                !ntpTimeSynced ? "MENUNGGU SINKRONISASI WAKTU NTP" :
                (heaterScheduleActive ? "AKTIF (18:00 - 05:00 WIB)" : "DI LUAR JADWAL (OFF)"));

  Serial.printf("Pemanas (PTC)   : %s\n",
                heaterState ? "ON - MENYALA" : "OFF - STANDBY");

  Serial.printf("Alasan Kontrol  : %s\n", heaterReasonName());
  Serial.printf("Lockout Suhu    : %s\n", temperatureLockout ? "LOCKOUT AKTIF (SUHU TINGGI)" : "NORMAL");

  Serial.println("--------------------------------------------------");
  Serial.printf("Koneksi Wi-Fi   : %s (SSID: %s, IP: %s)\n",
                WiFi.status() == WL_CONNECTED ? "TERHUBUNG" : "OFFLINE",
                currentSsid,
                WiFi.status() == WL_CONNECTED ? WiFi.localIP().toString().c_str() : "-");

  Serial.printf("Broker MQTT     : %s [%s]\n",
                mqttClient.connected() ? "TERHUBUNG" : "BELUM TERHUBUNG",
                lastMqttPublishOk ? "KIRIM SUKSES" : "MENUNGGU / GAGAL");

  Serial.printf("HTTP Backend    : %s [%s, Kode: %d]\n",
                serverUrl,
                lastHttpPublishOk ? "SUKSES DITERIMA" : "BELUM / GAGAL",
                lastHttpResponseCode);

  Serial.println("==================================================");
}

// ==============================================================================
// 19. TAMPILAN LCD 20x4 (ANTI-FLICKER & CLEAN PADDING)
// ==============================================================================
void printLCDLine(uint8_t row, String line) {
  if (line.length() > 20) {
    line = line.substring(0, 20);
  }

  lcd.setCursor(0, row);
  lcd.print(line);
  for (size_t i = line.length(); i < 20; i++) {
    lcd.print(' ');
  }
}

void updateLCD() {
  if (currentPage == 0) {
    // HALAMAN 1: Monitoring Real-Time Sensor & Relay & Status Jaringan Langsung
    printLCDLine(0, " SMART ROOM DRYER   ");

    String sensorLine;
    if (dhtOK) {
      sensorLine = "T:";
      sensorLine += String(temp, 1);
      sensorLine += (char)223;  // Derajat Celcius
      sensorLine += "C H:";
      sensorLine += String(hum, 1);
      sensorLine += "%";
    } else {
      sensorLine = "T:ERROR H:ERROR";
    }
    printLCDLine(1, sensorLine);

    String lightRainLine = "L:";
    if (luxReadingValid) {
      lightRainLine += String((unsigned long)lux);
    } else {
      lightRainLine += "ERR";
    }
    lightRainLine += "lx";
    while (lightRainLine.length() < 11) {
      lightRainLine += ' ';
    }
    lightRainLine += "Rain:";
    lightRainLine += (isRaining ? "YES" : "NO");
    printLCDLine(2, lightRainLine);

    // Baris 4: Tampilkan status Heater & Status Koneksi Web Jelas!
    String statusLine = "H:";
    statusLine += (heaterState ? "ON " : "OFF");
    statusLine += "|NET:";
    if (WiFi.status() == WL_CONNECTED) {
      if (lastHttpPublishOk) {
        statusLine += "ONLINE(WEB)";
      } else if (mqttClient.connected()) {
        statusLine += "OK(MQTT)  ";
      } else {
        statusLine += "WIFI OK   ";
      }
    } else {
      statusLine += "DISCONNECT";
    }
    printLCDLine(3, statusLine);

  } else {
    // HALAMAN 2: Diagnostik, Jam NTP WIB, & IP Jaringan
    String diagnosticTitle = "DIAG ";
    if (ntpTimeSynced) {
      struct tm timeInfo;
      if (getLocalTime(&timeInfo, 10)) {
        char clockBuffer[9];
        strftime(clockBuffer, sizeof(clockBuffer), "%H:%M:%S", &timeInfo);
        diagnosticTitle += clockBuffer;
        diagnosticTitle += " WIB";
      } else {
        diagnosticTitle += "NTP MENUNGGU";
      }
    } else {
      diagnosticTitle += "NTP MENUNGGU";
    }
    printLCDLine(0, diagnosticTitle);

    const bool displayBhOk = bh1750Ready && luxReadingValid;
    String statusLine = "DHT:";
    statusLine += (dhtOK ? "OK" : "ERR");
    statusLine += " BH:";
    statusLine += (displayBhOk ? "OK" : "ERR");
    statusLine += " P:";
    statusLine += phaseShortName();
    printLCDLine(1, statusLine);

    String rainLine = "RAIN ADC:";
    rainLine += String(rainVal);
    rainLine += (isRaining ? " HUJAN" : " KERING");
    printLCDLine(2, rainLine);

    String networkLine;
    if (WiFi.status() == WL_CONNECTED) {
      networkLine = "IP:";
      networkLine += WiFi.localIP().toString();
    } else {
      networkLine = "WiFi: DISCONNECTED";
    }
    printLCDLine(3, networkLine);
  }
}
