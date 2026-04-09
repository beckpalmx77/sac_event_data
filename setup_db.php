<?php
date_default_timezone_set("Asia/Bangkok");
require_once 'config/connect_db.php';

$sql = file_get_contents('database.sql');

$statements = array_filter(array_map('trim', explode(';', $sql)));

foreach ($statements as $statement) {
    if (!empty($statement)) {
        try {
            $conn->exec($statement);
            echo "<p>✓ Executed</p>";
        } catch (PDOException $e) {
            echo "<p>Error: " . $e->getMessage() . "</p>";
        }
    }
}

echo "<h3>✅ สร้าง Database สำเร็จ!</h3>";
echo "<a href='index.php' class='btn btn-primary'>ไปหน้าบันทึกข้อมูล</a> | ";
echo "<a href='import_csv.php?confirm=yes' class='btn btn-success'>Import CSV</a>";