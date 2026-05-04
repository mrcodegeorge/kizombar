<?php
// Run Tier 2 Alter Migration - handles duplicate column errors gracefully
require_once __DIR__ . '/config/Database.php';

$database = new Database();
$db = $database->getConnection();

$sql = file_get_contents(__DIR__ . '/database/alter_tier2.sql');

// Split on semicolons, skip comment lines
$raw = explode(';', $sql);
$statements = [];
foreach ($raw as $s) {
    $s = trim($s);
    if (empty($s)) continue;
    // Skip if it's ONLY comment lines
    $lines = explode("\n", $s);
    $nonComment = array_filter($lines, function($l) {
        return !empty(trim($l)) && strpos(trim($l), '--') !== 0;
    });
    if (!empty($nonComment)) {
        $statements[] = $s;
    }
}

$success = 0;
$skipped = 0;
$errors  = [];

foreach ($statements as $stmt) {
    try {
        $db->exec($stmt);
        $success++;
    } catch (PDOException $e) {
        $msg = $e->getMessage();
        // Silently skip "Duplicate column name" and "Multiple primary key" errors
        if (strpos($msg, 'Duplicate column') !== false || strpos($msg, 'already exists') !== false) {
            echo "Skipped (column exists): " . substr(trim($stmt), 0, 70) . "\n";
            $skipped++;
        } else {
            $errors[] = $msg;
            echo "Error: " . $msg . "\n";
        }
    }
}

echo "\nTier 2 migration done. Applied: $success | Skipped: $skipped | Errors: " . count($errors) . "\n";
