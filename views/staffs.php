<div class="container">
    <main>
        <section class="form-section" id="form-section">
            
            <form id="staff-form">
                <h2 id="form-title">Add New Staff</h2>
                <input type="hidden" id="staff-id">
                <div class="form-group">
                    <label for="staff-tag">RFID Tag:</label>
                    <select id="staff-tag">
                        <option value="">-- Select RFID Tag Id --</option>
                        <!-- Tags will be populated here -->
                    </select>
                </div>
                <div class="form-group">
                    <label for="staff-avatar">Avatar:</label>
                    <input type="text" id="staff-avatar" required>
                </div>
                <div class="form-group">
                    <label for="staff-name">Name:</label>
                    <input type="text" id="staff-name" required>
                </div>
                <div class="form-group">
                    <label for="staff-position">Position:</label>
                    <textarea id="staff-position" rows="3"></textarea>
                </div>
                <div class="form-group">
                    <label for="staff-tag">Allowed Station:</label>
                    <div class="checkbox-group" id="stations-checkbox-group">
                        <!-- Checkboxes will be populated here -->
                        <div class="loading-stations">Loading stations...</div>
                    </div>
                </div>
                <div class="form-actions">
                    <button type="submit" id="save-btn">Add Staff</button>
                    <button type="button" class="btn-cancel" id="cancel-btn">Cancel</button>
                </div>
            </form>
        </section>

        <section class="table-section">
            <h2>Staffs</h2>
            
            <div class="table-top">
                <div class="search-box">
                    <input type="text" id="search-staffs" placeholder="Search staffs...">
                </div>
                <button class="add-button" id="add-new-btn">
                    <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M8 0a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2H9v6a1 1 0 1 1-2 0V9H1a1 1 0 0 1 0-2h6V1a1 1 0 0 1 1-1z"/>
                    </svg>
                    Add New Staff
                </button>
            </div>
            <div class="table-container">
                <table id="staffs-table">
                    <thead>
                        <tr>
                            <th></th>
                            <th>RFID Tag</th>
                            <th>Name</th>
                            <th>Position</th>                            
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