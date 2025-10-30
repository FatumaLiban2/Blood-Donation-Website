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
                <button id="adminsTable">Admins</button>
                <button id="patientsTable">Patients</button>
                <button id="donationsTable">Donations</button>
                <button id="shedulesTable">Schedules</button>
                <button id="reloadData">Reload</button>
            </div>

            <section id="admins" class="dashboard-section">
                <!-- Admins table will be rendered here -->
                <div class="list-container" id="adminsContainer"></div>
            </section>

            <section id="patients" class="dashboard-section hidden">
                <!-- Patients table will be rendered here -->
                <div class="list-container" id="patientsContainer"></div>
            </section>
        </main>

    <script src="scripts/admin.js"></script>
    </body>
</html>
