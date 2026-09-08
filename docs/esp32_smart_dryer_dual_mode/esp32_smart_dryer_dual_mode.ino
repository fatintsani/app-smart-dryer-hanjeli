/*
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
 *
 * ==============================================================================
 */


// ==============================================================================
// 1. LIBRARIES
// ==============================================================================

#include <WiFi.h>
#include <PubSubClient.h>
#include <ArduinoJson.h>
#include <Preferences.h>

#include <BLEDevice.h>
#include <BLEServer.h>
#include <BLEUtils.h>
#include <BLE2902.h>


// ==============================================================================
// 2. PIN DEFINITIONS
// ==============================================================================

// Relay
#define RELAY_EXHAUST_FAN_PIN   18
#define RELAY_INTAKE_FAN_PIN    19
#define RELAY_CIRC_FAN_PIN      21
#define RELAY_HEATER_PIN        22

// LED
#define STATUS_LED_PIN           2

// Sensor ADC
#define SENSOR_MOISTURE_ADC_PIN 34
#define SENSOR_WEIGHT_ADC_PIN   35


// ==============================================================================
// 3. BLE SERVICE & CHARACTERISTIC UUID
// ==============================================================================

#define SERVICE_UUID \
"19b10000-e8f2-537e-4f6c-d104768a1214"

#define CHAR_SENSOR_UUID \
"19b10001-e8f2-537e-4f6c-d104768a1214"

#define CHAR_STATUS_UUID \
"19b10002-e8f2-537e-4f6c-d104768a1214"

#define CHAR_CONTROL_UUID \
"19b10003-e8f2-537e-4f6c-d104768a1214"

#define CHAR_CONFIG_UUID \
"19b10004-e8f2-537e-4f6c-d104768a1214"


// ==============================================================================
// 4. GLOBAL OBJECTS
// ==============================================================================

Preferences preferences;

WiFiClient espClient;

PubSubClient mqttClient(espClient);


// ==============================================================================
// 5. BLE OBJECTS
// ==============================================================================

BLEServer* pServer = NULL;

BLECharacteristic* pSensorChar = NULL;
BLECharacteristic* pStatusChar = NULL;
BLECharacteristic* pControlChar = NULL;
BLECharacteristic* pConfigChar = NULL;

bool bleClientConnected = false;


// ==============================================================================
// 6. NETWORK CONFIGURATION
// ==============================================================================

String wifi_ssid     = "Desa_Wisata_Hanjeli_2.4G";
String wifi_password = "HanjeliSukabumi2026";

String mqtt_broker   = "broker.hivemq.com";

int mqtt_port        = 1883;

String device_id     = "SRD-001";


// ==============================================================================
// 7. ACTUATOR STATES
// ==============================================================================

bool exhaustFanState = true;
int  exhaustFanSpeed = 70;

bool intakeFanState = true;

bool circFanState = true;

bool auxHeaterState = false;
int  auxHeaterLevel = 0;

bool isManualOverride = false;

String overrideMode = "AUTO";


// ==============================================================================
// 8. SENSOR / TELEMETRY VARIABLES
// ==============================================================================

// NOTE:
// Saat ini masih menggunakan nilai simulasi.
// Ganti dengan sensor sebenarnya nanti.

float tempInternal     = 43.5;
float humidityInternal = 54.0;

float tempExternal     = 31.2;
float humidityExternal = 66.0;

float solarRadiation   = 760.0;

float grainMoisture    = 13.8;

float weightCurrentKg  = 44.5;


// ==============================================================================
// 9. TELEMETRY TIMER
// ==============================================================================

unsigned long lastTelemetryMillis = 0;

const unsigned long TELEMETRY_INTERVAL = 3000;


// ==============================================================================
// 10. FUNCTION PROTOTYPES
// ==============================================================================

// IMPORTANT:
// Prototype ini memperbaiki error:
// 'applyActuators' was not declared in this scope

void applyActuators();

void readSensors();

void mqttCallback(
  char* topic,
  byte* message,
  unsigned int length
);

void reconnectMqtt();

void broadcastTelemetry();


// ==============================================================================
// 11. BLE SERVER CALLBACKS
// ==============================================================================

class MyServerCallbacks : public BLEServerCallbacks {

