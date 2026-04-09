<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    echo '<script>alert("ไม่มีสิทธิ์เข้าถึง"); window.location.href="index.php";</script>';
    exit;
}

require_once 'config/connect_db.php';

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
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <style>body{font-family:'Kanit',sans-serif;background:#f8f9fa}</style>
</head>
<body>
    <div class="container py-4">
        <h3>👥 จัดการผู้ใช้</h3>
        
        <?php if ($message): ?>
            <div class="alert alert-success"><?= $message ?></div>
        <?php endif; ?>
        <?php if ($error): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">เพิ่มผู้ใช้ใหม่</div>
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
                            <th>ชื่อ-นามสกุล</th>
                            <th>สิทธิ์</th>
                            <th>วันที่สร้าง</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['full_name'] ?? '-') ?></td>
                            <td><?= $user['role'] === 'admin' ? 'ผู้ดูแล' : 'ผู้ใช้' ?></td>
                            <td><?= $user['created_at'] ?></td>
                            <td>
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
        
        <div class="mt-3">
            <a href="index.php" class="btn btn-secondary">กลับ</a>
            <a href="change_password.php" class="btn btn-warning">เปลี่ยนรหัสผ่าน</a>
        </div>
    </div>
</body>
</html>