<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

// Include config file (for potential future database operations)
require_once "config.php";

// Logout functionality
if(isset($_GET["logout"])){
    // Unset all of the session variables
    $_SESSION = array();

    // Destroy the session.
    session_destroy();

    // Redirect to login page
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome - Dental Clinic Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Additional styles for dashboard, can be moved to style.css later */
        body {
            display: block; /* Override login page's flex display */
        }
        .dashboard-container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .welcome-message {
            font-size: 24px;
            color: #00796b;
            margin-bottom: 20px;
        }
        .logout-button {
            background-color: #d32f2f;
            color: white;
            padding: 10px 15px;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }
        .logout-button:hover {
            background-color: #c62828;
        }
        .user-info {
            margin-bottom: 20px;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <div class="welcome-message">
            <h1>Welcome to the Dental Clinic Dashboard</h1>
        </div>

        <div class="user-info">
            <p>Hello, <strong><?php echo htmlspecialchars($_SESSION["username"]); ?></strong>!</p>
        </div>

        <p>This is your main dashboard area. More features will be added soon.</p>
        <br>
        <a href="dashboard.php?logout=true" class="logout-button">Logout</a>
    </div>

</body>
</html>
