document.addEventListener('DOMContentLoaded', function() {
    // Initialize scanner
    initializeScanner();
});

let autoScanInterval = null;
let currentScannerId = null;

async function initializeScanner() {
    try {
        await loadScanners();
        await loadRecentScans();
        await loadScannerStats();
        setupEventListeners();

        startAutoScan();
    } catch (error) {
        console.error('Error initializing scanner:', error);
        showNotification('Error initializing scanner interface', 'error');
    }
}

function setupEventListeners() {
    // Manual scan button
    document.getElementById('manual-scan-btn').addEventListener('click', performManualScan);
    
    // Auto-scan toggle
    document.getElementById('auto-scan-toggle').addEventListener('change', function(e) {
        if (e.target.checked) {
            startAutoScan();
        } else {
            stopAutoScan();
        }
    });
    
    // Scanner selection
    document.getElementById('scanner-select').addEventListener('change', function(e) {
        currentScannerId = e.target.value;
        updateScannerDetails();
        loadScannerStats();
    });
    
    // Enter key in tag input
    document.getElementById('tag-input').addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performManualScan();
        }
    });
    
    // Simulate RFID scanner input (for testing)
    document.addEventListener('keydown', function(e) {
        // Ctrl+Shift+S to simulate scan (for testing without hardware)
        if (e.ctrlKey && e.shiftKey && e.key === 'S') {
            e.preventDefault();
            simulateRFIDScan();
        }
    });
}

async function loadScanners() {
    try {
        const sessionToken = localStorage.getItem('session_token');
        const response = await fetch('/api/scanners', {
            headers: {
                'Authorization': `Bearer ${sessionToken}`
            }
        });
        
        if (!response.ok) throw new Error('Failed to load scanners');
        
        const scanners = await response.json();
        const scannerSelect = document.getElementById('scanner-select');
        
        // Clear existing options
        while (scannerSelect.options.length > 1) {
            scannerSelect.remove(1);
        }
        
        scanners.forEach(scanner => {
            const option = document.createElement('option');
            option.value = scanner.id;
            option.textContent = `${scanner.name} (${scanner.id})`;
            scannerSelect.appendChild(option);
        });
        
        // Select first scanner by default
        if (scanners.length > 0) {
            scannerSelect.value = scanners[0].id;
            currentScannerId = scanners[0].id;
            updateScannerDetails();
        }
        
    } catch (error) {
        console.error('Error loading scanners:', error);
        showNotification('Error loading scanners', 'error');
    }
}

async function updateScannerDetails() {
    if (!currentScannerId) return;
    
    try {
        const sessionToken = localStorage.getItem('session_token');
        const response = await fetch(`/api/scanners/${currentScannerId}`, {
            headers: {
                'Authorization': `Bearer ${sessionToken}`
            }
        });
        
        if (!response.ok) throw new Error('Failed to load scanner details');
        
        const scanner = await response.json();
        const detailsDiv = document.getElementById('scanner-details');
        
        detailsDiv.innerHTML = `
            <p><strong>Scanner ID:</strong> ${scanner.id}</p>
            <p><strong>Name:</strong> ${scanner.name}</p>
            <p><strong>Description:</strong> ${scanner.description || 'No description'}</p>
            <p><strong>Date Added:</strong> ${new Date(scanner.date_added).toLocaleDateString()}</p>
        `;
        
    } catch (error) {
        console.error('Error loading scanner details:', error);
    }
}

async function loadRecentScans() {
    if (!currentScannerId) return;

    try {
        const sessionToken = localStorage.getItem('session_token');
        const today = new Date().toISOString().split('T')[0];

        const response = await fetch(`/api/logs?scanner_id=${currentScannerId}&limit=10`, {
            headers: {
                'Authorization': `Bearer ${sessionToken}`
            }
        });
        
        if (!response.ok) throw new Error('Failed to load recent scans');
        
        const data = await response.json();
        
        // Handle both paginated and non-paginated responses
        const logs = data.logs || data;
        displayRecentScans(logs);
        
    } catch (error) {
        console.error('Error loading recent scans:', error);
        showNotification('Error loading recent scans', 'error');
    }
}

