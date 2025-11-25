// Web page HTML
const char index_html[] PROGMEM = R"rawliteral(
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>iConnect RFID Settings</title>
        <style>
            :root {
                --primary: #4361ee;
                --primary-light: #4895ef;
                --dark: #3a0ca3;
                --success: #4cc9f0;
                --text: #2b2d42;
                --text-light: #6c757d;
                --background: #f8f9fa;
                --card-bg: #ffffff;
                --border: #dee2e6;
                --shadow: rgba(0, 0, 0, 0.05);
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            }

            body {
                background-color: var(--background);
                color: var(--text);
                line-height: 1.6;
                padding: 20px;
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
            }

            .settings-container {
                background: var(--card-bg);
                border-radius: 16px;
                box-shadow: 0 8px 30px var(--shadow);
                width: 100%;
                max-width: 600px;
                overflow: hidden;
            }

            .settings-header {
                padding: 24px;
                border-bottom: 1px solid var(--border);
                background: linear-gradient(120deg, var(--primary), var(--dark));
                color: white;
            }

            .settings-header h1 {
                font-size: 1.8rem;
                font-weight: 600;
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .settings-header h1::before {
                content: '⚙️';
                font-size: 1.5rem;
            }

            .settings-header p {
                color: rgba(255, 255, 255, 0.8);
                margin-top: 8px;
                font-size: 0.95rem;
            }

            .settings-content {
                padding: 24px;
            }

            .setting-group {
                margin-bottom: 32px;
            }

            .setting-group h2 {
                font-size: 1.2rem;
                margin-top: 20px;
                margin-bottom: 16px;
                color: var(--dark);
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .setting-group h2::before {
                content: '';
                display: inline-block;
                width: 4px;
                height: 18px;
                background: var(--primary);
                border-radius: 2px;
            }

            .setting-item {
                margin-bottom: 24px;
            }

            .setting-label {
                display: flex;
                justify-content: between;
                align-items: center;
                margin-bottom: 12px;
            }

            .setting-label span {
                font-weight: 500;
            }

            .toggle-switch {
                position: relative;
                display: inline-block;
                width: 50px;
                height: 26px;
                margin-left: auto;
            }

            .toggle-switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            .slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: #ccc;
                transition: .4s;
                border-radius: 34px;
            }

            .slider:before {
                position: absolute;
                content: "";
                height: 18px;
                width: 18px;
                left: 4px;
                bottom: 4px;
                background-color: white;
                transition: .4s;
                border-radius: 50%;
            }

            input:checked + .slider {
                background-color: var(--primary);
            }

            input:checked + .slider:before {
                transform: translateX(24px);
            }

            .form-fields {
                display: grid;
                gap: 16px;
                margin-top: 20px;
            }

            .form-input {
                display: flex;
                flex-direction: column;
            }

            .form-input.multiple {
                flex-direction: row;
            }

            .form-input label {
                font-weight: 500;
                margin-bottom: 8px;
                color: var(--text-light);
                font-size: 0.9rem;
            }

            .form-input input {
                padding: 12px 16px;
                border: 1px solid var(--border);
                border-radius: 8px;
                font-size: 1rem;
                transition: all 0.3s ease;
                width: 100%;
            }

            .form-input input:focus {
                outline: none;
                border-color: var(--primary-light);
                box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
            }

            .form-input input:disabled {
                background-color: #f1f3f9;
                color: var(--text-light);
                cursor: not-allowed;
            }

            .settings-footer {
                padding: 16px 24px;
                border-top: 1px solid var(--border);
                display: flex;
                justify-content: flex-end;
                gap: 12px;
            }

            .btn {
                padding: 10px 20px;
                border-radius: 8px;
                font-weight: 500;
                cursor: pointer;
                transition: all 0.2s ease;
                border: none;
                font-size: 0.95rem;
            }

            .btn-primary {
                background: var(--primary);
                color: white;
            }

            .btn-primary:hover {
                background: var(--dark);
                transform: translateY(-2px);
            }

            .btn-secondary {
                background: transparent;
                color: var(--text-light);
            }

            .btn-secondary:hover {
                background: #f1f3f9;
                color: var(--text);
            }

            .description {
                font-size: 0.85rem;
                color: var(--text-light);
                margin-top: 6px;
                line-height: 1.4;
            }

            @media (max-width: 600px) {
                body {
                    padding: 10px;
                }
                
                .settings-header, .settings-content, .settings-footer {
                    padding: 20px;
                }
            }
        </style>
    </head>
    <body>
        <div class="settings-container">
            <div class="settings-header">
                <h1>iConnect RFID Settings</h1>
                <p>Configure your rfid scanner device settings</p>
            </div>
            
            <div class="settings-content">
                <div class="setting-group">
                    <h2>Device</h2>

                    <div class="form-fields">
                        <div class="form-input">
                            <label for="deviceId">Device ID</label>
                            <input type="text" id="deviceId" value="" readonly>
                            <p class="description">The rfid scanner device identification.</p>
                        </div>

                        <div class="form-input">
                            <label for="scannerId">Scanner System ID</label>
                            <input type="number" id="scannerId" name="scanner_id" value="" min="0" max="100">
                            <p class="description">The rfid scanner id assigned from the database.</p>
                        </div>
                    </div>

                    <h2>Wifi Settings</h2>
                    
                    <div class="form-fields">
                        <div class="form-input">
                            <label for="wifiSsid">SSID</label>
                            <input type="text" id="wifiSsid" name="wifi_ssid" value="">

                            <p class="description">The WIFI name of your local network.</p>
                        </div>
                        
                        <div class="form-input">
                            <label for="wifiPass">Password</label>
                            <input type="text" id="wifiPass" name="wifi_pass" placeholder="WIFI Password" value="">
                            <p class="description">The WIFI password of your local network.</p>
                        </div>
                    </div>   

                    <h2>Date API Settings</h2>
                    
                    <div class="setting-item">
                        <div class="setting-label">
                            <span>Turn on Remote Data End-Point</span>
                            <label class="toggle-switch">
                                <input type="checkbox" id="remoteAccess" name="remote_access">
                                <span class="slider"></span>
                            </label>
                        </div>
                        <p class="description">Enable this option to allow sending your system data from outside your local network.</p>
                    </div>
                    
                    <div class="form-fields">
                        <div class="form-input">
                            <label for="localUrl">Local API Endpoint</label>
                            <input type="text" id="localUrl" name="local_url" value="">

                            <p class="description">The URL used to access the system within your local network.</p>
                        </div>
                        
                        <div class="form-input">
                            <label for="remoteUrl">Remote API Endpoint</label>
                            <input type="text" id="remoteUrl" name="remote_url" placeholder="https://your-domain.example" value="">
                            <p class="description">The URL used to access the system from outside your local network.</p>
                        </div>
                    </div>

                    <h2>Device Static IP</h2>
                    <div class="form-fields">
                        <div class="form-input">
                            <label for="">IP</label>
                        </div>
                        
                        <div class="form-input multiple">
                            <input type="text" id="staticIpX" name="staticip_x" value="192">
                            <input type="text" id="staticIpY" name="staticip_y" value="168">
                            <input type="text" id="staticIpZ" name="staticip_z" value="1">
                            <input type="text" id="staticIpW" name="staticip_w" value="143">
                        </div>
                        <div class="form-input">
                            <p class="description">The static IP you want this device to be known in your local network.</p>
                        </div>
                    </div> 
                    <div class="form-fields">
                        <div class="form-input">
                            <label for="">Gateway IP</label>
                        </div>
                        
                        <div class="form-input multiple">
                            <input type="text" id="gatewayIpX" name="gatewayip_x" value="192">
                            <input type="text" id="gatewayIpY" name="gatewayip_y" value="168">
                            <input type="text" id="gatewayIpZ" name="gatewayip_z" value="1">
                            <input type="text" id="gatewayIpW" name="gatewayip_w" value="1">
                        </div>
                        <div class="form-input">
                            <p class="description">The gateway IP of your local network.</p>
                        </div>
                    </div>                         
                </div>
            </div>
            
            <div class="settings-footer">
                <button class="btn btn-primary" id="saveChanges">Save Changes</button>
                <button class="btn btn-primary" id="restartDevice">Restart Device</button>
            </div>
        </div>

        <script>
            function saveData() {
                const data = {            
                    remote_access: document.getElementById('remoteAccess').checked == true ? "ON" : "OFF",
                    scanner_id: document.getElementById('scannerId').value,
                    local_url: document.getElementById('localUrl').value,
                    remote_url: document.getElementById('remoteUrl').value,
                    wifi_ssid: document.getElementById('wifiSsid').value,
                    wifi_pass: document.getElementById('wifiPass').value,

                    staticip_x:  parseInt(document.getElementById('staticIpX').value),
                    staticip_y:  parseInt(document.getElementById('staticIpY').value),
                    staticip_z:  parseInt(document.getElementById('staticIpZ').value),
                    staticip_w:  parseInt(document.getElementById('staticIpW').value),

                    gatewayip_x:  parseInt(document.getElementById('gatewayIpX').value),
                    gatewayip_y:  parseInt(document.getElementById('gatewayIpY').value),
                    gatewayip_z:  parseInt(document.getElementById('gatewayIpZ').value),
                    gatewayip_w:  parseInt(document.getElementById('gatewayIpW').value),
                };

                fetch("/save", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(data)
                })
                .then(response => response.json())
                .then(result => {
                    console.error("Response: " + result.message);
                })
                .catch(error => {
                    console.error("Error: " + error);
                });
            }

            function currentValues() {
                fetch("/status")
                    .then(response => {
                        if (!response.ok) {
                            throw new Error("Network failed with message: " + response.statusText);
                        }
                        return response.json();
                    })
                    .then(res => {
                        document.getElementById('remoteAccess').checked = (res.remoteAccess == "ON" ? true : false);
                        document.getElementById('deviceId').value = res.deviceId;
                        document.getElementById('scannerId').value = res.scannerId;
                        document.getElementById('localUrl').value = res.localUrl;
                        document.getElementById('remoteUrl').value = res.remoteUrl;
                        document.getElementById('wifiSsid').value = res.wifiSsid;
                        document.getElementById('wifiPass').value = res.wifiPass;

                        document.getElementById('staticIpX').value = res.staticIpX;
                        document.getElementById('staticIpY').value = res.staticIpY;
                        document.getElementById('staticIpZ').value = res.staticIpZ;
                        document.getElementById('staticIpW').value = res.staticIpW;

                        document.getElementById('gatewayIpX').value = res.gatewayIpX;
                        document.getElementById('gatewayIpY').value = res.gatewayIpY;
                        document.getElementById('gatewayIpZ').value = res.gatewayIpZ;
                        document.getElementById('gatewayIpW').value = res.gatewayIpW;
                    })
                    .catch(error => {
                        console.error("Fetch error:", error);
                    });
            }
            
            currentValues();

            function checkValues() {
                fetch("/status")
                    .then(response => {
                        if (!response.ok) {
                            throw new Error("Network failed with message: " + response.statusText);
                        }
                        return response.json();
                    })
                    .then(res => {
                        var ra = document.getElementById('remoteAccess').checked;
                        var si = document.getElementById('scannerId').value;
                        var lu = document.getElementById('localUrl').value;
                        var ru = document.getElementById('remoteUrl').value;
                        var ws = document.getElementById('wifiSsid').value;
                        var wp = document.getElementById('wifiPass').value;

                        document.getElementById('staticIpX').value;
                        document.getElementById('staticIpY').value;
                        document.getElementById('staticIpZ').value;
                        document.getElementById('staticIpW').value;

                        document.getElementById('gatewayIpX').value;
                        document.getElementById('gatewayIpY').value;
                        document.getElementById('gatewayIpZ').value;
                        document.getElementById('gatewayIpW').value;

                        if(ra != (res.remoteAccess == "ON" ? true : false) || si != res.scannerId || lu != res.localUrl || ru != res.remoteUrl || ws != res.wifiSsid || wp != res.wifiPass) {
                            document.getElementById('remoteAccess').checked = (res.remoteAccess == "ON" ? true : false);
                            document.getElementById('scannerId').value = res.scannerId;
                            document.getElementById('localUrl').value = res.localUrl;
                            document.getElementById('remoteUrl').value = res.remoteUrl;
                            document.getElementById('wifiSsid').value = res.wifiSsid;
                            document.getElementById('wifiPass').value = res.wifiPass;

                            document.getElementById('staticIpX').value = res.staticIpX;
                            document.getElementById('staticIpY').value = res.staticIpY;
                            document.getElementById('staticIpZ').value = res.staticIpZ;
                            document.getElementById('staticIpW').value = res.staticIpW;

                            document.getElementById('gatewayIpX').value = res.gatewayIpX;
                            document.getElementById('gatewayIpY').value = res.gatewayIpY;
                            document.getElementById('gatewayIpZ').value = res.gatewayIpZ;
                            document.getElementById('gatewayIpW').value = res.gatewayIpW;
                        }
                    })
                    .catch(error => {
                        console.error("Fetch error:", error);
                    });
            }

            document.addEventListener('DOMContentLoaded', function() {
                // Check status every 1s
                // setInterval(checkValues, 1000);
                
                document.getElementById('remoteAccess').addEventListener('change', function() {
                    saveData();
                });

                document.getElementById('scannerId').addEventListener('change', function() {
                    saveData();
                });

                document.getElementById('localUrl').addEventListener('change', function() {
                    saveData();
                });

                document.getElementById('remoteUrl').addEventListener('change', function() {
                    saveData();
                });

                document.getElementById('saveChanges').addEventListener('click', function() {
                    saveData();
                });

                document.getElementById('wifiSsid').addEventListener('change', function() {
                    saveData();
                });

                document.getElementById('wifiPass').addEventListener('change', function() {
                    saveData();
                });

                document.getElementById('staticIpX').addEventListener('change', function() {
                    saveData();
                });
                document.getElementById('staticIpY').addEventListener('change', function() {
                    saveData();
                });
                document.getElementById('staticIpZ').addEventListener('change', function() {
                    saveData();
                });
                document.getElementById('staticIpW').addEventListener('change', function() {
                    saveData();
                });

                document.getElementById('gatewayIpX').addEventListener('change', function() {
                    saveData();
                });
                document.getElementById('gatewayIpY').addEventListener('change', function() {
                    saveData();
                });
                document.getElementById('gatewayIpZ').addEventListener('change', function() {
                    saveData();
                });
                document.getElementById('gatewayIpW').addEventListener('change', function() {
                    saveData();
                });

                document.getElementById('restartDevice').addEventListener('click', function(e) {
                    e.preventDefault();
                    var deviceId = document.getElementById('deviceId').value;
                    document.body.innerHTML = '<h2>'+deviceId+' device is restarting...</h2><br><p>Click <a href="/">here</a> to reload the page.</p>';
                    setTimeout( function(){ 
                        location.reload();
                    }, 5000);

                    fetch("/restart")
                    .then(response => {
                        if (!response.ok) {
                            throw new Error("Network failed with message: " + response.statusText);
                        }
                        return response.json();
                    })
                    .then(res => {
                            
                    })
                    .catch(error => {
                        console.error("Fetch error:", error);
                    });
                });
            });
        </script>
    </body>
</html>
)rawliteral";