  void onConnect(BLEServer* pServer) {

    bleClientConnected = true;

    Serial.println(
      "[BLE] Web Bluetooth Central connected!"
    );
  }


  void onDisconnect(BLEServer* pServer) {

    bleClientConnected = false;

    Serial.println(
      "[BLE] Web Bluetooth Central disconnected."
    );

    Serial.println(
      "[BLE] Restarting advertising..."
    );

    pServer->startAdvertising();
  }
};


// ==============================================================================
// 12. BLE CONTROL CALLBACK
// ==============================================================================

class ControlCallbacks : public BLECharacteristicCallbacks {

  void onWrite(BLECharacteristic* pCharacteristic) {

    String rxValue = pCharacteristic->getValue();

    if (rxValue.length() == 0) {
      return;
    }


    Serial.println(
      "[BLE Control Received]"
    );

    Serial.println(
      rxValue
    );


    // --------------------------------------------------------------------------
    // Parse JSON
    // --------------------------------------------------------------------------

    DynamicJsonDocument doc(1024);

    DeserializationError error =
      deserializeJson(doc, rxValue);


    if (error) {

      Serial.print(
        "[BLE] JSON Error: "
      );

      Serial.println(
        error.c_str()
      );

      return;
    }


    // --------------------------------------------------------------------------
    // Get data object
    // --------------------------------------------------------------------------

    JsonObject data =
      doc["data"].as<JsonObject>();


    // --------------------------------------------------------------------------
    // Update actuator state
    // --------------------------------------------------------------------------

    if (data.containsKey("exhaustFanStatus")) {

      exhaustFanState =
        data["exhaustFanStatus"];
    }


    if (data.containsKey("exhaustFanSpeed")) {

      exhaustFanSpeed =
        data["exhaustFanSpeed"];
    }


    if (data.containsKey("intakeFanStatus")) {

      intakeFanState =
        data["intakeFanStatus"];
    }


    if (data.containsKey("circFanStatus")) {

      circFanState =
        data["circFanStatus"];
    }


    if (data.containsKey("auxHeaterStatus")) {

      auxHeaterState =
        data["auxHeaterStatus"];
    }


    if (data.containsKey("auxHeaterLevel")) {

      auxHeaterLevel =
        data["auxHeaterLevel"];
    }


    if (data.containsKey("isOverrideActive")) {

      isManualOverride =
        data["isOverrideActive"];
    }


    if (data.containsKey("overrideMode")) {

      overrideMode =
        data["overrideMode"].as<String>();
    }


    // --------------------------------------------------------------------------
    // Apply hardware
    // --------------------------------------------------------------------------

    applyActuators();


    Serial.println(
      "[BLE] Actuator state updated."
    );
  }
};


// ==============================================================================
// 13. BLE CONFIGURATION CALLBACK
// ==============================================================================

class ConfigCallbacks : public BLECharacteristicCallbacks {

  void onWrite(BLECharacteristic* pCharacteristic) {

    String rxValue =
      pCharacteristic->getValue();


    if (rxValue.length() == 0) {
      return;
    }


    Serial.println(
      "[BLE Wi-Fi Config Received]"
    );

    Serial.println(
      rxValue
    );


    // --------------------------------------------------------------------------
    // Parse JSON
    // --------------------------------------------------------------------------

    DynamicJsonDocument doc(1024);

    DeserializationError error =
      deserializeJson(doc, rxValue);


    if (error) {

      Serial.print(
        "[BLE Config] JSON Error: "
      );

      Serial.println(
        error.c_str()
      );

      return;
    }


    // --------------------------------------------------------------------------
    // Check action
    // --------------------------------------------------------------------------

    if (doc["action"] != "SET_WIFI_CONFIG") {

      Serial.println(
        "[BLE Config] Unknown action."
      );

      return;
    }


    // --------------------------------------------------------------------------
    // Read configuration
    // --------------------------------------------------------------------------

    if (doc.containsKey("ssid")) {

      wifi_ssid =
        doc["ssid"].as<String>();
    }


    if (doc.containsKey("password")) {

      wifi_password =
        doc["password"].as<String>();
    }


    if (doc.containsKey("mqtt_broker")) {

      mqtt_broker =
        doc["mqtt_broker"].as<String>();
    }


    if (doc.containsKey("mqtt_port")) {

      mqtt_port =
        doc["mqtt_port"];
    }


    if (doc.containsKey("device_id")) {

      device_id =
        doc["device_id"].as<String>();
    }


    // --------------------------------------------------------------------------
    // Save configuration to NVS
    // --------------------------------------------------------------------------

    preferences.begin(
      "srd_config",
      false
    );


    preferences.putString(
      "ssid",
      wifi_ssid
    );


    preferences.putString(
      "password",
      wifi_password
    );


    preferences.putString(
      "broker",
      mqtt_broker
    );


    preferences.putInt(
      "port",
      mqtt_port
    );


    preferences.putString(
      "dev_id",
      device_id
    );


    preferences.end();


    Serial.println(
      "[NVS] Configuration saved."
    );


    // --------------------------------------------------------------------------
    // Reconnect Wi-Fi
    // --------------------------------------------------------------------------

    Serial.println(
      "[Wi-Fi] Reconnecting..."
    );


    WiFi.disconnect(true);

    delay(500);

    WiFi.mode(WIFI_STA);

    WiFi.begin(
      wifi_ssid.c_str(),
      wifi_password.c_str()
    );
  }
};


