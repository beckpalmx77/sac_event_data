<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/connect_db.php';

if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['create_event'])) {
        $event_name = trim($_POST['event_name'] ?? '');
        $event_date = $_POST['event_date'] ?? null;
        if (!empty($event_name)) {
            $stmt = $conn->prepare("INSERT INTO events (event_name, event_date) VALUES (?, ?)");
            $stmt->execute([$event_name, $event_date]);
            $new_event_id = $conn->lastInsertId();
            
            $stmt = $conn->prepare("INSERT INTO summary (event_id) VALUES (?)");
            $stmt->execute([$new_event_id]);
            
            $message = 'สร้างงาน Event สำเร็จ';
            $message_type = 'success';
        }
    } elseif (isset($_POST['update_event'])) {
        $event_id = $_POST['event_id'] ?? 0;
        $event_name = trim($_POST['event_name'] ?? '');
        $event_date = $_POST['event_date'] ?? null;
        if (!empty($event_name) && $event_id > 0) {
            $stmt = $conn->prepare("UPDATE events SET event_name = ?, event_date = ? WHERE id = ?");
            $stmt->execute([$event_name, $event_date, $event_id]);
            $message = 'แก้ไขงาน Event สำเร็จ';
            $message_type = 'success';
        }
    } elseif (isset($_POST['delete_event'])) {
        $event_id = $_POST['event_id'] ?? 0;
        if ($event_id > 0) {
            $stmt = $conn->prepare("DELETE FROM attendees WHERE event_id = ?");
            $stmt->execute([$event_id]);
            $stmt = $conn->prepare("DELETE FROM summary WHERE event_id = ?");
            $stmt->execute([$event_id]);
            $stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
            $stmt->execute([$event_id]);
            $message = 'ลบงาน Event สำเร็จ';
            $message_type = 'success';
        }
    } elseif (isset($_POST['select_event'])) {
        $_SESSION['event_id'] = $_POST['event_id'];
        header('Location: index.php');
        exit;
    }
}

$stmt = $conn->query("SELECT * FROM events ORDER BY id DESC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);

$edit_event = null;
if (isset($_GET['edit'])) {
    $edit_id = $_GET['edit'];
    $stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
    $stmt->execute([$edit_id]);
    $edit_event = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จัดการงาน Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { font-family: 'Kanit', sans-serif; background: #e9ecef; }
        .main-card { background: white; border-radius: 15px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); overflow: hidden; }
        .main-card .card-header { background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%); color: white; padding: 20px 25px; }
        .main-card .card-body { padding: 25px; }
        .event-card { background: white; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); padding: 20px; margin-bottom: 15px; transition: all 0.3s ease; }
        .event-card:hover { transform: translateY(-3px); box-shadow: 0 5px 20px rgba(0,0,0,0.15); }
        .btn-action { width: 40px; height: 40px; padding: 0; display: inline-flex; align-items: center; justify-content: center; border-radius: 8px; }
    </style>
