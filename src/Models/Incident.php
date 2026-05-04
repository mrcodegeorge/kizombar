<?php

class Incident {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function create($type, $description, $severity, $image_path = null, $reported_by) {
        $query = "INSERT INTO incidents (type, description, severity, image_path, reported_by) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$type, $description, $severity, $image_path, $reported_by]);
    }

    public function getLastInsertId() {
        return $this->conn->lastInsertId();
    }

    public function getAll() {
        $query = "SELECT i.*, u.name as reporter_name 
                  FROM incidents i 
                  JOIN users u ON i.reported_by = u.id 
                  ORDER BY i.created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT i.*, u.name as reporter_name FROM incidents i JOIN users u ON i.reported_by = u.id WHERE i.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function markZohoSynced($id) {
        $stmt = $this->conn->prepare("UPDATE incidents SET zoho_synced = 1 WHERE id = ?");
        return $stmt->execute([$id]);
    }
}
