<?php

require_once __DIR__ . '/../Models/Assignment.php';
require_once __DIR__ . '/../Models/Log.php';
require_once __DIR__ . '/../Models/Sop.php';

class StaffController {
    private $db;
    private $assignment;
    private $log;
    private $sop;

    public function __construct($db) {
        $this->db = $db;
        $this->assignment = new Assignment($db);
        $this->log = new Log($db);
        $this->sop = new Sop($db);
    }

    public function dashboard() {
        $user_id = $_SESSION['user_id'];
        $role = $_SESSION['user_role'];
        $branch = $_SESSION['user_branch'] ?? 'kizobar';
        $today = date('Y-m-d');
        
        $assignments = $this->assignment->getAssignmentsForUser($user_id, $role, $branch);
        
        $tasks = [];
        foreach ($assignments as $a) {
            $log = $this->log->getTodaysLog($a['sop_id'], $user_id, $today, $a['shift']);
            
            $tasks[] = [
                'sop_id' => $a['sop_id'],
                'title' => $a['sop_title'],
                'category' => $a['category'],
                'shift' => $a['shift'],
                'status' => $log ? $log['status'] : 'pending',
                'log_id' => $log ? $log['id'] : null
            ];
        }
        
        return $tasks;
    }

    public function checklist($sop_id, $shift) {
        $user_id = $_SESSION['user_id'];
        $today = date('Y-m-d');
        
        $log = $this->log->getTodaysLog($sop_id, $user_id, $today, $shift);
        
        if (!$log) {
            $branch = $_SESSION['user_branch'] ?? 'kizobar';
            $log_id = $this->log->createLog($sop_id, $user_id, $today, $shift, $branch);
            $this->log->initLogSteps($log_id, $sop_id);
            $log = $this->log->getTodaysLog($sop_id, $user_id, $today, $shift);
        }
        
        $sop_data = $this->sop->getById($sop_id);
        $steps = $this->log->getLogSteps($log['id']);
        
        return [
            'sop' => $sop_data,
            'log' => $log,
            'steps' => $steps
        ];
    }

    public function toggleStep() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $log_id = $_POST['log_id'];
            $step_id = $_POST['step_id'];
            $completed = $_POST['completed'] == 'true' ? 1 : 0;
            
            $this->log->updateStepStatus($log_id, $step_id, $completed);
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit();
        }
    }

    public function uploadProof() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
            $log_id = $_POST['log_id'];
            $step_id = $_POST['step_id'];
            $photo = $_FILES['photo'];

            $uploadDir = __DIR__ . '/../../public/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            $extension = strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png'];

            if (!in_array($extension, $allowed)) {
                echo json_encode(['success' => false, 'message' => 'Invalid file type']);
                exit();
            }

            $fileName = "proof_{$log_id}_{$step_id}_" . time() . ".{$extension}";
            $destination = $uploadDir . $fileName;

            if (move_uploaded_file($photo['tmp_name'], $destination)) {
                $dbPath = "uploads/" . $fileName;
                
                $query = "UPDATE sop_log_steps SET image_path = ? WHERE sop_log_id = ? AND step_id = ?";
                $stmt = $this->db->prepare($query);
                $stmt->execute([$dbPath, $log_id, $step_id]);

                echo json_encode(['success' => true, 'path' => $dbPath]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Failed to move uploaded file']);
            }
            exit();
        }
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
        exit();
    }
}