</head>
<body>
    <div class="container-fluid py-3 px-4">
        <div class="text-center mb-3">
            <img src="img/logo/logo text-01.png" alt="Logo" style="height: 60px;">
        </div>
        
        <div class="main-card">
            <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h2 class="m-0">📅 จัดการงาน Event</h2>
                <div>
                    <a href="index.php" class="btn btn-info btn-sm">🏠 กลับหน้าหลัก</a>
                </div>
            </div>
            
            <div class="card-body">
                <?php if ($message): ?>
                    <div class="alert alert-<?= $message_type === 'success' ? 'success' : 'danger' ?> alert-dismissible fade show" role="alert">
                        <?= $message ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>
                
                <div class="row">
                    <div class="col-lg-5 mb-4">
                        <div class="card">
                            <div class="card-header bg-primary text-white">
                                <h5 class="mb-0"><i class="bi bi-plus-circle"></i> <?= $edit_event ? 'แก้ไขงาน Event' : 'สร้างงานใหม่' ?></h5>
                            </div>
                            <div class="card-body">
                                <form method="POST">
                                    <?php if ($edit_event): ?>
                                        <input type="hidden" name="event_id" value="<?= $edit_event['id'] ?>">
                                        <input type="hidden" name="update_event" value="1">
                                    <?php else: ?>
                                        <input type="hidden" name="create_event" value="1">
                                    <?php endif; ?>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">ชื่องาน Event</label>
                                        <input type="text" class="form-control" name="event_name" 
                                               value="<?= $edit_event ? htmlspecialchars($edit_event['event_name']) : '' ?>" 
                                               placeholder="กรุณากรอกชื่องาน" required>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label class="form-label">วันที่จัดงาน</label>
                                        <input type="date" class="form-control" name="event_date" 
                                               value="<?= $edit_event ? $edit_event['event_date'] : '' ?>">
                                    </div>
                                    
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle"></i> <?= $edit_event ? 'บันทึกการแก้ไข' : 'สร้างงาน' ?>
                                        </button>
                                        <?php if ($edit_event): ?>
                                            <a href="manage_event.php" class="btn btn-secondary">ยกเลิก</a>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-lg-7">
                        <div class="card">
                            <div class="card-header bg-secondary text-white">
                                <h5 class="mb-0"><i class="bi bi-calendar3"></i> รายการงาน Event (<?= count($events) ?>)</h5>
                            </div>
                            <div class="card-body">
                                <?php if (empty($events)): ?>
                                    <div class="text-center text-muted py-4">
                                        <i class="bi bi-calendar-x" style="font-size: 3rem;"></i>
                                        <p class="mt-2">ยังไม่มีงาน Event</p>
                                    </div>
                                <?php else: ?>
                                    <div class="table-responsive">
                                        <table class="table table-hover" id="eventTable">
                                            <thead class="table-dark">
                                                <tr>
                                                    <th class="text-center" style="width:60px">ลำดับ</th>
                                                    <th>ชื่องาน</th>
                                                    <th>วันที่จัดงาน</th>
                                                    <th>วันที่สร้าง</th>
                                                    <th class="text-center" style="width:180px">จัดการ</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($events as $index => $event): ?>
                                                <tr>
                                                    <td class="text-center"><?= $index + 1 ?></td>
                                                    <td><strong><?= htmlspecialchars($event['event_name']) ?></strong></td>
                                                    <td><?= $event['event_date'] ? date('d/m/Y', strtotime($event['event_date'])) : '-' ?></td>
                                                    <td><?= date('d/m/Y H:i', strtotime($event['created_at'])) ?></td>
                                                    <td class="text-center">
                                                        <form method="POST" class="d-inline">
                                                            <input type="hidden" name="event_id" value="<?= $event['id'] ?>">
                                                            <button type="submit" name="select_event" class="btn btn-success btn-action" title="เข้าสู่งาน">
                                                                <i class="bi bi-box-arrow-in-right"></i>
                                                            </button>
                                                        </form>
                                                        <a href="?edit=<?= $event['id'] ?>" class="btn btn-warning btn-action" title="แก้ไข">
                                                            <i class="bi bi-pencil"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-danger btn-action" title="ลบ" 
                                                                onclick="confirmDelete(<?= $event['id'] ?>, '<?= htmlspecialchars($event['event_name']) ?>')">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">ยืนยันการลบ</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>คุณต้องการลบงาน Event <strong id="deleteEventName"></strong> ใช่หรือไม่?</p>
                    <p class="text-danger"><i class="bi bi-exclamation-triangle"></i> คำเตือน: ข้อมูลทั้งหมดในงานนี้จะถูกลบถาวร</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <form method="POST">
                        <input type="hidden" name="event_id" id="deleteEventId">
                        <button type="submit" name="delete_event" class="btn btn-danger">ลบ</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#eventTable').DataTable({
                language: { url: 'https://cdn.datatables.net/plug-ins/1.13.6/i18n/th.json' },
                order: [[0, 'desc']]
            });
        });

        function confirmDelete(id, name) {
            document.getElementById('deleteEventId').value = id;
            document.getElementById('deleteEventName').textContent = name;
            new bootstrap.Modal(document.getElementById('deleteModal')).show();
        }
    </script>
</body>
</html>