// ==============================================================================
// 14. HARDWARE ACTUATOR CONTROL
// ==============================================================================

void applyActuators() {

  digitalWrite(
    RELAY_EXHAUST_FAN_PIN,
    exhaustFanState ? HIGH : LOW
  );


  digitalWrite(
    RELAY_INTAKE_FAN_PIN,
    intakeFanState ? HIGH : LOW
  );


  digitalWrite(
    RELAY_CIRC_FAN_PIN,
    circFanState ? HIGH : LOW
  );


  digitalWrite(
    RELAY_HEATER_PIN,
    auxHeaterState ? HIGH : LOW
  );


  Serial.println(
    "[ACTUATOR] Hardware state applied."
  );
}


// ==============================================================================
// 15. READ SENSORS
// ==============================================================================

void readSensors() {

  /*
   * --------------------------------------------------------------------------
   * IMPORTANT
   * --------------------------------------------------------------------------
   *
   * Saat ini sensor masih berupa simulasi.
   *
   * Contoh implementasi sensor sebenarnya:
   *
   * float h_int = dhtInternal.readHumidity();
   * float t_int = dhtInternal.readTemperature();
   *
   * atau:
   *
   * int moistureRaw =
   *   analogRead(SENSOR_MOISTURE_ADC_PIN);
   *
   * --------------------------------------------------------------------------
   */


  // Simulasi temperature

  tempInternal +=
    random(-5, 6) / 10.0;


  // Simulasi humidity

  humidityInternal +=
    random(-8, 9) / 10.0;


  // Limit temperature

  if (tempInternal < 35.0) {

    tempInternal = 35.0;
  }


  if (tempInternal > 58.0) {

    tempInternal = 58.0;
  }


  // Limit humidity

  if (humidityInternal < 30.0) {

    humidityInternal = 30.0;
  }


  if (humidityInternal > 85.0) {

    humidityInternal = 85.0;
  }
}


// ==============================================================================
// 16. MQTT CALLBACK
// ==============================================================================

void mqttCallback(
  char* topic,
  byte* message,
  unsigned int length
) {

  String payload = "";


  for (
    unsigned int i = 0;
    i < length;
    i++
  ) {

    payload +=
      (char)message[i];
  }


  Serial.print(
    "[MQTT Inbound] "
  );

  Serial.print(
    topic
  );

  Serial.print(
    " : "
  );

  Serial.println(
    payload
  );


  // --------------------------------------------------------------------------
  // Parse JSON
  // --------------------------------------------------------------------------

  DynamicJsonDocument doc(1024);

  DeserializationError error =
    deserializeJson(
      doc,
      payload
    );


  if (error) {

    Serial.print(
      "[MQTT] JSON Error: "
    );

    Serial.println(
      error.c_str()
    );

    return;
  }


  // --------------------------------------------------------------------------
  // Read data
  // --------------------------------------------------------------------------

  JsonObject data =
    doc["data"].as<JsonObject>();


  if (data.containsKey("exhaustFanStatus")) {

    exhaustFanState =
      data["exhaustFanStatus"];
  }


  if (data.containsKey("exhaustFanSpeed")) {

    exhaustFanSpeed =
      data["exhaustFanSpeed"];
  }


  if (data.containsKey("intakeFanStatus")) {

    intakeFanState =
      data["intakeFanStatus"];
  }


  if (data.containsKey("circFanStatus")) {

    circFanState =
      data["circFanStatus"];
  }


  if (data.containsKey("auxHeaterStatus")) {

    auxHeaterState =
      data["auxHeaterStatus"];
  }


  if (data.containsKey("auxHeaterLevel")) {

    auxHeaterLevel =
      data["auxHeaterLevel"];
  }


  if (data.containsKey("isOverrideActive")) {

    isManualOverride =
      data["isOverrideActive"];
  }


  if (data.containsKey("overrideMode")) {

    overrideMode =
      data["overrideMode"].as<String>();
  }


  // --------------------------------------------------------------------------
  // Apply actuator
  // --------------------------------------------------------------------------

  applyActuators();


  Serial.println(
    "[MQTT] Actuator state updated."
  );
}


