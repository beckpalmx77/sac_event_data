<?php
date_default_timezone_set("Asia/Bangkok");
require_once 'config/connect_db.php';

echo "<h3>เพิ่ม column used_room �และ total_used_room...</h3>";

try {
    $conn->exec("ALTER TABLE attendees ADD COLUMN used_room TINYINT(1) DEFAULT 0 AFTER use_room");
    echo "<p>✓ เพิ่ม used_room ใน attendees</p>";
} catch (PDOException $e) {
    echo "<p>attendees: " . $e->getMessage() . "</p>";
}

try {
    $conn->exec("ALTER TABLE summary ADD COLUMN total_used_room INT DEFAULT 0 AFTER total_use_room");
    echo "<p>✓ เพิ่ม total_used_room ใน summary</p>";
} catch (PDOException $e) {
    echo "<p>summary: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<a href='index.php'>กลับหน้าบันทึกข้อมูล</a>";