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
require_permission('manage_permission');

$message = '';
$error = '';

$stmt = $conn->query("SELECT id, username, full_name, role FROM ims_user ORDER BY username");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $conn->query("SELECT * FROM permissions ORDER BY is_system DESC, id");
$all_permissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

$selected_user_id = $_POST['user_id'] ?? $_GET['user_id'] ?? 0;
$user_permission_ids = [];

if ($selected_user_id && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_permissions'])) {
    $selected_user_id = intval($_POST['user_id']);
    $perms = $_POST['permissions'] ?? [];

    try {
        $stmt = $conn->prepare("DELETE FROM user_permissions WHERE user_id = ?");
        $stmt->execute([$selected_user_id]);

        if (!empty($perms)) {
            $insert = $conn->prepare("INSERT INTO user_permissions (user_id, permission_id) VALUES (?, ?)");
            foreach ($perms as $pid) {
                $insert->execute([$selected_user_id, intval($pid)]);
            }
        }
        $message = 'บันทึกสิทธิ์เรียบร้อย';

        $stmt = $conn->prepare("SELECT permission_id FROM user_permissions WHERE user_id = ?");
        $stmt->execute([$selected_user_id]);
        $user_permission_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (Exception $e) {
        $error = 'เกิดข้อผิดพลาด: ' . $e->getMessage();
    }
} elseif ($selected_user_id) {
    $stmt = $conn->prepare("SELECT permission_id FROM user_permissions WHERE user_id = ?");
    $stmt->execute([$selected_user_id]);
    $user_permission_ids = $stmt->fetchAll(PDO::FETCH_COLUMN);
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการสิทธิ์การเข้าใช้งาน</title>
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
        
        .perm-item { padding: 10px 15px; border: 1px solid #dee2e6; border-radius: 8px; margin-bottom: 8px; transition: all 0.2s; }
        .perm-item:hover { background: #f8f9fa; border-color: #0d6efd; }
        .perm-item .form-check-input:checked ~ .form-check-label { color: #0d6efd; font-weight: 600; }
        .perm-item.system { background: #f0f0f0; }
    </style>
</head>
<body>
    <div class="container-fluid py-3 px-4">
        <div class="main-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="d-flex align-items-center gap-2">
                    <img src="img/logo/logo text-01.png" alt="Logo" style="height: 35px;">
                    <h5 class="m-0" style="color:#0d6efd">🔐 จัดการสิทธิ์การเข้าใช้งาน</h5>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="me-2 text-muted"><?= htmlspecialchars($_SESSION['full_name'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
                    <span class="me-2 text-muted small" id="headerClock"></span>
                    <?php nav_link('manage_user', '👥 จัดการผู้ใช้', 'manage_user'); ?>
                    <?php nav_link('manage_event', '📅 จัดการ Event', 'manage_event'); ?>
                    <?php nav_link('dashboard', '📊 Dashboard', 'dashboard'); ?>
                    <a href="main" class="btn btn-outline-secondary btn-sm">🏠 หน้าหลัก</a>
                    <a href="change_password" class="btn btn-outline-secondary btn-sm">🔑 เปลี่ยนรหัส</a>
                    <a href="logout" class="btn btn-outline-secondary btn-sm">ออก</a>
                </div>
            </div>
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-success alert-dismissible fade show"><?= $message ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alert alert-danger alert-dismissible fade show"><?= $error ?><button type="button" class="btn-close" data-bs-dismiss="alert"></button></div>
                <?php endif; ?>

                <div class="row g-4">
                    <div class="col-md-4">
                        <h6 class="fw-bold mb-3">👤 เลือกผู้ใช้</h6>
                        <div class="list-group">
                            <?php foreach ($users as $u):
                                $is_sel = $selected_user_id == $u['id'];
                            ?>
                            <a href="?user_id=<?= $u['id'] ?>"
                               class="list-group-item list-group-item-action d-flex justify-content-between align-items-center <?= $is_sel ? 'active' : '' ?>">
                                <div>
                                    <strong><?= htmlspecialchars($u['full_name'] ?: $u['username']) ?></strong>
                                    <br><small class="text-muted">@<?= htmlspecialchars($u['username']) ?></small>
                                </div>
                                <span class="badge bg-<?= $u['role'] === 'admin' ? 'danger' : 'secondary' ?> rounded-pill">
                                    <?= $u['role'] === 'admin' ? 'แอดมิน' : 'ผู้ใช้' ?>
                                </span>
                            </a>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <div class="col-md-8">
                        <?php if ($selected_user_id):
                            $sel_user = null;
                            foreach ($users as $u) {
                                if ($u['id'] == $selected_user_id) { $sel_user = $u; break; }
                            }
                        ?>
                        <h6 class="fw-bold mb-3">
                            📋 กำหนดสิทธิ์ให้: <span class="text-primary"><?= htmlspecialchars($sel_user['full_name'] ?: $sel_user['username']) ?></span>
                            <?php if ($sel_user['role'] === 'admin'): ?>
                                <span class="badge bg-warning text-dark ms-2">มีสิทธิ์ทุกหน้าจออยู่แล้ว (สามารถบันทึกเพิ่มเติมได้)</span>
                            <?php endif; ?>
                        </h6>
                        <form method="POST">
                            <input type="hidden" name="user_id" value="<?= $selected_user_id ?>">
                            <input type="hidden" name="save_permissions" value="1">

                            <?php foreach ($all_permissions as $perm):
                                $checked = in_array($perm['id'], $user_permission_ids);
                            ?>
                            <div class="perm-item <?= $perm['is_system'] ? 'system' : '' ?>">
                                <div class="form-check form-switch d-flex align-items-center gap-3 m-0">
                                    <input class="form-check-input" type="checkbox"
                                           name="permissions[]" value="<?= $perm['id'] ?>"
                                           id="perm_<?= $perm['id'] ?>"
                                           <?= $checked ? 'checked' : '' ?>>
                                    <label class="form-check-label flex-grow-1" for="perm_<?= $perm['id'] ?>">
                                        <strong><?= htmlspecialchars($perm['permission_name']) ?></strong>
                                        <?php if ($perm['description']): ?>
                                            <br><small class="text-muted"><?= htmlspecialchars($perm['description']) ?></small>
                                        <?php endif; ?>
                                        <?php if ($perm['is_system']): ?>
                                            <span class="badge bg-info ms-1">จำเป็น</span>
                                        <?php endif; ?>
                                    </label>
                                </div>
                            </div>
                            <?php endforeach; ?>

                            <div class="mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-lg"></i> บันทึกสิทธิ์
                                </button>
                            </div>
                        </form>
                        <?php else: ?>
                        <div class="text-center text-muted py-5">
                            <i class="bi bi-person-check" style="font-size: 3rem;"></i>
                            <p class="mt-3">กรุณาเลือกผู้ใช้จากด้านซ้ายเพื่อกำหนดสิทธิ์</p>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateClock() {
            document.getElementById('headerClock').textContent = new Date().toLocaleDateString('th-TH', { year: 'numeric', month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit' });
        }
        updateClock();
        setInterval(updateClock, 1000);
    </script>
</body>
</html>