// ==============================================================================
// 17. MQTT RECONNECT
// ==============================================================================

void reconnectMqtt() {

  if (
    WiFi.status() != WL_CONNECTED
  ) {

    return;
  }


  if (
    mqttClient.connected()
  ) {

    return;
  }


  Serial.print(
    "[MQTT] Connecting to broker: "
  );

  Serial.println(
    mqtt_broker
  );


  // --------------------------------------------------------------------------
  // Generate unique MQTT client ID
  // --------------------------------------------------------------------------

  String clientId =
    "ESP32_SRD_" +
    device_id +
    "_" +
    String(
      random(0xffff),
      HEX
    );


  // --------------------------------------------------------------------------
  // Connect
  // --------------------------------------------------------------------------

  if (
    mqttClient.connect(
      clientId.c_str()
    )
  ) {

    Serial.println(
      "[MQTT] Connected!"
    );


    // ------------------------------------------------------------------------
    // Subscribe topic
    // ------------------------------------------------------------------------

    String cmdTopic1 =
      "smartroomdryer/device/" +
      device_id +
      "/command";


    String cmdTopic2 =
      "hanjeli/greenhouse/control";


    mqttClient.subscribe(
      cmdTopic1.c_str()
    );


    mqttClient.subscribe(
      cmdTopic2.c_str()
    );


    Serial.print(
      "[MQTT] Subscribed: "
    );

    Serial.println(
      cmdTopic1
    );


    Serial.print(
      "[MQTT] Subscribed: "
    );

    Serial.println(
      cmdTopic2
    );
  }

  else {

    Serial.print(
      "[MQTT] Failed, rc="
    );

    Serial.println(
      mqttClient.state()
    );
  }
}


// ==============================================================================
// 18. BROADCAST TELEMETRY
// ==============================================================================

void broadcastTelemetry() {

  // --------------------------------------------------------------------------
  // Read sensors
  // --------------------------------------------------------------------------

  readSensors();


  // --------------------------------------------------------------------------
  // Build JSON
  // --------------------------------------------------------------------------

  DynamicJsonDocument doc(1024);


  doc["device_id"] =
    device_id;


  doc["temperature"] =
    round(tempInternal * 10) / 10.0;


  doc["humidity"] =
    round(humidityInternal * 10) / 10.0;


  doc["tempExternal"] =
    round(tempExternal * 10) / 10.0;


  doc["humidityExternal"] =
    round(humidityExternal * 10) / 10.0;


  doc["solarRadiation"] =
    solarRadiation;


  doc["grainMoisture"] =
    round(grainMoisture * 10) / 10.0;


  doc["weightCurrentKg"] =
    round(weightCurrentKg * 10) / 10.0;


  doc["exhaustFanStatus"] =
    exhaustFanState;


  doc["exhaustFanSpeed"] =
    exhaustFanSpeed;


  doc["intakeFanStatus"] =
    intakeFanState;


  doc["circFanStatus"] =
    circFanState;


  doc["heaterStatus"] =
    auxHeaterState;


  doc["heaterLevel"] =
    auxHeaterLevel;


  doc["isOverrideActive"] =
    isManualOverride;


  doc["overrideMode"] =
    overrideMode;


  // --------------------------------------------------------------------------
  // Serialize JSON
  // --------------------------------------------------------------------------

  String jsonBuffer;


  serializeJson(
    doc,
    jsonBuffer
  );


  // --------------------------------------------------------------------------
  // MQTT
  // --------------------------------------------------------------------------

  if (
    mqttClient.connected()
  ) {

    String topic1 =
      "smartroomdryer/device/" +
      device_id +
      "/telemetry";


    String topic2 =
      "hanjeli/greenhouse/telemetry";


    mqttClient.publish(
      topic1.c_str(),
      jsonBuffer.c_str()
    );


    mqttClient.publish(
      topic2.c_str(),
      jsonBuffer.c_str()
    );


    Serial.println(
      "[MQTT Tx] " +
      jsonBuffer
    );
  }


  // --------------------------------------------------------------------------
  // BLE
  // --------------------------------------------------------------------------

  if (
    bleClientConnected &&
    pSensorChar != NULL
  ) {

    pSensorChar->setValue(
      (uint8_t*)jsonBuffer.c_str(),
      jsonBuffer.length()
    );


    pSensorChar->notify();


    Serial.println(
      "[BLE Tx] " +
      jsonBuffer
    );
  }
}


