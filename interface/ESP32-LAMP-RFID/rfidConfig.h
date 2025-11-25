#ifndef RFIDCONFIG_H
#define RFIDCONFIG_H

#include <ArduinoJson.h> 

#pragma once

// DEVICE ACCESS POINT
#define DEVICE_SSID = "IC-RFID-0001"; // Hardcoded for each mcu
#define DEVICE_PASS = "moICRFID21";

// OLED configuration
#define SCREEN_WIDTH 128
#define SCREEN_HEIGHT 32
#define OLED_RESET -1

// RFID configuration
#define RST_PIN 4
#define SS_PIN 5

// OUTPUT
#define BUZZ_PIN 13
#define REMOTE_PIN 2
#define LOGIN_PIN 12
#define LOGOUT_PIN 14

// Temperature module
// #define DHTPIN 15      // GPIO4 (change to your pin)
// #define DHTTYPE DHT11  // DHT11 or DHT22


#endif