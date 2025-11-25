#include <SPI.h>
#include <MFRC522.h>
#include <WiFi.h>
#include <HTTPClient.h>
#include <Wire.h>
#include <Adafruit_GFX.h>
#include <Adafruit_SSD1306.h>
#include <ArduinoJson.h>
// #include <DHT.h>
#include <ESPAsyncWebServer.h>
#include <Preferences.h>
#include <rfidDashboard.h>
#include <rfidConfig.h>
#include <rfidPreferences.h>

// Set your own ESP32 Wi-Fi credentials
const char* wssid = "IC-RFID-0001"; // Hardcoded for each mcu
const char* wpassword = "moICRFID21"; 

// Set web server port to 80
AsyncWebServer server(80);

// Initialize OLED
Adafruit_SSD1306 display(SCREEN_WIDTH, SCREEN_HEIGHT, &Wire, OLED_RESET);

// Initialize RFID SCANNER
MFRC522 mfrc522(SS_PIN, RST_PIN);

// Initialize Outputs
bool buzzState = LOW;
bool ledState = LOW;

// DHT dht(DHTPIN, DHTTYPE);
// unsigned long lastSendTime = 0;
// const unsigned long interval = 60000;

void setup() {

  // Initialize pins
  pinMode(BUZZ_PIN, OUTPUT);
  pinMode(REMOTE_PIN, OUTPUT);
  pinMode(LOGIN_PIN, OUTPUT);
  pinMode(LOGOUT_PIN, OUTPUT);

  getPreferences();

  // Remote sending data indicator
  if (remoteAccess == "ON") {
    ledState = HIGH;
  } else {
    ledState = LOW;
  }
  digitalWrite(REMOTE_PIN, ledState);

  Serial.begin(115200);
  // Initialize DHT
  // dht.begin();

  // Initialize RFID
  SPI.begin();
  mfrc522.PCD_Init();

  // Initialize OLED
  if (!display.begin(SSD1306_SWITCHCAPVCC, 0x3C)) {
    Serial.println(F("SSD1306 allocation failed"));
    for (;;)
      ;  // Don't proceed, loop forever
  }
  display.display();
  delay(2000);  // Pause for 2 seconds
  display.clearDisplay();

  // Initialize Wi-Fi Access Point
  WiFi.softAP(wssid, wpassword);
  Serial.println("Device Access Point");
  Serial.print("SSID: ");
  Serial.println(wssid);
  Serial.print("Password: ");
  Serial.println(wpassword);
  Serial.print("IP Address: ");
  Serial.println(WiFi.softAPIP());

  display.clearDisplay();
  display.setCursor(0, 0);
  display.println("Access Point Started!");
  display.println(WiFi.softAPIP()); // Should show static IP
  display.display();

  // Web Server
  // Serve homepage
  server.on("/", HTTP_GET, [](AsyncWebServerRequest *request) {
    request->send_P(200, "text/html", index_html);
  });
  
  // Reset
  server.on("/restart", HTTP_GET, [](AsyncWebServerRequest *request) {
    Serial.println("ESP32 will restart in 5 seconds...");
    
    // Restart ESP32
    ESP.restart();
    request->send_P(200, "text/plain", "Restarting the device...");
  });

  // Handle AJAX SAVE POST
  server.on("/save", HTTP_POST, [](AsyncWebServerRequest *request) {}, NULL, [](AsyncWebServerRequest *request, uint8_t *data, size_t len, size_t index, size_t total) {
    String body = "";
    for (size_t i = 0; i < len; i++) {
      body += (char)data[i];
    }
    Serial.println("Received JSON: " + body);

    // Parse JSON
    DynamicJsonDocument doc(256);
    DeserializationError error = deserializeJson(doc, body);

    String message;
    if (!error) {
      savePreferences(doc);

      String remoteAccessValue = doc["remote_access"].as<String>();
      if (remoteAccessValue == "ON") {
        ledState = HIGH;
      } else {
        ledState = LOW;
      }
      digitalWrite(REMOTE_PIN, ledState);
    } else {
      message = "Invalid JSON received";
    }

    // Respond with JSON
    DynamicJsonDocument response(128);
    response["message"] = message;
    String json;
    serializeJson(response, json);
    request->send(200, "application/json", json);
  });

  // Send Current status
  server.on("/status", HTTP_GET, [](AsyncWebServerRequest *request) {
    int currState = digitalRead(REMOTE_PIN);
    // ledState = currState;
    // String status = (ledState == HIGH) ? "ON" : "OFF";

    getPreferences();

    String json = "{\"remoteAccess\":\"" + String(remoteAccess) + "\",\"deviceId\":\"" + String(wssid) + "\",\"scannerId\":\"" + scannerId + "\",\"localUrl\":\"" + localUrl + "\",\"remoteUrl\":\"" + remoteUrl + "\",\"wifiSsid\":\"" + wifiSsid + "\",\"wifiPass\":\"" + wifiPass + "\",\"staticIpX\":\"" + staticIpX + "\",\"staticIpY\":\"" + staticIpY + "\",\"staticIpZ\":\"" + staticIpZ + "\",\"staticIpW\":\"" + staticIpW + "\",\"gatewayIpX\":\"" + gatewayIpX + "\",\"gatewayIpY\":\"" + gatewayIpY + "\",\"gatewayIpZ\":\"" + gatewayIpZ + "\",\"gatewayIpW\":\"" + gatewayIpW + "\"}";
    request->send(200, "application/json", json);
  });

  // Start server
  server.begin();
  
  // Connect to WiFi
  if(!wifiSsid.isEmpty() && !wifiPass.isEmpty()) {

    getPreferences();

    display.setTextSize(1);
    display.setTextColor(SSD1306_WHITE);
    display.setCursor(0, 0);
    display.println("Connecting to WiFi");
    display.display();

    // Setup the static IP
    IPAddress local_IP(staticIpX, staticIpY, staticIpZ, staticIpW);
    IPAddress gateway(gatewayIpX, gatewayIpY, gatewayIpZ, gatewayIpW);
    IPAddress subnet(255, 255, 255, 0);
    IPAddress primaryDNS(8, 8, 8, 8);    // Optional
    IPAddress secondaryDNS(8, 8, 4, 4);  // Optional

    if (!WiFi.config(local_IP, gateway, subnet, primaryDNS, secondaryDNS)) {
      Serial.println("⚠️  STA Failed to configure");
    }

    WiFi.begin(wifiSsid, wifiPass);
    WiFi.setSleep(false);

    while (WiFi.status() != WL_CONNECTED) {
      delay(500);
      display.clearDisplay();
      display.setCursor(0, 0);
      display.println("Wifi Failed!");
      display.display();
    }

    display.clearDisplay();
    display.setCursor(0, 0);
    display.println("WiFi connected!");
    display.println(WiFi.localIP()); // Should show static IP
    display.display();

    Serial.println("WiFi connected!");
    Serial.print("Network IP: ");
    Serial.println(WiFi.localIP());
  } else {
    display.clearDisplay();
    display.setCursor(0, 0);
    display.println("Setup your wifi!");
    display.println(WiFi.localIP()); // Should show static IP
    display.display();
  }

  delay(2000);
}

