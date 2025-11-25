#ifndef RFIDPREFERENCES_H
#define RFIDPREFERENCES_H

#include <ArduinoJson.h> 

#pragma once
// Parameters variable to hold saved values
extern int scannerId;
extern String localUrl;
extern String remoteUrl;
extern String remoteAccess;
extern String wifiSsid;
extern String wifiPass;
extern String serverURL;

extern int staticIpX;
extern int staticIpY;
extern int staticIpZ;
extern int staticIpW;

extern int gatewayIpX;
extern int gatewayIpY;
extern int gatewayIpZ;
extern int gatewayIpW;

// Declare function(s)
void getPreferences();
void savePreferences(DynamicJsonDocument &doc);

#endif