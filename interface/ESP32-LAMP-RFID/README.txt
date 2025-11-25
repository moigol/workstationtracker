RFID-RC522  ->  ESP32
SDA         ->  GPIO5 (or any other pin, but adjust code accordingly)
SCK         ->  GPIO18
MOSI        ->  GPIO23
MISO        ->  GPIO19
GND         ->  GND
RST         ->  GPIO4 (or any other pin)
3.3V        ->  3.3V

OLED Display -> ESP32
GND          -> GND
VCC          -> 3.3V
SCL          -> GPIO22 (I2C Clock)
SDA          -> GPIO21 (I2C Data)