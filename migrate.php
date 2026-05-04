<?php
$host = 'localhost';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "Running schema.sql...\n";
    $schema = file_get_contents('d:/test/database/schema.sql');
    $pdo->exec($schema);

    echo "Running seed.sql...\n";
    $seed = file_get_contents('d:/test/database/seed.sql');
    $pdo->exec($seed);

    echo "Migration completed successfully!\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
