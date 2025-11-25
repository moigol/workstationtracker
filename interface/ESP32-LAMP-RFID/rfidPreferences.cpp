#include <Arduino.h>
#include <rfidConfig.h>
#include <rfidPreferences.h>
#include <Preferences.h>

const char* settingsKey = "rfid-mo"; 

// Parameters variable to hold saved values
int scannerId = 0;
String localUrl = "";
String remoteUrl = "";
String remoteAccess = "OFF";
String wifiSsid = "";
String wifiPass = "";
String serverURL = "";

int staticIpX = 192;
int staticIpY = 168;
int staticIpZ = 1;
int staticIpW = 1;

int gatewayIpX = 192;
int gatewayIpY = 168;
int gatewayIpZ = 1;
int gatewayIpW = 1;

void getPreferences() {
  
  Preferences preferences;

  // Open Preferences namespace
  preferences.begin("rfid-mo", false);

  scannerId = preferences.getInt("scannerId", 0);
  localUrl = preferences.getString("localUrl");
  remoteUrl = preferences.getString("remoteUrl");
  remoteAccess = preferences.getString("remoteAccess", "OFF");
  wifiSsid = preferences.getString("wifiSsid", "");
  wifiPass = preferences.getString("wifiPass", "");

  staticIpX = preferences.getInt("staticIpX", 192);
  staticIpY = preferences.getInt("staticIpY", 168);
  staticIpZ = preferences.getInt("staticIpZ", 1);
  staticIpW = preferences.getInt("staticIpW", 1);

  gatewayIpX = preferences.getInt("gatewayIpX", 192);
  gatewayIpY = preferences.getInt("gatewayIpY", 168);
  gatewayIpZ = preferences.getInt("gatewayIpZ", 1);
  gatewayIpW = preferences.getInt("gatewayIpW", 1);

  preferences.end();
}

void savePreferences(DynamicJsonDocument &doc) {

  Preferences preferences;

  // Open Preferences namespace
  preferences.begin("rfid-mo", false);

  if (doc["remote_access"]) {
    String remoteAccessValue = doc["remote_access"].as<String>();
    preferences.putString("remoteAccess", remoteAccessValue);
  }
  if (doc["scanner_id"]) {
    scannerId = doc["scanner_id"].as<int>();
    preferences.putInt("scannerId", scannerId);
  }
  if (doc["local_url"]) {
    localUrl = doc["local_url"].as<String>();
    preferences.putString("localUrl", localUrl);
  }
  if (doc["remote_url"]) {
    remoteUrl = doc["remote_url"].as<String>();
    preferences.putString("remoteUrl", remoteUrl);
  }
  if (doc["wifi_ssid"]) {
    wifiSsid = doc["wifi_ssid"].as<String>();
    preferences.putString("wifiSsid", wifiSsid);
  }
  if (doc["wifi_pass"]) {
    wifiPass = doc["wifi_pass"].as<String>();
    preferences.putString("wifiPass", wifiPass);
  }

  if (doc["staticip_x"]) {
    staticIpX = doc["staticip_x"].as<int>();
    preferences.putInt("staticIpX", staticIpX);
  }
  if (doc["staticip_y"]) {
    staticIpY = doc["staticip_y"].as<int>();
    preferences.putInt("staticIpY", staticIpY);
  }
  if (doc["staticip_z"]) {
    staticIpZ = doc["staticip_z"].as<int>();
    preferences.putInt("staticIpZ", staticIpZ);
  }
  if (doc["staticip_w"]) {
    staticIpW = doc["staticip_w"].as<int>();
    preferences.putInt("staticIpW", staticIpW);
  }

  if (doc["gatewayip_x"]) {
    gatewayIpX = doc["gatewayip_x"].as<int>();
    preferences.putInt("gatewayIpX", gatewayIpX);
  }
  if (doc["gatewayip_y"]) {
    gatewayIpY = doc["gatewayip_y"].as<int>();
    preferences.putInt("gatewayIpY", gatewayIpY);
  }
  if (doc["gatewayip_z"]) {
    gatewayIpZ = doc["gatewayip_z"].as<int>();
    preferences.putInt("gatewayIpZ", gatewayIpZ);
  }
  if (doc["gatewayip_w"]) {
    gatewayIpW = doc["gatewayip_w"].as<int>();
    preferences.putInt("gatewayIpW", gatewayIpW);
  }

  preferences.end();
}