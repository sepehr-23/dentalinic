<?php
// IMPORTANT: Replace these placeholder values with your actual database credentials!
$db_host = 'localhost'; // Usually 'localhost'
$db_name = 'YOUR_DATABASE_NAME_HERE'; // e.g., 'zuevmixi_dental'
$db_user = 'YOUR_DATABASE_USER_HERE'; // e.g., 'zuevmixi_dental_user'
$db_pass = 'YOUR_DATABASE_PASSWORD_HERE'; // e.g., 'your_strong_password'

header('Content-Type: application/json; charset=utf-8'); // Set content type to JSON

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Database connection
        $dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];
        $pdo = new PDO($dsn, $db_user, $db_pass, $options);

        // Retrieve and sanitize form data (basic sanitization with htmlspecialchars)
        // For more robust validation, consider using filter_var or a validation library
        $full_name = isset($_POST['full_name']) ? htmlspecialchars(trim($_POST['full_name'])) : null;
        $phone_number = isset($_POST['phone_number']) ? htmlspecialchars(trim($_POST['phone_number'])) : null;
        $age = isset($_POST['age']) && $_POST['age'] !== '' ? (int)$_POST['age'] : null; // Ensure age is integer or null
        $gender = isset($_POST['gender']) ? htmlspecialchars(trim($_POST['gender'])) : null;
        $marital_status = isset($_POST['marital_status']) ? htmlspecialchars(trim($_POST['marital_status'])) : null;
        $address = isset($_POST['address']) ? htmlspecialchars(trim($_POST['address'])) : null;
        $medical_history = isset($_POST['medical_history']) ? htmlspecialchars(trim($_POST['medical_history'])) : null;
        $allergies = isset($_POST['allergies']) ? htmlspecialchars(trim($_POST['allergies'])) : null;
        $insurance_info = isset($_POST['insurance_info']) ? htmlspecialchars(trim($_POST['insurance_info'])) : null;
        $general_notes = isset($_POST['general_notes']) ? htmlspecialchars(trim($_POST['general_notes'])) : null;

        // Basic validation: Check if required fields are provided
        if (empty($full_name) || empty($phone_number)) {
            echo json_encode(['status' => 'error', 'message' => 'نام و نام خانوادگی و شماره تماس الزامی هستند.']);
            exit;
        }

        // File handling (optional, if you have a file input named 'patientFile')
        // For now, we are not saving the file path to DB, just acknowledging it.
        // $patient_file_path = null;
        // if (isset($_FILES['patientFile']) && $_FILES['patientFile']['error'] == UPLOAD_ERR_OK) {
        //     $upload_dir = '../uploads/'; // Create this directory if it doesn't exist
        //     if (!is_dir($upload_dir)) {
        //         mkdir($upload_dir, 0777, true);
        //     }
        //     $file_name = time() . '_' . basename($_FILES['patientFile']['name']);
        //     $patient_file_path = $upload_dir . $file_name;
        //     if (!move_uploaded_file($_FILES['patientFile']['tmp_name'], $patient_file_path)) {
        //          $patient_file_path = null; // Failed to move
        //     }
        // }


        // SQL preparation and execution
        $sql = "INSERT INTO patients (full_name, phone_number, age, gender, marital_status, address, medical_history, allergies, insurance_info, general_notes)
                VALUES (:full_name, :phone_number, :age, :gender, :marital_status, :address, :medical_history, :allergies, :insurance_info, :general_notes)";

        $stmt = $pdo->prepare($sql);

        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':age', $age, PDO::PARAM_INT); // Bind age as integer
        $stmt->bindParam(':gender', $gender);
        $stmt->bindParam(':marital_status', $marital_status);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':medical_history', $medical_history);
        $stmt->bindParam(':allergies', $allergies);
        $stmt->bindParam(':insurance_info', $insurance_info);
        $stmt->bindParam(':general_notes', $general_notes);
        // If handling file uploads and storing path:
        // $stmt->bindParam(':patient_file_path', $patient_file_path);


        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'بیمار با موفقیت اضافه شد.']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'خطا در اجرای دستور پایگاه داده.']);
        }

    } catch (PDOException $e) {
        // Log error to a file or monitoring system in a real application
        error_log("Database Error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'خطا در اتصال به پایگاه داده یا ثبت اطلاعات: ' . $e->getMessage()]);
    } catch (Exception $e) {
        error_log("General Error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'یک خطای عمومی رخ داد: ' . $e->getMessage()]);
    }

} else {
    // Handle non-POST requests
    echo json_encode(['status' => 'error', 'message' => 'اسکریپت فقط درخواست‌های POST را پردازش می‌کند.']);
}
?>
