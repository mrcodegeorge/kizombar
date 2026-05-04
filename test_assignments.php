<?php
require_once __DIR__ . '/config/Database.php';

$db = (new Database())->getConnection();
$stmt = $db->query("SELECT s.title, a.assigned_to_role, a.shift FROM sops s JOIN sop_assignments a ON s.id = a.sop_id");
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($assignments as $a) {
    echo "- {$a['title']} -> {$a['assigned_to_role']} ({$a['shift']} shift)\n";
}
