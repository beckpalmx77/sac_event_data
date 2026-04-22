<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'config/connect_db.php';

$event_id = $_GET['event_id'] ?? 0;
$type = $_GET['type'] ?? 'shop';

if (!$event_id) {
    $stmt = $conn->query("SELECT id FROM events ORDER BY id DESC LIMIT 1");
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    $event_id = $event['id'] ?? 0;
}

header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="export_' . $type . '_' . date('Ymd His') . '.csv"');

$output = fopen('php://output', 'w');
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

$columns = [
    'ลำดับ' => 'total_no',
    'เซลส์' => 'sales_name',
    'ลำดับในเซลส์' => 'order_no',
    'รายชื่อ' => 'shop_name',
    'ประเภท' => 'type',
    'จังหวัด' => 'province',
    'หมายเหตุ' => 'note',
    'คน (ลงทะเบียน)' => 'participants_before',
    'คน (มาจริง)' => 'participants_after',
    'จองห้องพัก' => 'reserve_room',
    'ใช้ห้องจริง' => 'used_room',
    'จอง 40' => 'tire_40_before',
    'จอง 80' => 'tire_80_before',
    'จอง 120' => 'tire_120_before',
    'จอง 200' => 'tire_200_before',
    'จอง 300' => 'tire_300_before',
    'จอง 600' => 'tire_600_before',
    'จองจริง 40' => 'tire_40_after',
    'จองจริง 80' => 'tire_80_after',
    'จองจริง 120' => 'tire_120_after',
    'จองจริง 200' => 'tire_200_after',
    'จองจริง 300' => 'tire_300_after',
    'จองจริง 600' => 'tire_600_after',
    'ห้องพัก' => 'room_att',
    'ล่องเรือ' => 'ship_att',
    'งานเลี้ยงเย็น' => 'night_att',
 ];

$header = array_keys($columns);
fputcsv($output, $header);

$stmt = $conn->prepare("SELECT * FROM attendees WHERE event_id = ? AND type = ? ORDER BY id");
$stmt->execute([$event_id, $type]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $row) {
    $data = [];
    foreach ($columns as $col) {
        $val = $row[$col] ?? '';
        if ($col === 'type') {
            $val = ($val === 'user') ? 'ผู้ใช้' : 'ร้านค้า';
        }
        if (is_numeric($val) && $val == 0) {
            $val = '';
        }
        $data[] = $val;
    }
    fputcsv($output, $data);
}

$stmt = $conn->query("SELECT * FROM events WHERE id = $event_id");
$event = $stmt->fetch(PDO::FETCH_ASSOC);
$eventName = $event['event_name'] ?? '';

$totals = ['รวม', '', '', count($rows), '', '', '', 
    array_sum(array_column($rows, 'participants_before')),
    array_sum(array_column($rows, 'participants_after')),
    array_sum(array_column($rows, 'reserve_room')),
    array_sum(array_column($rows, 'used_room')),
    array_sum(array_column($rows, 'tire_40_before')),
    array_sum(array_column($rows, 'tire_80_before')),
    array_sum(array_column($rows, 'tire_120_before')),
    array_sum(array_column($rows, 'tire_200_before')),
    array_sum(array_column($rows, 'tire_300_before')),
    array_sum(array_column($rows, 'tire_600_before')),
    array_sum(array_column($rows, 'tire_40_after')),
    array_sum(array_column($rows, 'tire_80_after')),
    array_sum(array_column($rows, 'tire_120_after')),
    array_sum(array_column($rows, 'tire_200_after')),
    array_sum(array_column($rows, 'tire_300_after')),
    array_sum(array_column($rows, 'tire_600_after')),
    array_sum(array_column($rows, 'room_att')),
    array_sum(array_column($rows, 'ship_att')),
    array_sum(array_column($rows, 'night_att'))
];
fputcsv($output, $totals);

fclose($output);