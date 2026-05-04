<?php
require_once __DIR__ . '/config/Database.php';
$db = (new Database())->getConnection();
$sql = file_get_contents(__DIR__ . '/database/attendance.sql');
try {
    $db->exec($sql);
    echo "Attendance table created successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
