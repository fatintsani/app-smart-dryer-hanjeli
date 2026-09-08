/*
 * ==============================================================================
 * SMART ROOM DRYER — DESA WISATA HANJELI
 * ESP32 FIRMWARE: STANDALONE IoT SENSOR SIMULATOR & DUAL CONNECTIVITY
 * ==============================================================================
 * 
 * Deskripsi:
 * Firmware ini dirancang khusus untuk pengujian sistem TANPA SENSOR FISIK.
 * ESP32 akan menghasilkan data telemetri realistis secara matematis di dalam
 * mikrokontroler dan memancarkannya secara real-time melalui:
 *   1. Bluetooth Low Energy (Web Bluetooth API di Google Chrome / MS Edge)
 *   2. Wi-Fi + MQTT Broker (HiveMQ / EMQX / Local Broker)
 * 
 * Fitur:
 *   - Simulasi Dinamis: Suhu, Kelembapan, Radiasi Surya, Kadar Air Gabah, Berat
 *   - Respon Aktuator Nyata: Perubahan status kipas/pemanas dari web akan
 *     mempengaruhi dinamika suhu simulasi dan mengendalikan Onboard LED (Pin 2).
 *   - Wi-Fi Provisioning via BLE (SSID & Password disimpan ke NVS/Preferences).
 *   - 100% Kompatibel dengan Dashboard Smart Dryer Hanjeli.
 * 
 * Hardware Target: ESP32 DevKit V1 (30-pin / 38-pin) - Cukup tancap kabel USB!
 * Library yang Dibutuhkan (Arduino IDE):
 *   - PubSubClient (oleh Nick O'Leary)
 *   - ArduinoJson (oleh Benoit Blanchon - v6 atau v7)
 *   - BLE, WiFi, Preferences (Sudah bawaan ESP32 Board Package)
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

// ==============================================================================
// 1. PIN & INDIKATOR HARDWARE
// ==============================================================================
#define ONBOARD_LED_PIN         2   // LED biru onboard ESP32 untuk indikator aktif

// ==============================================================================
// 2. BLE UUID DEFINITIONS (STANDAR 128-BIT HANJELI SMART DRYER)
// ==============================================================================
#define SERVICE_UUID           "19b10000-e8f2-537e-4f6c-d104768a1214"
#define CHAR_SENSOR_UUID       "19b10001-e8f2-537e-4f6c-d104768a1214" // Read / Notify
#define CHAR_STATUS_UUID       "19b10002-e8f2-537e-4f6c-d104768a1214" // Read / Notify
#define CHAR_CONTROL_UUID      "19b10003-e8f2-537e-4f6c-d104768a1214" // Write
#define CHAR_CONFIG_UUID       "19b10004-e8f2-537e-4f6c-d104768a1214" // Write (Wi-Fi Provisioning)

// ==============================================================================
// 3. GLOBAL OBJECTS & STATE
// ==============================================================================
Preferences preferences;
WiFiClient espClient;
PubSubClient mqttClient(espClient);

BLEServer* pServer = NULL;
BLECharacteristic* pSensorChar = NULL;
BLECharacteristic* pStatusChar = NULL;
BLECharacteristic* pControlChar = NULL;
BLECharacteristic* pConfigChar = NULL;

bool bleClientConnected = false;
bool oldBleClientConnected = false;

// Kredensial Jaringan Default (dapat diubah via BLE Setup atau disimpan di NVS)
String wifi_ssid     = "OPPO Reno 5 X";
String wifi_password = "admin123";
String mqtt_broker   = "broker.hivemq.com";
int    mqtt_port     = 1883;
String device_id     = "HANJELI-001";

// Status Aktuator
bool exhaustFanStatus = false;
int  exhaustFanSpeed  = 0;
bool blowerFanStatus  = false;
int  blowerFanSpeed   = 0;
bool auxHeaterStatus  = false;
int  auxHeaterLevel   = 0;
bool uvSterilizerStatus = false;
bool roofVentStatus   = false;
String controlMode    = "AUTOMATIC";

// Nilai Sensor Simulasi ESP32
float simTempInternal     = 42.5;
float simTempExternal     = 31.0;
float simHumidityInternal = 55.0;
float simHumidityExternal = 68.0;
float simSolarRadiation   = 750.0;
float simGrainMoisture    = 14.5;
float simWeightCurrentKg  = 48.0;

// Timer
unsigned long lastTelemetryMillis = 0;
const unsigned long TELEMETRY_INTERVAL = 2500; // Kirim data setiap 2.5 detik
unsigned long simStepCounter = 0;

// Function Prototypes
void updateSimulationPhysics();
void broadcastTelemetry();
void reconnectMqtt();
void mqttCallback(char* topic, byte* payload, unsigned int length);
void loadNetworkPreferences();
void saveNetworkPreferences(String ssid, String pass, String broker);

// ==============================================================================
// 4. BLE SERVER & CHARACTERISTIC CALLBACKS
// ==============================================================================
class ServerCallbacks : public BLEServerCallbacks {
  void onConnect(BLEServer* pServer) {
    bleClientConnected = true;
    digitalWrite(ONBOARD_LED_PIN, HIGH);
    Serial.println("\n[BLE] >>> Web Bluetooth Dashboard Terhubung! <<<");
  }

  void onDisconnect(BLEServer* pServer) {
    bleClientConnected = false;
    digitalWrite(ONBOARD_LED_PIN, LOW);
    Serial.println("\n[BLE] <<< Web Bluetooth Dashboard Terputus. Restart Advertising... <<<");
    pServer->startAdvertising();
  }
};

class ControlCallbacks : public BLECharacteristicCallbacks {
  void onWrite(BLECharacteristic* pCharacteristic) {
    String rxValue = pCharacteristic->getValue();
    if (rxValue.length() == 0) return;

    Serial.println("\n[BLE Control Command Diterima]:");
    Serial.println(rxValue);

    StaticJsonDocument<512> doc;
    DeserializationError error = deserializeJson(doc, rxValue);
    if (error) {
      Serial.print("[BLE] Gagal parse JSON kontrol: ");
      Serial.println(error.c_str());
      return;
    }

    JsonObject data = doc["data"].isNull() ? doc.as<JsonObject>() : doc["data"].as<JsonObject>();

    if (data.containsKey("exhaustFanStatus")) exhaustFanStatus = data["exhaustFanStatus"];
    if (data.containsKey("exhaustFanSpeed"))  exhaustFanSpeed  = data["exhaustFanSpeed"];
    if (data.containsKey("blowerFanStatus"))  blowerFanStatus  = data["blowerFanStatus"];
    if (data.containsKey("blowerFanSpeed"))   blowerFanSpeed   = data["blowerFanSpeed"];
    if (data.containsKey("auxHeaterStatus"))  auxHeaterStatus  = data["auxHeaterStatus"];
    if (data.containsKey("auxHeaterLevel"))   auxHeaterLevel   = data["auxHeaterLevel"];
    if (data.containsKey("uvSterilizerStatus")) uvSterilizerStatus = data["uvSterilizerStatus"];
    if (data.containsKey("roofVentStatus"))   roofVentStatus   = data["roofVentStatus"];
    if (data.containsKey("controlMode"))      controlMode      = data["controlMode"].as<String>();

    Serial.printf("[Aktuator Diperbarui] Fan: %s (%d%%) | Heater: %s (%d%%) | Mode: %s\n",
      exhaustFanStatus ? "ON" : "OFF", exhaustFanSpeed,
      auxHeaterStatus ? "ON" : "OFF", auxHeaterLevel,
      controlMode.c_str()
    );

    // Kirim konfirmasi status aktuator balik ke BLE & MQTT
    broadcastTelemetry();
  }
};

class ConfigCallbacks : public BLECharacteristicCallbacks {
  void onWrite(BLECharacteristic* pCharacteristic) {
    String rxValue = pCharacteristic->getValue();
    if (rxValue.length() == 0) return;

    Serial.println("\n[BLE Wi-Fi Provisioning Diterima]: " + rxValue);

    StaticJsonDocument<512> doc;
    DeserializationError error = deserializeJson(doc, rxValue);
    if (!error) {
      String newSsid   = doc["ssid"] | "";
      String newPass   = doc["password"] | "";
      String newBroker = doc["broker"] | mqtt_broker;

      if (newSsid.length() > 0) {
        saveNetworkPreferences(newSsid, newPass, newBroker);
        Serial.println("[BLE] Kredensial Wi-Fi berhasil disimpan ke NVS! Mencoba menghubungkan...");
        WiFi.disconnect();
        WiFi.begin(wifi_ssid.c_str(), wifi_password.c_str());
      }
    }
  }
};

// ==============================================================================
// 5. SETUP
// ==============================================================================
void setup() {
  Serial.begin(115200);
  delay(1000);

  Serial.println("\n========================================================");
  Serial.println(" SMART ROOM DRYER — DESA WISATA HANJELI");
  Serial.println(" ESP32 STANDALONE SIMULATOR FIRMWARE");
  Serial.println("========================================================");

  pinMode(ONBOARD_LED_PIN, OUTPUT);
  digitalWrite(ONBOARD_LED_PIN, LOW);

  // 1. Muat Konfigurasi NVS
  loadNetworkPreferences();

  // 2. Inisialisasi Bluetooth Low Energy (BLE)
  Serial.println("[BLE] Menginisialisasi BLE GATT Server...");
  BLEDevice::init("SmartDryer-Hanjeli-001");
  pServer = BLEDevice::createServer();
  pServer->setCallbacks(new ServerCallbacks());

  BLEService* pService = pServer->createService(SERVICE_UUID);

  // Sensor Telemetry Characteristic (Read & Notify)
  pSensorChar = pService->createCharacteristic(
    CHAR_SENSOR_UUID,
    BLECharacteristic::PROPERTY_READ | BLECharacteristic::PROPERTY_NOTIFY
  );
  pSensorChar->addDescriptor(new BLE2902());

  // Actuator Status Characteristic (Read & Notify)
  pStatusChar = pService->createCharacteristic(
    CHAR_STATUS_UUID,
    BLECharacteristic::PROPERTY_READ | BLECharacteristic::PROPERTY_NOTIFY
  );
  pStatusChar->addDescriptor(new BLE2902());

  // Actuator Control Characteristic (Write)
  pControlChar = pService->createCharacteristic(
    CHAR_CONTROL_UUID,
    BLECharacteristic::PROPERTY_WRITE
  );
  pControlChar->setCallbacks(new ControlCallbacks());

  // Wi-Fi Config Characteristic (Write)
  pConfigChar = pService->createCharacteristic(
    CHAR_CONFIG_UUID,
    BLECharacteristic::PROPERTY_WRITE
  );
  pConfigChar->setCallbacks(new ConfigCallbacks());

  pService->start();

  BLEAdvertising* pAdvertising = BLEDevice::getAdvertising();
  pAdvertising->addServiceUUID(SERVICE_UUID);
  pAdvertising->setScanResponse(true);
  pAdvertising->setMinPreferred(0x06);
  pAdvertising->setMinPreferred(0x12);
  BLEDevice::startAdvertising();

  Serial.println("[BLE] Siap! Nama Bluetooth: SmartDryer-Hanjeli-001");

  // 3. Inisialisasi Wi-Fi (Non-blocking fallback)
  Serial.printf("[Wi-Fi] Menghubungkan ke SSID: %s ...\n", wifi_ssid.c_str());
  WiFi.mode(WIFI_STA);
  IPAddress dns1(8, 8, 8, 8);
  IPAddress dns2(1, 1, 1, 1);
  WiFi.config(INADDR_NONE, INADDR_NONE, INADDR_NONE, dns1, dns2);
  WiFi.begin(wifi_ssid.c_str(), wifi_password.c_str());

  mqttClient.setServer(mqtt_broker.c_str(), mqtt_port);
  mqttClient.setBufferSize(1024);
  mqttClient.setKeepAlive(60);
  mqttClient.setCallback(mqttCallback);

  Serial.println("[Sistem] Simulator ESP32 Berjalan Siap Memancarkan Data.");
}

// ==============================================================================
// 6. MAIN LOOP
// ==============================================================================
void loop() {
  // 1. Maintain Wi-Fi & MQTT (Jika Wi-Fi tersedia)
  if (WiFi.status() == WL_CONNECTED) {
    static bool wifiReported = false;
    if (!wifiReported) {
      Serial.printf("\n[Wi-Fi] >>> TERHUBUNG! IP ESP32: %s <<<\n", WiFi.localIP().toString().c_str());
      wifiReported = true;
    }
    if (!mqttClient.connected()) {
      reconnectMqtt();
    }
    mqttClient.loop();
  }

  // 2. Siklus Pengiriman Telemetri Simulasi
  unsigned long currentMillis = millis();
  if (currentMillis - lastTelemetryMillis >= TELEMETRY_INTERVAL) {
    lastTelemetryMillis = currentMillis;

    // Update matematika simulasi
    updateSimulationPhysics();

    // Broadcast ke BLE dan MQTT
    broadcastTelemetry();
  }

  // 3. Heartbeat LED Kedip Halus
  if (!bleClientConnected && WiFi.status() != WL_CONNECTED) {
    // Kedip 100ms setiap 2 detik jika standby
    digitalWrite(ONBOARD_LED_PIN, (millis() % 2000 < 100) ? HIGH : LOW);
  }

  delay(20);
}

// ==============================================================================
// 7. MATEMATIKA FISIKA SIMULASI RUANG PENGERING HANJELI
// ==============================================================================
void updateSimulationPhysics() {
  simStepCounter++;

  // Variasi fluktuasi alami (noise)
  float noise = ((rand() % 20) - 10) / 50.0; // -0.2 s/d +0.2

  // Pengaruh Radiasi Matahari (Simulasi kurva terang 600 - 950 W/m2)
  float solarOscillation = sin(simStepCounter * 0.05) * 150.0;
  simSolarRadiation = 780.0 + solarOscillation + (noise * 15.0);
  if (simSolarRadiation < 0) simSolarRadiation = 0;

  // Suhu Eksternal (Lingkungan Hanjeli Sukabumi: 28°C - 33°C)
  simTempExternal = 30.5 + sin(simStepCounter * 0.03) * 2.5 + noise;
  simHumidityExternal = 65.0 - sin(simStepCounter * 0.03) * 8.0 + (noise * 2.0);

  // Dinamika Suhu Internal Ruang Pengering Green House:
  // - Dasar suhu naik akibat radiasi surya rumah kaca (Greenhouse Effect)
  // - Tambahan panas jika Auxiliary Heater aktif
  // - Penurunan suhu jika Exhaust Fan aktif membuang udara panas lembap
  float heaterContribution = auxHeaterStatus ? (auxHeaterLevel * 0.08) : 0.0;
  float fanCooling = exhaustFanStatus ? (exhaustFanSpeed * 0.04) : 0.0;
  float targetInternalTemp = simTempExternal + 10.0 + (simSolarRadiation * 0.012) + heaterContribution - fanCooling;

  // Smooth interpolation menuju target (inersia termal)
  simTempInternal += (targetInternalTemp - simTempInternal) * 0.15 + (noise * 0.1);

  // Kelembapan Internal: Berbanding terbalik dengan suhu & dipengaruhi exhaust fan
  float targetInternalHum = 75.0 - (simTempInternal - 30.0) * 1.6 - (exhaustFanStatus ? (exhaustFanSpeed * 0.15) : 0.0);
  if (targetInternalHum < 25.0) targetInternalHum = 25.0;
  if (targetInternalHum > 85.0) targetInternalHum = 85.0;
  simHumidityInternal += (targetInternalHum - simHumidityInternal) * 0.15 + (noise * 0.2);

  // Penurunan Kadar Air Gabah & Berat Gabah bertahap menuju target 12%
  if (simGrainMoisture > 12.0) {
    float dryingRate = (simTempInternal > 40.0 ? 0.02 : 0.008);
    simGrainMoisture -= dryingRate;
    simWeightCurrentKg -= (dryingRate * 0.05);
  } else {
    simGrainMoisture = 12.0 + (noise * 0.05);
  }
}

// ==============================================================================
// 8. BROADCAST TELEMETRI KE BLE & MQTT
// ==============================================================================
void broadcastTelemetry() {
  // 1. Buat Payload Telemetri JSON
  StaticJsonDocument<512> doc;
  doc["deviceId"]            = device_id;
  doc["tempInternal"]        = round(simTempInternal * 10.0) / 10.0;
  doc["tempExternal"]        = round(simTempExternal * 10.0) / 10.0;
  doc["humidityInternal"]    = round(simHumidityInternal * 10.0) / 10.0;
  doc["humidityExternal"]    = round(simHumidityExternal * 10.0) / 10.0;
  doc["solarRadiation"]      = round(simSolarRadiation);
  doc["grainMoisture"]       = round(simGrainMoisture * 10.0) / 10.0;
  doc["weightCurrentKg"]     = round(simWeightCurrentKg * 10.0) / 10.0;
  doc["hasData"]             = true;
  doc["timestamp"]           = millis();

  // Status Aktuator
  JsonObject act = doc.createNestedObject("actuators");
  act["exhaustFanStatus"]    = exhaustFanStatus;
  act["exhaustFanSpeed"]     = exhaustFanSpeed;
  act["blowerFanStatus"]     = blowerFanStatus;
  act["blowerFanSpeed"]      = blowerFanSpeed;
  act["auxHeaterStatus"]     = auxHeaterStatus;
  act["auxHeaterLevel"]      = auxHeaterLevel;
  act["uvSterilizerStatus"]  = uvSterilizerStatus;
  act["roofVentStatus"]      = roofVentStatus;
  act["controlMode"]         = controlMode;

  String jsonOutput;
  serializeJson(doc, jsonOutput);

  // 2. Cetak ke Serial Monitor Arduino IDE
  Serial.printf("[Simulasi Live] Suhu: %.1f°C | Hum: %.1f%% | Solar: %.0f W/m² | Gabah: %.1f%% | Fan: %s\n",
    simTempInternal, simHumidityInternal, simSolarRadiation, simGrainMoisture, exhaustFanStatus ? "ON" : "OFF"
  );

  // 3. Kirim via Bluetooth BLE (Notify ke Web Browser)
  if (bleClientConnected && pSensorChar != NULL) {
    pSensorChar->setValue(jsonOutput.c_str());
    pSensorChar->notify();
  }

  // 4. Kirim via MQTT (Jika Wi-Fi Terhubung)
  if (mqttClient.connected()) {
    mqttClient.publish("hanjeli/greenhouse/telemetry", jsonOutput.c_str());
    mqttClient.publish("greenhouse/telemetry", jsonOutput.c_str());
  }
}

// ==============================================================================
// 9. MQTT RECEIVER & CONTROL HANDLER
// ==============================================================================
void mqttCallback(char* topic, byte* payload, unsigned int length) {
  String message = "";
  for (unsigned int i = 0; i < length; i++) {
    message += (char)payload[i];
  }

  Serial.printf("\n[MQTT Command] Topik: %s | Payload: %s\n", topic, message.c_str());

  StaticJsonDocument<512> doc;
  DeserializationError err = deserializeJson(doc, message);
  if (!err) {
    if (doc.containsKey("exhaustFanStatus")) exhaustFanStatus = doc["exhaustFanStatus"];
    if (doc.containsKey("exhaustFanSpeed"))  exhaustFanSpeed  = doc["exhaustFanSpeed"];
    if (doc.containsKey("blowerFanStatus"))  blowerFanStatus  = doc["blowerFanStatus"];
    if (doc.containsKey("auxHeaterStatus"))  auxHeaterStatus  = doc["auxHeaterStatus"];
    if (doc.containsKey("auxHeaterLevel"))   auxHeaterLevel   = doc["auxHeaterLevel"];
    if (doc.containsKey("controlMode"))      controlMode      = doc["controlMode"].as<String>();

    broadcastTelemetry();
  }
}

void reconnectMqtt() {
  if (WiFi.status() != WL_CONNECTED) return;

  static unsigned long lastMqttAttempt = 0;
  if (millis() - lastMqttAttempt < 4000) return;
  lastMqttAttempt = millis();

  String clientId = "SmartDryer-" + String(random(10000, 99999));
  Serial.printf("[MQTT] Menghubungkan ke %s:%d ...", mqtt_broker.c_str(), mqtt_port);
  
  bool connected = mqttClient.connect(clientId.c_str());
  if (!connected) {
    // Direct IP fallback for HiveMQ (3.120.61.61) in case DNS is filtered on mobile hotspot
    IPAddress fallbackIp(3, 120, 61, 61);
    mqttClient.setServer(fallbackIp, mqtt_port);
    connected = mqttClient.connect(clientId.c_str());
  }

  if (connected) {
    Serial.println(" TERHUBUNG!");
    mqttClient.subscribe("greenhouse/control");
    mqttClient.subscribe("greenhouse/actuators");
    mqttClient.subscribe("hanjeli/greenhouse/control");
    mqttClient.subscribe("hanjeli/greenhouse/actuators");
  } else {
    Serial.printf(" Gagal (rc=%d)\n", mqttClient.state());
  }
}

// ==============================================================================
// 10. PREFERENCES (NVS STORAGE)
// ==============================================================================
void loadNetworkPreferences() {
  preferences.begin("dryer_config", true);
  wifi_ssid     = preferences.getString("ssid", wifi_ssid);
  wifi_password = preferences.getString("password", wifi_password);
  mqtt_broker   = preferences.getString("broker", mqtt_broker);
  mqtt_port     = preferences.getInt("port", mqtt_port);
  device_id     = preferences.getString("device_id", device_id);
  preferences.end();
}

void saveNetworkPreferences(String ssid, String pass, String broker) {
  preferences.begin("dryer_config", false);
  preferences.putString("ssid", ssid);
  preferences.putString("password", pass);
  preferences.putString("broker", broker);
  preferences.end();

  wifi_ssid     = ssid;
  wifi_password = pass;
  mqtt_broker   = broker;
}
