<?php

class Sop {
    private $conn;
    private $table_name = "sops";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $query = "SELECT s.*, u.name as creator_name FROM " . $this->table_name . " s 
                  LEFT JOIN users u ON s.created_by = u.id 
                  ORDER BY s.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getSteps($sop_id) {
        $query = "SELECT * FROM sop_steps WHERE sop_id = ? ORDER BY order_index ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $sop_id);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create($title, $category, $description, $created_by, $requires_signoff = 0, $branch = 'all') {
        $query = "INSERT INTO " . $this->table_name . " (title, category, description, created_by, requires_signoff, branch) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$title, $category, $description, $created_by, $requires_signoff, $branch]);
        return $this->conn->lastInsertId();
    }

    public function addStep($sop_id, $step_text, $order_index, $requires_photo = 0) {
        $query = "INSERT INTO sop_steps (sop_id, step_text, order_index, requires_photo) VALUES (?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$sop_id, $step_text, $order_index, $requires_photo]);
    }

    public function deleteSteps($sop_id) {
        $query = "DELETE FROM sop_steps WHERE sop_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$sop_id]);
    }

    public function update($id, $title, $category, $description, $requires_signoff = 0, $branch = 'all') {
        $query = "UPDATE " . $this->table_name . " SET title = ?, category = ?, description = ?, requires_signoff = ?, branch = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$title, $category, $description, $requires_signoff, $branch, $id]);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }
}
