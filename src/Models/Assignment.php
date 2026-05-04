<?php

class Assignment {
    private $conn;
    private $table_name = "sop_assignments";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT a.*, s.title as sop_title, u.name as user_name 
                  FROM " . $this->table_name . " a
                  JOIN sops s ON a.sop_id = s.id
                  LEFT JOIN users u ON a.assigned_to_user_id = u.id
                  ORDER BY a.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($sop_id, $user_id, $role, $frequency, $shift) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (sop_id, assigned_to_user_id, assigned_to_role, frequency, shift) 
                  VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$sop_id, $user_id ?: null, $role ?: null, $frequency, $shift]);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    public function getAssignmentsForUser($user_id, $role, $branch) {
        $query = "SELECT a.*, s.title as sop_title, s.category 
                  FROM " . $this->table_name . " a
                  JOIN sops s ON a.sop_id = s.id
                  WHERE (a.assigned_to_user_id = ? OR a.assigned_to_role = ?) 
                  AND (s.branch = 'all' OR s.branch = ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$user_id, $role, $branch]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
