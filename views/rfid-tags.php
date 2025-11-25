<div class="container">
    <main>
        <section class="form-section" id="form-section">            
            <form id="tag-form">
                <h2 id="form-title">Add New RFID Tag</h2>
                <input type="hidden" id="tag-id">
                <div class="form-group">
                    <label for="tag-uid">Tag UID:</label>
                    <input type="text" id="tag-uid" required>
                </div>
                <div class="form-group">
                    <label for="tag-name">Name:</label>
                    <input type="text" id="tag-name" required>
                </div>
                <div class="form-group">
                    <label for="tag-description">Description:</label>
                    <textarea id="tag-description" rows="3"></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" id="save-btn">Add Tag</button>
                    <button type="button" class="btn-cancel" id="cancel-btn">Cancel</button>
                </div>
            </form>
        </section>
        
        <section class="table-section">
            <h2>RFID Tags</h2>
            <div class="table-top">
                <div class="search-box">
                    <input type="text" id="search-tags" placeholder="Search tags...">
                </div>
                <button class="add-button" id="add-new-btn">
                    <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M8 0a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2H9v6a1 1 0 1 1-2 0V9H1a1 1 0 0 1 0-2h6V1a1 1 0 0 1 1-1z"/>
                    </svg>
                    Add New Tag
                </button>
            </div>
            
            <div class="table-container">
                <table id="tags-table">
                    <thead>
                        <tr>
                            <th>Tag UID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Date Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Tags will be populated here -->
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
<script src="assets/js/rfid-tags.js"></script>