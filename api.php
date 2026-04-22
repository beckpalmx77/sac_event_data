<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'config/connect_db.php';

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'get_event':
        getEvent();
        break;
    case 'add_attendee':
        addAttendee();
        break;
    case 'get_attendees':
        getAttendees();
        break;
    case 'get_summary':
        getSummary();
        break;
    case 'get_dashboard_summary':
        getDashboardSummary();
        break;
    case 'get_last_order':
        getLastOrder();
        break;
    case 'update_attendee':
        updateAttendee();
        break;
    case 'delete_attendee':
        deleteAttendee();
        break;
    case 'get_provinces':
        getProvinces();
        break;
    case 'logout':
        logout();
        break;
    default:
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
}

function logout() {
    session_destroy();
    header('Location: login.php');
    exit;
}

function getEvent() {
    global $conn;
    $stmt = $conn->query("SELECT * FROM events ORDER BY id DESC LIMIT 1");
    $event = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$event) {
        $stmt = $conn->query("INSERT INTO events (event_name) VALUES ('งานEvent')");
        $event_id = $conn->lastInsertId();
        $stmt = $conn->query("SELECT * FROM events WHERE id = $event_id");
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stmt = $conn->prepare("INSERT INTO summary (event_id) VALUES (?)");
        $stmt->execute([$event_id]);
    }
    
    echo json_encode($event);
}

