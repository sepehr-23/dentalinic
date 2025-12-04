<?php
require_once 'includes/functions.php';

if (!isLoggedIn()) {
    header("Location: pages/login.php");
    exit();
}

$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';

switch ($page) {
    case 'dashboard':
        require 'pages/dashboard.php';
        break;
    case 'invoices':
        require 'pages/invoices.php';
        break;
    case 'manage_users':
        require 'pages/manage_users.php';
        break;
    case 'logout':
        require 'pages/logout.php';
        break;
    default:
        require 'pages/dashboard.php';
        break;
}
