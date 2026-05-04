-- Tier 3 Safe Alter Migration

-- ============================================================
-- Feature 10: PIN Login
-- ============================================================
ALTER TABLE users ADD COLUMN pin VARCHAR(255) DEFAULT NULL;

-- ============================================================
-- Feature 11: Push Subscriptions
-- ============================================================
CREATE TABLE IF NOT EXISTS push_subscriptions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    endpoint TEXT NOT NULL,
    p256dh TEXT NOT NULL,
    auth TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- ============================================================
-- Feature 12: Multi-Branch Support
-- ============================================================
ALTER TABLE users ADD COLUMN branch ENUM('kizobar', 'kizo_cafe') DEFAULT 'kizobar';

ALTER TABLE sops ADD COLUMN branch ENUM('kizobar', 'kizo_cafe', 'all') DEFAULT 'all';

ALTER TABLE sop_assignments ADD COLUMN branch ENUM('kizobar', 'kizo_cafe', 'all') DEFAULT 'all';

ALTER TABLE sop_logs ADD COLUMN branch ENUM('kizobar', 'kizo_cafe') DEFAULT 'kizobar';
