-- Attendance & Liveness Verification Table

USE kizo_sop_v3;

CREATE TABLE IF NOT EXISTS attendance_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    branch ENUM('kizobar', 'kizo_cafe') NOT NULL,
    type ENUM('clock_in', 'clock_out') NOT NULL,
    video_path VARCHAR(255) DEFAULT NULL,
    audio_path VARCHAR(255) DEFAULT NULL,
    verification_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    timestamp TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);
