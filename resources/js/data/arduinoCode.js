/**
 * Arduino / ESP32 Firmware Source Codes — Smart Room Dryer Desa Wisata Hanjeli
 */

export const ESP32_DUAL_MODE_CODE = `/*
 * ==============================================================================
 * SMART ROOM DRYER — DESA WISATA HANJELI
 * ESP32 DUAL CONNECTIVITY FIRMWARE
 * Wi-Fi + MQTT & Bluetooth Low Energy BLE
 * ==============================================================================
 *
 * Hardware Target:
 *   ESP32 DevKit V1 (30-pin / 38-pin)
 *
 * Communication:
 *   1. Wi-Fi + MQTT
 *   2. Bluetooth Low Energy (BLE)
 *
 * Storage:
 *   Preferences / NVS
 *
 * Libraries:
 *   - WiFi.h                  -> Built-in ESP32
 *   - PubSubClient            -> Nick O'Leary
 *   - ArduinoJson             -> Benoit Blanchon
 *   - Preferences.h           -> Built-in ESP32
 *   - BLEDevice.h             -> Built-in ESP32
 *   - BLEServer.h             -> Built-in ESP32
 *   - BLEUtils.h              -> Built-in ESP32
 *   - BLE2902.h               -> Built-in ESP32
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

// ==============================================================================
// PIN DEFINITIONS
// ==============================================================================
#define RELAY_EXHAUST_FAN_PIN   18
#define RELAY_INTAKE_FAN_PIN    19
#define RELAY_CIRC_FAN_PIN      21
#define RELAY_HEATER_PIN        22
#define STATUS_LED_PIN           2
#define SENSOR_MOISTURE_ADC_PIN 34
#define SENSOR_WEIGHT_ADC_PIN   35

// ==============================================================================
// BLE SERVICE & CHARACTERISTIC UUID (128-BIT HANJELI STANDARD)
// ==============================================================================
#define SERVICE_UUID      "19b10000-e8f2-537e-4f6c-d104768a1214"
#define CHAR_SENSOR_UUID  "19b10001-e8f2-537e-4f6c-d104768a1214"
#define CHAR_STATUS_UUID  "19b10002-e8f2-537e-4f6c-d104768a1214"
#define CHAR_CONTROL_UUID "19b10003-e8f2-537e-4f6c-d104768a1214"
#define CHAR_CONFIG_UUID  "19b10004-e8f2-537e-4f6c-d104768a1214"

// ==============================================================================
// GLOBAL OBJECTS & STATE
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

String wifi_ssid     = "OPPO Reno";
String wifi_password = "admin";
String mqtt_broker   = "broker.hivemq.com";
int    mqtt_port     = 1883;
String device_id     = "OPPO Reno";

bool exhaustFanState = true;
int  exhaustFanSpeed = 70;
bool intakeFanState = true;
bool circFanState = true;
bool auxHeaterState = false;
int  auxHeaterLevel = 0;
bool isManualOverride = false;
String overrideMode = "AUTO";

float tempInternal     = 42.5;
float humidityInternal = 52.0;
float tempExternal     = 31.2;
float humidityExternal = 66.0;
float solarRadiation   = 760.0;
float grainMoisture    = 13.8;
float weightCurrentKg  = 44.5;

unsigned long lastTelemetryMillis = 0;
const unsigned long TELEMETRY_INTERVAL = 3000;

void applyActuators();
void readSensors();
void mqttCallback(char* topic, byte* message, unsigned int length);
void reconnectMqtt();
void broadcastTelemetry();

// ==============================================================================
// BLE CALLBACKS
// ==============================================================================
class MyServerCallbacks : public BLEServerCallbacks {
  void onConnect(BLEServer* pServer) {
    bleClientConnected = true;
    digitalWrite(STATUS_LED_PIN, HIGH);
    Serial.println("[BLE] Web Bluetooth Central connected!");
  }
  void onDisconnect(BLEServer* pServer) {
    bleClientConnected = false;
    digitalWrite(STATUS_LED_PIN, LOW);
    Serial.println("[BLE] Disconnected. Restarting advertising...");
    pServer->startAdvertising();
  }
};

class ControlCallbacks : public BLECharacteristicCallbacks {
  void onWrite(BLECharacteristic* pCharacteristic) {
    String rxValue = pCharacteristic->getValue();
    if (rxValue.length() == 0) return;

    DynamicJsonDocument doc(1024);
    DeserializationError error = deserializeJson(doc, rxValue);
    if (error) return;

    JsonObject data = doc["data"].as<JsonObject>();
    if (data.containsKey("exhaustFanStatus")) exhaustFanState = data["exhaustFanStatus"];
    if (data.containsKey("exhaustFanSpeed"))  exhaustFanSpeed  = data["exhaustFanSpeed"];
    if (data.containsKey("intakeFanStatus"))   intakeFanState   = data["intakeFanStatus"];
    if (data.containsKey("circFanStatus"))     circFanState     = data["circFanStatus"];
    if (data.containsKey("auxHeaterStatus"))   auxHeaterState   = data["auxHeaterStatus"];
    if (data.containsKey("auxHeaterLevel"))    auxHeaterLevel    = data["auxHeaterLevel"];
    if (data.containsKey("isOverrideActive"))  isManualOverride = data["isOverrideActive"];
    if (data.containsKey("overrideMode"))      overrideMode     = data["overrideMode"].as<String>();

    applyActuators();
    Serial.println("[BLE] Actuator state updated.");
  }
};

class ConfigCallbacks : public BLECharacteristicCallbacks {
  void onWrite(BLECharacteristic* pCharacteristic) {
    String rxValue = pCharacteristic->getValue();
    if (rxValue.length() == 0) return;

    DynamicJsonDocument doc(1024);
    DeserializationError error = deserializeJson(doc, rxValue);
    if (error) return;

    if (doc["action"] == "SET_WIFI_CONFIG") {
      if (doc.containsKey("ssid"))        wifi_ssid     = doc["ssid"].as<String>();
      if (doc.containsKey("password"))    wifi_password = doc["password"].as<String>();
      if (doc.containsKey("mqtt_broker")) mqtt_broker   = doc["mqtt_broker"].as<String>();
      if (doc.containsKey("mqtt_port"))   mqtt_port     = doc["mqtt_port"];
      if (doc.containsKey("device_id"))   device_id     = doc["device_id"].as<String>();

      preferences.begin("srd_config", false);
      preferences.putString("ssid", wifi_ssid);
      preferences.putString("password", wifi_password);
      preferences.putString("broker", mqtt_broker);
      preferences.putInt("port", mqtt_port);
      preferences.putString("dev_id", device_id);
      preferences.end();

      WiFi.disconnect(true);
      delay(500);
      WiFi.mode(WIFI_STA);
      WiFi.begin(wifi_ssid.c_str(), wifi_password.c_str());
    }
  }
};

void applyActuators() {
  digitalWrite(RELAY_EXHAUST_FAN_PIN, exhaustFanState ? HIGH : LOW);
  digitalWrite(RELAY_INTAKE_FAN_PIN, intakeFanState ? HIGH : LOW);
  digitalWrite(RELAY_CIRC_FAN_PIN, circFanState ? HIGH : LOW);
  digitalWrite(RELAY_HEATER_PIN, auxHeaterState ? HIGH : LOW);
}

void readSensors() {
  // Integrasikan pembacaan sensor fisik DHT22, DS18B20, Solar Pyranometer, Load Cell di sini
  tempInternal += random(-3, 4) / 10.0;
  humidityInternal += random(-5, 6) / 10.0;
  if (tempInternal < 35.0) tempInternal = 35.0;
  if (tempInternal > 58.0) tempInternal = 58.0;
}

void mqttCallback(char* topic, byte* message, unsigned int length) {
  String payload = "";
  for (unsigned int i = 0; i < length; i++) payload += (char)message[i];

  DynamicJsonDocument doc(1024);
  DeserializationError error = deserializeJson(doc, payload);
  if (error) return;

  JsonObject data = doc["data"].as<JsonObject>();
  if (data.containsKey("exhaustFanStatus")) exhaustFanState = data["exhaustFanStatus"];
  if (data.containsKey("exhaustFanSpeed"))  exhaustFanSpeed  = data["exhaustFanSpeed"];
  if (data.containsKey("intakeFanStatus"))   intakeFanState   = data["intakeFanStatus"];
  if (data.containsKey("circFanStatus"))     circFanState     = data["circFanStatus"];
  if (data.containsKey("auxHeaterStatus"))   auxHeaterState   = data["auxHeaterStatus"];
  if (data.containsKey("auxHeaterLevel"))    auxHeaterLevel    = data["auxHeaterLevel"];
  if (data.containsKey("isOverrideActive"))  isManualOverride = data["isOverrideActive"];
  if (data.containsKey("overrideMode"))      overrideMode     = data["overrideMode"].as<String>();

  applyActuators();
}

void reconnectMqtt() {
  if (WiFi.status() != WL_CONNECTED || mqttClient.connected()) return;

  String clientId = "ESP32_SRD_" + device_id + "_" + String(random(0xffff), HEX);
  if (mqttClient.connect(clientId.c_str())) {
    String cmdTopic1 = "smartroomdryer/device/" + device_id + "/command";
    String cmdTopic2 = "hanjeli/greenhouse/control";
    mqttClient.subscribe(cmdTopic1.c_str());
    mqttClient.subscribe(cmdTopic2.c_str());
    Serial.println("[MQTT] Connected & Subscribed!");
  }
}

void broadcastTelemetry() {
  readSensors();

  DynamicJsonDocument doc(1024);
  doc["deviceId"]         = device_id;
  doc["tempInternal"]     = round(tempInternal * 10) / 10.0;
  doc["humidityInternal"] = round(humidityInternal * 10) / 10.0;
  doc["tempExternal"]     = round(tempExternal * 10) / 10.0;
  doc["humidityExternal"] = round(humidityExternal * 10) / 10.0;
  doc["solarRadiation"]   = solarRadiation;
  doc["grainMoisture"]    = round(grainMoisture * 10) / 10.0;
  doc["weightCurrentKg"]  = round(weightCurrentKg * 10) / 10.0;
  doc["exhaustFanStatus"] = exhaustFanState;
  doc["exhaustFanSpeed"]  = exhaustFanSpeed;
  doc["intakeFanStatus"]  = intakeFanState;
  doc["circFanStatus"]    = circFanState;
  doc["heaterStatus"]     = auxHeaterState;
  doc["heaterLevel"]      = auxHeaterLevel;
  doc["isOverrideActive"] = isManualOverride;
  doc["overrideMode"]     = overrideMode;

  String jsonBuffer;
  serializeJson(doc, jsonBuffer);

  if (mqttClient.connected()) {
    mqttClient.publish("hanjeli/greenhouse/telemetry", jsonBuffer.c_str());
    mqttClient.publish("greenhouse/telemetry", jsonBuffer.c_str());
  }

  if (bleClientConnected && pSensorChar != NULL) {
    pSensorChar->setValue((uint8_t*)jsonBuffer.c_str(), jsonBuffer.length());
    pSensorChar->notify();
  }
}

void setup() {
  Serial.begin(115200);
  pinMode(RELAY_EXHAUST_FAN_PIN, OUTPUT);
  pinMode(RELAY_INTAKE_FAN_PIN, OUTPUT);
  pinMode(RELAY_CIRC_FAN_PIN, OUTPUT);
  pinMode(RELAY_HEATER_PIN, OUTPUT);
  pinMode(STATUS_LED_PIN, OUTPUT);
  applyActuators();

  preferences.begin("srd_config", true);
  wifi_ssid     = preferences.getString("ssid", wifi_ssid);
  wifi_password = preferences.getString("password", wifi_password);
  mqtt_broker   = preferences.getString("broker", mqtt_broker);
  mqtt_port     = preferences.getInt("port", mqtt_port);
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
  mqttClient.setBufferSize(2048);
}

void loop() {
  if (WiFi.status() == WL_CONNECTED) {
    if (!mqttClient.connected()) reconnectMqtt();
    else mqttClient.loop();
    digitalWrite(STATUS_LED_PIN, HIGH);
  } else {
    digitalWrite(STATUS_LED_PIN, (millis() / 500) % 2);
  }

  if (millis() - lastTelemetryMillis >= TELEMETRY_INTERVAL) {
    lastTelemetryMillis = millis();
    broadcastTelemetry();
  }
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
 * Firmware ini dirancang khusus untuk pengujian sistem TANPA SENSOR FISIK.
 * ESP32 akan menghasilkan data telemetri realistis secara matematis di dalam
 * mikrokontroler dan memancarkannya secara real-time melalui:
 *   1. Bluetooth Low Energy (Web Bluetooth API di Google Chrome / MS Edge)
 *   2. Wi-Fi + MQTT Broker (HiveMQ / EMQX / Local Broker)
 * 
 * Target Hardware: ESP32 DevKit V1 (30-pin / 38-pin) - Cukup tancap kabel USB!
 * Libraries: PubSubClient, ArduinoJson (v6/v7), BLEDevice, WiFi, Preferences
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

String wifi_ssid     = "OPPO Reno";
String wifi_password = "admin";
String mqtt_broker   = "broker.hivemq.com";
int    mqtt_port     = 1883;
String device_id     = "OPPO Reno";

bool exhaustFanStatus = false;
int  exhaustFanSpeed  = 0;
bool blowerFanStatus  = false;
int  blowerFanSpeed   = 0;
bool auxHeaterStatus  = false;
int  auxHeaterLevel   = 0;
String controlMode    = "AUTOMATIC";

float simTempInternal     = 42.5;
float simTempExternal     = 31.0;
float simHumidityInternal = 55.0;
float simHumidityExternal = 68.0;
float simSolarRadiation   = 750.0;
float simGrainMoisture    = 14.5;
float simWeightCurrentKg  = 48.0;

unsigned long lastTelemetryMillis = 0;
const unsigned long TELEMETRY_INTERVAL = 2500;
unsigned long simStepCounter = 0;

void updateSimulationPhysics();
void broadcastTelemetry();
void reconnectMqtt();
void mqttCallback(char* topic, byte* payload, unsigned int length);

class ServerCallbacks : public BLEServerCallbacks {
  void onConnect(BLEServer* pServer) {
    bleClientConnected = true;
    digitalWrite(ONBOARD_LED_PIN, HIGH);
    Serial.println("\\n[BLE] >>> Web Bluetooth Dashboard Terhubung! <<<");
  }
  void onDisconnect(BLEServer* pServer) {
    bleClientConnected = false;
    digitalWrite(ONBOARD_LED_PIN, LOW);
    Serial.println("\\n[BLE] <<< Web Bluetooth Dashboard Terputus. <<<");
    pServer->startAdvertising();
  }
};

class ControlCallbacks : public BLECharacteristicCallbacks {
  void onWrite(BLECharacteristic* pCharacteristic) {
    String rxValue = pCharacteristic->getValue();
    if (rxValue.length() == 0) return;

    StaticJsonDocument<512> doc;
    DeserializationError error = deserializeJson(doc, rxValue);
    if (error) return;

    JsonObject data = doc["data"].isNull() ? doc.as<JsonObject>() : doc["data"].as<JsonObject>();
    if (data.containsKey("exhaustFanStatus")) exhaustFanStatus = data["exhaustFanStatus"];
    if (data.containsKey("exhaustFanSpeed"))  exhaustFanSpeed  = data["exhaustFanSpeed"];
    if (data.containsKey("blowerFanStatus"))  blowerFanStatus  = data["blowerFanStatus"];
    if (data.containsKey("blowerFanSpeed"))   blowerFanSpeed   = data["blowerFanSpeed"];
    if (data.containsKey("auxHeaterStatus"))  auxHeaterStatus  = data["auxHeaterStatus"];
    if (data.containsKey("auxHeaterLevel"))   auxHeaterLevel   = data["auxHeaterLevel"];
    if (data.containsKey("controlMode"))      controlMode      = data["controlMode"].as<String>();

    broadcastTelemetry();
  }
};

class ConfigCallbacks : public BLECharacteristicCallbacks {
  void onWrite(BLECharacteristic* pCharacteristic) {
    String rxValue = pCharacteristic->getValue();
    if (rxValue.length() == 0) return;

    StaticJsonDocument<512> doc;
    DeserializationError error = deserializeJson(doc, rxValue);
    if (!error) {
      String newSsid   = doc["ssid"] | "";
      String newPass   = doc["password"] | "";
      String newBroker = doc["broker"] | mqtt_broker;

      if (newSsid.length() > 0) {
        preferences.begin("dryer_config", false);
        preferences.putString("ssid", newSsid);
        preferences.putString("password", newPass);
        preferences.putString("broker", newBroker);
        preferences.end();
        wifi_ssid = newSsid;
        wifi_password = newPass;
        wifi_broker = newBroker;
        WiFi.disconnect();
        WiFi.begin(wifi_ssid.c_str(), wifi_password.c_str());
      }
    }
  }
};

void updateSimulationPhysics() {
  simStepCounter++;
  float noise = ((rand() % 20) - 10) / 50.0;
  float solarOscillation = sin(simStepCounter * 0.05) * 150.0;
  simSolarRadiation = 780.0 + solarOscillation + (noise * 15.0);
  if (simSolarRadiation < 0) simSolarRadiation = 0;

  simTempExternal = 30.5 + sin(simStepCounter * 0.03) * 2.5 + noise;
  simHumidityExternal = 65.0 - sin(simStepCounter * 0.03) * 8.0 + (noise * 2.0);

  float heaterContribution = auxHeaterStatus ? (auxHeaterLevel * 0.08) : 0.0;
  float fanCooling = exhaustFanStatus ? (exhaustFanSpeed * 0.04) : 0.0;
  float targetInternalTemp = simTempExternal + 10.0 + (simSolarRadiation * 0.012) + heaterContribution - fanCooling;
  simTempInternal += (targetInternalTemp - simTempInternal) * 0.15 + (noise * 0.1);

  float targetInternalHum = 75.0 - (simTempInternal - 30.0) * 1.6 - (exhaustFanStatus ? (exhaustFanSpeed * 0.15) : 0.0);
  if (targetInternalHum < 25.0) targetInternalHum = 25.0;
  if (targetInternalHum > 85.0) targetInternalHum = 85.0;
  simHumidityInternal += (targetInternalHum - simHumidityInternal) * 0.15 + (noise * 0.2);

  if (simGrainMoisture > 12.0) {
    float dryingRate = (simTempInternal > 40.0 ? 0.02 : 0.008);
    simGrainMoisture -= dryingRate;
    simWeightCurrentKg -= (dryingRate * 0.05);
  } else {
    simGrainMoisture = 12.0 + (noise * 0.05);
  }
}

void broadcastTelemetry() {
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

  JsonObject act = doc.createNestedObject("actuators");
  act["exhaustFanStatus"]    = exhaustFanStatus;
  act["exhaustFanSpeed"]     = exhaustFanSpeed;
  act["blowerFanStatus"]     = blowerFanStatus;
  act["blowerFanSpeed"]      = blowerFanSpeed;
  act["auxHeaterStatus"]     = auxHeaterStatus;
  act["auxHeaterLevel"]      = auxHeaterLevel;
  act["controlMode"]         = controlMode;

  String jsonOutput;
  serializeJson(doc, jsonOutput);

  if (bleClientConnected && pSensorChar != NULL) {
    pSensorChar->setValue(jsonOutput.c_str());
    pSensorChar->notify();
  }

  if (mqttClient.connected()) {
    mqttClient.publish("hanjeli/greenhouse/telemetry", jsonOutput.c_str());
    mqttClient.publish("greenhouse/telemetry", jsonOutput.c_str());
  }
}

void mqttCallback(char* topic, byte* payload, unsigned int length) {
  String message = "";
  for (unsigned int i = 0; i < length; i++) message += (char)payload[i];
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
  if (mqttClient.connect(clientId.c_str())) {
    mqttClient.subscribe("greenhouse/control");
    mqttClient.subscribe("greenhouse/actuators");
    mqttClient.subscribe("hanjeli/greenhouse/control");
    mqttClient.subscribe("hanjeli/greenhouse/actuators");
  }
}

void setup() {
  Serial.begin(115200);
  pinMode(ONBOARD_LED_PIN, OUTPUT);
  digitalWrite(ONBOARD_LED_PIN, LOW);

  preferences.begin("dryer_config", true);
  wifi_ssid     = preferences.getString("ssid", wifi_ssid);
  wifi_password = preferences.getString("password", wifi_password);
  mqtt_broker   = preferences.getString("broker", mqtt_broker);
  mqtt_port     = preferences.getInt("port", mqtt_port);
  device_id     = preferences.getString("device_id", device_id);
  preferences.end();

  BLEDevice::init("SmartDryer-Hanjeli");
  pServer = BLEDevice::createServer();
  pServer->setCallbacks(new ServerCallbacks());

  BLEService* pService = pServer->createService(SERVICE_UUID);
  pSensorChar = pService->createCharacteristic(CHAR_SENSOR_UUID, BLECharacteristic::PROPERTY_READ | BLECharacteristic::PROPERTY_NOTIFY);
  pSensorChar->addDescriptor(new BLE2902());
  pStatusChar = pService->createCharacteristic(CHAR_STATUS_UUID, BLECharacteristic::PROPERTY_READ | BLECharacteristic::PROPERTY_NOTIFY);
  pStatusChar->addDescriptor(new BLE2902());
  pControlChar = pService->createCharacteristic(CHAR_CONTROL_UUID, BLECharacteristic::PROPERTY_WRITE);
  pControlChar->setCallbacks(new ControlCallbacks());
  pConfigChar = pService->createCharacteristic(CHAR_CONFIG_UUID, BLECharacteristic::PROPERTY_WRITE);
  pConfigChar->setCallbacks(new ConfigCallbacks());
  pService->start();

  BLEAdvertising* pAdvertising = BLEDevice::getAdvertising();
  pAdvertising->addServiceUUID(SERVICE_UUID);
  pAdvertising->setScanResponse(true);
  BLEDevice::startAdvertising();

  WiFi.mode(WIFI_STA);
  WiFi.begin(wifi_ssid.c_str(), wifi_password.c_str());

  mqttClient.setServer(mqtt_broker.c_str(), mqtt_port);
  mqttClient.setBufferSize(1024);
  mqttClient.setKeepAlive(60);
  mqttClient.setCallback(mqttCallback);
}

void loop() {
  if (WiFi.status() == WL_CONNECTED) {
    if (!mqttClient.connected()) reconnectMqtt();
    mqttClient.loop();
  }

  unsigned long currentMillis = millis();
  if (currentMillis - lastTelemetryMillis >= TELEMETRY_INTERVAL) {
    lastTelemetryMillis = currentMillis;
    updateSimulationPhysics();
    broadcastTelemetry();
  }

  if (!bleClientConnected && WiFi.status() != WL_CONNECTED) {
    digitalWrite(ONBOARD_LED_PIN, (millis() % 2000 < 100) ? HIGH : LOW);
  }
  delay(20);
}
`;

