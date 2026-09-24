<?php
require_once"../includes/auth_admin.php";
require_once"../config/database.php";

$page_title = "Dashboard";
?>

<!Doctype html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dairy Management System -Dashboard</title>

    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="admin-layout">
        <?php include "../includes/sidebar.php";?>

        <div class="main-content">
            <div class="welcome-section">

            <div>
                <h2>
                    welcome,
                    <?php echo htmlspecialchars($_SESSION["user_name"]);?>!
                </h2>

                <p>
                    Here's what's happening in your dairy today.
               </p>

            </div>
            </div>

            <div class="dashboard-cards">
                <div class="dashboard-card">
                    .card-icon <farmer-icon></farmer-icon>
                </div>
            </div>
        </div>
    </div>
    
</body>
</html>