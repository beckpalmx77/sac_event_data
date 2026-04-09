<?php
date_default_timezone_set("Asia/Bangkok");
require_once 'config/connect_db.php';

echo "<h3>กำลัง update participants ให้เป็นจำนวนคน (ทุกคน = 1)...</h3>";

$stmt = $conn->query("UPDATE attendees SET participants_before = 1, participants_after = 1 WHERE type = 'user'");

$stmt = $conn->query("
    SELECT 
        SUM(participants_before) as p_before,
        SUM(participants_after) as p_after
    FROM attendees WHERE type = 'user'
");
$r = $stmt->fetch(PDO::FETCH_ASSOC);
echo "<p>คน (จอง): " . $r['p_before'] . "</p>";
echo "<p>คน (มาจริง): " . $r['p_after'] . "</p>";

$shopPct = $r['p_before'] > 0 ? round($r['p_after'] / $r['p_before'] * 100) : 0;
echo "<p>% มาจริง: $shopPct%</p>";

echo "<hr>";
echo "<a href='index.php'>กลับหน้าบันทึกข้อมูล</a>";