/**
 * Arduino / ESP32 Firmware Source Codes — Smart Room Dryer Desa Wisata Hanjeli
 * 
 * Hardware Pinout Sesuai Alat Fisik:
 * - DHT22 (Suhu & RH Ruang) : GPIO 25
 * - Relai Pemanas PTC 1     : GPIO 26
 * - Relai Pemanas PTC 2     : GPIO 27
 * - Sensor Hujan (ADC)      : GPIO 34
 * - I2C (BH1750 & LCD 20x4) : SDA GPIO 21, SCL GPIO 22
 * - LCD I2C Address         : 0x27 (20 Kolom x 4 Baris)
 */

export const ESP32_PHYSICAL_STANDALONE_CODE = `/*
 * ==============================================================================
 * SMART ROOM DRYER — DESA WISATA HANJELI
 * FIRMWARE RESMI PERANGKAT FISIK (STANDALONE + USB SERIAL TELEMETRY)
 * ==============================================================================
 * 
 * Pinout Hardware:
 *   - DHT22 (Suhu & Kelembapan)  : GPIO 25
 *   - Relay CH1 (Pemanas PTC 1)  : GPIO 26
 *   - Relay CH2 (Pemanas PTC 2)  : GPIO 27
 *   - Sensor Hujan (Analog ADC)  : GPIO 34
 *   - I2C Bus (SDA / SCL)        : GPIO 21 / GPIO 22
 *   - Sensor Cahaya (BH1750)     : I2C (0x23 / Auto)
 *   - LCD Display 20x4           : I2C (0x27)
 * 
 * Logika Operasional & Keselamatan:
 *   - Suhu < 55.0 °C  -> Pemanas PTC 1 & 2 Otomatis ON
 *   - Suhu > 60.0 °C  -> Pemanas PTC 1 & 2 Otomatis OFF
 *   - Sensor DHT22 Error >= 3x berturut-turut -> SHUT-OFF Pemanas (Safety)
 *   - LCD 20x4 Switch Halaman Otomatis (Monitoring & Diagnostik Sistem)
 *   - Output Serial: Log Human-Readable & Stream JSON Telemetri untuk Web Serial
 * ==============================================================================
 */

#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <DHT.h>
#include <BH1750.h>

// ==========================================
// 1. PINOUT ESP32 & THRESHOLD KONTROL
// ==========================================
#define DHTPIN        25      // Pin DATA DHT22
#define DHTTYPE       DHT22
#define RELAY_CH1     26      // Control Relay PTC 1
#define RELAY_CH2     27      // Control Relay PTC 2
#define RAIN_PIN      34      // Input ADC Sensor Hujan

#define RAIN_THRESH   2000    // Threshold ADC Hujan (< 2000 = Hujan)
#define MAX_DHT_ERRORS 3      // Batas error berturut-turut sebelum SHUT-OFF!

// ==========================================
// 2. OBJEK & VARIABEL GLOBAL
// ==========================================
LiquidCrystal_I2C lcd(0x27, 20, 4);
DHT dht(DHTPIN, DHTTYPE);
BH1750 lightMeter;

bool bh1750Ready = false;

// Variabel Data Sensor
float temp = 0.0;
float hum  = 0.0;
float lux  = 0.0;
int rainVal = 4095;
bool isRaining = false;

// Variabel Kontrol & Keamanan (Safety)
bool heaterState = false; 
bool dhtOK = false;
byte dhtErrorCount = 0;

// Non-blocking timer
unsigned long prevSensorMillis = 0;
const long sensorInterval = 1500; 

unsigned long prevDisplayMillis = 0;
const long pageInterval = 4000;  
byte currentPage = 0;

void setup() {
  Serial.begin(115200);
  Serial.println(F("\\n[BOOT] Starting System Smart Room Dryer Hanjeli..."));

  // Config Pin Relay
  pinMode(RELAY_CH1, OUTPUT);
  pinMode(RELAY_CH2, OUTPUT);
  digitalWrite(RELAY_CH1, LOW); 
  digitalWrite(RELAY_CH2, LOW);
  pinMode(RAIN_PIN, INPUT);

  Wire.begin(21, 22);
  Wire.setClock(100000);

  lcd.init();
  lcd.backlight();
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print(" SMART ROOM DRYER   ");
  lcd.setCursor(0, 1);
  lcd.print(" Booting System...  ");

  dht.begin();

  if (lightMeter.begin(BH1750::CONTINUOUS_HIGH_RES_MODE)) {
    bh1750Ready = true;
    Serial.println(F("[OK] BH1750 Detected"));
  } else {
    bh1750Ready = false;
    Serial.println(F("[WARN] BH1750 Not Responding!"));
  }

  delay(1000);
  lcd.clear();
}

void loop() {
  unsigned long currentMillis = millis();

  // Task 1: Pembacaan Sensor & Kontrol Pemanas
  if (currentMillis - prevSensorMillis >= sensorInterval) {
    prevSensorMillis = currentMillis;

    // --- BACA SENSOR DHT22 ---
    float t = dht.readTemperature();
    float h = dht.readHumidity();

    if (!isnan(t) && !isnan(h)) {
      temp = t;
      hum  = h;
      dhtOK = true;
      dhtErrorCount = 0;
    } else {
      dhtErrorCount++;
      Serial.print(F("[ERR] DHT22 Read Error! Fail count: "));
      Serial.println(dhtErrorCount);

      if (dhtErrorCount >= MAX_DHT_ERRORS) {
        dhtOK = false;
      }
    }

    // --- BACA SENSOR BH1750 ---
    if (bh1750Ready) {
      float l = lightMeter.readLightLevel();
      if (l >= 0) lux = l;
    }

    // --- BACA SENSOR HUJAN ---
    rainVal = analogRead(RAIN_PIN);
    isRaining = (rainVal < RAIN_THRESH);

    // =========================================================
    // LOGIKA KONTROL HEATER (DI BAWAH 55 ON / DI ATAS 60 OFF)
    // =========================================================
    if (!dhtOK) {
      heaterState = false; // Matikan heater jika sensor fault
    } 
    else {
      if (temp < 55.0) {
        heaterState = true;
      } 
      else if (temp > 60.0) {
        heaterState = false;
      }
    }

    // Terapkan status ke pin Relay
    digitalWrite(RELAY_CH1, heaterState ? HIGH : LOW);
    digitalWrite(RELAY_CH2, heaterState ? HIGH : LOW);

    printToSerial();
  }

  // Task 2: Switch Halaman LCD
  if (currentMillis - prevDisplayMillis >= pageInterval) {
    prevDisplayMillis = currentMillis;
    currentPage = !currentPage;
    lcd.clear();
  }

  // Task 3: Render Tampilan LCD 20x4
  updateLCD();
}

void printToSerial() {
  Serial.println(F("=========================================="));
  if (!dhtOK) {
    Serial.println(F("STATUS SENSOR : [CRITICAL ERROR] DHT22 FAULT!"));
  } else {
    Serial.print(F("Suhu (DHT22)   : ")); Serial.print(temp, 1); Serial.println(F(" °C"));
    Serial.print(F("Kelembapan     : ")); Serial.print(hum, 1);  Serial.println(F(" %"));
  }
  Serial.print(F("Cahaya (BH1750): ")); Serial.print(lux, 1);  Serial.println(F(" Lux"));
  Serial.print(F("Rain Raw ADC   : ")); Serial.print(rainVal); 
  Serial.print(F(" | Status Hujan: ")); Serial.println(isRaining ? F("YA") : F("TIDAK"));
  Serial.print(F("Status Heater  : ")); 
  if (!dhtOK) {
    Serial.println(F("OFF [SAFETY SHUTDOWN]"));
  } else {
    Serial.println(heaterState ? F("ON (Heating...)") : F("OFF (Standby)"));
  }
  Serial.println(F("==========================================\\n"));

  // Stream Web Serial JSON Telemetry Packet (Terhubung Langsung ke Web Dashboard)
  Serial.print(F("{\\"deviceId\\":\\"ESP32-HANJELI\\",\\"tempInternal\\":"));
  Serial.print(dhtOK ? temp : 0.0, 1);
  Serial.print(F(",\\"humidityInternal\\":"));
  Serial.print(dhtOK ? hum : 0.0, 1);
  Serial.print(F(",\\"solarRadiation\\":"));
  Serial.print(lux, 1);
  Serial.print(F(",\\"rainVal\\":"));
  Serial.print(rainVal);
  Serial.print(F(",\\"isRaining\\":"));
  Serial.print(isRaining ? F("true") : F("false"));
  Serial.print(F(",\\"heaterStatus\\":"));
  Serial.print(heaterState ? F("true") : F("false"));
  Serial.print(F(",\\"auxHeaterStatus\\":"));
  Serial.print(heaterState ? F("true") : F("false"));
  Serial.print(F(",\\"dhtOK\\":"));
  Serial.print(dhtOK ? F("true") : F("false"));
  Serial.println(F("}"));
}

void updateLCD() {
  if (currentPage == 0) {
    // PAGE 1: MONITORING UTAMA
    lcd.setCursor(0, 0);
    lcd.print(" SMART ROOM DRYER   ");

    lcd.setCursor(0, 1);
    if (dhtOK) {
      lcd.print("T:"); lcd.print(temp, 1); lcd.print((char)223); lcd.print("C  ");
      lcd.setCursor(10, 1);
      lcd.print("H:"); lcd.print(hum, 1); lcd.print("%   ");
    } else {
      lcd.print("T:ERR!    H:ERR!    ");
    }

    lcd.setCursor(0, 2);
    lcd.print("L:"); 
    if (lux < 10000) lcd.print(" ");
    lcd.print((int)lux); lcd.print("Lx ");
    
    lcd.setCursor(10, 2);
    lcd.print("Rain:"); lcd.print(isRaining ? "YES" : "NO ");

    lcd.setCursor(0, 3);
    lcd.print("H1:"); lcd.print(heaterState ? "ON " : "OFF");
    lcd.setCursor(10, 3);
    lcd.print("H2:"); lcd.print(heaterState ? "ON " : "OFF");

  } else {
    // PAGE 2: STATUS TEKNIS & DIAGNOSTIK
    lcd.setCursor(0, 0);
    lcd.print("[SYSTEM DIAGNOSTIC] ");

    lcd.setCursor(0, 1);
    lcd.print("DHT status : "); 
    lcd.print(dhtOK ? "OK  " : "FAULT!");

    lcd.setCursor(0, 2);
    lcd.print("BH1750 status: "); 
    lcd.print(bh1750Ready ? "OK  " : "ERR ");

    lcd.setCursor(0, 3);
    lcd.print("Rain Raw ADC : "); 
    lcd.print(rainVal); lcd.print("   ");
  }
}
`;