function getLastOrder() {
    global $conn;
    $event_id = $_POST['event_id'] ?? 0;
    $sales_name = $_POST['sales_name'] ?? '';
    $type = $_POST['type'] ?? 'shop';
    
    $stmt = $conn->prepare("
        SELECT order_no, total_no 
        FROM attendees 
        WHERE event_id = ? AND sales_name = ? AND type = ?
        ORDER BY id DESC LIMIT 1
    ");
    $stmt->execute([$event_id, $sales_name, $type]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
    $order_no = $result ? $result['order_no'] : 0;
    
    $stmt2 = $conn->prepare("
        SELECT total_no 
        FROM attendees 
        WHERE event_id = ? AND type = ?
        ORDER BY id DESC LIMIT 1
    ");
    $stmt2->execute([$event_id, $type]);
    $result2 = $stmt2->fetch(PDO::FETCH_ASSOC);
    $total_no = $result2 ? $result2['total_no'] : 0;
    
    echo json_encode(['order_no' => $order_no, 'total_no' => $total_no]);
}

function addAttendee() {
    global $conn;
    
    $event_id = $_POST['event_id'] ?? 0;
    $sales_name = $_POST['sales_name'] ?? '';
    $order_no = $_POST['order_no'] ?? 0;
    $total_no = $_POST['total_no'] ?? 0;
    $shop_name = $_POST['shop_name'] ?? '';
    $type = $_POST['type'] ?? 'shop';
    $province = $_POST['province'] ?? '';
    $note = $_POST['note'] ?? '';
    $participants_before = $_POST['participants_before'] ?? 0;
    $participants_after = $_POST['participants_after'] ?? 0;
    $reserve_room = intval($_POST['reserve_room'] ?? 0);
    $used_room = intval($_POST['used_room'] ?? 0);
    
    $tire_40_before = $_POST['tire_40_before'] ?? 0;
    $tire_40_after = $_POST['tire_40_after'] ?? 0;
    $tire_80_before = $_POST['tire_80_before'] ?? 0;
    $tire_80_after = $_POST['tire_80_after'] ?? 0;
    $tire_120_before = $_POST['tire_120_before'] ?? 0;
    $tire_120_after = $_POST['tire_120_after'] ?? 0;
    $tire_200_before = $_POST['tire_200_before'] ?? 0;
    $tire_200_after = $_POST['tire_200_after'] ?? 0;
    $tire_300_before = $_POST['tire_300_before'] ?? 0;
    $tire_300_after = $_POST['tire_300_after'] ?? 0;
    $tire_600_before = $_POST['tire_600_before'] ?? 0;
    $tire_600_after = $_POST['tire_600_after'] ?? 0;
$room_att = intval($_POST['room_att'] ?? 0);
    $ship_att = intval($_POST['ship_att'] ?? 0);
    $night_att = intval($_POST['night_attend'] ?? 0);
    $room_att_after = intval($_POST['room_att_after'] ?? 0);
    $ship_att_after = intval($_POST['ship_att_after'] ?? 0);
    $night_att_after = intval($_POST['night_att_after'] ?? 0);
    
    try {
        $stmt = $conn->prepare("
            INSERT INTO attendees (
                event_id, sales_name, order_no, total_no, shop_name, type, province, note,
                participants_before, participants_after, reserve_room, used_room,
                tire_40_before, tire_40_after, tire_80_before, tire_80_after,
                tire_120_before, tire_120_after, tire_200_before, tire_200_after,
                tire_300_before, tire_300_after, tire_600_before, tire_600_after,
                room_att, ship_att, night_att, room_att_after, ship_att_after, night_att_after
            ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");
        
        $stmt->execute([
            $event_id, $sales_name, $order_no, $total_no, $shop_name, $type, $province, $note,
            $participants_before, $participants_after, $reserve_room, $used_room,
            $tire_40_before, $tire_40_after, $tire_80_before, $tire_80_after,
            $tire_120_before, $tire_120_after, $tire_200_before, $tire_200_after,
            $tire_300_before, $tire_300_after, $tire_600_before, $tire_600_after,
            $room_att, $ship_att, $night_att, $room_att_after, $ship_att_after, $night_att_after
        ]);
        
        updateSummary($event_id);
        
        echo json_encode(['status' => 'success', 'id' => $conn->lastInsertId()]);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

function updateSummary($event_id) {
    global $conn;
    
$stmt = $conn->query("
        SELECT 
            COUNT(*) as total_shops,
            COALESCE(SUM(participants_before), 0) as total_participants_before,
            COALESCE(SUM(participants_after), 0) as total_participants_after,
            COALESCE(SUM(reserve_room), 0) as total_reserve_room,
            COALESCE(SUM(used_room), 0) as total_used_room,
            COALESCE(SUM(tire_40_before), 0) as total_tire_40_before,
            COALESCE(SUM(tire_40_after), 0) as total_tire_40_after,
            COALESCE(SUM(tire_80_before), 0) as total_tire_80_before,
            COALESCE(SUM(tire_80_after), 0) as total_tire_80_after,
            COALESCE(SUM(tire_120_before), 0) as total_tire_120_before,
            COALESCE(SUM(tire_120_after), 0) as total_tire_120_after,
            COALESCE(SUM(tire_200_before), 0) as total_tire_200_before,
            COALESCE(SUM(tire_200_after), 0) as total_tire_200_after,
            COALESCE(SUM(tire_300_before), 0) as total_tire_300_before,
            COALESCE(SUM(tire_300_after), 0) as total_tire_300_after,
            COALESCE(SUM(tire_600_before), 0) as total_tire_600_before,
            COALESCE(SUM(tire_600_after), 0) as total_tire_600_after,
            COALESCE(SUM(room_att), 0) as total_room_att,
            COALESCE(SUM(ship_att), 0) as total_ship_att,
            COALESCE(SUM(night_att), 0) as total_night_att,
            COALESCE(SUM(room_att_after), 0) as total_room_att_after,
            COALESCE(SUM(ship_att_after), 0) as total_ship_att_after,
            COALESCE(SUM(night_att_after), 0) as total_night_att_after
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
            total_reserve_room = ?, total_used_room = ?,
            total_tire_40_before = ?, total_tire_40_after = ?,
            total_tire_80_before = ?, total_tire_80_after = ?,
            total_tire_120_before = ?, total_tire_120_after = ?,
            total_tire_200_before = ?, total_tire_200_after = ?,
            total_tire_300_before = ?, total_tire_300_after = ?,
            total_tire_600_before = ?, total_tire_600_after = ?,
            total_tire_before = ?, total_tire_after = ?,
            total_room_att = ?, total_ship_att = ?, total_night_att = ?,
            total_room_att_after = ?, total_ship_att_after = ?, total_night_att_after = ?,
            updated_at = CURRENT_TIMESTAMP
        WHERE event_id = ?
    ");
    
    $stmt->execute([
        $result['total_shops'], 
        $result['total_participants_before'], $result['total_participants_after'],
        $result['total_reserve_room'], $result['total_used_room'],
        $result['total_tire_40_before'], $result['total_tire_40_after'],
        $result['total_tire_80_before'], $result['total_tire_80_after'],
        $result['total_tire_120_before'], $result['total_tire_120_after'],
        $result['total_tire_200_before'], $result['total_tire_200_after'],
        $result['total_tire_300_before'], $result['total_tire_300_after'],
        $result['total_tire_600_before'], $result['total_tire_600_after'],
        $total_tire_before, $total_tire_after,
        $result['total_room_att'], $result['total_ship_att'], $result['total_night_att'],
        $result['total_room_att_after'], $result['total_ship_att_after'], $result['total_night_att_after'],
        $event_id
    ]);
}

function getAttendees() {
    global $conn;
    $event_id = $_POST['event_id'] ?? 0;
    
    $stmt = $conn->prepare("SELECT * FROM attendees WHERE event_id = ? ORDER BY id DESC");
    $stmt->execute([$event_id]);
    $attendees = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($attendees);
}

function getSummary() {
    global $conn;
    $event_id = $_POST['event_id'] ?? 0;
    
    $stmt = $conn->query("SELECT * FROM summary WHERE event_id = $event_id");
    $summary = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode($summary);
}

function getDashboardSummary() {
    global $conn;
    $event_id = $_POST['event_id'] ?? 0;
    $type = $_POST['type'] ?? 'all';
    
    $typeFilter = '';
    if ($type === 'shop') $typeFilter = "AND type = 'shop'";
    else if ($type === 'user') $typeFilter = "AND type = 'user'";
    
    $sql = "
        SELECT 
            COUNT(*) as total_shops,
            COUNT(CASE WHEN participants_after > 0 THEN 1 END) as shops_came,
            COALESCE(SUM(participants_before), 0) as total_participants_before,
            COALESCE(SUM(participants_after), 0) as total_participants_after,
            COALESCE(SUM(reserve_room), 0) as total_reserve_room,
            COALESCE(SUM(used_room), 0) as total_used_room,
            COALESCE(SUM(tire_40_before), 0) as total_tire_40_before,
            COALESCE(SUM(tire_40_after), 0) as total_tire_40_after,
            COALESCE(SUM(tire_80_before), 0) as total_tire_80_before,
            COALESCE(SUM(tire_80_after), 0) as total_tire_80_after,
            COALESCE(SUM(tire_120_before), 0) as total_tire_120_before,
            COALESCE(SUM(tire_120_after), 0) as total_tire_120_after,
            COALESCE(SUM(tire_200_before), 0) as total_tire_200_before,
            COALESCE(SUM(tire_200_after), 0) as total_tire_200_after,
            COALESCE(SUM(tire_300_before), 0) as total_tire_300_before,
            COALESCE(SUM(tire_300_after), 0) as total_tire_300_after,
            COALESCE(SUM(tire_600_before), 0) as total_tire_600_before,
            COALESCE(SUM(tire_600_after), 0) as total_tire_600_after,
            COALESCE(SUM(room_att), 0) as total_room_att,
            COALESCE(SUM(ship_att), 0) as total_ship_att,
            COALESCE(SUM(night_att), 0) as total_night_att,
            COALESCE(SUM(room_att_after), 0) as total_room_att_after,
            COALESCE(SUM(ship_att_after), 0) as total_ship_att_after,
            COALESCE(SUM(night_att_after), 0) as total_night_att_after
        FROM attendees WHERE event_id = $event_id $typeFilter
    ";
    $stmt = $conn->query($sql);
    $summary = $stmt->fetch(PDO::FETCH_ASSOC);
    
    saveSummaryToDB($event_id, $summary);
    
    echo json_encode($summary);
}

function saveSummaryToDB($event_id, $data) {
    global $conn;
    
    $total_tire_before = ($data['total_tire_40_before'] ?? 0) + ($data['total_tire_80_before'] ?? 0) + 
                      ($data['total_tire_120_before'] ?? 0) + ($data['total_tire_200_before'] ?? 0) + 
                      ($data['total_tire_300_before'] ?? 0) + ($data['total_tire_600_before'] ?? 0);
    $total_tire_after = ($data['total_tire_40_after'] ?? 0) + ($data['total_tire_80_after'] ?? 0) + 
                       ($data['total_tire_120_after'] ?? 0) + ($data['total_tire_200_after'] ?? 0) + 
                       ($data['total_tire_300_after'] ?? 0) + ($data['total_tire_600_after'] ?? 0);
    
    $stmt = $conn->prepare("
        UPDATE summary SET 
            total_shops = ?, 
            total_participants_before = ?, total_participants_after = ?, 
            total_reserve_room = ?, total_used_room = ?,
            total_tire_40_before = ?, total_tire_40_after = ?,
            total_tire_80_before = ?, total_tire_80_after = ?,
            total_tire_120_before = ?, total_tire_120_after = ?,
            total_tire_200_before = ?, total_tire_200_after = ?,
            total_tire_300_before = ?, total_tire_300_after = ?,
            total_tire_600_before = ?, total_tire_600_after = ?,
            total_tire_before = ?, total_tire_after = ?,
            total_room_att = ?, total_ship_att = ?, total_night_att = ?,
            total_room_att_after = ?, total_ship_att_after = ?, total_night_att_after = ?,
            updated_at = CURRENT_TIMESTAMP
        WHERE event_id = ?
    ");
    
    $stmt->execute([
        $data['total_shops'], 
        $data['total_participants_before'], $data['total_participants_after'],
        $data['total_reserve_room'], $data['total_used_room'],
        $data['total_tire_40_before'], $data['total_tire_40_after'],
        $data['total_tire_80_before'], $data['total_tire_80_after'],
        $data['total_tire_120_before'], $data['total_tire_120_after'],
        $data['total_tire_200_before'], $data['total_tire_200_after'],
        $data['total_tire_300_before'], $data['total_tire_300_after'],
        $data['total_tire_600_before'], $data['total_tire_600_after'],
        $total_tire_before, $total_tire_after,
        $data['total_room_att'], $data['total_ship_att'], $data['total_night_att'],
        $data['total_room_att_after'], $data['total_ship_att_after'], $data['total_night_att_after'],
        $event_id
    ]);
}

function updateAttendee() {
    global $conn;
    
    $id = $_POST['id'] ?? 0;
    $sales_name = $_POST['sales_name'] ?? '';
    $order_no = $_POST['order_no'] ?? 0;
    $total_no = $_POST['total_no'] ?? 0;
    $shop_name = $_POST['shop_name'] ?? '';
    $type = $_POST['type'] ?? 'shop';
    $province = $_POST['province'] ?? '';
    $note = $_POST['note'] ?? '';
    $participants_before = $_POST['participants_before'] ?? 0;
    $participants_after = $_POST['participants_after'] ?? 0;
    $reserve_room = intval($_POST['reserve_room'] ?? 0);
    $used_room = intval($_POST['used_room'] ?? 0);
    
    $tire_40_before = $_POST['tire_40_before'] ?? 0;
    $tire_40_after = $_POST['tire_40_after'] ?? 0;
    $tire_80_before = $_POST['tire_80_before'] ?? 0;
    $tire_80_after = $_POST['tire_80_after'] ?? 0;
    $tire_120_before = $_POST['tire_120_before'] ?? 0;
    $tire_120_after = $_POST['tire_120_after'] ?? 0;
    $tire_200_before = $_POST['tire_200_before'] ?? 0;
    $tire_200_after = $_POST['tire_200_after'] ?? 0;
    $tire_300_before = $_POST['tire_300_before'] ?? 0;
    $tire_300_after = $_POST['tire_300_after'] ?? 0;
    $tire_600_before = $_POST['tire_600_before'] ?? 0;
    $tire_600_after = $_POST['tire_600_after'] ?? 0;
    $room_att = intval($_POST['room_att'] ?? 0);
    $ship_att = intval($_POST['ship_att'] ?? 0);
    $night_att = intval($_POST['night_attend'] ?? 0);
    $room_att_after = intval($_POST['room_att_after'] ?? 0);
    $ship_att_after = intval($_POST['ship_att_after'] ?? 0);
    $night_att_after = intval($_POST['night_att_after'] ?? 0);
    
    try {
        $stmt = $conn->prepare("
            UPDATE attendees SET 
                sales_name = ?, order_no = ?, total_no = ?, shop_name = ?, type = ?, province = ?, note = ?,
                participants_before = ?, participants_after = ?, reserve_room = ?, used_room = ?,
                tire_40_before = ?, tire_40_after = ?, tire_80_before = ?, tire_80_after = ?,
                tire_120_before = ?, tire_120_after = ?, tire_200_before = ?, tire_200_after = ?,
                tire_300_before = ?, tire_300_after = ?, tire_600_before = ?, tire_600_after = ?,
                room_att = ?, ship_att = ?, night_att = ?, room_att_after = ?, ship_att_after = ?, night_att_after = ?
            WHERE id = ?
        ");
        
        $stmt->execute([
            $sales_name, $order_no, $total_no, $shop_name, $type, $province, $note,
            $participants_before, $participants_after, $reserve_room, $used_room,
            $tire_40_before, $tire_40_after, $tire_80_before, $tire_80_after,
            $tire_120_before, $tire_120_after, $tire_200_before, $tire_200_after,
            $tire_300_before, $tire_300_after, $tire_600_before, $tire_600_after,
            $room_att, $ship_att, $night_att, $room_att_after, $ship_att_after, $night_att_after,
            $id
        ]);
        
        $stmt = $conn->query("SELECT event_id FROM attendees WHERE id = $id");
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        updateSummary($event['event_id']);
        
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

function deleteAttendee() {
    global $conn;
    
    $id = $_POST['id'] ?? 0;
    
    try {
        $stmt = $conn->query("SELECT event_id FROM attendees WHERE id = $id");
        $event = $stmt->fetch(PDO::FETCH_ASSOC);
        
        $stmt = $conn->prepare("DELETE FROM attendees WHERE id = ?");
        $stmt->execute([$id]);
        
        if ($event) {
            updateSummary($event['event_id']);
        }
        
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}

function getProvinces() {
    global $conn;
    
    $search = $_POST['search'] ?? '';
    
    try {
        if ($search) {
            $stmt = $conn->prepare("SELECT name_th FROM provinces WHERE name_th LIKE ? LIMIT 20");
            $stmt->execute(["%$search%"]);
        } else {
            $stmt = $conn->query("SELECT name_th FROM provinces ORDER BY name_th");
        }
        
        $provinces = $stmt->fetchAll(PDO::FETCH_COLUMN);
        
        echo json_encode($provinces);
    } catch (PDOException $e) {
        echo json_encode([]);
    }
}