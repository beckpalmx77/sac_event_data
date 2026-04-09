<?php
require_once 'config/connect_db.php';

$stmt = $conn->prepare("CREATE TABLE IF NOT EXISTS ims_user (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100),
    role ENUM('admin', 'user') DEFAULT 'user',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)");
$stmt->execute();

$hashAdmin = password_hash('admin123', PASSWORD_DEFAULT);
$hashUser = password_hash('user', PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO ims_user (username, password, full_name, role) VALUES (?, ?, ?, ?)");

try {
    $stmt->execute(['admin', $hashAdmin, 'ผู้ดูแลระบบ', 'admin']);
    echo "เพิ่ม admin สำเร็จ<br>";
} catch (Exception $e) {
    echo "admin มีอยู่แล้ว<br>";
}

try {
    $stmt->execute(['user', $hashUser, 'ผู้ใช้งาน', 'user']);
    echo "เพิ่ม user สำเร็จ<br>";
} catch (Exception $e) {
    echo "user มีอยู่แล้ว<br>";
}

echo "<br>✅ Admin - Username: admin, Password: admin123<br>";
echo "✅ User - Username: user, Password: user";