void loop() {
  // Temp read implementation
  // String current_hdt = showTemp();

  display.clearDisplay();
  display.setCursor(0, 0);
  display.println("Welcome! Scan Your ID");
  // display.println(current_hdt);
  display.display();

  // Look for new cards
  if (!mfrc522.PICC_IsNewCardPresent() || !mfrc522.PICC_ReadCardSerial()) {
    delay(50);
    return;
  }

  // Show UID on serial monitor and OLED
  String uid = "";
  for (byte i = 0; i < mfrc522.uid.size; i++) {
    uid += String(mfrc522.uid.uidByte[i] < 0x10 ? "0" : "");
    uid += String(mfrc522.uid.uidByte[i], HEX);
  }

  display.clearDisplay();
  display.setCursor(0, 0);
  display.println("ID:" + uid);
  display.display();

  // Send UID to server
  if (WiFi.status() == WL_CONNECTED) {
    HTTPClient http;
    String postData = "device_id=" + String(wssid) + "&scanner_id=" + String(scannerId) + "&tag_id=" + uid;

    getPreferences();
    
    serverURL = remoteAccess == "ON" ? remoteUrl : localUrl;
    
    Serial.println("RFID UID: " + uid);
    Serial.println("Posting to: " + serverURL);  

    http.begin(serverURL);
    http.addHeader("Content-Type", "application/x-www-form-urlencoded");

    // Buzz
    digitalWrite(BUZZ_PIN, HIGH);
    delay(500);
    digitalWrite(BUZZ_PIN, LOW);
    delay(500);

    int httpResponseCode = http.POST(postData);
    Serial.println(String(httpResponseCode));

    if (httpResponseCode > 0) {
      String response = http.getString();

      Serial.println(String(response));
      // Parse JSON response
      parseAndDisplayResponse(response);
    } else {
      display.println("Error sending data");
      display.println("Code: " + String(httpResponseCode));
    }
    display.display();
    http.end();
  }

  delay(3000);  // Wait 3 seconds before next scan
}

// String showTemp() {
//   static String lastValue = "";
//   unsigned long currentMillis = millis();

//   if (currentMillis - lastSendTime >= interval) {
//     lastSendTime = currentMillis;

//     float temperature = dht.readTemperature();
//     float humidity = dht.readHumidity();

//     // Check if the readings are valid
//     if (isnan(temperature) || isnan(humidity)) {
//       Serial.println("Failed to read from DHT sensor!");
//       return lastValue;
//     }

//     Serial.print("Temperature: ");
//     Serial.print(temperature);
//     Serial.print(" °C | Humidity: ");
//     Serial.print(humidity);
//     Serial.println(" %");
//     lastValue = String(temperature) + " degC | " + String(humidity) + " %";
//     return lastValue;
//     // TODO: send data via WiFi/MQTT/HTTP if needed
//   }

//   return lastValue;
// }

void parseAndDisplayResponse(String jsonResponse) {
  // Create a JSON document
  StaticJsonDocument<200> doc;
  DeserializationError error = deserializeJson(doc, jsonResponse);

  if (error) {
    Serial.print("JSON parse failed: ");
    Serial.println(error.c_str());
    display.clearDisplay();
    display.setCursor(0, 0);
    display.println("Error return must be JSON data.");
    display.display();
    return;
  }

  // Extract values from JSON
  const char *status = doc["status"];    // "success" or "error"
  const char *message = doc["message"];  // Detailed message
  
  if(doc["access"] != "") {
    if(doc["access"] == "In") {
      // In PIN
      digitalWrite(LOGIN_PIN, HIGH);
      delay(500);
      digitalWrite(LOGIN_PIN, LOW);
      delay(500);
    } else {
      // Out PIN
      digitalWrite(LOGOUT_PIN, HIGH);
      delay(500);
      digitalWrite(LOGOUT_PIN, LOW);
      delay(500);
    }
  }

  // Display parsed values on OLED
  display.clearDisplay();
  display.setCursor(0, 0);
  display.println(String(message));
  display.display();
}