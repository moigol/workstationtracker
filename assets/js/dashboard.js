document.addEventListener('DOMContentLoaded', function() {
    // Load filters and logs when page loads
    loadScanners();
    loadTags();
    loadLogs();
    loadStaffs();
    
    // Add event listener for filter button
    document.getElementById('apply-filters').addEventListener('click', function() {
        currentPage = 1;
        loadLogs();
    });
    
    // Add event listener for reset filter button
    document.getElementById('reset-filters').addEventListener('click', function() {
        document.getElementById('scanner-filter').value = '';
        document.getElementById('tag-filter').value = '';
        document.getElementById('staff-filter').value = '';
        document.getElementById('type-filter').value = '';
        document.getElementById('date-from').value = '';
        document.getElementById('date-to').value = '';
        currentPage = 1;
        loadLogs();
    });
    
    // Pagination event listeners
    document.getElementById('prev-page').addEventListener('click', function() {
        if (currentPage > 1) {
            currentPage--;
            loadLogs();
        }
    });
    
    document.getElementById('next-page').addEventListener('click', function() {
        if (currentPage < totalPages) {
            currentPage++;
            loadLogs();
        }
    });

    startRealTimeUpdates();
});

let currentPage = 1;
let totalPages = 1;
const logsPerPage = 20;

function loadScanners() {
    fetch('/api/scanners')
        .then(response => response.json())
        .then(data => {
            const scannerFilter = document.getElementById('scanner-filter');
            data.forEach(scanner => {
                const option = document.createElement('option');
                option.value = scanner.id;
                option.textContent = scanner.name;
                scannerFilter.appendChild(option);
            });
        })
        .catch(error => console.error('Error loading scanners:', error));
}

function loadTags() {
    fetch('/api/rfid-tags')
        .then(response => response.json())
        .then(data => {
            const tagFilter = document.getElementById('tag-filter');
            data.forEach(tag => {
                const option = document.createElement('option');
                option.value = tag.tag_id;
                option.textContent = `${tag.tag_id} (${tag.name})`;
                tagFilter.appendChild(option);
            });
        })
        .catch(error => console.error('Error loading tags:', error));
}

function loadItems() {
    fetch('/api/items')
        .then(response => response.json())
        .then(data => {
            const itemFilter = document.getElementById('item-filter');
            data.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = `${item.name} (${item.tag_id})`;
                itemFilter.appendChild(option);
            });
        })
        .catch(error => console.error('Error loading items:', error));
}

function loadStaffs() {
    fetch('/api/staffs')
        .then(response => response.json())
        .then(data => {
            const staffFilter = document.getElementById('staff-filter');
            data.forEach(staff => {
                const option = document.createElement('option');
                option.value = staff.id;
                option.textContent = `${staff.name} (${staff.tag_id})`;
                staffFilter.appendChild(option);
            });
        })
        .catch(error => console.error('Error loading staffs:', error));
}

function loadLogs() {
    const scannerId = document.getElementById('scanner-filter').value;
    const tagId = document.getElementById('tag-filter').value;
    const staffId = document.getElementById('staff-filter').value;
    const dateFrom = document.getElementById('date-from').value;
    const dateTo = document.getElementById('date-to').value;
    
    // Build query string
    let queryParams = [];
    if (scannerId) queryParams.push(`scanner_id=${scannerId}`);
    if (tagId) queryParams.push(`tag_id=${tagId}`);
    if (staffId) queryParams.push(`staff_id=${staffId}`);
    if (dateFrom) queryParams.push(`date_from=${dateFrom}`);
    if (dateTo) queryParams.push(`date_to=${dateTo}`);
    queryParams.push(`page=${currentPage}`);
    queryParams.push(`limit=${logsPerPage}`);
    
    const queryString = queryParams.length ? `?${queryParams.join('&')}` : '';
    
    fetch(`/api/logs${queryString}`)
        .then(response => response.json())
        .then(data => {
            const tableBody = document.querySelector('#logs-table tbody');
            tableBody.innerHTML = '';
            console.log(data);
            if (data.logs && data.logs.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = `<td colspan="6" style="text-align: center;">No logs found</td>`;
                tableBody.appendChild(row);
                return;
            }
            
            // Update pagination info
            if (data.totalPages) {
                totalPages = data.totalPages;
                updatePagination();
            }
            
            data.logs.forEach(log => {
                const row = document.createElement('tr');
                row.innerHTML = `                    
                    <td>${log.staff_name || 'No staff assigned'}</td>
                    <td><span class="badge tag">${log.scanner_name}</span></td>
                    <td><small class="badge in">${log.tag_name}</small> <small class="badge out">${log.tag_id}</small></td>
                    <td>${new Date(log.date_time).toLocaleString()}</td>
                    <td>${(log.date_time_out) ? new Date(log.date_time_out).toLocaleString() : ''}</td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => console.error('Error loading logs:', error));
}

function updatePagination() {
    const prevButton = document.getElementById('prev-page');
    const nextButton = document.getElementById('next-page');
    const pageInfo = document.getElementById('page-info');
    
    pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;
    
    prevButton.disabled = currentPage <= 1;
    nextButton.disabled = currentPage >= totalPages;
}

function startRealTimeUpdates() {
    setInterval(() => {
        loadLogs();
    }, 3000); // Update every 3 seconds
}

function exportLogsToCSV() {
    // Get current filter values
    const scannerId = document.getElementById('scanner-filter').value;
    const tagId = document.getElementById('tag-filter').value;
    const staffId = document.getElementById('staff-filter').value;
    const type = document.getElementById('type-filter').value;
    const dateFrom = document.getElementById('date-from').value;
    const dateTo = document.getElementById('date-to').value;
    
    // Build query string
    let queryParams = [];
    if (scannerId) queryParams.push(`scanner_id=${scannerId}`);
    if (tagId) queryParams.push(`tag_id=${tagId}`);
    if (staffId) queryParams.push(`staff_id=${staffId}`);
    if (type) queryParams.push(`type=${type}`);
    if (dateFrom) queryParams.push(`date_from=${dateFrom}`);
    if (dateTo) queryParams.push(`date_to=${dateTo}`);
    queryParams.push(`export=csv`);
    
    const queryString = queryParams.length ? `?${queryParams.join('&')}` : '';
    
    window.open(`/api/logs${queryString}`, '_blank');
}