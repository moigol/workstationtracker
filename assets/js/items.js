document.addEventListener('DOMContentLoaded', function() {
    const itemForm = document.getElementById('item-form');
    const cancelBtn = document.getElementById('cancel-btn');
    const saveBtn = document.getElementById('save-btn');
    const formTitle = document.getElementById('form-title');
    const itemIdInput = document.getElementById('item-id');
    const addNewBtn = document.getElementById('add-new-btn');
    const formSection = document.getElementById('form-section');
    
    let editingId = null;
    
    // Load items and tags when page loads
    loadItems();
    loadAvailableTags();
    
    // Add event listener for form submission
    itemForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const name = document.getElementById('item-name').value;
        const description = document.getElementById('item-description').value;
        const tagId = document.getElementById('item-tag').value;
        editingId = document.getElementById('item-id').value;
        if (editingId) {
            updateItem(editingId, name, description, tagId);
        } else {
            addItem(name, description, tagId);
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
    document.getElementById('search-items').addEventListener('input', function(e) {
        filterTableList(e.target.value,"#items-table","items");
    });
});

function loadItems() {
    fetch('/api/items')
        .then(response => response.json())
        .then(data => {
            const tableBody = document.querySelector('#items-table tbody');
            tableBody.innerHTML = '';
            
            if (data.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = `<td colspan="6" style="text-align: center;">No items found</td>`;
                tableBody.appendChild(row);
                return;
            }
            
            data.forEach(item => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${item.id}</td>
                    <td>${item.name}</td>
                    <td>${item.description || '-'}</td>
                    <td>${item.tag_id ? `${item.tag_id}${item.tag_name ? ` (${item.tag_name})` : ''}` : 'Not assigned'}</td>
                    <td>${new Date(item.date_added).toLocaleDateString()}</td>
                    <td class="action-buttons">
                        <button class="btn btn-edit" onclick="editItem(${item.id}, '${item.name.replace(/'/g, "\\'")}', '${item.description ? item.description.replace(/'/g, "\\'") : ''}', '${item.tag_id ? item.tag_id.replace(/'/g, "\\'") : ''}')">Edit</button>
                        <button class="btn btn-delete" onclick="deleteItem(${item.id})">Delete</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => {
            console.error('Error loading items:', error);
            showNotification('Error loading items', 'error');
        });
}

function loadAvailableTags() {
    fetch('/api/rfid-tags')
        .then(response => response.json())
        .then(data => {
            const tagSelect = document.getElementById('item-tag');
            // Clear existing options except the first one
            while (tagSelect.options.length > 1) {
                tagSelect.remove(1);
            }
            
            data.forEach(tag => {
                var tagname = (tag.item_name) ? "(Assigned to item "+ tag.item_name +")" : '';
                const option = document.createElement('option');
                option.value = tag.tag_id;
                option.textContent = `${tag.tag_id} - ${tag.name} ${tagname}`;

                if (tag.item_name) {
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

function addItem(name, description, tagId) {
    fetch('/api/items', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            name: name,
            description: description,
            tag_id: tagId || null
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resetForm();
            loadItems();
            loadAvailableTags();
            showNotification('Item added successfully!', 'success');
        } else {
            showNotification('Error adding item: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error adding item:', error);
        showNotification('Error adding item', 'error');
    });
}

function updateItem(id, name, description, tagId) {
    fetch(`/api/items/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            name: name,
            description: description,
            tag_id: tagId || null
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resetForm();
            loadItems();
            loadAvailableTags();
            showNotification('Item updated successfully!', 'success');
        } else {
            showNotification('Error updating item: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error updating item:', error);
        showNotification('Error updating item', 'error');
    });
}

function deleteItem(id) {
    if (confirm('Are you sure you want to delete this item?')) {
        fetch(`/api/items/${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadItems();
                showNotification('Item deleted successfully!', 'success');
            } else {
                showNotification('Error deleting item: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting item:', error);
            showNotification('Error deleting item', 'error');
        });
    }
}

function editItem(id, name, description, tagId) {
    document.getElementById('item-id').value = id;
    document.getElementById('item-name').value = name;
    document.getElementById('item-description').value = description;
    
    // Set the tag selection
    const tagSelect = document.getElementById('item-tag');
    if (tagId) {
        tagSelect.value = tagId;
    } else {
        tagSelect.value = '';
    }
    
    editingId = id;
    document.getElementById('form-title').textContent = 'Edit Item';
    document.getElementById('save-btn').textContent = 'Update Item';

    document.getElementById('form-section').classList.add('show');
}

function resetForm() {
    document.getElementById('item-form').reset();
    document.getElementById('item-id').value = '';
    editingId = null;
    document.getElementById('form-title').textContent = 'Add New Item';
    document.getElementById('save-btn').textContent = 'Add Item';
    
    document.getElementById('form-section').classList.remove('show');
}