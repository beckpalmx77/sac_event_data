<?php
date_default_timezone_set("Asia/Bangkok");
require_once 'config/connect_db.php';

echo "<h3>Shop (type=shop)</h3>";
$stmt = $conn->query("
    SELECT 
        COUNT(*) as count,
        SUM(participants_before) as p_before,
        SUM(participants_after) as p_after,
        SUM(tire_40_before + tire_80_before + tire_120_before + tire_200_before + tire_300_before + tire_600_before) as tire_before,
        SUM(tire_40_after + tire_80_after + tire_120_after + tire_200_after + tire_300_after + tire_600_after) as tire_after
    FROM attendees WHERE type = 'shop'
");
$shop = $stmt->fetch(PDO::FETCH_ASSOC);
echo "<p>Count: " . $shop['count'] . "</p>";
echo "<p>participants_before: " . $shop['p_before'] . "</p>";
echo "<p>participants_after: " . $shop['p_after'] . "</p>";
echo "<p>tire_before (sum): " . $shop['tire_before'] . "</p>";
echo "<p>tire_after (sum): " . $shop['tire_after'] . "</p>";

echo "<h3>User (type=user)</h3>";
$stmt = $conn->query("
    SELECT 
        COUNT(*) as count,
        SUM(participants_before) as p_before,
        SUM(participants_after) as p_after,
        SUM(tire_40_before + tire_80_before + tire_120_before + tire_200_before + tire_300_before + tire_600_before) as tire_before,
        SUM(tire_40_after + tire_80_after + tire_120_after + tire_200_after + tire_300_after + tire_600_after) as tire_after
    FROM attendees WHERE type = 'user'
");
$user = $stmt->fetch(PDO::FETCH_ASSOC);
echo "<p>Count: " . $user['count'] . "</p>";
echo "<p>participants_before: " . $user['p_before'] . "</p>";
echo "<p>participants_after: " . $user['p_after'] . "</p>";
echo "<p>tire_before (sum): " . $user['tire_before'] . "</p>";
echo "<p>tire_after (sum): " . $user['tire_after'] . "</p>";

echo "<h4>Sample user data</h4>";
$stmt = $conn->query("SELECT shop_name, participants_before, participants_after, tire_40_before, tire_80_before, tire_120_before, tire_200_before, tire_300_before, tire_600_before FROM attendees WHERE type = 'user' LIMIT 5");
echo "<table border='1'>";
echo "<tr><th>shop_name</th><th>p_before</th><th>p_after</th><th>40</th><th>80</th><th>120</th><th>200</th><th>300</th><th>600</th></tr>";
while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    echo "<tr>";
    echo "<td>" . $row['shop_name'] . "</td>";
    echo "<td>" . $row['participants_before'] . "</td>";
    echo "<td>" . $row['participants_after'] . "</td>";
    echo "<td>" . $row['tire_40_before'] . "</td>";
    echo "<td>" . $row['tire_80_before'] . "</td>";
    echo "<td>" . $row['tire_120_before'] . "</td>";
    echo "<td>" . $row['tire_200_before'] . "</td>";
    echo "<td>" . $row['tire_300_before'] . "</td>";
    echo "<td>" . $row['tire_600_before'] . "</td>";
    echo "</tr>";
}
echo "</table>";