function displayRecentScans(logs) {
    const container = document.getElementById('recent-scans-list');
    
    // Check if logs is an array and has items
    if (!Array.isArray(logs) || logs.length === 0) {
        container.innerHTML = '<div class="no-scans">No recent scans</div>';
        return;
    }

    container.innerHTML = logs.map(log => `
        <div class="scan-card ${log.type.toLowerCase()}">
            <div class="scan-header">
                <img src="/assets/images/avatar/${log.avatar}" />
            </div>
            <div class="scan-details">
                <div class="scan-detail">
                    <span class="scan-label">Badge ID</span>
                    <span class="scan-value">${log.tag_id}</span>
                </div>
                <div class="scan-detail">
                    <span class="scan-label">Station</span>
                    <span class="scan-value">${log.scanner_name}</span>
                </div>
                <div class="scan-detail">
                    <span class="scan-label">Name</span>
                    <span class="scan-value">${log.staff_name || 'No item assigned'}</span>
                </div>
                <div class="scan-detail">
                    <span class="scan-label">Time In</span>
                    <span class="scan-value">${new Date(log.date_time).toLocaleString()}</span>
                </div>
            </div>
        </div>
    `).join('');
}

async function loadScannerStats() {
    if (!currentScannerId) return;
    
    try {
        const sessionToken = localStorage.getItem('session_token');
        const today = new Date().toISOString().split('T')[0];
        
        // Today's scans
        const todayResponse = await fetch(`/api/logs?scanner_id=${currentScannerId}&date_from=${today}&limit=1000`, {
            headers: {
                'Authorization': `Bearer ${sessionToken}`
            }
        });
        
        // Total scans
        const totalResponse = await fetch(`/api/logs?scanner_id=${currentScannerId}&limit=1`, {
            headers: {
                'Authorization': `Bearer ${sessionToken}`
            }
        });
        
        if (!todayResponse.ok || !totalResponse.ok) throw new Error('Failed to load scanner stats');
        
        const todayData = await todayResponse.json();
        const totalData = await totalResponse.json();
        
        // Handle paginated responses
        const todayLogs = todayData.logs || todayData;
        const totalLogs = totalData.totalLogs || (Array.isArray(totalData) ? totalData.length : 0);
        
        document.getElementById('today-scans').textContent = Array.isArray(todayLogs) ? todayLogs.length : 0;
        document.getElementById('total-scans').textContent = totalLogs;
        
        // Last scan time
        if (Array.isArray(todayLogs) && todayLogs.length > 0) {
            const lastScan = new Date(todayLogs[0].date_time);
            document.getElementById('last-scan').textContent = lastScan.toLocaleTimeString();
        } else {
            document.getElementById('last-scan').textContent = '-';
        }
        
    } catch (error) {
        console.error('Error loading scanner stats:', error);
        document.getElementById('today-scans').textContent = '0';
        document.getElementById('total-scans').textContent = '0';
        document.getElementById('last-scan').textContent = '-';
    }
}

function getLogsFromResponse(data) {
    if (Array.isArray(data)) {
        return data;
    } else if (data && Array.isArray(data.logs)) {
        return data.logs;
    } else {
        return [];
    }
}

async function performManualScan() {
    const tagInput = document.getElementById('tag-input');
    const scanType = document.getElementById('scan-type');
    const scannerId = document.getElementById('scanner-select');
    
    const tagId = tagInput.value.trim();
    const type = scanType.value;
    const scanner_id = scannerId.value;
    
    if (!tagId || !scanner_id) {
        showNotification('Please enter tag UID and select a scanner', 'error');
        return;
    }
    
    await recordScan(tagId, type, scanner_id);
    tagInput.value = ''; // Clear input after scan
    tagInput.focus(); // Refocus for next scan
}

async function recordScan(tagId, type, scannerId) {
    
    try {
        const sessionToken = localStorage.getItem('session_token');
        const response = await fetch('/api/rfid-log', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Authorization': `Bearer ${sessionToken}`
            },
            body: JSON.stringify({
                tag_id: tagId,
                type: type,
                scanner_id: scannerId
            })
        });
        
        const data = await response.json();
        

        if (data.status == 'Success') {
            showNotification('Scan recorded successfully!', 'success');
            updateScannerStatus('Scan recorded', 'success');
            
            // Refresh recent scans and stats
            await loadRecentScans();
            await loadScannerStats();
            
            // Visual feedback
            flashScanIndicator();
            
        } else {
            showNotification('Error recording scan: ' + data.message, 'error');
            updateScannerStatus('Scan failed', 'error');
        }
        
    } catch (error) {
        console.error('Error recording scan:', error);
        showNotification('Error recording scan', 'error');
        updateScannerStatus('Scan failed', 'error');
    }
}

