<div class="container">
    <main>
        <section class="filters">
            <div class="filter-group">
                <label for="date-from">Date From:</label>
                <input type="date" id="date-from">
            </div>
            <div class="filter-group">
                <label for="date-to">Date To:</label>
                <input type="date" id="date-to">
            </div>
            <div class="filter-group">
                <label for="scanner-filter">Station:</label>
                <select id="scanner-filter">
                    <option value="">All Stations</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="tag-filter">RFID Tag:</label>
                <select id="tag-filter">
                    <option value="">All Tags</option>
                </select>
            </div>
            <div class="filter-group">
                <label for="staff-filter">Staff:</label>
                <select id="staff-filter">
                    <option value="">All Staffs</option>
                </select>
            </div>
            
            <button id="apply-filters">Apply Filters</button>
            <button id="reset-filters">Reset Filters</button>
        </section>

        <section class="logs">
            <h2>Recent Logs</h2>
            <div class="table-container">
                <table id="logs-table">
                    <thead>
                        <tr>
                            <th>Staff</th>
                            <th>Station</th>                           
                            <th>Tag ID</th>
                            <th>Date & Time In</th>
                            <th>Date & Time Out</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Logs will be populated here -->
                    </tbody>
                </table>
            </div>
            <div class="pagination">
                <button id="prev-page" disabled>Previous</button>
                <span id="page-info">Page 1 of 1</span>
                <button id="next-page" disabled>Next</button>
            </div>
        </section>
    </main>
</div>

<script src="assets/js/dashboard.js"></script>