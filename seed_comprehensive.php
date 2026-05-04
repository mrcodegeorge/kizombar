<?php
require_once __DIR__ . '/config/Database.php';

$db = (new Database())->getConnection();

// Clear existing SOPs to avoid duplicates during this comprehensive seeding
$db->exec("SET FOREIGN_KEY_CHECKS = 0;");
$db->exec("TRUNCATE TABLE sop_steps;");
$db->exec("TRUNCATE TABLE sop_assignments;");
$db->exec("TRUNCATE TABLE sops;");
$db->exec("SET FOREIGN_KEY_CHECKS = 1;");

$sops = [
    // --- KIZOBAR (RESTAURANT/BAR) ---
    [
        'title' => 'Bar Opening Procedures',
        'description' => 'Daily setup for the bar area to ensure readiness for service.',
        'branch' => 'kizobar',
        'steps' => [
            ['title' => 'Check Fridge Temperatures', 'is_required' => 1, 'requires_photo' => 1],
            ['title' => 'Replenish Ice Bins', 'is_required' => 1, 'requires_photo' => 0],
            ['title' => 'Slice Fresh Garnishes (Lemons/Limes)', 'is_required' => 1, 'requires_photo' => 1],
            ['title' => 'Test Draft Lines for Clarity', 'is_required' => 0, 'requires_photo' => 0],
            ['title' => 'Set up POS and Cash Drawer', 'is_required' => 1, 'requires_photo' => 0]
        ]
    ],
    [
        'title' => 'Kitchen Food Safety Check',
        'description' => 'Mandatory hygiene and safety checks for the kitchen staff.',
        'branch' => 'kizobar',
        'steps' => [
            ['title' => 'Verify Meat Storage Temp (< 5°C)', 'is_required' => 1, 'requires_photo' => 1],
            ['title' => 'Sanitize All Work Surfaces', 'is_required' => 1, 'requires_photo' => 0],
            ['title' => 'Check Handwash Station Soap/Towels', 'is_required' => 1, 'requires_photo' => 0],
            ['title' => 'Label and Date All Prep Items', 'is_required' => 1, 'requires_photo' => 1]
        ]
    ],
    [
        'title' => 'Bar Closing & Sanitation',
        'description' => 'End of shift cleaning and inventory securing.',
        'branch' => 'kizobar',
        'steps' => [
            ['title' => 'Clean and Sanitize Beer Mats', 'is_required' => 1, 'requires_photo' => 0],
            ['title' => 'Restock Back Bar Spirits', 'is_required' => 1, 'requires_photo' => 1],
            ['title' => 'Empty and Wash All Shakers/Tools', 'is_required' => 1, 'requires_photo' => 0],
            ['title' => 'Mop Behind-Bar Area', 'is_required' => 1, 'requires_photo' => 1]
        ]
    ],

    // --- KIZO CAFE ---
    [
        'title' => 'Barista Station Morning Setup',
        'description' => 'Calibration and cleaning of coffee equipment.',
        'branch' => 'kizo_cafe',
        'steps' => [
            ['title' => 'Backflush Espresso Machine', 'is_required' => 1, 'requires_photo' => 0],
            ['title' => 'Dial-in Espresso (Check extraction time)', 'is_required' => 1, 'requires_photo' => 1],
            ['title' => 'Stock Milk Fridge (Check Expiry Dates)', 'is_required' => 1, 'requires_photo' => 0],
            ['title' => 'Wipe down Pastry Display Case', 'is_required' => 1, 'requires_photo' => 1]
        ]
    ],
    [
        'title' => 'Hourly Cafe Maintenance',
        'description' => 'Regular checks to keep the cafe pristine.',
        'branch' => 'kizo_cafe',
        'steps' => [
            ['title' => 'Clear and Wipe Customer Tables', 'is_required' => 1, 'requires_photo' => 0],
            ['title' => 'Check and Clean Restrooms', 'is_required' => 1, 'requires_photo' => 1],
            ['title' => 'Refill Water Station / Sugar / Napkins', 'is_required' => 1, 'requires_photo' => 0]
        ]
    ],
    [
        'title' => 'Pastry & Inventory Audit',
        'description' => 'Mid-day check of stock levels.',
        'branch' => 'kizo_cafe',
        'steps' => [
            ['title' => 'Count Remaining Croissants/Cakes', 'is_required' => 1, 'requires_photo' => 1],
            ['title' => 'Record Low Coffee Bean Levels', 'is_required' => 1, 'requires_photo' => 0],
            ['title' => 'Verify Sandwich Prep for Lunch', 'is_required' => 1, 'requires_photo' => 1]
        ]
    ]
];

foreach ($sops as $sopData) {
    // Determine category based on branch
    $category = $sopData['branch'] === 'kizobar' ? 'Bar' : 'Café';
    
    // Insert SOP
    $stmt = $db->prepare("INSERT INTO sops (title, category, description, branch, created_by) VALUES (?, ?, ?, ?, 1)");
    $stmt->execute([$sopData['title'], $category, $sopData['description'], $sopData['branch']]);
    $sopId = $db->lastInsertId();

    // Insert Steps
    $stepStmt = $db->prepare("INSERT INTO sop_steps (sop_id, step_text, requires_photo, order_index) VALUES (?, ?, ?, ?)");
    $index = 1;
    foreach ($sopData['steps'] as $step) {
        $stepStmt->execute([$sopId, $step['title'], $step['requires_photo'] ?? 0, $index++]);
    }

    // Assign to Branch Roles (Staff)
    $role = $sopData['branch'] === 'kizobar' ? 'staff_bar' : 'staff_cafe';
    $assignStmt = $db->prepare("INSERT INTO sop_assignments (sop_id, assigned_to_role, branch) VALUES (?, ?, ?)");
    $assignStmt->execute([$sopId, $role, $sopData['branch']]);
}

echo "Comprehensive SOPs seeded successfully for Kizobar and Kizo Café.\n";