export const ESP32_DUAL_MODE_CODE = `/*
 * ==============================================================================
 * SMART ROOM DRYER — DESA WISATA HANJELI
 * ESP32 DUAL CONNECTIVITY FIRMWARE (PRODUKSI & IOT CLOUD/BLE/USB)
 * Wi-Fi + MQTT, Web Bluetooth BLE & Web Serial Direct
 * ==============================================================================
 * 
 * Pinout Sesuai Alat Fisik:
 *   - DHT22 (Suhu & Kelembapan)  : GPIO 25
 *   - Relay CH1 (Pemanas PTC 1)  : GPIO 26
 *   - Relay CH2 (Pemanas PTC 2)  : GPIO 27
 *   - Sensor Hujan (Analog ADC)  : GPIO 34
 *   - I2C (BH1750 & LCD 20x4)    : SDA GPIO 21 / SCL GPIO 22
 *   - LCD I2C Address            : 0x27
 * 
 * Libraries Diperlukan:
 *   - DHT sensor library (Adafruit)
 *   - BH1750 (Christopher Laws)
 *   - LiquidCrystal_I2C
 *   - PubSubClient (Nick O'Leary)
 *   - ArduinoJson (v6/v7)
 *   - WiFi.h & Preferences.h (Built-in ESP32)
 *   - BLEDevice.h & BLEServer.h (Built-in ESP32)
 * ==============================================================================
 */

#include <Wire.h>
#include <LiquidCrystal_I2C.h>
#include <DHT.h>
#include <BH1750.h>
#include <WiFi.h>
#include <PubSubClient.h>
#include <ArduinoJson.h>
#include <Preferences.h>
#include <BLEDevice.h>
#include <BLEServer.h>
#include <BLEUtils.h>
#include <BLE2902.h>

// ==============================================================================
// 1. PIN DEFINITIONS & SAFETY THRESHOLDS
// ==============================================================================
#define DHTPIN        25      // Pin DATA DHT22
#define DHTTYPE       DHT22
#define RELAY_CH1     26      // Control Relay PTC 1
#define RELAY_CH2     27      // Control Relay PTC 2
#define RAIN_PIN      34      // Input ADC Sensor Hujan
#define STATUS_LED_PIN 2      // Onboard LED status

#define RAIN_THRESH   2000    // Threshold ADC Hujan (< 2000 = Hujan)
#define MAX_DHT_ERRORS 3      // Batas error berturut-turut sebelum SHUT-OFF

// ==============================================================================
// 2. BLE UUIDS (128-BIT HANJELI STANDARD)
// ==============================================================================
#define SERVICE_UUID      "19b10000-e8f2-537e-4f6c-d104768a1214"
#define CHAR_SENSOR_UUID  "19b10001-e8f2-537e-4f6c-d104768a1214"
#define CHAR_STATUS_UUID  "19b10002-e8f2-537e-4f6c-d104768a1214"
#define CHAR_CONTROL_UUID "19b10003-e8f2-537e-4f6c-d104768a1214"
#define CHAR_CONFIG_UUID  "19b10004-e8f2-537e-4f6c-d104768a1214"

// ==============================================================================
// 3. GLOBAL OBJECTS & STATE
// ==============================================================================
LiquidCrystal_I2C lcd(0x27, 20, 4);
DHT dht(DHTPIN, DHTTYPE);
BH1750 lightMeter;
bool bh1750Ready = false;

Preferences preferences;
WiFiClient espClient;
PubSubClient mqttClient(espClient);

BLEServer* pServer = NULL;
BLECharacteristic* pSensorChar = NULL;
BLECharacteristic* pStatusChar = NULL;
BLECharacteristic* pControlChar = NULL;
BLECharacteristic* pConfigChar = NULL;
bool bleClientConnected = false;

String wifi_ssid     = "GreenHouse_Hanjeli";
String wifi_password = "hanjelismartdryer";
String mqtt_broker   = "broker.emqx.io";
int    mqtt_port     = 1883;
String device_id     = "ESP32-HANJELI-01";

// Sensor Values
float temp = 0.0;
float hum  = 0.0;
float lux  = 0.0;
int rainVal = 4095;
bool isRaining = false;

// Control States
bool heaterState = false;
bool dhtOK = false;
byte dhtErrorCount = 0;
bool isManualOverride = false;
String overrideMode = "AUTO";

unsigned long prevSensorMillis = 0;
const long sensorInterval = 1500;
unsigned long prevDisplayMillis = 0;
const long pageInterval = 4000;
byte currentPage = 0;

void applyActuators();
void readSensors();
void printToSerial();
void updateLCD();
void broadcastTelemetry();
void reconnectMqtt();
void mqttCallback(char* topic, byte* message, unsigned int length);

// ==============================================================================
// BLE CALLBACKS
// ==============================================================================
class MyServerCallbacks : public BLEServerCallbacks {
  void onConnect(BLEServer* pServer) {
    bleClientConnected = true;
    digitalWrite(STATUS_LED_PIN, HIGH);
    Serial.println("[BLE] Dashboard Web Bluetooth Terhubung!");
  }
  void onDisconnect(BLEServer* pServer) {
    bleClientConnected = false;
    digitalWrite(STATUS_LED_PIN, LOW);
    Serial.println("[BLE] Dashboard Terputus. Mulai advertising kembali...");
    pServer->startAdvertising();
  }
};

class ControlCallbacks : public BLECharacteristicCallbacks {
  void onWrite(BLECharacteristic* pCharacteristic) {
    String rxValue = pCharacteristic->getValue();
    if (rxValue.length() == 0) return;

    DynamicJsonDocument doc(512);
    DeserializationError error = deserializeJson(doc, rxValue);
    if (error) return;

    JsonObject data = doc["data"].isNull() ? doc.as<JsonObject>() : doc["data"].as<JsonObject>();
    if (data.containsKey("auxHeaterStatus")) heaterState = data["auxHeaterStatus"];
    if (data.containsKey("isOverrideActive")) isManualOverride = data["isOverrideActive"];
    if (data.containsKey("overrideMode"))     overrideMode = data["overrideMode"].as<String>();

    applyActuators();
    broadcastTelemetry();
  }
};

class ConfigCallbacks : public BLECharacteristicCallbacks {
  void onWrite(BLECharacteristic* pCharacteristic) {
    String rxValue = pCharacteristic->getValue();
    if (rxValue.length() == 0) return;

    DynamicJsonDocument doc(512);
    DeserializationError error = deserializeJson(doc, rxValue);
    if (error) return;

    if (doc["action"] == "SET_WIFI_CONFIG") {
      if (doc.containsKey("ssid"))        wifi_ssid     = doc["ssid"].as<String>();
      if (doc.containsKey("password"))    wifi_password = doc["password"].as<String>();
      if (doc.containsKey("mqtt_broker")) mqtt_broker   = doc["mqtt_broker"].as<String>();
      if (doc.containsKey("device_id"))   device_id     = doc["device_id"].as<String>();

      preferences.begin("srd_config", false);
      preferences.putString("ssid", wifi_ssid);
      preferences.putString("password", wifi_password);
      preferences.putString("broker", mqtt_broker);
      preferences.putString("dev_id", device_id);
      preferences.end();

      WiFi.disconnect(true);
      delay(300);
      WiFi.begin(wifi_ssid.c_str(), wifi_password.c_str());
    }
  }
};

void applyActuators() {
  if (!dhtOK && !isManualOverride) {
    heaterState = false; // Safety shutoff
  }
  digitalWrite(RELAY_CH1, heaterState ? HIGH : LOW);
  digitalWrite(RELAY_CH2, heaterState ? HIGH : LOW);
}

void readSensors() {
  // 1. DHT22
  float t = dht.readTemperature();
  float h = dht.readHumidity();

  if (!isnan(t) && !isnan(h)) {
    temp = t;
    hum  = h;
    dhtOK = true;
    dhtErrorCount = 0;
  } else {
    dhtErrorCount++;
    if (dhtErrorCount >= MAX_DHT_ERRORS) {
      dhtOK = false;
    }
  }

  // 2. BH1750
  if (bh1750Ready) {
    float l = lightMeter.readLightLevel();
    if (l >= 0) lux = l;
  }

  // 3. Sensor Hujan
  rainVal = analogRead(RAIN_PIN);
  isRaining = (rainVal < RAIN_THRESH);

  // 4. Kontrol Otomatis Pemanas (Bila tidak dalam mode manual)
  if (!isManualOverride) {
    if (!dhtOK) {
      heaterState = false;
    } else {
      if (temp < 55.0) {
        heaterState = true;
      } else if (temp > 60.0) {
        heaterState = false;
      }
    }
    applyActuators();
  }
}

void broadcastTelemetry() {
  DynamicJsonDocument doc(512);
  doc["deviceId"]         = device_id;
  doc["tempInternal"]     = dhtOK ? (round(temp * 10.0) / 10.0) : 0.0;
  doc["humidityInternal"] = dhtOK ? (round(hum * 10.0) / 10.0) : 0.0;
  doc["solarRadiation"]   = round(lux);
  doc["lux"]              = round(lux);
  doc["rainVal"]          = rainVal;
  doc["isRaining"]        = isRaining;
  doc["heaterStatus"]     = heaterState;
  doc["auxHeaterStatus"]  = heaterState;
  doc["dhtOK"]            = dhtOK;
  doc["isOverrideActive"] = isManualOverride;
  doc["overrideMode"]     = overrideMode;
  doc["hasData"]          = true;

  String jsonBuffer;
  serializeJson(doc, jsonBuffer);

  // Kirim via MQTT
  if (mqttClient.connected()) {
    mqttClient.publish("hanjeli/greenhouse/telemetry", jsonBuffer.c_str());
    mqttClient.publish("greenhouse/telemetry", jsonBuffer.c_str());
  }

  // Kirim via Web Bluetooth BLE
  if (bleClientConnected && pSensorChar != NULL) {
    pSensorChar->setValue((uint8_t*)jsonBuffer.c_str(), jsonBuffer.length());
    pSensorChar->notify();
  }
}

void mqttCallback(char* topic, byte* message, unsigned int length) {
  String payload = "";
  for (unsigned int i = 0; i < length; i++) payload += (char)message[i];

  DynamicJsonDocument doc(512);
  if (deserializeJson(doc, payload) == DeserializationError::Ok) {
    JsonObject data = doc["data"].isNull() ? doc.as<JsonObject>() : doc["data"].as<JsonObject>();
    if (data.containsKey("auxHeaterStatus")) heaterState = data["auxHeaterStatus"];
    if (data.containsKey("isOverrideActive")) isManualOverride = data["isOverrideActive"];
    if (data.containsKey("overrideMode"))     overrideMode = data["overrideMode"].as<String>();
    applyActuators();
    broadcastTelemetry();
  }
}

void reconnectMqtt() {
  if (WiFi.status() != WL_CONNECTED || mqttClient.connected()) return;
  String clientId = "ESP32_SRD_" + device_id + "_" + String(random(0xffff), HEX);
  if (mqttClient.connect(clientId.c_str())) {
    mqttClient.subscribe("hanjeli/greenhouse/control");
    mqttClient.subscribe("greenhouse/control");
    Serial.println("[MQTT] Terkoneksi & Berlangganan!");
  }
}

void printToSerial() {
  Serial.println(F("=========================================="));
  if (!dhtOK) {
    Serial.println(F("STATUS SENSOR : [CRITICAL ERROR] DHT22 FAULT!"));
  } else {
    Serial.print(F("Suhu (DHT22)   : ")); Serial.print(temp, 1); Serial.println(F(" °C"));
    Serial.print(F("Kelembapan     : ")); Serial.print(hum, 1);  Serial.println(F(" %"));
  }
  Serial.print(F("Cahaya (BH1750): ")); Serial.print(lux, 1);  Serial.println(F(" Lux"));
  Serial.print(F("Rain Raw ADC   : ")); Serial.print(rainVal); 
  Serial.print(F(" | Status Hujan: ")); Serial.println(isRaining ? F("YA") : F("TIDAK"));
  Serial.print(F("Status Heater  : ")); 
  if (!dhtOK) {
    Serial.println(F("OFF [SAFETY SHUTDOWN]"));
  } else {
    Serial.println(heaterState ? F("ON (Heating...)") : F("OFF (Standby)"));
  }
  Serial.println(F("==========================================\\n"));

  // Stream Web Serial JSON Packet
  Serial.print(F("{\\"deviceId\\":\\"")); Serial.print(device_id);
  Serial.print(F("\\",\\"tempInternal\\":")); Serial.print(dhtOK ? temp : 0.0, 1);
  Serial.print(F(",\\"humidityInternal\\":")); Serial.print(dhtOK ? hum : 0.0, 1);
  Serial.print(F(",\\"solarRadiation\\":")); Serial.print(lux, 1);
  Serial.print(F(",\\"rainVal\\":")); Serial.print(rainVal);
  Serial.print(F(",\\"isRaining\\":")); Serial.print(isRaining ? F("true") : F("false"));
  Serial.print(F(",\\"heaterStatus\\":")); Serial.print(heaterState ? F("true") : F("false"));
  Serial.print(F(",\\"auxHeaterStatus\\":")); Serial.print(heaterState ? F("true") : F("false"));
  Serial.print(F(",\\"dhtOK\\":")); Serial.print(dhtOK ? F("true") : F("false"));
  Serial.println(F("}"));
}

void updateLCD() {
  if (currentPage == 0) {
    lcd.setCursor(0, 0);
    lcd.print(" SMART ROOM DRYER   ");

    lcd.setCursor(0, 1);
    if (dhtOK) {
      lcd.print("T:"); lcd.print(temp, 1); lcd.print((char)223); lcd.print("C  ");
      lcd.setCursor(10, 1);
      lcd.print("H:"); lcd.print(hum, 1); lcd.print("%   ");
    } else {
      lcd.print("T:ERR!    H:ERR!    ");
    }

    lcd.setCursor(0, 2);
    lcd.print("L:"); 
    if (lux < 10000) lcd.print(" ");
    lcd.print((int)lux); lcd.print("Lx ");
    
    lcd.setCursor(10, 2);
    lcd.print("Rain:"); lcd.print(isRaining ? "YES" : "NO ");

    lcd.setCursor(0, 3);
    lcd.print("H1:"); lcd.print(heaterState ? "ON " : "OFF");
    lcd.setCursor(10, 3);
    lcd.print("H2:"); lcd.print(heaterState ? "ON " : "OFF");
  } else {
    lcd.setCursor(0, 0);
    lcd.print("[SYSTEM DIAGNOSTIC] ");

    lcd.setCursor(0, 1);
    lcd.print("DHT status : "); 
    lcd.print(dhtOK ? "OK  " : "FAULT!");

    lcd.setCursor(0, 2);
    lcd.print("BH1750 status: "); 
    lcd.print(bh1750Ready ? "OK  " : "ERR ");

    lcd.setCursor(0, 3);
    lcd.print("Rain Raw ADC : "); 
    lcd.print(rainVal); lcd.print("   ");
  }
}

void setup() {
  Serial.begin(115200);
  pinMode(RELAY_CH1, OUTPUT);
  pinMode(RELAY_CH2, OUTPUT);
  pinMode(STATUS_LED_PIN, OUTPUT);
  pinMode(RAIN_PIN, INPUT);
  digitalWrite(RELAY_CH1, LOW);
  digitalWrite(RELAY_CH2, LOW);

  Wire.begin(21, 22);
  Wire.setClock(100000);

  lcd.init();
  lcd.backlight();
  lcd.clear();
  lcd.setCursor(0, 0);
  lcd.print(" SMART ROOM DRYER   ");
  lcd.setCursor(0, 1);
  lcd.print(" Booting Dual IoT.. ");

  dht.begin();
  if (lightMeter.begin(BH1750::CONTINUOUS_HIGH_RES_MODE)) {
    bh1750Ready = true;
  }

  preferences.begin("srd_config", true);
  wifi_ssid     = preferences.getString("ssid", wifi_ssid);
  wifi_password = preferences.getString("password", wifi_password);
  mqtt_broker   = preferences.getString("broker", mqtt_broker);
  device_id     = preferences.getString("dev_id", device_id);
  preferences.end();

  BLEDevice::init("SmartDryer-Hanjeli");
  pServer = BLEDevice::createServer();
  pServer->setCallbacks(new MyServerCallbacks());

  BLEService* pService = pServer->createService(SERVICE_UUID);
  pSensorChar = pService->createCharacteristic(CHAR_SENSOR_UUID, BLECharacteristic::PROPERTY_READ | BLECharacteristic::PROPERTY_NOTIFY);
  pSensorChar->addDescriptor(new BLE2902());
  pStatusChar = pService->createCharacteristic(CHAR_STATUS_UUID, BLECharacteristic::PROPERTY_READ | BLECharacteristic::PROPERTY_NOTIFY);
  pStatusChar->addDescriptor(new BLE2902());
  pControlChar = pService->createCharacteristic(CHAR_CONTROL_UUID, BLECharacteristic::PROPERTY_WRITE | BLECharacteristic::PROPERTY_READ);
  pControlChar->setCallbacks(new ControlCallbacks());
  pConfigChar = pService->createCharacteristic(CHAR_CONFIG_UUID, BLECharacteristic::PROPERTY_WRITE | BLECharacteristic::PROPERTY_READ);
  pConfigChar->setCallbacks(new ConfigCallbacks());
  pService->start();

  BLEAdvertising* pAdvertising = BLEDevice::getAdvertising();
  pAdvertising->addServiceUUID(SERVICE_UUID);
  pAdvertising->setScanResponse(true);
  BLEDevice::startAdvertising();

  WiFi.mode(WIFI_STA);
  WiFi.begin(wifi_ssid.c_str(), wifi_password.c_str());

  mqttClient.setServer(mqtt_broker.c_str(), mqtt_port);
  mqttClient.setCallback(mqttCallback);
  mqttClient.setBufferSize(1024);

  delay(1000);
  lcd.clear();
}

void loop() {
  if (WiFi.status() == WL_CONNECTED) {
    if (!mqttClient.connected()) reconnectMqtt();
    else mqttClient.loop();
    digitalWrite(STATUS_LED_PIN, HIGH);
  } else {
    digitalWrite(STATUS_LED_PIN, (millis() / 500) % 2);
  }

  unsigned long currentMillis = millis();

  // Task 1: Pembacaan Sensor & Kontrol
  if (currentMillis - prevSensorMillis >= sensorInterval) {
    prevSensorMillis = currentMillis;
    readSensors();
    printToSerial();
    broadcastTelemetry();
  }

  // Task 2: Switch LCD
  if (currentMillis - prevDisplayMillis >= pageInterval) {
    prevDisplayMillis = currentMillis;
    currentPage = !currentPage;
    lcd.clear();
  }

  // Task 3: Update LCD
  updateLCD();
  delay(10);
}
`;

