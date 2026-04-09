<?php
date_default_timezone_set("Asia/Bangkok");
require_once 'config/connect_db.php';

$conn->exec("SET FOREIGN_KEY_CHECKS = 0");
$conn->exec("TRUNCATE TABLE attendees");
$conn->exec("TRUNCATE TABLE summary");
$conn->exec("TRUNCATE TABLE events");
$conn->exec("SET FOREIGN_KEY_CHECKS = 1");

echo "<h3>✅ ลบข้อมูลทั้งหมดสำเร็จ!</h3>";
echo "<p>ลบข้อมูล: attendees, summary, events</p>";
echo "<a href='index.php' class='btn btn-primary'>ไปหน้าบันทึกข้อมูล</a>";