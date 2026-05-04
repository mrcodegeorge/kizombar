<?php

/**
 * Kizo SOP Manager - Missed SOP Checker (Cron Job)
 * 
 * This script checks for assigned SOPs from previous days that were not completed.
 * It will log them as 'missed' in Zoho CRM.
 * 
 * Usage: php cron/missed_sop_checker.php
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../src/Integrations/ZohoCRM.php';

$database = new Database();
$db = $database->getConnection();
$zoho = new ZohoCRM();

$yesterday = date('Y-m-d', strtotime('-1 day'));

echo "Checking for missed SOPs for $yesterday...\n";

// Find SOP logs from yesterday that are still 'pending'
$query = "SELECT l.id, s.title, u.name as staff_name 
          FROM sop_logs l 
          JOIN sops s ON l.sop_id = s.id 
          JOIN users u ON l.user_id = u.id 
          WHERE l.date = ? AND l.status = 'pending'";

$stmt = $db->prepare($query);
$stmt->execute([$yesterday]);
$missedLogs = $stmt->fetchAll(PDO::FETCH_ASSOC);

$missedCount = 0;

foreach ($missedLogs as $log) {
    echo "Found missed SOP: {$log['title']} by {$log['staff_name']}\n";
    
    try {
        // Send to Zoho CRM
        $subject = "Missed SOP: " . $log['title'];
        $description = "Staff: " . $log['staff_name'] . "\nStatus: Missed\nDate: " . $yesterday;
        
        $response = $zoho->createTask($subject, $description);
        
        if ($response) {
            echo "Successfully logged to Zoho CRM.\n";
            $missedCount++;
            
            // Update the DB status to 'missed'
            $updateQuery = "UPDATE sop_logs SET status = 'missed' WHERE id = ?";
            $updateStmt = $db->prepare($updateQuery);
            $updateStmt->execute([$log['id']]);
            echo "Status updated to missed in DB.\n";
        }
    } catch (Exception $e) {
        echo "Error logging to Zoho: " . $e->getMessage() . "\n";
    }
}

echo "Completed. Logged $missedCount missed SOPs to Zoho CRM.\n";
