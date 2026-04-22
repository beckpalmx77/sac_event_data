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
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .event-card {
            background: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
            overflow: hidden;
            max-width: 500px;
            width: 100%;
        }
        .event-header {
            background: linear-gradient(135deg, #ffffff 0%, #babfc7 100%);
            padding: 30px;
            text-align: center;
        }
        .event-header img {
            max-width: 160px;
            height: auto;
        }
        .event-header h2 {
            color: white;
            font-weight: 600;
            margin-top: 15px;
            margin-bottom: 0;
            font-size: 1.4rem;
        }
        .event-body {
            padding: 30px;
        }
        .btn-create {
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 20px;
            font-weight: 600;
            color: white;
            transition: all 0.3s ease;
        }
        .btn-create:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(56, 239, 125, 0.4);
        }
        .event-btn {
            border-radius: 12px;
            padding: 15px 20px;
            transition: all 0.3s ease;
            border: 2px solid #e0e0e0;
            background: #f8f9fa;
        }
        .event-btn:hover {
            border-color: #2a5298;
            background: #e8f4ff;
            transform: translateX(5px);
        }
        .logout-btn {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="event-card">
        <div class="event-header">
            <img src="img/logo/logo text-01.png" alt="Logo">
            <h2>เลือกงาน Event</h2>
        </div>
        
<div class="event-body">
                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 text-primary">สร้างงานใหม่</h5>
                        <?php if ($_SESSION['role'] === 'admin'): ?>
                        <a href="manage_event.php" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-gear"></i> จัดการงาน
                        </a>
                        <?php endif; ?>
                    </div>
                <form method="POST">
                    <input type="hidden" name="create_event" value="1">
                    <div class="input-group">
                        <input type="text" class="form-control" name="event_name" placeholder="ชื่องาน Event" required>
                        <button class="btn btn-create" type="submit">
                            <i class="bi bi-plus-circle"></i> สร้าง
                        </button>
                    </div>
                </form>
            </div>
            
            <div>
                <h5 class="mb-3 text-secondary">งานที่มีอยู่</h5>
                <?php if (empty($events)): ?>
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-calendar-x" style="font-size: 2rem;"></i>
                        <p class="mb-0 mt-2">ยังไม่มีงาน Event</p>
                    </div>
                <?php else: ?>
                    <form method="POST">
                        <input type="hidden" name="select_event" value="1">
                        <div class="d-grid gap-2">
                            <?php foreach ($events as $event): ?>
                            <button type="submit" name="event_id" value="<?= $event['id'] ?>" class="btn event-btn text-start">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong>📅 <?= htmlspecialchars($event['event_name']) ?></strong>
                                        <div class="text-muted small">
                                            <?php if ($event['event_date']): ?>
                                                📆 วันที่จัด: <?= date('d/m/Y', strtotime($event['event_date'])) ?>
                                            <?php else: ?>
                                                <?= $event['created_at'] ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <span class="text-primary">เข้า →</span>
                                </div>
                            </button>
                            <?php endforeach; ?>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
            
            <div class="mt-4 text-center">
                <?php if ($_SESSION['role'] === 'admin'): ?>
                <a href="manage_event.php" class="btn btn-outline-primary logout-btn me-2">
                    <i class="bi bi-gear"></i> จัดการงาน Event
                </a>
                <?php endif; ?>
                <a href="logout.php" class="btn btn-outline-danger logout-btn">
                    <i class="bi bi-box-arrow-left"></i> ออกจากระบบ
                </a>
            </div>
        </div>
    </div>
</body>
</html>