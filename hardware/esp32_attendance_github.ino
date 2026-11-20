/*
  Smart Attendance - ESP32 API example
  This example shows the network request format.
  Connect your Arduino Uno to ESP32 using Serial and replace
  the demo fingerprint_id with the ID received from Arduino.
*/
#include <WiFi.h>
#include <HTTPClient.h>

const char* WIFI_SSID = "YOUR_WIFI_NAME";
const char* WIFI_PASSWORD = "YOUR_WIFI_PASSWORD";

// IMPORTANT: use your laptop IPv4 address, NOT localhost.
const char* API_URL = "http://YOUR_LAPTOP_IP/smart_attendance/api/attendance.php";

void setup() {
  Serial.begin(115200);
  WiFi.begin(WIFI_SSID, WIFI_PASSWORD);
  while (WiFi.status() != WL_CONNECTED) delay(500);
}

void loop() {
  // Replace 1 with the fingerprint ID received from Arduino Uno.
  int fingerprint_id = 1;
  sendAttendance(fingerprint_id);
  delay(10000);
}

void sendAttendance(int fingerprint_id) {
  if (WiFi.status() != WL_CONNECTED) return;

  HTTPClient http;
  http.begin(API_URL);
  http.addHeader("Content-Type", "application/json");

  String body = "{\"fingerprint_id\":" + String(fingerprint_id) +
                ",\"device_id\":\"ESP32_01\",\"subject_id\":1}";

  int code = http.POST(body);
  Serial.println(code);
  Serial.println(http.getString());
  http.end();
}
