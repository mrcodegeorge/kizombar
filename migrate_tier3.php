<?php
require_once __DIR__ . '/config/Database.php';
$db = (new Database())->getConnection();
$sql = file_get_contents(__DIR__ . '/database/alter_tier3.sql');
$stmts = array_filter(array_map('trim', explode(';', $sql)));
$ok = 0; $skip = 0;
foreach ($stmts as $s) {
    $lines = explode("\n", $s);
    $code = implode(' ', array_filter($lines, function($l) {
        return trim($l) !== '' && strpos(trim($l), '--') !== 0;
    }));
    if (empty(trim($code))) continue;
    try {
        $db->exec($s);
        $ok++;
        echo "OK: " . substr(trim($s), 0, 60) . "\n";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate') !== false) {
            $skip++;
            echo "Skip: " . substr(trim($s), 0, 60) . "\n";
        } else {
            echo "ERR: " . $e->getMessage() . "\n";
        }
    }
}
echo "\nDone. Applied: $ok | Skipped: $skip\n";
