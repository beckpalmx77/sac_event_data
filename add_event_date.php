<?php
require_once 'config/connect_db.php';

try {
    $conn->exec("ALTER TABLE events ADD COLUMN event_date DATE AFTER event_name");
    echo "Added event_date column";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}