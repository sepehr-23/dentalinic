<?php
// IMPORTANT: Replace these placeholder values with your actual database credentials!
$db_host = 'localhost'; // Usually 'localhost'
$db_name = 'YOUR_DATABASE_NAME_HERE'; // e.g., 'zuevmixi_dental'
$db_user = 'YOUR_DATABASE_USER_HERE'; // e.g., 'zuevmixi_dental_user'
$db_pass = 'YOUR_DATABASE_PASSWORD_HERE'; // e.g., 'your_strong_password'

header('Content-Type: application/json; charset=utf-8');

try {
    // Database connection
    $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, $db_user, $db_pass, $options);

    // SQL query to fetch all patients, ordered by creation date (assuming you have a created_at column)
    // If you don't have 'created_at', you might order by 'id' or another relevant column.
    // Ensure your 'payment_status' column in the database stores Farsi values as specified.
    $stmt = $pdo->query("SELECT id, full_name, phone_number, age, gender, marital_status, address, medical_history, allergies, insurance_info, general_notes, payment_status, created_at FROM patients ORDER BY created_at DESC");
    $patients = $stmt->fetchAll();

    echo json_encode(['status' => 'success', 'patients' => $patients]);

} catch (PDOException $e) {
    error_log("Database Error (get_patients.php): " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'خطا در بازیابی اطلاعات بیماران: ' . $e->getMessage()]);
} catch (Exception $e) {
    error_log("General Error (get_patients.php): " . $e->getMessage());
    echo json_encode(['status' => 'error', 'message' => 'یک خطای عمومی در بازیابی اطلاعات رخ داد: ' . $e->getMessage()]);
}
?>
