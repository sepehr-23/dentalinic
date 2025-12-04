<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
requireLogin();

if (!in_array($_SESSION['role'], ['system_manager', 'senior_manager', 'province_manager'])) {
    // Redirect if user doesn't have permission
    header("Location: ../index.php");
    exit();
}

$currentUserId = $_SESSION['user_id'];
$currentUserRole = $_SESSION['role'];

// Handle user creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_user'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role = $_POST['role'];
    $province_id = isset($_POST['province_id']) ? $_POST['province_id'] : null;
    $superior_id = null;

    if ($currentUserRole == 'system_manager') {
        $superior_id = $_POST['superior_id'];
    } else {
        $superior_id = $currentUserId;
    }

    $stmt = $conn->prepare("INSERT INTO users (username, password, role, superior_id, province_id) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssis", $username, $password, $role, $superior_id, $province_id);
    $stmt->execute();
}

// Handle user deletion
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $userIdToDelete = $_POST['user_id'];
    $subordinates = getSubordinates($currentUserId, $conn);

    // Security Check: Ensure the user being deleted is a subordinate of the current user.
    // System manager can delete anyone except themself.
    if ($userIdToDelete != $currentUserId && (in_array($userIdToDelete, $subordinates) || $currentUserRole == 'system_manager')) {
        // Before deleting, you might want to handle their invoices (e.g., reassign or delete)
        // For now, we'll just delete the user.
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $userIdToDelete);
        $stmt->execute();
    }
}

// Fetch users to display
$usersToDisplay = [];
if ($currentUserRole == 'system_manager') {
    $result = $conn->query("SELECT u.*, s.username as superior_name, p.name as province_name
                            FROM users u
                            LEFT JOIN users s ON u.superior_id = s.id
                            LEFT JOIN provinces p ON u.province_id = p.id");
    while($row = $result->fetch_assoc()) {
        $usersToDisplay[] = $row;
    }
} else {
    $subordinateIds = getSubordinates($currentUserId, $conn);
    if (!empty($subordinateIds)) {
        $placeholders = implode(',', array_fill(0, count($subordinateIds), '?'));
        $sql = "SELECT u.*, s.username as superior_name, p.name as province_name
                FROM users u
                LEFT JOIN users s ON u.superior_id = s.id
                LEFT JOIN provinces p ON u.province_id = p.id
                WHERE u.id IN ($placeholders)";
        $stmt = $conn->prepare($sql);
        $types = str_repeat('i', count($subordinateIds));
        $stmt->bind_param($types, ...$subordinateIds);
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()) {
            $usersToDisplay[] = $row;
        }
    }
}


// Fetch provinces for the form
$provincesResult = $conn->query("SELECT * FROM provinces");

// Fetch potential superiors for the form (for system manager)
$superiorsResult = null;
if ($currentUserRole == 'system_manager') {
    $superiorsResult = $conn->query("SELECT id, username FROM users WHERE role IN ('system_manager', 'senior_manager', 'province_manager')");
}

?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت کاربران</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="content">
        <div class="card">
            <h2>مدیریت کاربران</h2>

            <div class="card user-form">
                <h3>ایجاد کاربر جدید</h3>
                <form action="manage_users.php" method="post">
                    <div class="form-group">
                        <label for="username">نام کاربری</label>
                        <input type="text" name="username" id="username" required>
                    </div>
                    <div class="form-group">
                        <label for="password">رمز عبور</label>
                        <input type="password" name="password" id="password" required>
                    </div>
                    <div class="form-group">
                        <label for="role">سطح دسترسی</label>
                        <select name="role" id="role" required>
                            <?php if ($currentUserRole == 'system_manager'): ?>
                                <option value="senior_manager">مدیر ارشد</option>
                                <option value="province_manager">مدیر استان</option>
                                <option value="store_manager">مسئول فروشگاه</option>
                            <?php elseif ($currentUserRole == 'senior_manager'): ?>
                                <option value="province_manager">مدیر استان</option>
                                <option value="store_manager">مسئول فروشگاه</option>
                            <?php elseif ($currentUserRole == 'province_manager'): ?>
                                <option value="store_manager">مسئول فروشگاه</option>
                            <?php endif; ?>
                        </select>
                    </div>
                     <div class="form-group">
                        <label for="province_id">استان</label>
                        <select name="province_id" id="province_id">
                            <option value="">انتخاب کنید</option>
                            <?php while ($province = $provincesResult->fetch_assoc()): ?>
                                <option value="<?php echo $province['id']; ?>"><?php echo $province['name']; ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                     <?php if ($currentUserRole == 'system_manager'): ?>
                        <div class="form-group">
                            <label for="superior_id">مافوق</label>
                            <select name="superior_id" id="superior_id" required>
                                <?php while ($superior = $superiorsResult->fetch_assoc()): ?>
                                    <option value="<?php echo $superior['id']; ?>"><?php echo $superior['username']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    <?php endif; ?>
                    <button type="submit" name="create_user" class="btn">ایجاد کاربر</button>
                </form>
            </div>

            <div class="card">
                <h3>لیست کاربران</h3>
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>نام کاربری</th>
                            <th>سطح دسترسی</th>
                            <th>استان</th>
                            <th>مافوق</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                         <?php foreach($usersToDisplay as $user): ?>
                            <tr>
                                <td><?php echo $user['username']; ?></td>
                                <td><span class="role-badge"><?php echo $user['role']; ?></span></td>
                                <td><span class="province-badge"><?php echo $user['province_name'] ?? '-'; ?></span></td>
                                <td><?php echo $user['superior_name'] ?? '-'; ?></td>
                                <td class="users-table-actions">
                                    <form action="manage_users.php" method="post" onsubmit="return confirm('آیا از حذف این کاربر اطمینان دارید؟');">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                                        <button type="submit" name="delete_user" class="delete-btn small-btn">حذف</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
