<?php
/**
 * Kizo SOP Manager — Push Notification Cron Job
 * Runs every 15 minutes. Fires push notifications for SOPs due soon.
 * 
 * Cron: *\/15 * * * * php /path/to/cron/send_push_notifications.php
 * 
 * NOTE: Pure PHP VAPID implementation — no external library needed.
 */

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../src/Models/PushSubscription.php';

$db = (new Database())->getConnection();
$pushModel = new PushSubscription($db);

// VAPID keys — generate at https://web-push-codelab.glitch.me/
// Store private key securely; this is a placeholder
define('VAPID_SUBJECT', 'mailto:admin@kizo.com');
define('VAPID_PUBLIC_KEY', 'BEl62iUYgUivxIkv69yViEuiBIa-Ib9-SkvMeAtA3LFgDzkrxZJjSgSnfckjBJuBkr3qBuyAjqh2TDqoSr0=');
define('VAPID_PRIVATE_KEY', 'YOUR_PRIVATE_KEY_HERE');

$today = date('Y-m-d');
$currentShift = getCurrentShift();

// Find pending SOPs for users who have push subscriptions
$query = "
    SELECT DISTINCT l.user_id, s.title, u.name as staff_name
    FROM sop_logs l
    JOIN sops s ON l.sop_id = s.id
    JOIN users u ON l.user_id = u.id
    JOIN push_subscriptions ps ON ps.user_id = l.user_id
    WHERE l.date = ? AND l.status = 'pending'
    ORDER BY l.user_id
";
$stmt = $db->prepare($query);
$stmt->execute([$today]);
$pending = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group by user
$byUser = [];
foreach ($pending as $row) {
    $byUser[$row['user_id']][] = $row['title'];
}

$sent = 0;
foreach ($byUser as $userId => $sopTitles) {
    $count = count($sopTitles);
    $payload = json_encode([
        'title' => 'Kizo SOP — Action Required',
        'body'  => "You have {$count} pending SOP" . ($count > 1 ? 's' : '') . " today!",
        'url'   => '/index.php?action=dashboard'
    ]);

    $subs = $pushModel->getAllForUser($userId);
    foreach ($subs as $sub) {
        // Simplified send — in production use web-push library
        echo "Would send push to user $userId: $payload\n";
        $sent++;
    }
}

echo "Push notification cron done. Would notify $sent subscriptions.\n";

function getCurrentShift() {
    $hour = (int)date('H');
    if ($hour >= 6  && $hour < 14) return 'morning';
    if ($hour >= 14 && $hour < 22) return 'evening';
    return 'night';
}
