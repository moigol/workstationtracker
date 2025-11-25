document.addEventListener('DOMContentLoaded', function() {
    const staffForm = document.getElementById('staff-form');
    const cancelBtn = document.getElementById('cancel-btn');
    const saveBtn = document.getElementById('save-btn');
    const formTitle = document.getElementById('form-title');
    const staffIdInput = document.getElementById('staff-id');
    const addNewBtn = document.getElementById('add-new-btn');
    const formSection = document.getElementById('form-section');
    
    let editingId = null;
    
    // Load staffs and tags when page loads
    loadStaffs();
    loadAvailableTags();
    loadStations();
    
    // Add event listener for form submission
    staffForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const allowedStations = getSelectedStations();
        const avatar = document.getElementById('staff-avatar').value;
        const name = document.getElementById('staff-name').value;
        const position = document.getElementById('staff-position').value;
        const tagId = document.getElementById('staff-tag').value;
        editingId = document.getElementById('staff-id').value;
        if (editingId) {
            updateStaff(editingId, name, position, tagId, allowedStations, avatar);
        } else {
            addStaff(name, position, tagId, allowedStations, avatar);
        }
    });
    
    addNewBtn.addEventListener('click', function() {
        resetForm();
        formSection.classList.add('show');
    });

    // Cancel edit
    cancelBtn.addEventListener('click', function() {
        resetForm();
    });
    
    // Search functionality
    document.getElementById('search-staffs').addEventListener('input', function(e) {
        filterTableList(e.target.value,"#staffs-table","staffs");
    });
});

function loadStaffs() {
    fetch('/api/staffs')
        .then(response => response.json())
        .then(data => {
            const tableBody = document.querySelector('#staffs-table tbody');
            tableBody.innerHTML = '';
            
            if (data.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = `<td colspan="6" style="text-align: center;">No staffs found</td>`;
                tableBody.appendChild(row);
                return;
            }
            
            data.forEach(staff => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td><img src="/assets/images/avatar/${staff.avatar}" width="50" /></td>
                    <td><small class="badge tag">${staff.id} | ${(staff.tag_id) ? staff.tag_id : 'Unassigned'}</small></td>
                    <td>${staff.name}</td>
                    <td>${staff.position || '-'}</td>                    
                    <td>${new Date(staff.date_added).toLocaleDateString()}</td>
                    <td class="action-buttons">
                        <button class="btn btn-edit" onclick="editStaff(${staff.id}, '${staff.name.replace(/'/g, "\\'")}', '${staff.position ? staff.position.replace(/'/g, "\\'") : ''}', '${staff.tag_id ? staff.tag_id.replace(/'/g, "\\'") : ''}', '${staff.avatar.replace(/'/g, "\\'")}', '${staff.allowed_stations}')">Edit</button>
                        <button class="btn btn-delete" onclick="deleteStaff(${staff.id})">Delete</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Error loading staffs:', error);
            showNotification('Error loading staffs', 'error');
        });
}

function loadAvailableTags() {
    fetch('/api/rfid-tags')
        .then(response => response.json())
        .then(data => {
            const tagSelect = document.getElementById('staff-tag');
            // Clear existing options except the first one
            while (tagSelect.options.length > 1) {
                tagSelect.remove(1);
            }
            
            data.forEach(tag => {
                var tagname = (tag.staff_name) ? "(Assigned to staff "+ tag.staff_name +")" : '';
                const option = document.createElement('option');
                option.value = tag.tag_id;
                option.textContent = `${tag.tag_id} - ${tag.name} ${tagname}`;

                if (tag.staff_name) {
                    option.disabled = true;
                }

                tagSelect.appendChild(option);
            });
        })
        .catch(error => {
            console.error('Error loading tags:', error);
            showNotification('Error loading available tags', 'error');
        });
}

function loadScanners() {
    fetch('/api/scanners')
        .then(response => response.json())
        .then(data => {
            const scannerFilter = document.getElementById('staff-stations');
            data.forEach(scanner => {
                const option = document.createElement('option');
                option.value = scanner.id;
                option.textContent = scanner.name;
                scannerFilter.appendChild(option);
            });
        })
        .catch(error => console.error('Error loading scanners:', error));
}

