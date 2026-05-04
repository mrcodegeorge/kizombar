<?php

class Inventory {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getAll() {
        $stmt = $this->conn->query("SELECT * FROM inventory_items ORDER BY category, name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getByCategory($category) {
        $stmt = $this->conn->prepare("SELECT * FROM inventory_items WHERE category = ? ORDER BY name");
        $stmt->execute([$category]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLowStockByCategory($category) {
        $stmt = $this->conn->prepare("SELECT * FROM inventory_items WHERE category = ? AND current_stock <= min_threshold ORDER BY name");
        $stmt->execute([$category]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function updateStock($id, $stock) {
        $stmt = $this->conn->prepare("UPDATE inventory_items SET current_stock = ? WHERE id = ?");
        return $stmt->execute([$stock, $id]);
    }

    public function addItem($name, $unit, $stock, $threshold, $category) {
        $stmt = $this->conn->prepare("INSERT INTO inventory_items (name, unit, current_stock, min_threshold, category) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$name, $unit, $stock, $threshold, $category]);
    }
}
