<?php

require_once __DIR__ . '/../Models/Attendance.php';
require_once __DIR__ . '/../Models/User.php';

class AttendanceController {
    private $db;
    private $attendance;

    public function __construct($db) {
        $this->db = $db;
        $this->attendance = new Attendance($db);
    }

    public function showClockIn() {
        AuthController::checkAuth();
        $user_id = $_SESSION['user_id'];
        $status = $this->attendance->getTodayStatus($user_id);
        
        include __DIR__ . '/../../views/staff/clock_in.php';
    }

    public function handleClockIn() {
        AuthController::checkAuth();
        header('Content-Type: application/json');
        
        try {
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $user_id = $_SESSION['user_id'];
                // Ensure branch matches the ENUM in DB
                $branch = $_SESSION['user_branch'] ?? 'kizobar';
                if ($branch === 'kizo_cafe') $branch = 'kizo_cafe'; // Matching enum
                
                $videoPath = null;
                $audioPath = null;

                $uploadDir = __DIR__ . '/../../public/uploads/attendance/';
                if (!is_dir($uploadDir)) {
                    @mkdir($uploadDir, 0777, true);
                }

                if (isset($_FILES['video'])) {
                    $videoName = "vid_{$user_id}_" . time() . ".webm";
                    if (@move_uploaded_file($_FILES['video']['tmp_name'], $uploadDir . $videoName)) {
                        $videoPath = "uploads/attendance/" . $videoName;
                    } else {
                        throw new Exception("Failed to save video file. Check permissions.");
                    }
                }

                if ($this->attendance->clockIn($user_id, $branch, $videoPath, $audioPath)) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Database error saving record.']);
                }
                exit();
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            exit();
        }
    }

    public function handleClockOut() {
        AuthController::checkAuth();
        $user_id = $_SESSION['user_id'];
        $branch = $_SESSION['user_branch'] ?? 'kizobar';
        
        if ($this->attendance->clockOut($user_id, $branch)) {
            header("Location: index.php?action=clock_in&msg=clocked_out");
        } else {
            header("Location: index.php?action=clock_in&error=1");
        }
        exit();
    }

    public function adminDashboard() {
        AuthController::checkAdmin();
        $branch = $_SESSION['active_branch'] ?? 'all';
        $logs = $this->attendance->getAllForAdmin($branch);
        
        include __DIR__ . '/../../views/admin/attendance.php';
    }

    public function updateStatus() {
        AuthController::checkAdmin();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $status = $_POST['status'];
            if ($this->attendance->updateVerification($id, $status)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false]);
            }
            exit();
        }
    }
}