export const ESP32_SIMULATOR_CODE = `/*
 * ==============================================================================
 * SMART ROOM DRYER — DESA WISATA HANJELI
 * ESP32 FIRMWARE: STANDALONE IoT SENSOR SIMULATOR & DUAL CONNECTIVITY
 * ==============================================================================
 * 
 * Deskripsi:
 * Firmware simulator untuk pengujian sistem TANPA SENSOR FISIK.
 * Menghasilkan data realistis (Suhu 55-60°C target, Kelembapan, Lux, Hujan)
 * dan memancarkannya via Bluetooth Low Energy (BLE), Wi-Fi MQTT & Web Serial.
 * 
 * Target Hardware: ESP32 DevKit V1 (Cukup tancap kabel USB ke laptop)
 * ==============================================================================
 */

#include <WiFi.h>
#include <PubSubClient.h>
#include <ArduinoJson.h>
#include <Preferences.h>
#include <BLEDevice.h>
#include <BLEServer.h>
#include <BLEUtils.h>
#include <BLE2902.h>
#include <math.h>

#define ONBOARD_LED_PIN         2

#define SERVICE_UUID           "19b10000-e8f2-537e-4f6c-d104768a1214"
#define CHAR_SENSOR_UUID       "19b10001-e8f2-537e-4f6c-d104768a1214"
#define CHAR_STATUS_UUID       "19b10002-e8f2-537e-4f6c-d104768a1214"
#define CHAR_CONTROL_UUID      "19b10003-e8f2-537e-4f6c-d104768a1214"
#define CHAR_CONFIG_UUID       "19b10004-e8f2-537e-4f6c-d104768a1214"

Preferences preferences;
WiFiClient espClient;
PubSubClient mqttClient(espClient);

BLEServer* pServer = NULL;
BLECharacteristic* pSensorChar = NULL;
BLECharacteristic* pStatusChar = NULL;
BLECharacteristic* pControlChar = NULL;
BLECharacteristic* pConfigChar = NULL;

bool bleClientConnected = false;

String wifi_ssid     = "GreenHouse_Hanjeli";
String wifi_password = "hanjelismartdryer";
String mqtt_broker   = "broker.emqx.io";
int    mqtt_port     = 1883;
String device_id     = "ESP32-SIM-HANJELI";

bool heaterStatus     = false;
String controlMode    = "AUTOMATIC";

float simTempInternal     = 54.5;
float simHumidityInternal = 48.0;
float simLux              = 780.0;
int   simRainRaw          = 3800;
bool  simIsRaining        = false;

unsigned long lastTelemetryMillis = 0;
const unsigned long TELEMETRY_INTERVAL = 2000;
unsigned long simStepCounter = 0;

void updateSimulationPhysics() {
  simStepCounter++;

  // Simulasi fluktuasi termal di sekitar 55°C - 58°C
  float thermalNoise = (((rand() % 21) - 10) / 20.0);
  
  if (simTempInternal < 55.0) {
    heaterStatus = true;
  } else if (simTempInternal > 60.0) {
    heaterStatus = false;
  }

  if (heaterStatus) {
    simTempInternal += 0.35 + (thermalNoise * 0.1);
  } else {
    simTempInternal -= 0.25 - (thermalNoise * 0.1);
  }

  if (simTempInternal < 45.0) simTempInternal = 45.0;
  if (simTempInternal > 62.0) simTempInternal = 62.0;

  simHumidityInternal = 65.0 - ((simTempInternal - 35.0) * 0.8) + thermalNoise;
  if (simHumidityInternal < 25.0) simHumidityInternal = 25.0;

  simLux = 750.0 + (sin(simStepCounter * 0.1) * 120.0);
}

void broadcastTelemetry() {
  StaticJsonDocument<512> doc;
  doc["deviceId"]            = device_id;
  doc["tempInternal"]        = round(simTempInternal * 10.0) / 10.0;
  doc["humidityInternal"]    = round(simHumidityInternal * 10.0) / 10.0;
  doc["solarRadiation"]      = round(simLux);
  doc["lux"]                 = round(simLux);
  doc["rainVal"]             = simRainRaw;
  doc["isRaining"]           = simIsRaining;
  doc["heaterStatus"]        = heaterStatus;
  doc["auxHeaterStatus"]     = heaterStatus;
  doc["dhtOK"]               = true;
  doc["hasData"]             = true;

  String jsonOutput;
  serializeJson(doc, jsonOutput);

  // Serial Stream
  Serial.println(jsonOutput);

  if (bleClientConnected && pSensorChar != NULL) {
    pSensorChar->setValue(jsonOutput.c_str());
    pSensorChar->notify();
  }

  if (mqttClient.connected()) {
    mqttClient.publish("hanjeli/greenhouse/telemetry", jsonOutput.c_str());
  }
}

class ServerCallbacks : public BLEServerCallbacks {
  void onConnect(BLEServer* pServer) {
    bleClientConnected = true;
    digitalWrite(ONBOARD_LED_PIN, HIGH);
  }
  void onDisconnect(BLEServer* pServer) {
    bleClientConnected = false;
    digitalWrite(ONBOARD_LED_PIN, LOW);
    pServer->startAdvertising();
  }
};

void setup() {
  Serial.begin(115200);
  pinMode(ONBOARD_LED_PIN, OUTPUT);

  BLEDevice::init("SmartDryer-Hanjeli");
  pServer = BLEDevice::createServer();
  pServer->setCallbacks(new ServerCallbacks());

  BLEService* pService = pServer->createService(SERVICE_UUID);
  pSensorChar = pService->createCharacteristic(CHAR_SENSOR_UUID, BLECharacteristic::PROPERTY_READ | BLECharacteristic::PROPERTY_NOTIFY);
  pSensorChar->addDescriptor(new BLE2902());
  pStatusChar = pService->createCharacteristic(CHAR_STATUS_UUID, BLECharacteristic::PROPERTY_READ | BLECharacteristic::PROPERTY_NOTIFY);
  pStatusChar->addDescriptor(new BLE2902());
  pService->start();

  BLEAdvertising* pAdvertising = BLEDevice::getAdvertising();
  pAdvertising->addServiceUUID(SERVICE_UUID);
  BLEDevice::startAdvertising();
}

void loop() {
  unsigned long currentMillis = millis();
  if (currentMillis - lastTelemetryMillis >= TELEMETRY_INTERVAL) {
    lastTelemetryMillis = currentMillis;
    updateSimulationPhysics();
    broadcastTelemetry();
  }
  delay(20);
}
`;

