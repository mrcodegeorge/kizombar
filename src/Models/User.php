<?php

class User {
    private $conn;
    private $table_name = "users";

    public $id;
    public $name;
    public $email;
    public $password;
    public $role;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function login($email, $password) {
        $query = "SELECT id, name, password, role, branch FROM " . $this->table_name . " WHERE email = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $email);
        $stmt->execute();

        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($password, $row['password'])) {
                $this->id = $row['id'];
                $this->name = $row['name'];
                $this->role = $row['role'];
                $this->branch = $row['branch'];
                return true;
            }
        }
        return false;
    }

    public function getUserById($id) {
        $query = "SELECT id, name, email, role, branch FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllUsers() {
        $query = "SELECT id, name, email, role, branch, created_at FROM " . $this->table_name . " WHERE role != 'admin' ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function createUser($name, $email, $password, $role, $branch = 'kizobar') {
        $query = "INSERT INTO " . $this->table_name . " (name, email, password, role, branch) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        return $stmt->execute([$name, $email, $hashed, $role, $branch]);
    }

    public function updatePassword($id, $currentPassword, $newPassword) {
        // First verify current password
        $query = "SELECT password FROM " . $this->table_name . " WHERE id = ? LIMIT 0,1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        
        if($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if(password_verify($currentPassword, $row['password'])) {
                // Current password matches, update to new
                $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
                $updateQuery = "UPDATE " . $this->table_name . " SET password = ? WHERE id = ?";
                $updateStmt = $this->conn->prepare($updateQuery);
                return $updateStmt->execute([$newHash, $id]);
            }
        }
        return false;
    }
}
