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
require_permission('manage_user');

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_user'])) {
        $username = $_POST['username'] ?? '';
        $password = $_POST['password'] ?? '';
        $full_name = $_POST['full_name'] ?? '';
        $role = $_POST['role'] ?? 'user';
        
        if (empty($username) || empty($password)) {
            $error = 'กรุณากรอกชื่อผู้ใช้และรหัสผ่าน';
        } else {
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO ims_user (username, password, full_name, role) VALUES (?, ?, ?, ?)");
            try {
                $stmt->execute([$username, $hash, $full_name, $role]);
                $message = 'เพิ่ม用户สำเร็จ';
            } catch (Exception $e) {
                $error = 'ชื่อผู้ใช้ซ้ำ';
            }
        }
    } elseif (isset($_POST['update_user'])) {
        $user_id = intval($_POST['user_id'] ?? 0);
        $new_role = $_POST['role'] ?? '';
        $new_full_name = $_POST['full_name'] ?? '';
        if ($user_id > 0 && in_array($new_role, ['admin', 'user'])) {
            $stmt = $conn->prepare("UPDATE ims_user SET role = ?, full_name = ? WHERE id = ?");
            $stmt->execute([$new_role, $new_full_name, $user_id]);
            $message = 'อัปเดตสิทธิ์ผู้ใช้สำเร็จ';
        }
    } elseif (isset($_POST['delete_user'])) {
        $user_id = $_POST['user_id'] ?? 0;
        if ($user_id != $_SESSION['user_id']) {
            $stmt = $conn->prepare("DELETE FROM ims_user WHERE id = ?");
            $stmt->execute([$user_id]);
            $message = 'ลบ用户สำเร็จ';
        } else {
            $error = 'ไม่สามารถลบตัวเองได้';
        }
    }
}

$stmt = $conn->query("SELECT id, username, full_name, role, created_at FROM ims_user ORDER BY id");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการผู้ใช้</title>
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
                    <h5 class="m-0" style="color:#0d6efd">👥 จัดการผู้ใช้</h5>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="me-2 text-muted"><?= htmlspecialchars($_SESSION['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="me-2 text-muted small" id="headerClock"></span>
                    <?php nav_link('manage_permission', '🔐 จัดการสิทธิ์', 'manage_permission'); ?>
                    <?php nav_link('manage_event', '📅 จัดการ Event', 'manage_event'); ?>
                    <?php nav_link('dashboard', '📊 Dashboard', 'dashboard'); ?>
                    <a href="main" class="btn btn-outline-secondary btn-sm">🏠 หน้าหลัก</a>
                    <a href="change_password" class="btn btn-outline-secondary btn-sm">🔑 เปลี่ยนรหัส</a>
                    <a href="logout" class="btn btn-outline-secondary btn-sm">ออก</a>
                </div>
            </div>
            <div class="card-body">
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?= $message ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="card mb-4">
            <div class="card-header bg-secondary text-white">เพิ่มผู้ใช้ใหม่</div>
            <div class="card-body">
                <form method="POST" class="row g-3">
                    <input type="hidden" name="add_user" value="1">
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="username" placeholder="ชื่อผู้ใช้" required>
                    </div>
                    <div class="col-md-3">
                        <input type="password" class="form-control" name="password" placeholder="รหัสผ่าน" required>
                    </div>
                    <div class="col-md-3">
                        <input type="text" class="form-control" name="full_name" placeholder="ชื่อ-นามสกุล">
                    </div>
                    <div class="col-md-2">
                        <select class="form-select" name="role">
                            <option value="user">ผู้ใช้</option>
                            <option value="admin">ผู้ดูแล</option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <button type="submit" class="btn btn-success">เพิ่ม</button>
                    </div>
                </form>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">รายชื่อผู้ใช้</div>
            <div class="card-body">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>ชื่อผู้ใช้</th>
                            <th>ชื่อ-นามสกุล / สิทธิ์</th>
                            <th>วันที่สร้าง</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td class="align-middle"><?= htmlspecialchars($user['username']) ?></td>
                            <td>
                                <form method="POST" class="d-flex align-items-center gap-1">
                                    <input type="hidden" name="update_user" value="1">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <input type="text" class="form-control form-control-sm" name="full_name" value="<?= htmlspecialchars($user['full_name'] ?? '') ?>" style="min-width:100px;width:auto" placeholder="ชื่อ">
                                    <select class="form-select form-select-sm" name="role" style="width:80px">
                                        <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>ผู้ใช้</option>
                                        <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                    </select>
                                    <button type="submit" class="btn btn-sm btn-outline-primary" title="บันทึก">💾</button>
                                </form>
                            </td>
                            <td class="align-middle"><?= $user['created_at'] ?></td>
                            <td>
                                <?php if (has_permission('manage_permission')): ?>
                                <a href="manage_permission?user_id=<?= $user['id'] ?>" class="btn btn-sm btn-info">สิทธิ์</a>
                                <?php endif; ?>
                                <?php if ($user['id'] != $_SESSION['user_id']): ?>
                                <form method="POST" class="d-inline" onsubmit="return confirm('ยืนยันการลบ?')">
                                    <input type="hidden" name="delete_user" value="1">
                                    <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">ลบ</button>
                                </form>
                                <?php else: ?>
                                <span class="text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
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