document.addEventListener('DOMContentLoaded', function() {
    const scannerForm = document.getElementById('scanner-form');
    const cancelBtn = document.getElementById('cancel-btn');
    const saveBtn = document.getElementById('save-btn');
    const formTitle = document.getElementById('form-title');
    const scannerIdInput = document.getElementById('scanner-id');
    const addNewBtn = document.getElementById('add-new-btn');
    const formSection = document.getElementById('form-section');
    
    let editingId = null;
    
    // Load scanners when page loads
    loadScanners();
    
    // Add event listener for form submission
    scannerForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const name = document.getElementById('scanner-name').value;
        const description = document.getElementById('scanner-description').value;
        editingId = document.getElementById('scanner-id').value;
        if (editingId) {
            updateScanner(editingId, name, description);
        } else {
            addScanner(name, description);
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
    document.getElementById('search-scanners').addEventListener('input', function(e) {
        filterTableList(e.target.value,"#scanners-table","scanners");
    });
});

function loadScanners() {
    fetch('/api/scanners')
        .then(response => response.json())
        .then(data => {
            const tableBody = document.querySelector('#scanners-table tbody');
            tableBody.innerHTML = '';
            
            if (data.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = `<td colspan="5" style="text-align: center;">No scanners found</td>`;
                tableBody.appendChild(row);
                return;
            }
            
            data.forEach(scanner => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${scanner.id}</td>
                    <td>${scanner.name}</td>
                    <td>${scanner.description || '-'}</td>
                    <td>${new Date(scanner.date_added).toLocaleDateString()}</td>
                    <td class="action-buttons">
                        <button class="btn btn-edit" onclick="editScanner('${scanner.id}', '${scanner.name.replace(/'/g, "\\'")}', '${scanner.description ? scanner.description.replace(/'/g, "\\'") : ''}')">Edit</button>
                        <button class="btn btn-delete" onclick="deleteScanner('${scanner.id}')">Delete</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Error loading scanners:', error);
            showNotification('Error loading scanners', 'error');
        });
}

function addScanner(name, description) {
    fetch('/api/scanners', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            name: name,
            description: description
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resetForm();
            loadScanners();
            showNotification('Scanner added successfully!', 'success');
        } else {
            showNotification('Error adding scanner: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error adding scanner:', error);
        showNotification('Error adding scanner', 'error');
    });
}

function updateScanner(id, name, description) {
    fetch(`/api/scanners/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            name: name,
            description: description
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resetForm();
            loadScanners();
            showNotification('Scanner updated successfully!', 'success');
        } else {
            showNotification('Error updating scanner: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error updating scanner:', error);
        showNotification('Error updating scanner', 'error');
    });
}

function deleteScanner(id) {
    if (confirm('Are you sure you want to delete this scanner? This action cannot be undone.')) {
        fetch(`/api/scanners/${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadScanners();
                showNotification('Scanner deleted successfully!', 'success');
            } else {
                showNotification('Error deleting scanner: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting scanner:', error);
            showNotification('Error deleting scanner', 'error');
        });
    }
}

function editScanner(id, name, description) {
    document.getElementById('scanner-id').value = id;
    document.getElementById('scanner-name').value = name;
    document.getElementById('scanner-description').value = description;
    
    editingId = id;
    document.getElementById('form-title').textContent = 'Edit Scanner';
    document.getElementById('save-btn').textContent = 'Update Scanner';

    document.getElementById('form-section').classList.add('show');
}

function resetForm() {
    document.getElementById('scanner-form').reset();
    document.getElementById('scanner-id').value = '';
    editingId = null;
    document.getElementById('form-title').textContent = 'Add New Scanner';
    document.getElementById('save-btn').textContent = 'Add Scanner';
    
    document.getElementById('form-section').classList.remove('show');
}