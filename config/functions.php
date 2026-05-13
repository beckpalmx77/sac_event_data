<?php
function load_user_permissions($user_id) {
    global $conn;
    $stmt = $conn->prepare("
        SELECT p.permission_key
        FROM user_permissions up
        JOIN permissions p ON up.permission_id = p.id
        WHERE up.user_id = ?
    ");
    $stmt->execute([$user_id]);
    $_SESSION['permissions'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

function has_permission($key) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        return true;
    }
    return isset($_SESSION['permissions']) && in_array($key, $_SESSION['permissions']);
}

function require_permission($key) {
    if (!has_permission($key)) {
        echo '<script>alert("ไม่มีสิทธิ์เข้าถึงหน้านี้"); window.location.href="main";</script>';
        exit;
    }
}

function nav_link($url, $label, $permission_key) {
    if (has_permission($permission_key)) {
        echo '<a href="' . $url . '" class="btn btn-sm ' . ($label === 'ออก' ? 'btn-outline-secondary' : 'btn-outline-primary') . '">' . $label . '</a>' . "\n";
    }
}
