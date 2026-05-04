<?php
/**
 * Kizo SOP Manager — Full Reset & Seed
 * Drops all tables, recreates them with latest migrations, and seeds test data.
 */

require_once __DIR__ . '/config/Database.php';
$database = new Database();
// First connection without DB name to run schema.sql which creates the DB
$initial_pdo = new PDO("mysql:host=localhost", "root", "");
$initial_pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function runSqlFile($db, $path) {
    echo "Running $path...\n";
    $sql = file_get_contents($path);
    $stmts = array_filter(array_map('trim', explode(';', $sql)));
    foreach ($stmts as $s) {
        if (empty($s)) continue;
        try {
            $db->exec($s);
        } catch (PDOException $e) {
            echo "Warning in $path: " . $e->getMessage() . "\n";
        }
    }
}

// 1. Initial Schema (creates the DB)
runSqlFile($initial_pdo, __DIR__ . '/database/schema.sql');

// Now get the standard connection
$db = $database->getConnection();

// 2. Tier 2 Migration
runSqlFile($db, __DIR__ . '/database/alter_tier2.sql');

// 3. Tier 3 Migration
runSqlFile($db, __DIR__ . '/database/alter_tier3.sql');

// 4. Seed Data
echo "Seeding data...\n";
$password = password_hash('password123', PASSWORD_DEFAULT);
$pin = password_hash('1234', PASSWORD_DEFAULT);

// Users
$db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)")
   ->execute(['Admin User', 'admin@kizo.com', $password, 'admin']);

$db->prepare("INSERT INTO users (name, email, password, role, pin, branch) VALUES (?, ?, ?, ?, ?, ?)")
   ->execute(['John Kizobar', 'john@kizo.com', $password, 'staff_bar', $pin, 'kizobar']);

$db->prepare("INSERT INTO users (name, email, password, role, pin, branch) VALUES (?, ?, ?, ?, ?, ?)")
   ->execute(['Sarah Café', 'sarah@kizo.com', $password, 'staff_cafe', $pin, 'kizo_cafe']);

// SOPs
$db->prepare("INSERT INTO sops (title, category, description, created_by, branch) VALUES (?, ?, ?, ?, ?)")
   ->execute(['Bar Opening - Kizobar', 'Bar', 'Morning opening checklist for Kizobar', 1, 'kizobar']);
$sop1 = $db->lastInsertId();

$db->prepare("INSERT INTO sops (title, category, description, created_by, branch, requires_signoff) VALUES (?, ?, ?, ?, ?, ?)")
   ->execute(['Coffee Station Setup - Café', 'Café', 'Morning coffee setup for Kizo Café', 1, 'kizo_cafe', 1]);
$sop2 = $db->lastInsertId();

$db->prepare("INSERT INTO sops (title, category, description, created_by, branch) VALUES (?, ?, ?, ?, ?)")
   ->execute(['Closing Cleaning (All)', 'Cleaning', 'General closing cleanup for all branches', 1, 'all']);
$sop3 = $db->lastInsertId();

// Steps
$db->prepare("INSERT INTO sop_steps (sop_id, step_text, order_index, requires_photo) VALUES (?, ?, ?, ?)")
   ->execute([$sop1, 'Check beer taps', 1, 0]);
$db->prepare("INSERT INTO sop_steps (sop_id, step_text, order_index, requires_photo) VALUES (?, ?, ?, ?)")
   ->execute([$sop1, 'Clean bar counter (Photo required)', 2, 1]);

$db->prepare("INSERT INTO sop_steps (sop_id, step_text, order_index, requires_photo) VALUES (?, ?, ?, ?)")
   ->execute([$sop2, 'Warm up espresso machine', 1, 0]);

$db->prepare("INSERT INTO sop_steps (sop_id, step_text, order_index, requires_photo) VALUES (?, ?, ?, ?)")
   ->execute([$sop3, 'Mop floors', 1, 0]);

// Assignments
$db->prepare("INSERT INTO sop_assignments (sop_id, assigned_to_user_id, frequency, shift, branch) VALUES (?, ?, ?, ?, ?)")
   ->execute([$sop1, 2, 'daily', 'morning', 'kizobar']);
$db->prepare("INSERT INTO sop_assignments (sop_id, assigned_to_user_id, frequency, shift, branch) VALUES (?, ?, ?, ?, ?)")
   ->execute([$sop3, 2, 'daily', 'evening', 'kizobar']);

$db->prepare("INSERT INTO sop_assignments (sop_id, assigned_to_user_id, frequency, shift, branch) VALUES (?, ?, ?, ?, ?)")
   ->execute([$sop2, 3, 'daily', 'morning', 'kizo_cafe']);
$db->prepare("INSERT INTO sop_assignments (sop_id, assigned_to_user_id, frequency, shift, branch) VALUES (?, ?, ?, ?, ?)")
   ->execute([$sop3, 3, 'daily', 'evening', 'kizo_cafe']);

echo "\nDatabase fully reset and seeded for Tier 3!\n";
