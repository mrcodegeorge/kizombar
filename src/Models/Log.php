<?php

class Log {
    private $conn;

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getTodaysLog($sop_id, $user_id, $date, $shift) {
        $query = "SELECT * FROM sop_logs WHERE sop_id = ? AND user_id = ? AND date = ? AND shift = ? LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$sop_id, $user_id, $date, $shift]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function createLog($sop_id, $user_id, $date, $shift, $branch = 'kizobar') {
        $query = "INSERT INTO sop_logs (sop_id, user_id, date, shift, status, branch) VALUES (?, ?, ?, ?, 'pending', ?)";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$sop_id, $user_id, $date, $shift, $branch]);
        return $this->conn->lastInsertId();
    }

    public function getLogSteps($log_id) {
        $query = "SELECT ls.*, s.step_text 
                  FROM sop_log_steps ls 
                  JOIN sop_steps s ON ls.step_id = s.id 
                  WHERE ls.sop_log_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$log_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function initLogSteps($log_id, $sop_id) {
        $query = "INSERT INTO sop_log_steps (sop_log_id, step_id) 
                  SELECT ?, id FROM sop_steps WHERE sop_id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$log_id, $sop_id]);
    }

    public function updateStepStatus($log_id, $step_id, $completed) {
        $query = "UPDATE sop_log_steps SET completed = ?, completed_at = ? WHERE sop_log_id = ? AND step_id = ?";
        $completed_at = $completed ? date('Y-m-d H:i:s') : null;
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$completed, $completed_at, $log_id, $step_id]);

        // Check if all steps are completed
        $this->updateOverallStatus($log_id);
    }

    private function updateOverallStatus($log_id) {
        $query = "SELECT COUNT(*) as total, SUM(completed) as completed FROM sop_log_steps WHERE sop_log_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$log_id]);
        $stats = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($stats['total'] > 0 && $stats['total'] == $stats['completed']) {
            // Get current status to avoid duplicate API calls
            $stmtCurrent = $this->conn->prepare("SELECT l.status, s.title, u.name as staff_name FROM sop_logs l JOIN sops s ON l.sop_id = s.id JOIN users u ON l.user_id = u.id WHERE l.id = ?");
            $stmtCurrent->execute([$log_id]);
            $currentData = $stmtCurrent->fetch(PDO::FETCH_ASSOC);

            if ($currentData && $currentData['status'] !== 'completed') {
                $update = "UPDATE sop_logs SET status = 'completed', completed_at = NOW() WHERE id = ?";
                $this->conn->prepare($update)->execute([$log_id]);
                
                // Trigger Zoho CRM Integration ONLY for Complaint Handling
                if (stripos($currentData['title'], 'Complaint') !== false) {
                    try {
                        require_once __DIR__ . '/../Integrations/ZohoCRM.php';
                        $zoho = new ZohoCRM();
                        $zoho->logSOPCompletion($currentData['title'], $currentData['staff_name'], 'Completed');
                    } catch (Exception $e) {
                        $logFile = __DIR__ . '/../../logs/zoho_errors.log';
                        if (!is_dir(dirname($logFile))) { mkdir(dirname($logFile), 0777, true); }
                        file_put_contents($logFile, "[" . date('Y-m-d H:i:s') . "] Hook Exception: " . $e->getMessage() . "\n", FILE_APPEND);
                    }
                }
            }
        } else {
            $update = "UPDATE sop_logs SET status = 'pending', completed_at = NULL WHERE id = ?";
            $this->conn->prepare($update)->execute([$log_id]);
        }
    }

    public function getAdminStats($branch = 'all') {
        $today = date('Y-m-d');
        $stats = [];
        
        $where = "WHERE date = '$today'";
        if ($branch !== 'all') {
            $where .= " AND branch = '" . $branch . "'";
        }
        
        $q1 = "SELECT COUNT(*) FROM sop_logs $where AND status = 'completed'";
        $stats['completed_today'] = $this->conn->query($q1)->fetchColumn();

        $q2 = "SELECT COUNT(*) FROM sop_logs $where";
        $stats['total_today'] = $this->conn->query($q2)->fetchColumn();

        return $stats;
    }
}
