<div class="container">
    <main>
        <section class="form-section" id="form-section">
            
            <form id="item-form">
                <h2 id="form-title">Add New Item</h2>
                <input type="hidden" id="item-id">
                <div class="form-group">
                    <label for="item-tag">RFID Tag:</label>
                    <select id="item-tag">
                        <option value="">-- Select RFID Tag Id --</option>
                        <!-- Tags will be populated here -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="item-name">Name:</label>
                    <input type="text" id="item-name" required>
                </div>
                <div class="form-group">
                    <label for="item-description">Description:</label>
                    <textarea id="item-description" rows="3"></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" id="save-btn">Add Item</button>
                    <button type="button" class="btn-cancel" id="cancel-btn">Cancel</button>
                </div>
            </form>
        </section>

        <section class="table-section">
            <h2>Staffs</h2>
            
            <div class="table-top">
                <div class="search-box">
                    <input type="text" id="search-items" placeholder="Search items...">
                </div>
                <button class="add-button" id="add-new-btn">
                    <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M8 0a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2H9v6a1 1 0 1 1-2 0V9H1a1 1 0 0 1 0-2h6V1a1 1 0 0 1 1-1z"/>
                    </svg>
                    Add New Item
                </button>
            </div>
            <div class="table-container">
                <table id="items-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>RFID Tag</th>
                            <th>Date Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Staffs will be populated here -->
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
<script src="assets/js/staffs.js"></script> 