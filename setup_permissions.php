<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login');
    exit;
}
date_default_timezone_set("Asia/Bangkok");
require_once 'config/connect_db.php';

$sql = file_get_contents('create_permission_tables.sql');
$statements = array_filter(array_map('trim', explode(';', $sql)));

echo '<!DOCTYPE html><html lang="th"><head><meta charset="UTF-8">';
echo '<title>ติดตั้งระบบสิทธิ์</title>';
echo '<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">';
echo '<link href="https://fonts.googleapis.com/css2?family=Kanit:wght@400;600&display=swap" rel="stylesheet">';
echo '<style>body{font-family:"Kanit",sans-serif;padding:20px}</style></head><body>';
echo '<div class="container"><h2>📋 ติดตั้งระบบสิทธิ์การเข้าใช้งาน</h2>';

$hasError = false;
foreach ($statements as $statement) {
    if (!empty($statement)) {
        try {
            $conn->exec($statement);
            echo '<div class="alert alert-success py-2">✓ ' . htmlspecialchars(mb_substr($statement, 0, 80)) . '...</div>';
        } catch (PDOException $e) {
            echo '<div class="alert alert-danger py-2">⚠ ' . htmlspecialchars($e->getMessage()) . '</div>';
            $hasError = true;
        }
    }
}

if (!$hasError) {
    echo '<div class="alert alert-success"><h4>✅ ติดตั้งระบบสิทธิ์สำเร็จ!</h4>';
    echo '<p>สร้างตาราง permissions และ user_permissions เรียบร้อย</p>';
    echo '<p>สิทธิ์ทั้งหมดถูกมอบให้ผู้ดูแล (admin) อัตโนมัติ</p></div>';
}

echo '<a href="manage_permission" class="btn btn-primary">ไปจัดการสิทธิ์</a> ';
echo '<a href="main" class="btn btn-secondary">กลับหน้าหลัก</a>';
echo '</div></body></html>';
