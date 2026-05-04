<?php

class Attendance {
    private $conn;
    private $table_name = "attendance_logs";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function clockIn($userId, $branch, $videoPath, $audioPath) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (user_id, branch, type, video_path, audio_path, verification_status) 
                  VALUES (?, ?, 'clock_in', ?, ?, 'pending')";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$userId, $branch, $videoPath, $audioPath]);
    }

    public function clockOut($userId, $branch) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (user_id, branch, type, verification_status) 
                  VALUES (?, ?, 'clock_out', 'approved')";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$userId, $branch]);
    }

    public function getTodayStatus($userId) {
        $today = date('Y-m-d');
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE user_id = ? AND DATE(timestamp) = ? 
                  ORDER BY timestamp DESC LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$userId, $today]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllForAdmin($branch = 'all') {
        $where = "";
        $params = [];
        if ($branch !== 'all') {
            $where = "WHERE a.branch = ?";
            $params = [$branch];
        }

        $query = "SELECT a.*, u.name as staff_name 
                  FROM " . $this->table_name . " a
                  JOIN users u ON a.user_id = u.id
                  $where
                  ORDER BY a.timestamp DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateVerification($id, $status) {
        $query = "UPDATE " . $this->table_name . " SET verification_status = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$status, $id]);
    }
}