// ==============================================================================
// 19. SETUP
// ==============================================================================

void setup() {

  // --------------------------------------------------------------------------
  // Serial
  // --------------------------------------------------------------------------

  Serial.begin(
    115200
  );


  delay(1000);


  Serial.println(
    "\n\n========================================================"
  );


  Serial.println(
    "  SMART ROOM DRYER — DESA WISATA HANJELI"
  );


  Serial.println(
    "  Dual Connectivity"
  );


  Serial.println(
    "  Wi-Fi + MQTT & Bluetooth BLE"
  );


  Serial.println(
    "========================================================"
  );


  // --------------------------------------------------------------------------
  // Initialize relay pins
  // --------------------------------------------------------------------------

  pinMode(
    RELAY_EXHAUST_FAN_PIN,
    OUTPUT
  );


  pinMode(
    RELAY_INTAKE_FAN_PIN,
    OUTPUT
  );


  pinMode(
    RELAY_CIRC_FAN_PIN,
    OUTPUT
  );


  pinMode(
    RELAY_HEATER_PIN,
    OUTPUT
  );


  pinMode(
    STATUS_LED_PIN,
    OUTPUT
  );


  // --------------------------------------------------------------------------
  // Apply initial actuator state
  // --------------------------------------------------------------------------

  applyActuators();


  // --------------------------------------------------------------------------
  // Load saved configuration
  // --------------------------------------------------------------------------

  preferences.begin(
    "srd_config",
    true
  );


  wifi_ssid =
    preferences.getString(
      "ssid",
      wifi_ssid
    );


  wifi_password =
    preferences.getString(
      "password",
      wifi_password
    );


  mqtt_broker =
    preferences.getString(
      "broker",
      mqtt_broker
    );


  mqtt_port =
    preferences.getInt(
      "port",
      mqtt_port
    );


  device_id =
    preferences.getString(
      "dev_id",
      device_id
    );


  preferences.end();


  // --------------------------------------------------------------------------
  // Print configuration
  // --------------------------------------------------------------------------

  Serial.println(
    "[CONFIG] Device ID: " +
    device_id
  );


  Serial.println(
    "[CONFIG] MQTT Broker: " +
    mqtt_broker
  );


  Serial.println(
    "[CONFIG] MQTT Port: " +
    String(mqtt_port)
  );


  // --------------------------------------------------------------------------
  // BLE initialization
  // --------------------------------------------------------------------------

  Serial.println(
    "[BLE] Starting BLE GATT Server..."
  );


  BLEDevice::init(
    "SmartDryer-Hanjeli"
  );


  pServer =
    BLEDevice::createServer();


  pServer->setCallbacks(
    new MyServerCallbacks()
  );


  // --------------------------------------------------------------------------
  // Create BLE service
  // --------------------------------------------------------------------------

  BLEService* pService =
    pServer->createService(
      SERVICE_UUID
    );


  // --------------------------------------------------------------------------
  // Sensor characteristic
  // --------------------------------------------------------------------------

  pSensorChar =
    pService->createCharacteristic(

      CHAR_SENSOR_UUID,

      BLECharacteristic::PROPERTY_READ |
      BLECharacteristic::PROPERTY_NOTIFY
    );


  pSensorChar->addDescriptor(
    new BLE2902()
  );


  // --------------------------------------------------------------------------
  // Status characteristic
  // --------------------------------------------------------------------------

  pStatusChar =
    pService->createCharacteristic(

      CHAR_STATUS_UUID,

      BLECharacteristic::PROPERTY_READ |
      BLECharacteristic::PROPERTY_NOTIFY
    );


  pStatusChar->addDescriptor(
    new BLE2902()
  );


  // --------------------------------------------------------------------------
  // Control characteristic
  // --------------------------------------------------------------------------

  pControlChar =
    pService->createCharacteristic(

      CHAR_CONTROL_UUID,

      BLECharacteristic::PROPERTY_WRITE |
      BLECharacteristic::PROPERTY_READ
    );


  pControlChar->setCallbacks(
    new ControlCallbacks()
  );


  // --------------------------------------------------------------------------
  // Config characteristic
  // --------------------------------------------------------------------------

  pConfigChar =
    pService->createCharacteristic(

      CHAR_CONFIG_UUID,

      BLECharacteristic::PROPERTY_WRITE |
      BLECharacteristic::PROPERTY_READ
    );


  pConfigChar->setCallbacks(
    new ConfigCallbacks()
  );


  // --------------------------------------------------------------------------
  // Start BLE service
  // --------------------------------------------------------------------------

  pService->start();


  // --------------------------------------------------------------------------
  // BLE advertising
  // --------------------------------------------------------------------------

  BLEAdvertising* pAdvertising =
    BLEDevice::getAdvertising();


  pAdvertising->addServiceUUID(
    SERVICE_UUID
  );


  pAdvertising->setScanResponse(
    true
  );


  pAdvertising->setMinPreferred(
    0x06
  );


  pAdvertising->setMinPreferred(
    0x12
  );


  BLEDevice::startAdvertising();


  Serial.println(
    "[BLE] Advertising started."
  );


  Serial.println(
    "[BLE] Device name: SmartDryer-Hanjeli"
  );


  // --------------------------------------------------------------------------
  // Wi-Fi
  // --------------------------------------------------------------------------

  Serial.print(
    "[Wi-Fi] Connecting to: "
  );


  Serial.println(
    wifi_ssid
  );


  WiFi.mode(
    WIFI_STA
  );


  WiFi.begin(
    wifi_ssid.c_str(),
    wifi_password.c_str()
  );


  // --------------------------------------------------------------------------
  // MQTT
  // --------------------------------------------------------------------------

  mqttClient.setServer(
    mqtt_broker.c_str(),
    mqtt_port
  );


  mqttClient.setCallback(
    mqttCallback
  );


  // --------------------------------------------------------------------------
  // MQTT buffer
  // --------------------------------------------------------------------------

  mqttClient.setBufferSize(
    2048
  );


  Serial.println(
    "[SYSTEM] Setup completed."
  );
}


