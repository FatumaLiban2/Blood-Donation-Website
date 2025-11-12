document.addEventListener('DOMContentLoaded', function () {
    // Tab buttons
    const adminsBtn = document.getElementById('adminsTable');
    const patientsBtn = document.getElementById('patientsTable');
    const appointmentsBtn = document.getElementById('appointmentsTable');
    const doneAppointmentsBtn = document.getElementById('doneAppointmentsTable');
    const reloadBtn = document.getElementById('reloadData');

    // Sections
    const adminsSection = document.getElementById('admins-section');
    const patientsSection = document.getElementById('patients-section');
    const appointmentsSection = document.getElementById('appointments-section');
    const doneAppointmentsSection = document.getElementById('done-appointments-section');

    // Containers
    const adminsContainer = document.getElementById('adminsContainer');
    const patientsContainer = document.getElementById('patientsContainer');
    const appointmentsContainer = document.getElementById('appointmentsContainer');
    const doneAppointmentsContainer = document.getElementById('doneAppointmentsContainer');

    // Active view tracking
    let currentView = 'admins';
    let cachedData = null;

    // Tab switching
    function switchToAdmins() {
        currentView = 'admins';
        adminsSection.classList.remove('hidden');
        patientsSection.classList.add('hidden');
        appointmentsSection.classList.add('hidden');
        doneAppointmentsSection.classList.add('hidden');
        adminsBtn.classList.add('active');
        patientsBtn.classList.remove('active');
        appointmentsBtn.classList.remove('active');
        doneAppointmentsBtn.classList.remove('active');
    }

    function switchToPatients() {
        currentView = 'patients';
        patientsSection.classList.remove('hidden');
        adminsSection.classList.add('hidden');
        appointmentsSection.classList.add('hidden');
        doneAppointmentsSection.classList.add('hidden');
        patientsBtn.classList.add('active');
        adminsBtn.classList.remove('active');
        appointmentsBtn.classList.remove('active');
        doneAppointmentsBtn.classList.remove('active');
    }

    function switchToAppointments() {
        currentView = 'appointments';
        appointmentsSection.classList.remove('hidden');
        adminsSection.classList.add('hidden');
        patientsSection.classList.add('hidden');
        doneAppointmentsSection.classList.add('hidden');
        appointmentsBtn.classList.add('active');
        adminsBtn.classList.remove('active');
        patientsBtn.classList.remove('active');
        doneAppointmentsBtn.classList.remove('active');
    }

    function switchToDoneAppointments() {
        currentView = 'done-appointments';
        doneAppointmentsSection.classList.remove('hidden');
        adminsSection.classList.add('hidden');
        patientsSection.classList.add('hidden');
        appointmentsSection.classList.add('hidden');
        doneAppointmentsBtn.classList.add('active');
        adminsBtn.classList.remove('active');
        patientsBtn.classList.remove('active');
        appointmentsBtn.classList.remove('active');
    }

    // Add event listeners
    if (adminsBtn) {
        adminsBtn.addEventListener('click', switchToAdmins);
    }
    
    if (patientsBtn) {
        patientsBtn.addEventListener('click', switchToPatients);
    }
    
    if (appointmentsBtn) {
        appointmentsBtn.addEventListener('click', switchToAppointments);
    }

    if (doneAppointmentsBtn) {
        doneAppointmentsBtn.addEventListener('click', switchToDoneAppointments);
    }
    
    if (reloadBtn) {
        reloadBtn.addEventListener('click', () => fetchData(true));
    }

    // Logout button
    const logoutBtn = document.querySelector('.logout-button');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = '/handlers/logouthandler.php';
            }
        });
    }

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
        appointmentsContainer.innerHTML = '<div class="loading">Loading appointments...</div>';
        doneAppointmentsContainer.innerHTML = '<div class="loading">Loading completed appointments...</div>';
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
                appointmentsContainer.innerHTML = errorMsg;
                doneAppointmentsContainer.innerHTML = errorMsg;
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
            appointmentsContainer.innerHTML = errorMsg;
            doneAppointmentsContainer.innerHTML = errorMsg;
            console.error('Fetch error:', error);
        } finally {
            reloadBtn.disabled = false;
        }
    }

    // Confirm appointment completion
    async function confirmAppointment(appointmentId, button) {
        if (!confirm('Are you sure you want to mark this appointment as completed?')) {
            return;
        }

        button.disabled = true;
        button.textContent = 'Confirming...';

        try {
            const response = await fetch('/handlers/confirmAppointmentHandler.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ appointment_id: appointmentId })
            });

            const result = await response.json();

            if (result.success) {
                button.textContent = 'Completed';
                button.classList.add('completed');
                // Reload data to reflect changes
                await fetchData(true);
            } else {
                alert('Error: ' + (result.error || 'Failed to confirm appointment'));
                button.disabled = false;
                button.textContent = 'Confirm';
            }
        } catch (error) {
            console.error('Confirm error:', error);
            alert('Error confirming appointment');
            button.disabled = false;
            button.textContent = 'Confirm';
        }
    }

    // Create appointments table with confirm buttons (pending only)
    function createAppointmentsTable(appointments) {
        if (!appointments || appointments.length === 0) {
            return '<div class="empty">No pending appointments found.</div>';
        }

        let html = '<table class="data-table"><thead><tr>';
        html += '<th>ID</th><th>Patient Name</th><th>Type</th><th>Blood Group</th>';
        html += '<th>Date</th><th>Time</th><th>Notes</th><th>Action</th>';
        html += '</tr></thead><tbody>';

        appointments.forEach(apt => {
            // Format date
            const date = new Date(apt.schedule_day).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });

            // Format time
            const time = apt.schedule_time ? apt.schedule_time.substring(0, 5) : '';

            html += '<tr>';
            html += `<td>${apt.appointments_id}</td>`;
            html += `<td>${apt.full_name}</td>`;
            html += `<td>${apt.appointment_type}</td>`;
            html += `<td>${apt.blood_group}</td>`;
            html += `<td>${date}</td>`;
            html += `<td>${time}</td>`;
            html += `<td>${apt.additional_notes || '-'}</td>`;
            html += `<td><button class="confirm-btn" data-id="${apt.appointments_id}">Confirm</button></td>`;
            html += '</tr>';
        });

        html += '</tbody></table>';
        return html;
    }

    // Create done appointments table (completed only)
    function createDoneAppointmentsTable(appointments) {
        if (!appointments || appointments.length === 0) {
            return '<div class="empty">No completed appointments found.</div>';
        }

        let html = '<table class="data-table"><thead><tr>';
        html += '<th>ID</th><th>Patient Name</th><th>Type</th><th>Blood Group</th>';
        html += '<th>Date</th><th>Time</th><th>Notes</th><th>Status</th>';
        html += '</tr></thead><tbody>';

        appointments.forEach(apt => {
            // Format date
            const date = new Date(apt.schedule_day).toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });

            // Format time
            const time = apt.schedule_time ? apt.schedule_time.substring(0, 5) : '';

            html += '<tr>';
            html += `<td>${apt.appointments_id}</td>`;
            html += `<td>${apt.full_name}</td>`;
            html += `<td>${apt.appointment_type}</td>`;
            html += `<td>${apt.blood_group}</td>`;
            html += `<td>${date}</td>`;
            html += `<td>${time}</td>`;
            html += `<td>${apt.additional_notes || '-'}</td>`;
            html += `<td><span class="status-badge status-completed">Completed</span></td>`;
            html += '</tr>';
        });

        html += '</tbody></table>';
        return html;
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

        // Render pending appointments table
        appointmentsContainer.innerHTML = createAppointmentsTable(data.appointments || []);

        // Render completed appointments table
        doneAppointmentsContainer.innerHTML = createDoneAppointmentsTable(data.completedAppointments || []);

        // Add event listeners to confirm buttons
        document.querySelectorAll('.confirm-btn:not(.completed)').forEach(btn => {
            btn.addEventListener('click', function() {
                const appointmentId = this.getAttribute('data-id');
                confirmAppointment(appointmentId, this);
            });
        });
    }

    // Initial data fetch
    fetchData();
});
