<?php
date_default_timezone_set("Asia/Bangkok");
require_once 'config/connect_db.php';

if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'yes') {
    echo "<h3>กรุณาตรวจสอบก่อน import</h3>";
    echo "<p>จะ import ข้อมูลจาก data_shop1.csv (ร้านค้า) และ data_user2.csv (ผู้ใช้)</p>";
    echo "<a href='?confirm=yes' class='btn btn-primary'>ยืนยัน Import</a>";
    exit;
}

$conn->exec("SET FOREIGN_KEY_CHECKS = 0");
$conn->exec("TRUNCATE TABLE attendees");
$conn->exec("TRUNCATE TABLE summary");
$conn->exec("TRUNCATE TABLE events");
$conn->exec("SET FOREIGN_KEY_CHECKS = 1");

$eventName = 'งาน Event';
$stmt = $conn->prepare("INSERT INTO events (event_name) VALUES (?)");
$stmt->execute([$eventName]);
$event_id = $conn->lastInsertId();

$stmt = $conn->prepare("INSERT INTO summary (event_id) VALUES (?)");
$stmt->execute([$event_id]);

function importCSV($file, $type) {
    global $conn, $event_id;
    
    $rows = array_map('str_getcsv', file($file));
    array_shift($rows);
    
    $imported = 0;
    $salesName = '';
    
    foreach ($rows as $row) {
        if (empty(trim($row[4] ?? ''))) continue;
        
        $salesName = trim($row[0]) ?: $salesName;
        $orderNo = isset($row[1]) ? intval(trim($row[1])) : 0;
        $totalNo = isset($row[2]) ? intval(trim($row[2])) : 0;
        $shopName = isset($row[4]) ? trim($row[4]) : '';
        $province = isset($row[5]) ? trim($row[5]) : '';
        $note = isset($row[6]) ? trim($row[6]) : '';
        $participants = isset($row[7]) ? intval(trim($row[7])) : 0;
        $useRoom = isset($row[8]) ? intval(trim($row[8])) : 0;
        
        $tire_40 = isset($row[9]) ? intval(trim(str_replace(' ', '', $row[9]))) : 0;
        $tire_80 = isset($row[10]) ? intval(trim(str_replace(' ', '', $row[10]))) : 0;
        $tire_120 = isset($row[11]) ? intval(trim(str_replace(' ', '', $row[11]))) : 0;
        $tire_200 = isset($row[12]) ? intval(trim($row[12])) : 0;
        $tire_300 = isset($row[13]) ? intval(trim(str_replace(' ', '', $row[13]))) : 0;
        $tire_600 = isset($row[14]) ? intval(trim(str_replace(' ', '', $row[14]))) : 0;
        
        $stmt = $conn->prepare("
            INSERT INTO attendees (
                event_id, sales_name, order_no, total_no, shop_name, type, province, note,
                participants_before, participants_after, use_room,
                tire_40_before, tire_80_before, tire_120_before, tire_200_before, tire_300_before, tire_600_before,
                tire_40_after, tire_80_after, tire_120_after, tire_200_after, tire_300_after, tire_600_after
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $event_id, $salesName, $orderNo, $totalNo, $shopName, $type, $province, $note,
            $participants, $participants, $useRoom,
            $tire_40, $tire_80, $tire_120, $tire_200, $tire_300, $tire_600,
            $tire_40, $tire_80, $tire_120, $tire_200, $tire_300, $tire_600
        ]);
        
        $imported++;
    }
    
    return $imported;
}

$shopCount = importCSV('data_shop1.csv', 'shop');
$userCount = importCSV('data_user2.csv', 'user');

$stmt = $conn->query("
    SELECT 
        COUNT(*) as total_shops,
        SUM(CASE WHEN type = 'shop' THEN 1 ELSE 0 END) as shop_count,
        SUM(CASE WHEN type = 'user' THEN 1 ELSE 0 END) as user_count,
        COALESCE(SUM(participants_before), 0) as total_participants_before,
        COALESCE(SUM(participants_after), 0) as total_participants_after,
        COALESCE(SUM(use_room), 0) as total_use_room,
        COALESCE(SUM(tire_40_before), 0) as total_tire_40_before,
        COALESCE(SUM(tire_80_before), 0) as total_tire_80_before,
        COALESCE(SUM(tire_120_before), 0) as total_tire_120_before,
        COALESCE(SUM(tire_200_before), 0) as total_tire_200_before,
        COALESCE(SUM(tire_300_before), 0) as total_tire_300_before,
        COALESCE(SUM(tire_600_before), 0) as total_tire_600_before,
        COALESCE(SUM(tire_40_after), 0) as total_tire_40_after,
        COALESCE(SUM(tire_80_after), 0) as total_tire_80_after,
        COALESCE(SUM(tire_120_after), 0) as total_tire_120_after,
        COALESCE(SUM(tire_200_after), 0) as total_tire_200_after,
        COALESCE(SUM(tire_300_after), 0) as total_tire_300_after,
        COALESCE(SUM(tire_600_after), 0) as total_tire_600_after
    FROM attendees WHERE event_id = $event_id
");
$result = $stmt->fetch(PDO::FETCH_ASSOC);

$total_tire_before = $result['total_tire_40_before'] + $result['total_tire_80_before'] + $result['total_tire_120_before'] 
                   + $result['total_tire_200_before'] + $result['total_tire_300_before'] + $result['total_tire_600_before'];
$total_tire_after = $result['total_tire_40_after'] + $result['total_tire_80_after'] + $result['total_tire_120_after'] 
                  + $result['total_tire_200_after'] + $result['total_tire_300_after'] + $result['total_tire_600_after'];

$stmt = $conn->prepare("
    UPDATE summary SET 
        total_shops = ?, 
        total_participants_before = ?, total_participants_after = ?, 
        total_use_room = ?,
        total_tire_40_before = ?, total_tire_40_after = ?,
        total_tire_80_before = ?, total_tire_80_after = ?,
        total_tire_120_before = ?, total_tire_120_after = ?,
        total_tire_200_before = ?, total_tire_200_after = ?,
        total_tire_300_before = ?, total_tire_300_after = ?,
        total_tire_600_before = ?, total_tire_600_after = ?,
        total_tire_before = ?, total_tire_after = ?
    WHERE event_id = ?
");

$stmt->execute([
    $result['total_shops'], 
    $result['total_participants_before'], $result['total_participants_after'],
    $result['total_use_room'],
    $result['total_tire_40_before'], $result['total_tire_40_after'],
    $result['total_tire_80_before'], $result['total_tire_80_after'],
    $result['total_tire_120_before'], $result['total_tire_120_after'],
    $result['total_tire_200_before'], $result['total_tire_200_after'],
    $result['total_tire_300_before'], $result['total_tire_300_after'],
    $result['total_tire_600_before'], $result['total_tire_600_after'],
    $total_tire_before, $total_tire_after, $event_id
]);

echo "<h3>✅ Import สำเร็จ!</h3>";
echo "<p>ร้านค้า: $shopCount รายการ | ผู้ใช้: $userCount รายการ | รวม: " . ($shopCount + $userCount) . " รายการ</p>";
echo "<a href='index.php' class='btn btn-primary'>ไปหน้าบันทึกข้อมูล</a> | ";
echo "<a href='dashboard.php' class='btn btn-info'>ไปหน้า Dashboard</a>";