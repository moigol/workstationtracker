<div class="container">
    <main>
        <section class="form-section" id="form-section">
            <form id="scanner-form">
                <h2 id="form-title">Add New Scanner</h2>
                <input type="hidden" id="scanner-id">
                <div class="form-group">
                    <label for="scanner-name">Name:</label>
                    <input type="text" id="scanner-name" required>
                </div>
                <div class="form-group">
                    <label for="scanner-description">Description:</label>
                    <textarea id="scanner-description" rows="3"></textarea>
                </div>
                <div class="form-actions">
                    <button type="submit" id="save-btn">Add Scanner</button>
                    <button type="button" class="btn-cancel" id="cancel-btn">Cancel</button>
                </div>
            </form>
        </section>

        <section class="table-section">
            <h2>Location Scanners</h2>

            <div class="table-top">
                <div class="search-box">
                    <input type="text" id="search-scanners" placeholder="Search scanners...">
                </div>
                <button class="add-button" id="add-new-btn">
                    <svg width="12" height="12" viewBox="0 0 16 16" fill="currentColor">
                        <path d="M8 0a1 1 0 0 1 1 1v6h6a1 1 0 1 1 0 2H9v6a1 1 0 1 1-2 0V9H1a1 1 0 0 1 0-2h6V1a1 1 0 0 1 1-1z"/>
                    </svg>
                    Add New Location Scanner
                </button>
            </div>

            <div class="table-container">
                <table id="scanners-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Description</th>
                            <th>Date Added</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Scanners will be populated here -->
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</div>
<script src="assets/js/scanners.js"></script>