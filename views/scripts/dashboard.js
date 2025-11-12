document.addEventListener('DOMContentLoaded', function() {
    console.log('Dashboard script loaded');
    
    // Navigation
    const navLinks = document.querySelectorAll('.nav-link');
    const sections = document.querySelectorAll('.dashboard-section');
    
    console.log('Found nav links:', navLinks.length);
    console.log('Found sections:', sections.length);
    
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Clicked:', this.getAttribute('data-section'));
            
            // Remove active class from all links
            navLinks.forEach(l => l.classList.remove('active'));
            
            // Add active class to clicked link
            this.classList.add('active');
            
            // Hide all sections
            sections.forEach(section => {
                section.classList.add('hidden');
                console.log('Hiding section:', section.id);
            });
            
            // Show selected section
            const sectionId = this.getAttribute('data-section') + '-section';
            const targetSection = document.getElementById(sectionId);
            console.log('Showing section:', sectionId, targetSection);
            if (targetSection) {
                targetSection.classList.remove('hidden');
            }
        });
    });
    
    // Logout functionality
    const logoutBtn = document.getElementById('logoutBtn');
    if (logoutBtn) {
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to logout?')) {
                window.location.href = '../handlers/logouthandler.php';
            }
        });
    }
    
    // Load dashboard data
    loadDashboardData();
    
    // Schedule form submission
    const scheduleForm = document.getElementById('scheduleForm');
    if (scheduleForm) {
        scheduleForm.addEventListener('submit', handleScheduleSubmit);
    }
    
    // Set minimum date for appointment to today
    const appointmentDateInput = document.getElementById('appointmentDate');
    if (appointmentDateInput) {
        const today = new Date().toISOString().split('T')[0];
        appointmentDateInput.setAttribute('min', today);
    }
});

// Load dashboard data
function loadDashboardData() {
    console.log('Loading dashboard data...');
    fetch('../handlers/fetchdataPatient.php')
        .then(response => {
            console.log('Response status:', response.status);
            return response.json();
        })
        .then(data => {
            console.log('Received data:', data);
            if (data.success) {
                console.log('Appointments:', data.appointments);
                updateStats(data.stats);
                displayRecentAppointments(data.recentAppointments);
                displayAllAppointments(data.appointments);
                displayDonationHistory(data.history);
                updateProfile(data.profile);
            } else {
                console.error('Error loading dashboard data:', data.message);
                showEmptyState();
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
            showEmptyState();
        });
}

// Update statistics
function updateStats(stats) {
    if (!stats) return;
    
    document.getElementById('totalDonations').textContent = stats.totalDonations || 9;
    document.getElementById('upcomingCount').textContent = stats.upcomingCount || 2;
    document.getElementById('lastDonation').textContent = stats.lastDonation || '-';
    document.getElementById('nextEligible').textContent = stats.nextEligible || '-';
}

// Display recent appointments
function displayRecentAppointments(appointments) {
    const container = document.getElementById('recentAppointments');
    
    if (!appointments || appointments.length === 0) {
        container.innerHTML = '<div class="empty-state"><p>No recent appointments</p></div>';
        return;
    }
    
    container.innerHTML = appointments.map(apt => `
        <div class="appointment-item">
            <h4>Appointment on ${formatDate(apt.appointment_date)}</h4>
            <p><strong>Time:</strong> ${apt.appointment_time || '-'}</p>
            <p><strong>Type:</strong> ${apt.appointment_type || '-'}</p>
            <p><strong>Blood Group:</strong> ${apt.blood_group || apt.blood_type || '-'}</p>
            ${apt.notes ? `<p><strong>Notes:</strong> ${apt.notes}</p>` : ''}
            <span class="appointment-status status-${apt.status}">${capitalizeFirst(apt.status)}</span>
        </div>
    `).join('');
}

// Display all appointments
function displayAllAppointments(appointments) {
    const container = document.getElementById('appointmentsContainer');
    
    if (!appointments || appointments.length === 0) {
        container.innerHTML = '<div class="empty-state"><p>No appointments found</p></div>';
        return;
    }
    
    container.innerHTML = appointments.map(apt => `
        <div class="appointment-item">
            <h4>Appointment on ${formatDate(apt.appointment_date)}</h4>
            <p><strong>Time:</strong> ${apt.appointment_time || '-'}</p>
            <p><strong>Type:</strong> ${apt.appointment_type || '-'}</p>
            <p><strong>Blood Group:</strong> ${apt.blood_group || apt.blood_type || '-'}</p>
            ${apt.notes ? `<p><strong>Notes:</strong> ${apt.notes}</p>` : ''}
            <span class="appointment-status status-${apt.status}">${capitalizeFirst(apt.status)}</span>
        </div>
    `).join('');
}

// Display donation history
function displayDonationHistory(history) {
    const container = document.getElementById('historyContainer');
    
    if (!history || history.length === 0) {
        container.innerHTML = '<div class="empty-state"><p>No donation history</p></div>';
        return;
    }
    
    container.innerHTML = history.map(item => `
        <div class="history-item">
            <div class="history-item-info">
                <h4>Donation on ${formatDate(item.appointment_date)}</h4>
                <p><strong>Type:</strong> ${item.appointment_type || '-'}</p>
                <p><strong>Blood Group:</strong> ${item.blood_group || item.blood_type || '-'}</p>
            </div>
            <div class="history-item-badge">Completed</div>
        </div>
    `).join('');
}

// Update profile information
function updateProfile(profile) {
    if (!profile) return;
    
    // Profile information is displayed in PHP
}

// Handle schedule form submission
function handleScheduleSubmit(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    const data = Object.fromEntries(formData.entries());
    
    fetch('../handlers/scheduleAppointmentHandler.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(result => {
        if (result.success) {
            alert('Appointment scheduled successfully!');
            e.target.reset();
            loadDashboardData(); // Reload data
            
            // Switch to appointments view
            document.querySelector('[data-section="appointments"]').click();
        } else {
            alert('Error: ' + (result.message || 'Failed to schedule appointment'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred. Please try again.');
    });
}

// Show empty state for all sections
function showEmptyState() {
    document.getElementById('recentAppointments').innerHTML = 
        '<div class="empty-state"><p>Unable to load data. Please refresh the page.</p></div>';
    document.getElementById('appointmentsContainer').innerHTML = 
        '<div class="empty-state"><p>Unable to load data. Please refresh the page.</p></div>';
    document.getElementById('historyContainer').innerHTML = 
        '<div class="empty-state"><p>Unable to load data. Please refresh the page.</p></div>';
}

// Utility functions
function formatDate(dateString) {
    if (!dateString) return '-';
    const date = new Date(dateString);
    return date.toLocaleDateString('en-US', { 
        year: 'numeric', 
        month: 'short', 
        day: 'numeric' 
    });
}

function capitalizeFirst(str) {
    if (!str) return '';
    return str.charAt(0).toUpperCase() + str.slice(1);
}