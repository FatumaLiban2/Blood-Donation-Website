document.addEventListener('DOMContentLoaded', function () {
    // Tab buttons
    const adminsBtn = document.getElementById('adminsTable');
    const patientsBtn = document.getElementById('patientsTable');
    const reloadBtn = document.getElementById('reloadData');

    // Sections
    const adminsSection = document.getElementById('admins-section');
    const patientsSection = document.getElementById('patients-section');

    // Containers
    const adminsContainer = document.getElementById('adminsContainer');
    const patientsContainer = document.getElementById('patientsContainer');

    // Active view tracking
    let currentView = 'admins';
    let cachedData = null;

    // Tab switching
    function switchToAdmins() {
        currentView = 'admins';
        adminsSection.classList.remove('hidden');
        patientsSection.classList.add('hidden');
        adminsBtn.classList.add('active');
        patientsBtn.classList.remove('active');
    }

    function switchToPatients() {
        currentView = 'patients';
        patientsSection.classList.remove('hidden');
        adminsSection.classList.add('hidden');
        patientsBtn.classList.add('active');
        adminsBtn.classList.remove('active');
    }

    adminsBtn.addEventListener('click', switchToAdmins);
    patientsBtn.addEventListener('click', switchToPatients);
    reloadBtn.addEventListener('click', () => fetchData(true));

    // Create table from data
    function createTable(items, columns) {
        if (!items || items.length === 0) {
            return '<div class="empty">No data found.</div>';
        }

        let html = '<table class="data-table"><thead><tr>';
        
        // Headers
        columns.forEach(col => {
            html += `<th>${col.label}</th>`;
        });
        html += '</tr></thead><tbody>';

        // Rows
        items.forEach(item => {
            html += '<tr>';
            columns.forEach(col => {
                let value = item[col.key];
                
                // Format boolean values
                if (col.key === 'is_verified') {
                    value = value ? 'Yes' : 'No';
                }
                
                html += `<td>${value ?? ''}</td>`;
            });
            html += '</tr>';
        });

        html += '</tbody></table>';
        return html;
    }

    // Fetch data from server
    async function fetchData(forceReload = false) {
        // Use cached data if available and not forcing reload
        if (cachedData && !forceReload) {
            renderData(cachedData);
            return;
        }

        // Show loading state
        adminsContainer.innerHTML = '<div class="loading">Loading admins...</div>';
        patientsContainer.innerHTML = '<div class="loading">Loading patients...</div>';
        reloadBtn.disabled = true;

        try {
            const response = await fetch('/handlers/fetchData.php', {
                credentials: 'same-origin',
                method: 'GET',
            });

            // Handle auth errors
            if (response.status === 401 || response.status === 403) {
                const errorMsg = '<div class="error">You are not authorized to view this data. Please log in as an admin.</div>';
                adminsContainer.innerHTML = errorMsg;
                patientsContainer.innerHTML = errorMsg;
                reloadBtn.disabled = false;
                return;
            }

            // Handle other errors
            if (!response.ok) {
                const text = await response.text();
                console.error('Server response:', text);
                throw new Error(`Server error: ${response.status}`);
            }

            // Try to parse JSON, but catch parsing errors and show the actual response
            let data;
            try {
                const text = await response.text();
                data = JSON.parse(text);
            } catch (parseError) {
                const text = await response.text();
                console.error('Response text:', text);
                throw new Error('Server returned invalid JSON. Check console for details.');
            }

            if (data.error) {
                throw new Error(data.error);
            }

            // Cache the data
            cachedData = data;
            renderData(data);

        } catch (error) {

            const errorMsg = `<div class="error">Error loading data: ${error.message}</div>`;
            adminsContainer.innerHTML = errorMsg;
            patientsContainer.innerHTML = errorMsg;
            console.error('Fetch error:', error);
        } finally {
            reloadBtn.disabled = false;
        }
    }

    // Render the fetched data
    function renderData(data) {
        // Define columns for admins
        const adminColumns = [
            { key: 'id', label: 'ID' },
            { key: 'first_name', label: 'First Name' },
            { key: 'last_name', label: 'Last Name' },
            { key: 'email', label: 'Email' },
            { key: 'telephone', label: 'Telephone' }
        ];

        // Define columns for patients
        const patientColumns = [
            { key: 'id', label: 'ID' },
            { key: 'first_name', label: 'First Name' },
            { key: 'last_name', label: 'Last Name' },
            { key: 'email', label: 'Email' },
            { key: 'telephone', label: 'Telephone' },
            { key: 'is_verified', label: 'Verified' }
        ];

        // Render admins table
        adminsContainer.innerHTML = createTable(data.admins || [], adminColumns);
        
        // Render patients table
        patientsContainer.innerHTML = createTable(data.patients || [], patientColumns);
    }

    // Initial data fetch
    fetchData();
});
