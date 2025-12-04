<?php
require_once '../includes/db.php';
require_once '../includes/functions.php';
requireLogin();

$userId = $_SESSION['user_id'];
$role = $_SESSION['role'];

// Function to get the senior manager of a user
function getSeniorManager($userId, $conn) {
    $sql = "SELECT superior_id, role FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user['role'] == 'senior_manager' || $user['role'] == 'system_manager') {
        return $userId;
    }

    if ($user['superior_id'] === null) {
        return $userId; // Should not happen in a well-structured hierarchy, but as a fallback
    }

    return getSeniorManager($user['superior_id'], $conn);
}


// Handle invoice creation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create_invoice'])) {
    $amount = $_POST['amount'];
    // Get user's province_id
    $stmt = $conn->prepare("SELECT province_id FROM users WHERE id = ?");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $provinceId = $user['province_id'];

    $stmt = $conn->prepare("INSERT INTO invoices (user_id, province_id, amount) VALUES (?, ?, ?)");
    $stmt->bind_param("iid", $userId, $provinceId, $amount);
    $stmt->execute();
}

// Handle invoice actions (approve/reject)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && (isset($_POST['approve']) || isset($_POST['reject']))) {
    $invoiceId = $_POST['invoice_id'];
    $action = isset($_POST['approve']) ? 'approved' : 'rejected';

    if ($role == 'province_manager') {
        if ($action == 'rejected') {
            $reason = $_POST['rejection_reason'];
            $stmt = $conn->prepare("UPDATE invoices SET status = ?, rejection_reason = ?, rejected_by_province_manager_at = NOW() WHERE id = ?");
            $stmt->bind_param("ssi", $action, $reason, $invoiceId);
        } else {
            $stmt = $conn->prepare("UPDATE invoices SET status = 'approved_by_province_manager', approved_by_province_manager_at = NOW() WHERE id = ?");
            $stmt->bind_param("i", $invoiceId);
        }
        $stmt->execute();
    } elseif ($role == 'senior_manager') {
         if ($action == 'rejected') {
            $reason = $_POST['rejection_reason'];
            $stmt = $conn->prepare("UPDATE invoices SET status = ?, rejection_reason = ?, rejected_by_senior_manager_at = NOW() WHERE id = ?");
            $stmt->bind_param("ssi", $action, $reason, $invoiceId);
        } else {
            $stmt = $conn->prepare("UPDATE invoices SET status = 'approved', approved_by_senior_manager_at = NOW() WHERE id = ?");
            $stmt->bind_param("i", $invoiceId);
        }
        $stmt->execute();
    }
}

// Fetch invoices for the entire group
$seniorManagerId = getSeniorManager($userId, $conn);
$groupUserIds = getSubordinates($seniorManagerId, $conn);
array_push($groupUserIds, $seniorManagerId); // Add the senior manager themself

$placeholders = implode(',', array_fill(0, count($groupUserIds), '?'));

$sql = "SELECT i.*, u.username, p.name as province_name
        FROM invoices i
        JOIN users u ON i.user_id = u.id
        JOIN provinces p ON i.province_id = p.id ";

if ($role != 'system_manager') {
     $sql .= "WHERE i.user_id IN ($placeholders)";
}

$sql .= " ORDER BY i.created_at DESC";

$stmt = $conn->prepare($sql);
if ($role != 'system_manager') {
    $types = str_repeat('i', count($groupUserIds));
    $stmt->bind_param($types, ...$groupUserIds);
}
$stmt->execute();
$invoices = $stmt->get_result();

?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت فاکتورها</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <?php include 'header.php'; ?>
    <div class="content">
        <div class="card">
            <h2>مدیریت فاکتورها</h2>

            <?php if (hasRole('store_manager')): ?>
            <div class="card user-form">
                <h3>ثبت فاکتور جدید</h3>
                <form action="invoices.php" method="post">
                    <div class="form-group">
                        <label for="amount">مبلغ</label>
                        <input type="number" name="amount" id="amount" required>
                    </div>
                    <button type="submit" name="create_invoice" class="btn">ثبت فاکتور</button>
                </form>
            </div>
            <?php endif; ?>

            <div class="card">
                <h3>لیست فاکتورها</h3>
                <table>
                    <thead>
                        <tr>
                            <th>شماره</th>
                            <th>کاربر ثبت کننده</th>
                            <th>استان</th>
                            <th>مبلغ</th>
                            <th>وضعیت</th>
                            <th>تاریخ ثبت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($invoice = $invoices->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $invoice['id']; ?></td>
                                <td><?php echo $invoice['username']; ?></td>
                                <td><?php echo $invoice['province_name']; ?></td>
                                <td><?php echo number_format($invoice['amount']); ?></td>
                                <td>
                                    <?php
                                    $status_text = '';
                                    switch ($invoice['status']) {
                                        case 'pending':
                                            $status_text = 'در انتظار تایید مدیر استان';
                                            break;
                                        case 'approved_by_province_manager':
                                            $status_text = 'در انتظار تایید مدیر ارشد';
                                            break;
                                        case 'approved':
                                            $status_text = 'تایید شده';
                                            break;
                                        case 'rejected':
                                            $status_text = 'رد شده';
                                            break;
                                    }
                                    echo $status_text;
                                    ?>
                                </td>
                                <td><?php echo $invoice['created_at']; ?></td>
                                <td>
                                    <?php if (hasRole('province_manager') && $invoice['status'] == 'pending'): ?>
                                        <form action="invoices.php" method="post" style="display: inline-block;">
                                            <input type="hidden" name="invoice_id" value="<?php echo $invoice['id']; ?>">
                                            <button type="submit" name="approve" class="btn btn-secondary small-btn">تایید</button>
                                        </form>
                                        <button class="btn btn-danger small-btn" onclick="openRejectModal(<?php echo $invoice['id']; ?>)">رد</button>
                                    <?php endif; ?>

                                    <?php if (hasRole('senior_manager') && $invoice['status'] == 'approved_by_province_manager'): ?>
                                        <form action="invoices.php" method="post" style="display: inline-block;">
                                            <input type="hidden" name="invoice_id" value="<?php echo $invoice['id']; ?>">
                                            <button type="submit" name="approve" class="btn btn-secondary small-btn">تایید</button>
                                        </form>
                                         <button class="btn btn-danger small-btn" onclick="openRejectModal(<?php echo $invoice['id']; ?>)">رد</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:1000; justify-content:center; align-items:center;">
        <div style="background:white; padding:20px; border-radius:5px; width: 400px; text-align: right;">
            <form action="invoices.php" method="post">
                <h3>دلیل رد فاکتور</h3>
                <input type="hidden" name="invoice_id" id="modal_invoice_id">
                <textarea name="rejection_reason" rows="4" style="width: 100%;" required></textarea>
                <br><br>
                <button type="submit" name="reject" class="btn btn-danger">ثبت دلیل و رد</button>
                <button type="button" class="btn" onclick="closeRejectModal()">انصراف</button>
            </form>
        </div>
    </div>

    <script>
        function openRejectModal(invoiceId) {
            document.getElementById('modal_invoice_id').value = invoiceId;
            document.getElementById('rejectModal').style.display = 'flex';
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').style.display = 'none';
        }
    </script>

</body>
</html>
