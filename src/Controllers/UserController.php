<?php

require_once __DIR__ . '/../Models/User.php';

class UserController {
    private $db;
    private $user;

    public function __construct($db) {
        $this->db = $db;
        $this->user = new User($db);
    }

    public function listStaff() {
        return $this->user->getAllUsers();
    }

    public function createStaff() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? '';
            $branch = $_POST['branch'] ?? 'kizobar';

            if (empty($name) || empty($email) || empty($password) || empty($role)) {
                return ['success' => false, 'message' => 'All fields are required.'];
            }

            try {
                if ($this->user->createUser($name, $email, $password, $role, $branch)) {
                    header("Location: index.php?action=staff&msg=created");
                    exit();
                } else {
                    return ['success' => false, 'message' => 'Failed to create staff. Email might already exist.'];
                }
            } catch (PDOException $e) {
                if ($e->getCode() == 23000) { // Duplicate entry
                    return ['success' => false, 'message' => 'Email address is already in use.'];
                }
                return ['success' => false, 'message' => 'Database error occurred.'];
            }
        }
        return null;
    }
}
