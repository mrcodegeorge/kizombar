<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = 'kizo_sop_manager';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->query("SELECT id, name, email, role FROM users");
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo "Current Users in Database:\n";
    foreach ($users as $u) {
        echo "- {$u['name']} ({$u['email']}) [Role: {$u['role']}]\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
