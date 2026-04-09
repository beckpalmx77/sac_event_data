<?php
date_default_timezone_set("Asia/Bangkok");
require_once 'config/connect_db.php';

$stmt = $conn->query("
    SELECT 
        COUNT(*) as total_user,
        COALESCE(SUM(tire_40_before), 0) as tire_40,
        COALESCE(SUM(tire_80_before), 0) as tire_80,
        COALESCE(SUM(tire_120_before), 0) as tire_120,
        COALESCE(SUM(tire_200_before), 0) as tire_200,
        COALESCE(SUM(tire_300_before), 0) as tire_300,
        COALESCE(SUM(tire_600_before), 0) as tire_600
    FROM attendees WHERE type = 'user'
");
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$totalTire = $result['tire_40'] + $result['tire_80'] + $result['tire_120'] + $result['tire_200'] + $result['tire_300'] + $result['tire_600'];

echo "<h3>ข้อมูล type = user</h3>";
echo "<p>จำนวนผู้ใช้: " . $result['total_user'] . "</p>";
echo "<p>ยาง 40: " . $result['tire_40'] . "</p>";
echo "<p>ยาง 80: " . $result['tire_80'] . "</p>";
echo "<p>ยาง 120: " . $result['tire_120'] . "</p>";
echo "<p>ยาง 200: " . $result['tire_200'] . "</p>";
echo "<p>ยาง 300: " . $result['tire_300'] . "</p>";
echo "<p>ยาง 600: " . $result['tire_600'] . "</p>";
echo "<hr>";
echo "<p>รวมจองยาง: $totalTire</p>";
echo "<hr>";

echo "<h4>รายชื่อผู้ใช้ที่มียาง</h4>";
$stmt = $conn->query("
    SELECT shop_name, tire_40_before, tire_80_before, tire_120_before, tire_200_before, tire_300_before, tire_600_before
    FROM attendees WHERE type = 'user' AND (
        tire_40_before > 0 OR tire_80_before > 0 OR tire_120_before > 0 OR 
        tire_200_before > 0 OR tire_300_before > 0 OR tire_600_before > 0
    )
    ORDER BY shop_name
");
echo "<table border='1'>";
echo "<tr><th>shop_name</th><th>40</th><th>80</th><th>120</th><th>200</th><th>300</th><th>600</th></tr>";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $tireTotal = $row['tire_40_before'] + $row['tire_80_before'] + $row['tire_120_before'] + $row['tire_200_before'] + $row['tire_300_before'] + $row['tire_600_before'];
    echo "<tr>";
    echo "<td>" . $row['shop_name'] . "</td>";
    echo "<td>" . $row['tire_40_before'] . "</td>";
    echo "<td>" . $row['tire_80_before'] . "</td>";
    echo "<td>" . $row['tire_120_before'] . "</td>";
    echo "<td>" . $row['tire_200_before'] . "</td>";
    echo "<td>" . $row['tire_300_before'] . "</td>";
    echo "<td>" . $row['tire_600_before'] . "</td>";
    echo "</tr>";
}
echo "</table>";