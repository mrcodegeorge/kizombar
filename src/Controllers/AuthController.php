<?php

require_once __DIR__ . '/../Models/User.php';

class AuthController {
    private $db;
    private $user;

    public function __construct($db) {
        $this->db = $db;
        $this->user = new User($db);
    }

    public function handleLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            if ($this->user->login($email, $password)) {
                $_SESSION['user_id'] = $this->user->id;
                $_SESSION['user_name'] = $this->user->name;
                $_SESSION['user_role'] = $this->user->role;
                $_SESSION['user_branch'] = $this->user->branch;
                
                header("Location: index.php?action=dashboard");
                exit();
            } else {
                return "Invalid email or password.";
            }
        }
        return null;
    }

    public function logout() {
        session_destroy();
        header("Location: index.php?action=login");
        exit();
    }

    public static function checkAuth() {
        if (!isset($_SESSION['user_id'])) {
            header("Location: index.php?action=login");
            exit();
        }
    }

    public static function checkAdmin() {
        self::checkAuth();
        if ($_SESSION['user_role'] !== 'admin') {
            header("Location: index.php?action=dashboard");
            exit();
        }
    }

    public function getProfileData() {
        self::checkAuth();
        return $this->user->getUserById($_SESSION['user_id']);
    }

    public function handleProfileUpdate() {
        self::checkAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $currentPassword = $_POST['current_password'] ?? '';
            $newPassword = $_POST['new_password'] ?? '';
            $confirmPassword = $_POST['confirm_password'] ?? '';

            if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
                return ['success' => false, 'message' => 'All fields are required.'];
            }

            if ($newPassword !== $confirmPassword) {
                return ['success' => false, 'message' => 'New passwords do not match.'];
            }

            if (strlen($newPassword) < 6) {
                return ['success' => false, 'message' => 'New password must be at least 6 characters.'];
            }

            if ($this->user->updatePassword($_SESSION['user_id'], $currentPassword, $newPassword)) {
                return ['success' => true, 'message' => 'Password updated successfully!'];
            } else {
                return ['success' => false, 'message' => 'Incorrect current password.'];
            }
        }
        return null;
    }

    public function handlePinLogin() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = (int)($_POST['user_id'] ?? 0);
            $pin     = $_POST['pin'] ?? '';

            if (!$user_id || strlen($pin) !== 4 || !ctype_digit($pin)) {
                return 'Invalid PIN or user selection.';
            }

            $stmt = $this->db->prepare(
                "SELECT id, name, role, pin FROM users WHERE id = ? AND pin IS NOT NULL"
            );
            $stmt->execute([$user_id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row && password_verify($pin, $row['pin'])) {
                $_SESSION['user_id']   = $row['id'];
                $_SESSION['user_name'] = $row['name'];
                $_SESSION['user_role'] = $row['role'];
                header("Location: index.php?action=dashboard");
                exit();
            }
            return 'Incorrect PIN. Try again.';
        }
        return null;
    }

    public function handleSetPin() {
        self::checkAuth();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $pin     = $_POST['pin'] ?? '';
            $confirm = $_POST['pin_confirm'] ?? '';

            if (strlen($pin) !== 4 || !ctype_digit($pin)) {
                return ['success' => false, 'message' => 'PIN must be exactly 4 digits.'];
            }
            if ($pin !== $confirm) {
                return ['success' => false, 'message' => 'PINs do not match.'];
            }

            $hashed = password_hash($pin, PASSWORD_DEFAULT);
            $stmt   = $this->db->prepare("UPDATE users SET pin = ? WHERE id = ?");
            $stmt->execute([$hashed, $_SESSION['user_id']]);

            return ['success' => true, 'message' => 'PIN set successfully! You can now use Quick Login.'];
        }
        return null;
    }

    public function getStaffListForPin() {
        $stmt = $this->db->query(
            "SELECT id, name, role FROM users WHERE role != 'admin' AND pin IS NOT NULL ORDER BY name"
        );
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
