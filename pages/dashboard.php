<?php
require_once '../includes/functions.php';
requireLogin();
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>داشبورد</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="content">
        <div class="card">
            <h2>داشبورد</h2>
            <p>خوش آمدید، <?php echo $_SESSION['username']; ?>!</p>
        </div>
    </div>
</body>
</html>