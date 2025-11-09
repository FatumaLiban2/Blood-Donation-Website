<?php
require_once _DIR_ . '/../autoload.php';

// Validate session
$session = SessionManager::validateSession();
if (!$session) {
    header("Location: ../index.php");
    exit();
}

$patientId = $session->patient_id;
$patientEmail = $session->email;

// Get patient info
$patient = Patient::findByEmail($patientEmail);
if (!$patient) {
    header("Location: ../index.php");
    exit();
}

$patientName = $patient->getFirstName() . ' ' . $patient->getLastName();
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Donor Dashboard</title>
        <link rel="stylesheet" href="styles/dashboard.css">
    </head>

    <body>
        <div class="sidebar">
            <h2>Donor Dashboard</h2>
            <ul>
                <li><a href="#overview" class="nav-link active" data-section="overview">Dashboard</a></li>
                <li><a href="#appointments" class="nav-link" data-section="appointments">My Appointments</a></li>
                <li><a href="#schedule" class="nav-link" data-section="schedule">Schedule Appointment</a></li>
                <li><a href="#history" class="nav-link" data-section="history">Donation History</a></li>
                <li><a href="#profile" class="nav-link" data-section="profile">Profile</a></li>
                <li><a href="#" id="logoutBtn">Logout</a></li>
            </ul>
        </div>

        <div class="main-content">
            <header>
                <div class="header-content">
                    <h1>Welcome, <?php echo htmlspecialchars($patientName); ?>!</h1>
                    <div class="user-info">
                        <span><?php echo htmlspecialchars($patientEmail); ?></span>
                    </div>
                </div>
            </header>

            <main>
                <!-- Dashboard Overview Section -->
                <section id="overview-section" class="dashboard-section">
                    <h2>Dashboard Overview</h2>
                    
                    <div class="stats-container">
                        <div class="stat-card">
                            <h3>Total Donations</h3>
                            <p class="stat-number" id="totalDonations">0</p>
                            <span class="stat-label">All time</span>
                        </div>
                        <div class="stat-card">
                            <h3>Upcoming Appointments</h3>
                            <p class="stat-number" id="upcomingCount">0</p>
                            <span class="stat-label">Scheduled</span>
                        </div>
                        <div class="stat-card">
                            <h3>Last Donation</h3>
                            <p class="stat-number" id="lastDonation">-</p>
                            <span class="stat-label">Date</span>
                        </div>
                        <div class="stat-card">
                            <h3>Next Eligible</h3>
                            <p class="stat-number" id="nextEligible">-</p>
                            <span class="stat-label">Date</span>
                        </div>
                    </div>

                    <div class="recent-activity">
                        <h3>Recent Appointments</h3>
                        <div id="recentAppointments" class="appointments-list">
                            <div class="loading">Loading appointments...</div>
                        </div>
                    </div>
                </section>

                <!-- Appointments Section -->
                <section id="appointments-section" class="dashboard-section hidden">
                    <h2>My Appointments</h2>
                    <div id="appointmentsContainer" class="appointments-list">
                        <div class="loading">Loading appointments...</div>
                    </div>
                </section>

                <!-- Schedule Appointment Section -->
                <section id="schedule-section" class="dashboard-section hidden">
                    <h2>Schedule New Appointment</h2>
                    <form id="scheduleForm" class="appointment-form">
                        <div class="form-group">
                            <label for="appointmentDate">Preferred Date</label>
                            <input type="date" id="appointmentDate" name="appointmentDate" required>
                        </div>
                        <div class="form-group">
                            <label for="appointmentTime">Preferred Time</label>
                            <select id="appointmentTime" name="appointmentTime" required>
                                <option value="">Select time</option>
                                <option value="09:00">09:00 AM</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="11:00">11:00 AM</option>
                                <option value="14:00">02:00 PM</option>
                                <option value="15:00">03:00 PM</option>
                                <option value="16:00">04:00 PM</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="bloodType">Blood Type</label>
                            <select id="bloodType" name="bloodType" required>
                                <option value="">Select blood type</option>
                                <option value="A+">A+</option>
                                <option value="A-">A-</option>
                                <option value="B+">B+</option>
                                <option value="B-">B-</option>
                                <option value="AB+">AB+</option>
                                <option value="AB-">AB-</option>
                                <option value="O+">O+</option>
                                <option value="O-">O-</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="notes">Additional Notes (Optional)</label>
                            <textarea id="notes" name="notes" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn-primary">Schedule Appointment</button>
                    </form>
                </section>

                <!-- Donation History Section -->
                <section id="history-section" class="dashboard-section hidden">
                    <h2>Donation History</h2>
                    <div id="historyContainer" class="history-list">
                        <div class="loading">Loading history...</div>
                    </div>
                </section>

                <!-- Profile Section -->
                <section id="profile-section" class="dashboard-section hidden">
                    <h2>My Profile</h2>
                    <div class="profile-info">
                        <div class="info-group">
                            <label>Name</label>
                            <p><?php echo htmlspecialchars($patientName); ?></p>
                        </div>
                        <div class="info-group">
                            <label>Email</label>
                            <p><?php echo htmlspecialchars($patientEmail); ?></p>
                        </div>
                        <div class="info-group">
                            <label>Phone</label>
                            <p><?php echo htmlspecialchars($patient->getTelephone()); ?></p>
                        </div>
                        <div class="info-group">
                            <label>Member Since</label>
                            <p id="memberSince">-</p>
                        </div>
                    </div>
                </section>
            </main>
        </div>

        <script src="scripts/dashboard.js"></script>
    </body>
</html>