function loadStations() {
    // Replace with your actual API endpoint for stations
    fetch('/api/scanners')
        .then(response => response.json())
        .then(data => {
            stations = data;
            renderStationCheckboxes();
        })
        .catch(error => {
            console.error('Error loading stations:', error);
            document.getElementById('stations-checkbox-group').innerHTML = 
                '<div class="loading-stations">Error loading stations</div>';
        });
}

function renderStationCheckboxes(selectedStations = []) {
    const container = document.getElementById('stations-checkbox-group');
    
    if (!stations || stations.length === 0) {
        container.innerHTML = '<div class="loading-stations">No stations available</div>';
        return;
    }

    const checkboxesHtml = stations.map(station => `
        <div class="checkbox-item">
            <input type="checkbox" id="station-${station.id}" name="allowed_stations" value="${station.id}" 
                ${selectedStations.includes(station.id) ? 'checked' : ''}>
            <label for="station-${station.id}">${station.name}</label>
        </div>
    `).join('');

    container.innerHTML = checkboxesHtml + `
        <div class="checkbox-actions">
            <button type="button" id="select-all-stations">Select All</button>
            <button type="button" id="deselect-all-stations">Deselect All</button>
        </div>
    `;

    // Add event listeners for select all/deselect all
    document.getElementById('select-all-stations').addEventListener('click', selectAllStations);
    document.getElementById('deselect-all-stations').addEventListener('click', deselectAllStations);
}

function selectAllStations() {
    document.querySelectorAll('input[name="allowed_stations"]').forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAllStations() {
    document.querySelectorAll('input[name="allowed_stations"]').forEach(checkbox => {
        checkbox.checked = false;
    });
}

function getSelectedStations() {
    const selected = [];
    document.querySelectorAll('input[name="allowed_stations"]:checked').forEach(checkbox => {
        selected.push(checkbox.value);
    });
    return selected;
}

function addStaff(name, position, tagId, allowedStations, avatar) {
    fetch('/api/staffs', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            name: name,
            position: position,
            tag_id: tagId || null,
            allowed_stations: allowedStations,
            avatar: avatar
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resetForm();
            loadStaffs();
            loadAvailableTags();
            showNotification('Staff added successfully!', 'success');
        } else {
            showNotification('Error adding staff: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error adding staff:', error);
        showNotification('Error adding staff', 'error');
    });
}

function updateStaff(id, name, position, tagId, allowedStations, avatar) {
    fetch(`/api/staffs/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            name: name,
            position: position,
            tag_id: tagId || null,
            allowed_stations: allowedStations,
            avatar: avatar
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resetForm();
            loadStaffs();
            loadAvailableTags();
            showNotification('Staff updated successfully!', 'success');
        } else {
            showNotification('Error updating staff: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error updating staff:', error);
        showNotification('Error updating staff', 'error');
    });
}

function deleteStaff(id) {
    if (confirm('Are you sure you want to delete this staff?')) {
        fetch(`/api/staffs/${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadStaffs();
                showNotification('Staff deleted successfully!', 'success');
            } else {
                showNotification('Error deleting staff: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting staff:', error);
            showNotification('Error deleting staff', 'error');
        });
    }
}

function editStaff(id, name, position, tagId, avatar, allowed_stations) {
    document.getElementById('staff-id').value = id;
    document.getElementById('staff-avatar').value = avatar;
    document.getElementById('staff-name').value = name;
    document.getElementById('staff-position').value = position;
    
    // Set the tag selection
    const tagSelect = document.getElementById('staff-tag');
    if (tagId) {
        tagSelect.value = tagId;
    } else {
        tagSelect.value = '';
    }

    renderStationCheckboxes(allowed_stations);
    
    editingId = id;
    document.getElementById('form-title').textContent = 'Edit Staff';
    document.getElementById('save-btn').textContent = 'Update Staff';

    document.getElementById('form-section').classList.add('show');
}

function resetForm() {
    document.getElementById('staff-form').reset();
    document.getElementById('staff-id').value = '';
    editingId = null;
    document.getElementById('form-title').textContent = 'Add New Staff';
    document.getElementById('save-btn').textContent = 'Add Staff';
    
    document.getElementById('form-section').classList.remove('show');
}