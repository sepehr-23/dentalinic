<?php
session_start();

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit();
    }
}

function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] == $role;
}

function getSubordinates($userId, $conn) {
    $subordinates = [];
    $sql = "SELECT id FROM users WHERE superior_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $subordinates[] = $row['id'];
        $subordinates = array_merge($subordinates, getSubordinates($row['id'], $conn));
    }
    return $subordinates;
}
?>