export const ARDUINO_PROGRAMS = [
  {
    id: 'physical-standalone',
    title: 'Firmware Resmi Alat Fisik (DHT22, BH1750, Rain Sensor, Dual PTC & LCD 20x4)',
    filename: 'esp32_smart_dryer_physical_fixed.ino',
    badge: 'Produksi / Hardware Fix Langsung',
    badgeClass: 'badge-prod',
    desc: 'Program resmi mikrokontroler ESP32 langsung untuk hardware greenhouse fisik: Sensor DHT22 (Pin 25), BH1750 I2C (Pin 21/22), Sensor Hujan (Pin 34), Dual Relay PTC Heaters (Pin 26 & 27), LCD 20x4 I2C (0x27) dengan kontrol cerdas 55°C - 60°C serta proteksi safety shut-off.',
    target: 'ESP32 DevKit V1 (DHT22 Pin 25, Relay 26/27, Rain 34, I2C 21/22)',
    libraries: ['DHT sensor library (Adafruit)', 'BH1750 (Christopher Laws)', 'LiquidCrystal_I2C', 'Wire.h'],
    baudrate: 115200,
    code: ESP32_PHYSICAL_STANDALONE_CODE,
  },
  {
    id: 'dual-mode',
    title: 'Firmware Alat Fisik Dual IoT (Wi-Fi MQTT + Web Bluetooth BLE + Web Serial)',
    filename: 'esp32_smart_dryer_dual_iot.ino',
    badge: 'Hardware Nyata + Sinkronisasi Cloud/Web',
    badgeClass: 'badge-prod',
    desc: 'Program ESP32 untuk hardware fisik yang sama dengan tambahan konektivitas ganda: Wi-Fi MQTT broker dan Web Bluetooth BLE untuk kendali nirkabel jarak jauh dari dashboard operator.',
    target: 'ESP32 DevKit V1 (30-pin / 38-pin)',
    libraries: ['DHT sensor library', 'BH1750', 'LiquidCrystal_I2C', 'PubSubClient', 'ArduinoJson (v6/v7)', 'WiFi.h', 'BLEDevice.h', 'Preferences.h'],
    baudrate: 115200,
    code: ESP32_DUAL_MODE_CODE,
  },
  {
    id: 'simulator',
    title: 'Firmware Simulator ESP32 (Uji Coba Cepat Tanpa Sensor Tambahan)',
    filename: 'esp32_smart_dryer_simulator.ino',
    badge: 'Simulasi Mandiri / Plug & Play',
    badgeClass: 'badge-sim',
    desc: 'Program simulasi mandiri untuk uji coba transmisi Wi-Fi MQTT dan Bluetooth BLE langsung dari board ESP32 tanpa memerlukan modul sensor fisik tambahan (cukup colok kabel USB ke laptop/charger).',
    target: 'ESP32 DevKit V1 (Cukup colok kabel USB)',
    libraries: ['PubSubClient', 'ArduinoJson (v6/v7)', 'BLEDevice.h', 'WiFi.h', 'Preferences.h'],
    baudrate: 115200,
    code: ESP32_SIMULATOR_CODE,
  }
];
