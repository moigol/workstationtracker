document.addEventListener('DOMContentLoaded', function() {
    const tagForm = document.getElementById('tag-form');
    const cancelBtn = document.getElementById('cancel-btn');
    const saveBtn = document.getElementById('save-btn');
    const formTitle = document.getElementById('form-title');
    const tagIdInput = document.getElementById('tag-id');
    const addNewBtn = document.getElementById('add-new-btn');
    const formSection = document.getElementById('form-section');
    
    let editingId = null;
    
    // Load tags when page loads
    loadTags();
    
    // Add event listener for form submission
    tagForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const tagUid = document.getElementById('tag-uid').value;
        const name = document.getElementById('tag-name').value;
        const description = document.getElementById('tag-description').value;
        editingId = document.getElementById('tag-id').value;
        if (editingId) {
            updateTag(editingId, tagUid, name, description);
        } else {
            addTag(tagUid, name, description);
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
    document.getElementById('search-tags').addEventListener('input', function(e) {
        filterTableList(e.target.value,"#tags-table","RFID tags");
    });

    setInterval(checkTag, 2000); // check every 2s
});

function loadTags() {
    fetch('/api/rfid-tags')
        .then(response => response.json())
        .then(data => {
            const tableBody = document.querySelector('#tags-table tbody');
            tableBody.innerHTML = '';
            
            data.forEach(tag => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${tag.tag_id}</td>
                    <td>${tag.name}</td>
                    <td>${tag.description || '-'}</td>
                    <td>${new Date(tag.date_added).toLocaleDateString()}</td>
                    <td class="action-buttons">
                        <button class="btn btn-edit" onclick="editTag(${tag.id}, '${tag.tag_id}', '${tag.name}', '${tag.description}')">Edit</button>
                        <button class="btn btn-delete" onclick="deleteTag(${tag.id})">Delete</button>
                    </td>
                `;
                tableBody.appendChild(row);
            });
        })
        .catch(error => console.error('Error loading tags:', error));
}

function addTag(tagUid, name, description) {
    fetch('/api/rfid-tags', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            tag_id: tagUid,
            name: name,
            description: description
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resetForm();
            loadTags();
            showNotification('Tag added successfully!', 'success');
        } else {
            showNotification('Error adding tag: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error adding tag:', error);
        showNotification('Error adding tag', 'error');
    });
}

function updateTag(id, tagUid, name, description) {
    fetch(`/api/rfid-tags/${id}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            tag_id: tagUid,
            name: name,
            description: description
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resetForm();
            loadTags();
            showNotification('Tag updated successfully!', 'success');
        } else {
            showNotification('Error updating tag: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error updating tag:', error);
        showNotification('Error updating tag', 'error');
    });
}

function deleteTag(id) {
    if (confirm('Are you sure you want to delete this tag?')) {
        fetch(`/api/rfid-tags/${id}`, {
            method: 'DELETE'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                loadTags();
                showNotification('Tag deleted successfully!', 'success');
            } else {
                showNotification('Error deleting tag: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error deleting tag:', error);
            showNotification('Error deleting tag', 'error');
        });
    }
}

function editTag(id, tagUid, name, description) {
    document.getElementById('tag-id').value = id;
    document.getElementById('tag-uid').value = tagUid;
    document.getElementById('tag-name').value = name;
    document.getElementById('tag-description').value = description;
    
    editingId = id;
    document.getElementById('form-title').textContent = 'Edit RFID Tag';
    document.getElementById('save-btn').textContent = 'Update Tag';

    document.getElementById('form-section').classList.add('show');
}

function resetForm() {
    document.getElementById('tag-form').reset();
    document.getElementById('tag-id').value = '';
    editingId = null;
    document.getElementById('form-title').textContent = 'Add New RFID Tag';
    document.getElementById('save-btn').textContent = 'Add Tag';

    document.getElementById('form-section').classList.remove('show');
}

let lastTagId = null;

async function checkTag() {
    let response = await fetch('/api/unreg-tags');
    let data = await response.json();

    if (data.tag_id && data.tag_id !== lastTagId) {
        lastTagId = data.tag_id;
        
        let input = document.getElementById("tag-uid");

        if (document.activeElement === input) {
            input.value = data.tag_id;
        }
    }
}