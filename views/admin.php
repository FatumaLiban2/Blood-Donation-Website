<!DOCTYPE html>

<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Admin Dashboard</title>
        <link rel="stylesheet" href="views/styles/admin.css">
    </head>

    <body>
        <header>
            <div>
                <h1>LifeBlood Admin Dashboard</h1>

                <div class="admin-info">
                    <span>Welcome, Admin</span>
                    <button class="logout-button">Logout</button>
            </div>
            
            <nav class="admin-nav">
                <ul class="admin-nav-links">
                    <li><a href="#admins">Manage Admins</a></li>
                    <li><a href="#patients">Manage Patients</a></li>
                </ul>
            </nav>
        </header>

        <main>
            <section id="admins" class="modal">

            </section>
        </main>

        <script src="views/scripts/admin.js"></script>
    </body>
</html>