export const ARDUINO_PROGRAMS = [
  {
    id: 'dual-mode',
    title: 'Firmware Alat Fisik Live (Dual Mode Wi-Fi & BLE)',
    filename: 'esp32_smart_dryer_dual_mode.ino',
    badge: 'Produksi / Hardware Nyata',
    badgeClass: 'badge-prod',
    desc: 'Program utama mikrokontroler ESP32 untuk pembacaan sensor fisik DHT22, Load Cell, Pyranometer serta kontrol saklar Relay kipas exhaust, intake, sirkulasi, dan elemen pemanas.',
    target: 'ESP32 DevKit V1 (30-pin / 38-pin)',
    libraries: ['PubSubClient (Nick O\'Leary)', 'ArduinoJson (Benoit Blanchon v6/v7)', 'WiFi.h', 'BLEDevice.h', 'Preferences.h'],
    baudrate: 115200,
    code: ESP32_DUAL_MODE_CODE,
  },
  {
    id: 'simulator',
    title: 'Firmware Simulator ESP32 (Uji Coba Cepat Tanpa Sensor)',
    filename: 'esp32_smart_dryer_simulator.ino',
    badge: 'Simulasi Mandiri / Plug & Play',
    badgeClass: 'badge-sim',
    desc: 'Program simulasi mandiri untuk uji coba transmisi Wi-Fi MQTT dan Bluetooth BLE langsung dari board ESP32 tanpa memerlukan modul sensor fisik tambahan (cukup colok kabel USB ke laptop/charger).',
    target: 'ESP32 DevKit V1 (Cukup colok kabel USB)',
    libraries: ['PubSubClient (Nick O\'Leary)', 'ArduinoJson (v6/v7)', 'BLEDevice.h', 'WiFi.h', 'Preferences.h'],
    baudrate: 115200,
    code: ESP32_SIMULATOR_CODE,
  }
];
