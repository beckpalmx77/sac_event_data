<?php
require_once 'config/connect_db.php';

try {
    $conn->exec("ALTER TABLE attendees ADD COLUMN room_att_after INT DEFAULT 0 AFTER room_att");
    echo "Added room_att_after column<br>";
    
    $conn->exec("ALTER TABLE attendees ADD COLUMN ship_att_after INT DEFAULT 0 AFTER ship_att");
    echo "Added ship_att_after column<br>";
    
    $conn->exec("ALTER TABLE attendees ADD COLUMN night_att_after INT DEFAULT 0 AFTER night_att");
    echo "Added night_att_after column<br>";
    
    echo "Done!";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}