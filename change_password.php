<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login');
    exit;
}

require_once 'config/connect_db.php';
require_once 'config/functions.php';
if (!isset($_SESSION['permissions'])) {
    load_user_permissions($_SESSION['user_id']);
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'] ?? '';
    $new_password = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error = 'กรุณากรอกข้อมูลให้ครบ';
    } elseif ($new_password !== $confirm_password) {
        $error = 'รหัสผ่านใหม่ไม่ตรงกัน';
    } else {
        $stmt = $conn->prepare("SELECT password FROM ims_user WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (password_verify($current_password, $user['password'])) {
            $new_hash = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE ims_user SET password = ? WHERE id = ?");
            $stmt->execute([$new_hash, $_SESSION['user_id']]);
            $message = 'เปลี่ยนรหัสผ่านสำเร็จ';
        } else {
            $error = 'รหัสผ่านปัจจุบันไม่ถูกต้อง';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เปลี่ยนรหัสผ่าน</title>
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Kanit', sans-serif; background: #e9ecef; }
        .main-card { background: white; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); overflow: hidden; }
        .main-card .card-header { background: #f0f0f0; color: #0d6efd; padding: 20px 25px; border-bottom: 1px solid #dee2e6; }
        .main-card .card-body { padding: 20px; }
        
        @media (max-width: 576px) {
            .container { padding: 10px; }
        }
        @media (max-width: 768px) {
            .table th, .table td { font-size: 12px; padding: 5px; }
            .btn-sm { font-size: 10px; padding: 3px 6px; }
        }
        .table td, .table th { vertical-align: middle; }
    </style>
</head>
<body>
    <div class="container-fluid py-3 px-4">
        <div class="main-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <img src="img/logo/logo text-01.png" alt="Logo" style="height: 35px;">
                    <h5 class="m-0" style="color:#0d6efd">🔐 เปลี่ยนรหัสผ่าน</h5>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="me-2 text-muted"><?= htmlspecialchars($_SESSION['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="me-2 text-muted small" id="headerClock"></span>
                    <?php nav_link('dashboard', '📊 Dashboard', 'dashboard'); ?>
                    <?php nav_link('dashboard_graph', '📈 กราฟ', 'dashboard_graph'); ?>
                    <?php nav_link('dashboard-by-sales', '📊 แยกเซลส์', 'dashboard_by_sales'); ?>
                    <?php nav_link('manage_event', '📅 จัดการ Event', 'manage_event'); ?>
                    <?php nav_link('manage_user', '👥 จัดการผู้ใช้', 'manage_user'); ?>
                    <?php nav_link('manage_permission', '🔐 จัดการสิทธิ์', 'manage_permission'); ?>
                    <a href="main" class="btn btn-outline-secondary btn-sm">🏠 หน้าหลัก</a>
                    <a href="logout" class="btn btn-outline-secondary btn-sm">ออก</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <?php if ($message): ?>
                            <div class="alert alert-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                        <?php if ($error): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
                        <?php endif; ?>
                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">รหัสผ่านปัจจุบัน</label>
                                <input type="password" class="form-control" name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">รหัสผ่านใหม่</label>
                                <input type="password" class="form-control" name="new_password" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">ยืนยันรหัสผ่านใหม่</label>
                                <input type="password" class="form-control" name="confirm_password" required>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" class="btn btn-primary">บันทึก</button>
                                <a href="main" class="btn btn-secondary">🏠 กลับหน้าหลัก</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<script>
    function updateClock() {
        document.getElementById('headerClock').textContent = new Date().toLocaleDateString('th-TH', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' });
    }
    updateClock();
    setInterval(updateClock, 1000);
</script>
</body>
</html>