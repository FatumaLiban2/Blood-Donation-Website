<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles/admin.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.31/jspdf.plugin.autotable.min.js"></script>
    </head>

    <body>
        <header>
            <div class="dashboard-title">
                <h2>LifeBlood Admin Dashboard</h2>

                <div class="admin-info">
                    <span>Welcome, Admin</span>
                    <button class="logout-button">Logout</button>
                </div>
            </div>
        </header>

        <main>
            <div class="admin-controls">
                <button id="adminsTable" class="tab-btn active">Admins</button>
                <button id="patientsTable" class="tab-btn">Patients</button>
                <button id="appointmentsTable" class="tab-btn">Pending Appointments</button>
                <button id="doneAppointmentsTable" class="tab-btn">Done Appointments</button>
                <button id="reloadData" class="reload-btn">Reload</button>
                <button id="downloadCSV" class="download-btn">Download CSV</button>
                <button id="downloadPDF" class="download-btn">Download PDF</button>
            </div>

            <section id="admins-section" class="dashboard-section">
                <h3>Admins</h3>
                <div class="list-container" id="adminsContainer">
                    <div class="loading">Loading admins...</div>
                </div>
            </section>

            <section id="patients-section" class="dashboard-section hidden">
                <h3>Patients</h3>
                <div class="list-container" id="patientsContainer">
                    <div class="loading">Loading patients...</div>
                </div>
            </section>

            <section id="appointments-section" class="dashboard-section hidden">
                <h3>Pending Appointments</h3>
                <div class="list-container" id="appointmentsContainer">
                    <div class="loading">Loading appointments...</div>
                </div>
            </section>

            <section id="done-appointments-section" class="dashboard-section hidden">
                <h3>Completed Appointments</h3>
                <div class="list-container" id="doneAppointmentsContainer">
                    <div class="loading">Loading completed appointments...</div>
                </div>
            </section>
        </main>

    <script src="scripts/admin.js"></script>
    </body>
</html>
