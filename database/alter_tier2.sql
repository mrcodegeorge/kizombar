-- Tier 2 Safe Alter Migration



-- Feature 5: Smart SOP Scheduling columns on sop_assignments
ALTER TABLE sop_assignments 
    ADD COLUMN trigger_type ENUM('shift', 'interval', 'conditional') DEFAULT 'shift';

ALTER TABLE sop_assignments 
    ADD COLUMN interval_hours INT DEFAULT NULL;

ALTER TABLE sop_assignments 
    ADD COLUMN condition_traffic ENUM('low', 'medium', 'high') DEFAULT NULL;

-- Store current traffic level (single-row settings table)
CREATE TABLE IF NOT EXISTS app_settings (
    setting_key VARCHAR(100) PRIMARY KEY,
    setting_value VARCHAR(255) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO app_settings (setting_key, setting_value) 
VALUES ('traffic_level', 'low')
ON DUPLICATE KEY UPDATE setting_key = setting_key;

-- Feature 6: Incident Reporting
CREATE TABLE IF NOT EXISTS incidents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    type ENUM('equipment_failure', 'customer_issue', 'safety_hazard', 'other') NOT NULL,
    description TEXT NOT NULL,
    severity ENUM('low', 'medium', 'high', 'critical') NOT NULL,
    image_path VARCHAR(255) DEFAULT NULL,
    reported_by INT NOT NULL,
    zoho_synced TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reported_by) REFERENCES users(id) ON DELETE CASCADE
);

-- Feature 7: Sign-off on sops table
ALTER TABLE sops
    ADD COLUMN requires_signoff BOOLEAN DEFAULT FALSE;

-- Feature 7: Sign-off on sop_logs table
ALTER TABLE sop_logs
    ADD COLUMN signoff_status ENUM('pending', 'approved', 'rejected') DEFAULT NULL;

ALTER TABLE sop_logs
    ADD COLUMN signoff_by INT DEFAULT NULL;

ALTER TABLE sop_logs
    ADD COLUMN signoff_note VARCHAR(255) DEFAULT NULL;

ALTER TABLE sop_logs
    ADD COLUMN signoff_at TIMESTAMP NULL;

-- Feature 8: Inventory
CREATE TABLE IF NOT EXISTS inventory_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    unit VARCHAR(50) NOT NULL DEFAULT 'units',
    current_stock DECIMAL(10,2) DEFAULT 0,
    min_threshold DECIMAL(10,2) DEFAULT 0,
    category ENUM('Bar', 'Kitchen', 'Café', 'Cleaning', 'Other') DEFAULT 'Other',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Seed default inventory items
INSERT IGNORE INTO inventory_items (id, name, unit, current_stock, min_threshold, category) VALUES
(1, 'Vodka', 'L', 5, 2, 'Bar'),
(2, 'Rum', 'L', 3, 2, 'Bar'),
(3, 'Gin', 'L', 2, 2, 'Bar'),
(4, 'Tonic Water', 'bottles', 24, 12, 'Bar'),
(5, 'Lime', 'kg', 2, 1, 'Bar'),
(6, 'Coffee Beans', 'kg', 3, 1, 'Café'),
(7, 'Milk', 'L', 8, 4, 'Café'),
(8, 'Sugar', 'kg', 5, 2, 'Café'),
(9, 'Chicken', 'kg', 10, 5, 'Kitchen'),
(10, 'All-Purpose Flour', 'kg', 8, 3, 'Kitchen'),
(11, 'Cooking Oil', 'L', 5, 2, 'Kitchen'),
(12, 'Bleach', 'L', 2, 1, 'Cleaning'),
(13, 'Mop Heads', 'units', 4, 2, 'Cleaning'),
(14, 'Paper Towels', 'rolls', 20, 10, 'Cleaning');
