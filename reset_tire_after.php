<?php
require_once 'config/connect_db.php';
$conn->exec("UPDATE attendees SET tire_40_after = 0, tire_80_after = 0, tire_120_after = 0, tire_200_after = 0, tire_300_after = 0, tire_600_after = 0");
echo "Updated all tire_ _after fields to 0";