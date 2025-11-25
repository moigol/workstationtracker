<div class="container">
    <main>
        <div class="scanner-container">
            <div class="scan-interface">
                <h2>RFID Scanner Interface</h2>
                
                <div class="scanner-status">
                    <div class="status-indicator status-active" id="status-indicator"></div>
                    <span id="status-text">Scanner Ready</span>
                </div>

                <div class="scanner-selection">
                    <div class="form-group">
                        <label for="scanner-select">Select Station:</label>
                        <select id="scanner-select" required>
                            <option value="">Select a station</option>
                        </select>
                    </div>
                </div>

                <div class="auto-scan-controls">
                    <label class="toggle-switch">
                        <input type="checkbox" id="auto-scan-toggle" checked>
                        <span class="toggle-slider"></span>
                    </label>
                    <span>Auto-scan Mode</span>
                </div>

                <div class="scanner-form">
                    <div class="form-row">
                        <div class="form-group">
                            <label for="tag-input">RFID Tag UID:</label>
                            <input type="text" id="tag-input" placeholder="Enter or scan tag UID" required>
                        </div>
                        <div class="form-group">
                            <label for="scan-type">Scan Type:</label>
                            <select id="scan-type" required>
                                <option value="IN">IN</option>
                                <option value="OUT">OUT</option>
                            </select>
                        </div>
                    </div>
                    <button class="manual-scan-btn" id="manual-scan-btn">
                        Record Scan
                    </button>
                </div>

                <div class="scanner-info">
                    <h4>Scanner Information</h4>
                    <div id="scanner-details">
                        Select a scanner to view details
                    </div>
                </div>

                <div class="scanner-stats">
                    <div class="stat-card">
                        <div class="stat-value" id="today-scans">0</div>
                        <div class="stat-label">Today's Scans</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="total-scans">0</div>
                        <div class="stat-label">Total Scans</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value" id="last-scan">-</div>
                        <div class="stat-label">Last Scan</div>
                    </div>
                </div>
            </div>

            <div class="recent-scans">
                <h2>Recent Scans</h2>
                <div id="recent-scans-list">
                    <div class="no-scans">No recent scans</div>
                </div>
            </div>
        </div>
    </main>
</div>

<script src="assets/js/scan.js"></script>