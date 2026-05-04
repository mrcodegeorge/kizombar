<?php
/**
 * Kizo SOP Manager — Tier 3 Comprehensive Seeder
 * Resets and populates the database with multi-branch testing data.
 */

require_once __DIR__ . '/config/Database.php';

$db = (new Database())->getConnection();

echo "Resetting database...\n";
$db->exec("SET FOREIGN_KEY_CHECKS = 0");
$db->exec("TRUNCATE TABLE sop_log_steps");
$db->exec("TRUNCATE TABLE sop_logs");
$db->exec("TRUNCATE TABLE sop_assignments");
$db->exec("TRUNCATE TABLE sop_steps");
$db->exec("TRUNCATE TABLE sops");
$db->exec("TRUNCATE TABLE users");
$db->exec("SET FOREIGN_KEY_CHECKS = 1");

echo "Creating Admin and Staff...\n";
$password = password_hash('password123', PASSWORD_DEFAULT);
$pin = password_hash('1234', PASSWORD_DEFAULT);

// Admin
$db->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)")
   ->execute(['Admin User', 'admin@kizo.com', $password, 'admin']);

// Staff with PIN
$db->prepare("INSERT INTO users (name, email, password, role, pin, branch) VALUES (?, ?, ?, ?, ?, ?)")
   ->execute(['John Kizobar', 'john@kizo.com', $password, 'staff_bar', $pin, 'kizobar']);

$db->prepare("INSERT INTO users (name, email, password, role, pin, branch) VALUES (?, ?, ?, ?, ?, ?)")
   ->execute(['Sarah Café', 'sarah@kizo.com', $password, 'staff_cafe', $pin, 'kizo_cafe']);

echo "Creating Branch-Specific SOPs...\n";

// Kizobar SOP
$db->prepare("INSERT INTO sops (title, category, description, created_by, branch) VALUES (?, ?, ?, ?, ?)")
   ->execute(['Bar Opening - Kizobar', 'Bar', 'Morning opening checklist for Kizobar', 1, 'kizobar']);
$sop1 = $db->lastInsertId();

// Kizo Cafe SOP
$db->prepare("INSERT INTO sops (title, category, description, created_by, branch) VALUES (?, ?, ?, ?, ?)")
   ->execute(['Coffee Station Setup - Café', 'Café', 'Morning coffee setup for Kizo Café', 1, 'kizo_cafe']);
$sop2 = $db->lastInsertId();

// Shared SOP
$db->prepare("INSERT INTO sops (title, category, description, created_by, branch) VALUES (?, ?, ?, ?, ?)")
   ->execute(['Closing Cleaning (All)', 'Cleaning', 'General closing cleanup for all branches', 1, 'all']);
$sop3 = $db->lastInsertId();

echo "Adding Steps...\n";
$db->prepare("INSERT INTO sop_steps (sop_id, step_text, order_index, requires_photo) VALUES (?, ?, ?, ?)")
   ->execute([$sop1, 'Check beer taps', 1, 0]);
$db->prepare("INSERT INTO sop_steps (sop_id, step_text, order_index, requires_photo) VALUES (?, ?, ?, ?)")
   ->execute([$sop1, 'Clean bar counter (Photo required)', 2, 1]);

$db->prepare("INSERT INTO sop_steps (sop_id, step_text, order_index, requires_photo) VALUES (?, ?, ?, ?)")
   ->execute([$sop2, 'Warm up espresso machine', 1, 0]);

$db->prepare("INSERT INTO sop_steps (sop_id, step_text, order_index, requires_photo) VALUES (?, ?, ?, ?)")
   ->execute([$sop3, 'Mop floors', 1, 0]);

echo "Assigning SOPs...\n";
// John (Bar) gets Bar SOP and Shared SOP
$db->prepare("INSERT INTO sop_assignments (sop_id, assigned_to_user_id, frequency, shift) VALUES (?, ?, ?, ?)")
   ->execute([$sop1, 2, 'daily', 'morning']);
$db->prepare("INSERT INTO sop_assignments (sop_id, assigned_to_user_id, frequency, shift) VALUES (?, ?, ?, ?)")
   ->execute([$sop3, 2, 'daily', 'evening']);

// Sarah (Cafe) gets Cafe SOP and Shared SOP
$db->prepare("INSERT INTO sop_assignments (sop_id, assigned_to_user_id, frequency, shift) VALUES (?, ?, ?, ?)")
   ->execute([$sop2, 3, 'daily', 'morning']);
$db->prepare("INSERT INTO sop_assignments (sop_id, assigned_to_user_id, frequency, shift) VALUES (?, ?, ?, ?)")
   ->execute([$sop3, 3, 'daily', 'evening']);

echo "\nDatabase seeded successfully!\n";
echo "Admin: admin@kizo.com / password123\n";
echo "John (Kizobar): 1234 PIN / john@kizo.com\n";
echo "Sarah (Kizo Cafe): 1234 PIN / sarah@kizo.com\n";
