<?php
date_default_timezone_set("Asia/Bangkok");
require_once 'config/connect_db.php';

echo "<h3>เปลี่ยนชื่อ column use_room → reserve_room...</h3>";

try {
    $conn->exec("ALTER TABLE attendees CHANGE COLUMN use_room reserve_room TINYINT(1) DEFAULT 0");
    echo "<p>✓ เปลี่ยน use_room → reserve_room ใน attendees</p>";
} catch (PDOException $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

try {
    $conn->exec("ALTER TABLE summary CHANGE COLUMN total_use_room total_reserve_room INT DEFAULT 0");
    echo "<p>✓ เปลี่ยน total_use_room → total_reserve_room ใน summary</p>";
} catch (PDOException $e) {
    echo "<p>Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<a href='index.php'>กลับหน้าบันทึกข้อมูล</a>";