function startAutoScan() {
    updateScannerStatus('Auto-scan mode active', 'active');
    
    // Simulate auto-scan (in real implementation, this would listen to hardware)
    autoScanInterval = setInterval(() => {
        // This is where you would integrate with actual RFID hardware
        // For now, we'll just show a status update
        
        // Refresh recent scans and stats
        loadRecentScans();
        loadScannerStats();
        
        // Visual feedback
        flashScanIndicator();
    }, 500); // Check every 5 seconds
    
    showNotification('Auto-scan mode activated', 'success');
}

function stopAutoScan() {
    if (autoScanInterval) {
        clearInterval(autoScanInterval);
        autoScanInterval = null;
    }
    
    updateScannerStatus('Scanner Ready', 'ready');
    showNotification('Auto-scan mode deactivated', 'info');
}

function updateScannerStatus(message, status) {
    const indicator = document.getElementById('status-indicator');
    const statusText = document.getElementById('status-text');
    
    statusText.textContent = message;
    
    // Remove all status classes
    indicator.classList.remove('status-active', 'status-inactive');
    
    switch (status) {
        case 'active':
            indicator.classList.add('status-active');
            break;
        case 'error':
            indicator.classList.add('status-inactive');
            break;
        case 'ready':
        default:
            indicator.classList.add('status-active');
            break;
    }
}

function flashScanIndicator() {
    const indicator = document.getElementById('status-indicator');
    indicator.style.backgroundColor = '#f39c12';
    
    setTimeout(() => {
        indicator.style.backgroundColor = '';
    }, 500);
}

// Simulate RFID scan for testing
function simulateRFIDScan() {
    const testTags = [
        'TAG-001', 'TAG-002', 'TAG-003', 'TAG-004', 'TAG-005',
        'TAG-101', 'TAG-102', 'TAG-103', 'TAG-104', 'TAG-105'
    ];
    
    const randomTag = testTags[Math.floor(Math.random() * testTags.length)];
    const randomType = Math.random() > 0.5 ? 'IN' : 'OUT';
    
    document.getElementById('tag-input').value = randomTag;
    document.getElementById('scan-type').value = randomType;
    
    showNotification(`Simulated scan: ${randomTag} (${randomType})`, 'info');
}

function showNotification(message, type) {
    // Remove any existing notifications
    const existingNotifications = document.querySelectorAll('.scan-notification');
    existingNotifications.forEach(notification => notification.remove());
    
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `scan-notification ${type}`;
    notification.textContent = message;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 20px;
        border-radius: 4px;
        color: white;
        font-weight: 600;
        z-index: 1000;
        opacity: 0;
        transition: opacity 0.3s;
    `;
    
    if (type === 'success') {
        notification.style.backgroundColor = '#27ae60';
    } else if (type === 'error') {
        notification.style.backgroundColor = '#e74c3c';
    } else if (type === 'info') {
        notification.style.backgroundColor = '#3498db';
    } else {
        notification.style.backgroundColor = '#34495e';
    }
    
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.style.opacity = '1';
    }, 10);
    
    // Hide after 3 seconds
    setTimeout(() => {
        notification.style.opacity = '0';
        setTimeout(() => {
            if (notification.parentNode) {
                document.body.removeChild(notification);
            }
        }, 300);
    }, 3000);
}

// WebSocket integration for real-time scanning (optional)
function setupWebSocket() {
    // This would connect to your RFID scanner hardware via WebSocket
    // Example implementation:
    /*
    const ws = new WebSocket('ws://your-scanner-ip:port');
    
    ws.onmessage = function(event) {
        const scanData = JSON.parse(event.data);
        if (scanData.tag_id && currentScannerId) {
            // Auto-record the scan
            recordScan(scanData.tag_id, 'IN', currentScannerId);
        }
    };
    
    ws.onopen = function() {
        updateScannerStatus('Connected to scanner', 'active');
    };
    
    ws.onclose = function() {
        updateScannerStatus('Scanner disconnected', 'error');
    };
    */
}