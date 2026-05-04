-- Kizo SOP Manager Database Schema

CREATE DATABASE IF NOT EXISTS kizo_sop_v3;
USE kizo_sop_v3;

-- Drop tables in reverse order of dependencies
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS sop_log_steps;
DROP TABLE IF EXISTS sop_logs;
DROP TABLE IF EXISTS sop_assignments;
DROP TABLE IF EXISTS sop_steps;
DROP TABLE IF EXISTS sops;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS = 1;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff_bar', 'staff_kitchen', 'staff_cafe', 'staff_cleaning') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- SOPs table
CREATE TABLE IF NOT EXISTS sops (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    category ENUM('Bar', 'Kitchen', 'Café', 'Cleaning', 'Service', 'Inventory', 'Finance', 'Staff') NOT NULL,
    description TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
);

-- SOP Steps table
CREATE TABLE IF NOT EXISTS sop_steps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sop_id INT NOT NULL,
    step_text TEXT NOT NULL,
    requires_photo BOOLEAN DEFAULT FALSE,
    order_index INT NOT NULL,
    FOREIGN KEY (sop_id) REFERENCES sops(id) ON DELETE CASCADE
);

-- SOP Assignments table
CREATE TABLE IF NOT EXISTS sop_assignments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sop_id INT NOT NULL,
    assigned_to_user_id INT DEFAULT NULL,
    assigned_to_role VARCHAR(50) DEFAULT NULL,
    frequency ENUM('daily', 'weekly') DEFAULT 'daily',
    shift ENUM('morning', 'evening', 'night', 'all') DEFAULT 'all',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (sop_id) REFERENCES sops(id) ON DELETE CASCADE,
    FOREIGN KEY (assigned_to_user_id) REFERENCES users(id) ON DELETE SET NULL
);

-- SOP Logs table
CREATE TABLE IF NOT EXISTS sop_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sop_id INT NOT NULL,
    user_id INT NOT NULL,
    date DATE NOT NULL,
    shift ENUM('morning', 'evening', 'night') NOT NULL,
    status ENUM('pending', 'completed', 'missed') DEFAULT 'pending',
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (sop_id) REFERENCES sops(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- SOP Log Steps table
CREATE TABLE IF NOT EXISTS sop_log_steps (
    id INT AUTO_INCREMENT PRIMARY KEY,
    sop_log_id INT NOT NULL,
    step_id INT NOT NULL,
    completed TINYINT(1) DEFAULT 0,
    image_path VARCHAR(255) DEFAULT NULL,
    completed_at TIMESTAMP NULL,
    FOREIGN KEY (sop_log_id) REFERENCES sop_logs(id) ON DELETE CASCADE,
    FOREIGN KEY (step_id) REFERENCES sop_steps(id) ON DELETE CASCADE
);