// ==============================================================================
// 20. MAIN LOOP
// ==============================================================================

void loop() {

  // ============================================================================
  // WI-FI + MQTT
  // ============================================================================

  if (
    WiFi.status() == WL_CONNECTED
  ) {

    // --------------------------------------------------------------------------
    // MQTT connection
    // --------------------------------------------------------------------------

    if (
      !mqttClient.connected()
    ) {

      reconnectMqtt();
    }

    else {

      mqttClient.loop();
    }


    // --------------------------------------------------------------------------
    // Wi-Fi status LED
    // --------------------------------------------------------------------------

    digitalWrite(
      STATUS_LED_PIN,
      HIGH
    );
  }

  else {

    // --------------------------------------------------------------------------
    // Wi-Fi disconnected
    // Blink LED
    // --------------------------------------------------------------------------

    digitalWrite(
      STATUS_LED_PIN,
      (millis() / 500) % 2
    );
  }


  // ============================================================================
  // PERIODIC TELEMETRY
  // ============================================================================

  if (
    millis() - lastTelemetryMillis
    >= TELEMETRY_INTERVAL
  ) {

    lastTelemetryMillis =
      millis();


    broadcastTelemetry();
  }


  // ============================================================================
  // SMALL DELAY
  // ============================================================================

  delay(10);
}