<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="styles/admin.css">
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
                <button id="donationsTable" class="tab-btn" disabled>Donations</button>
                <button id="shedulesTable" class="tab-btn" disabled>Schedules</button>
                <button id="reloadData" class="reload-btn">Reload</button>
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
        </main>

    <script src="scripts/admin.js"></script>
    </body>
</html>
