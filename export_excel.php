<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'config/connect_db.php';

$event_id = $_GET['event_id'] ?? 0;
if (!$event_id) {
    $stmt = $conn->query("SELECT id FROM events ORDER BY id DESC LIMIT 1");
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    $event_id = $event['id'] ?? 0;
}

$eventName = 'Event Data';
$stmt = $conn->query("SELECT event_name FROM events WHERE id = $event_id");
$event = $stmt->fetch(PDO::FETCH_ASSOC);
if ($event) {
    $eventName = $event['event_name'];
}

header('Content-Type: application/vnd.ms-excel');
header('Content-Disposition: attachment; filename="export_' . str_replace(' ', '_', $eventName) . '_' . date('Ymd') . '.xls"');
header('Pragma: no-cache');
header('Expires: 0');

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

function getSheetData($type, $conn, $columns, $event_id) {
    $stmt = $conn->prepare("SELECT * FROM attendees WHERE event_id = ? AND type = ? ORDER BY total_no");
    $stmt->execute([$event_id, $type]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    $data = [];
    $header = array_keys($columns);
    $data[] = $header;
    
    foreach ($rows as $row) {
        $line = [];
        foreach ($columns as $col) {
            $val = $row[$col] ?? '';
            if ($col === 'type') {
                $val = ($val === 'user') ? 'ผู้ใช้' : 'ร้านค้า';
            }
            if (is_numeric($val) && $val == 0) {
                $val = '';
            }
            $line[] = $val;
        }
        $data[] = $line;
    }
    
    if (count($rows) > 0) {
        $shopRows = count($rows);
        $totalRow = [];
        foreach ($columns as $k => $v) {
            if ($k === 'ลำดับ') {
                $totalRow[] = 'รวม';
            } elseif ($k === 'รายชื่อ') {
                $totalRow[] = $shopRows . ' รายการ';
            } elseif (in_array($v, ['sales_name', 'order_no', 'type', 'province', 'note'])) {
                $totalRow[] = '';
            } else {
                $totalRow[] = array_sum(array_column($rows, $v));
            }
        }
        $data[] = $totalRow;
    }
    
    return $data;
}

function xmlEncode($str) {
    return htmlspecialchars($str, ENT_XML1, 'UTF-8');
}

$shopData = getSheetData('shop', $conn, $columns, $event_id);
$userData = getSheetData('user', $conn, $columns, $event_id);

echo '<?xml version="1.0"?>
<?mso-application progid="Excel.Sheet"?>
<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"
 xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet">
 <Styles>
  <Style ss:ID="Header">
   <Font ss:Bold="1"/>
   <Interior ss:Color="#CCCCCC" ss:Pattern="Solid"/>
  </Style>
  <Style ss:ID="Total">
   <Font ss:Bold="1"/>
   <Interior ss:Color="#FFFF00" ss:Pattern="Solid"/>
  </Style>
 </Styles>
 <Worksheet ss:Name="ร้านค้า">
  <Table>';

foreach ($shopData as $i => $row) {
    $styleId = ($i == 0) ? 'Header' : (($i == count($shopData)-1) ? 'Total' : '');
    echo '<Row ss:StyleID="' . $styleId . '">';
    foreach ($row as $cell) {
        echo '<Cell><Data ss:Type="String">' . xmlEncode($cell) . '</Data></Cell>';
    }
    echo '</Row>';
}

echo '  </Table>
 </Worksheet>
 <Worksheet ss:Name="ผู้ใช้">
  <Table>';

foreach ($userData as $i => $row) {
    $styleId = ($i == 0) ? 'Header' : (($i == count($userData)-1) ? 'Total' : '');
    echo '<Row ss:StyleID="' . $styleId . '">';
    foreach ($row as $cell) {
        echo '<Cell><Data ss:Type="String">' . xmlEncode($cell) . '</Data></Cell>';
    }
    echo '</Row>';
}

echo '  </Table>
 </Worksheet>
</Workbook>';