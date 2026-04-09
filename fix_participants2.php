<?php
date_default_timezone_set("Asia/Bangkok");
require_once 'config/connect_db.php';

echo "<h3>กำลัง update participants ให้เป็นจำนวนคน (ไม่ใช่ยาง)...</h3>";

$count = 0;
$stmt = $conn->query("SELECT id, tire_40_before, tire_80_before, tire_120_before, tire_200_before, tire_300_before, tire_600_before FROM attendees WHERE type = 'user'");
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $total_tires = $row['tire_40_before'] + $row['tire_80_before'] + $row['tire_120_before'] + $row['tire_200_before'] + $row['tire_300_before'] + $row['tire_600_before'];
    $persons = ($total_tires > 0) ? 1 : 0;
    
    $stmt2 = $conn->prepare("UPDATE attendees SET participants_before = ?, participants_after = ? WHERE id = ?");
    $stmt2->execute([$persons, $persons, $row['id']]);
    $count++;
}

echo "<p>Updated $count records</p>";

$stmt = $conn->query("
    SELECT 
        SUM(participants_before) as p_before,
        SUM(participants_after) as p_after,
        SUM(tire_40_before + tire_80_before + tire_120_before + tire_200_before + tire_300_before + tire_600_before) as tire_total
    FROM attendees WHERE type = 'user'
");
$r = $stmt->fetch(PDO::FETCH_ASSOC);
echo "<hr>";
echo "<p>คน (จอง): " . $r['p_before'] . "</p>";
echo "<p>คน (มาจริง): " . $r['p_after'] . "</p>";
echo "<p>จองยาง: " . $r['tire_total'] . "</p>";

$shopPct = $r['p_before'] > 0 ? round($r['p_after'] / $r['p_before'] * 100) : 0;
echo "<p>% มาจริง: $shopPct%</p>";

echo "<hr>";
echo "<a href='index.php'>กลับหน้าบันทึกข้อมูล</a>";