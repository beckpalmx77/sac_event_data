<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/connect_db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['select_event'])) {
    $_SESSION['event_id'] = $_POST['event_id'];
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_event'])) {
    $event_name = $_POST['event_name'] ?? '';
    if (!empty($event_name)) {
        $stmt = $conn->prepare("INSERT INTO events (event_name) VALUES (?)");
        $stmt->execute([$event_name]);
        $new_event_id = $conn->lastInsertId();
        
        $stmt = $conn->prepare("INSERT INTO summary (event_id) VALUES (?)");
        $stmt->execute([$new_event_id]);
        
        $_SESSION['event_id'] = $new_event_id;
        header('Location: index.php');
        exit;
    }
}

if (isset($_SESSION['event_id'])) {
    header('Location: index.php');
    exit;
}

$stmt = $conn->query("SELECT * FROM events ORDER BY id DESC");
$events = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เลือกงาน Event</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="img/favicon.ico">
    <style>body{font-family:'Kanit',sans-serif;background:#f8f9fa}</style>
</head>
<body>
    <div class="container py-5">
        <h2 class="text-center mb-4">📋 เลือกงาน Event</h2>
        
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white">สร้างงานใหม่</div>
                    <div class="card-body">
                        <form method="POST">
                            <input type="hidden" name="create_event" value="1">
                            <div class="input-group">
                                <input type="text" class="form-control" name="event_name" placeholder="ชื่องาน Event" required>
                                <button class="btn btn-success" type="submit">สร้าง</button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <div class="card mt-3">
                    <div class="card-header bg-secondary text-white">เลือกงานที่มีอยู่</div>
                    <div class="card-body">
                        <?php if (empty($events)): ?>
                            <p class="text-muted">ยังไม่มีงาน Event</p>
                        <?php else: ?>
                            <form method="POST">
                                <input type="hidden" name="select_event" value="1">
                                <div class="d-grid gap-2">
                                    <?php foreach ($events as $event): ?>
                                    <button type="submit" name="event_id" value="<?= $event['id'] ?>" class="btn btn-outline-primary text-start">
                                        📅 <?= htmlspecialchars($event['event_name']) ?>
                                        <small class="text-muted">(<?= $event['created_at'] ?>)</small>
                                    </button>
                                    <?php endforeach; ?>
                                </div>
                            </form>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="mt-3 text-center">
                    <a href="logout.php" class="btn btn-outline-danger">ออกจากระบบ</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>