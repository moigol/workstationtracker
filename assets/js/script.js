class MenuActiveHighlighter {
    constructor(menuSelector = '.menu-item') {
        this.menuSelector = menuSelector;
        this.init();
    }
    
    init() {
        this.setActiveItem();
        this.bindEvents();
    }
    
    getCurrentPath() {
        const path = window.location.pathname;
        return path === '/' ? '' : path;
    }
    
    setActiveItem() {
        try {
            const currentPath = this.getCurrentPath();
            const menuItems = document.querySelectorAll(this.menuSelector);
            
            // Reset all items
            menuItems.forEach(item => item.classList.remove('active'));
            
            // Find matching item
            const activeItem = Array.from(menuItems).find(item => {
                try {
                    const itemUrl = new URL(item.href);
                    const itemPath = itemUrl.pathname === '/' ? '' : itemUrl.pathname;
                    return itemPath === currentPath;
                } catch (error) {
                    console.warn('Error parsing URL:', error);
                    return false;
                }
            });
            
            if (activeItem) {
                activeItem.classList.add('active');
            }
            
        } catch (error) {
            console.error('Error setting active menu item:', error);
        }
    }
    
    bindEvents() {
        // Re-run when navigation occurs
        window.addEventListener('popstate', () => this.setActiveItem());
    }
}

// Usage
document.addEventListener('DOMContentLoaded', function() {
    new MenuActiveHighlighter();
});

function filterTableList(searchTerm, id, msg) {
    const rows = document.querySelectorAll(id+' tbody tr');
    let hasResults = false;
    
    rows.forEach(row => {
        // Skip the "no results" row
        if (row.cells.length === 1) {
            row.style.display = 'none';
            return;
        }
        
        const text = row.textContent.toLowerCase();
        if (text.includes(searchTerm.toLowerCase())) {
            row.style.display = '';
            hasResults = true;
        } else {
            row.style.display = 'none';
        }
    });
    
    // Show "no results" message if no matches
    if (!hasResults && rows.length > 0) {
        const noResultsRow = document.createElement('tr');
        noResultsRow.innerHTML = `<td colspan="100" style="text-align: center;">No ${msg} that matches your search criteria.</td>`;
        noResultsRow.id = 'no-results-row';
        document.querySelector(id+' tbody').appendChild(noResultsRow);
    } else {
        const noResultsRow = document.getElementById('no-results-row');
        if (noResultsRow) {
            noResultsRow.remove();
        }
    }
}

function showNotification(message, type) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
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
    } else {
        notification.style.backgroundColor = '#e74c3c';
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
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}