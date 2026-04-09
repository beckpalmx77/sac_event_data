<?php
date_default_timezone_set("Asia/Bangkok");
require_once 'config/connect_db.php';

if (!isset($_GET['confirm']) || $_GET['confirm'] !== 'yes') {
    echo "<h3>Update ข้อมูลผู้ใช้จาก sac_customer_event.csv</h3>";
    echo "<p>จะ update ข้อมูล type='user' โดย match ด้วย shop_name</p>";
    echo "<p>ช่องว่างจะถูกเปลี่ยนเป็น 0</p>";
    echo "<a href='?confirm=yes' class='btn btn-primary'>ยืนยัน Update</a>";
    exit;
}

$file = 'sac_customer_event.csv';
$rows = array_map('str_getcsv', file($file));
array_shift($rows);

$updated = 0;
$notFound = [];

foreach ($rows as $row) {
    $shopName = isset($row[0]) ? trim($row[0]) : '';
    if (empty($shopName)) continue;
    
    $tire_40 = isset($row[1]) ? intval(trim(str_replace(' ', '', $row[1]))) : 0;
    $tire_80 = isset($row[2]) ? intval(trim(str_replace(' ', '', $row[2]))) : 0;
    $tire_120 = isset($row[3]) ? intval(trim(str_replace(' ', '', $row[3]))) : 0;
    $tire_200 = isset($row[4]) ? intval(trim(str_replace(' ', '', $row[4]))) : 0;
    $tire_300 = isset($row[5]) ? intval(trim(str_replace(' ', '', $row[5]))) : 0;
    $tire_600 = isset($row[6]) ? intval(trim(str_replace(' ', '', $row[6]))) : 0;
    
    if ($tire_40 == 0 && $tire_80 == 0 && $tire_120 == 0 && $tire_200 == 0 && $tire_300 == 0 && $tire_600 == 0) {
        continue;
    }
    
    $stmt = $conn->prepare("
        UPDATE attendees SET 
            tire_40_before = ?, tire_80_before = ?, tire_120_before = ?, tire_200_before = ?, tire_300_before = ?, tire_600_before = ?,
            tire_40_after = ?, tire_80_after = ?, tire_120_after = ?, tire_200_after = ?, tire_300_after = ?, tire_600_after = ?
        WHERE shop_name = ? AND type = 'user'
    ");
    $stmt->execute([
        $tire_40, $tire_80, $tire_120, $tire_200, $tire_300, $tire_600,
        $tire_40, $tire_80, $tire_120, $tire_200, $tire_300, $tire_600,
        $shopName
    ]);
    
    if ($stmt->rowCount() > 0) {
        $updated++;
    } else {
        $notFound[] = $shopName;
    }
}

echo "<h3>✅ Update สำเร็จ!</h3>";
echo "<p>Updated: $updated รายการ</p>";
if (count($notFound) > 0) {
    echo "<p>ไม่พบ: " . count($notFound) . " รายการ</p>";
    echo "<ul>" . implode(', ', array_slice($notFound, 0, 10)) . "...</ul>";
}
echo "<a href='index.php' class='btn btn-primary'>กลับหน้าบันทึกข้อมูล</a>";