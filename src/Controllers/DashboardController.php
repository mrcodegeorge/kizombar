<?php

require_once __DIR__ . '/../Models/Log.php';
require_once __DIR__ . '/../Models/Sop.php';

class DashboardController {
    private $db;
    private $log;
    private $sop;

    public function __construct($db) {
        $this->db = $db;
        $this->log = new Log($db);
        $this->sop = new Sop($db);
    }

    public function adminDashboard() {
        $branch = $_SESSION['active_branch'] ?? 'all';
        $stats = $this->log->getAdminStats($branch);
        
        // Get recent logs
        $today = date('Y-m-d');
        $where = "WHERE l.date = ?";
        $params = [$today];
        if ($branch !== 'all') {
            $where .= " AND l.branch = ?";
            $params[] = $branch;
        }

        $query = "SELECT l.*, s.title, u.name as user_name 
                  FROM sop_logs l 
                  JOIN sops s ON l.sop_id = s.id 
                  JOIN users u ON l.user_id = u.id 
                  $where 
                  ORDER BY l.completed_at DESC LIMIT 10";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        $recent_logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Staff Compliance (Last 7 Days)
        $sevenDaysAgo = date('Y-m-d', strtotime('-7 days'));
        $compWhere = "WHERE u.role != 'admin' AND l.date >= ?";
        $compParams = [$sevenDaysAgo];
        if ($branch !== 'all') {
            $compWhere .= " AND l.branch = ?";
            $compParams[] = $branch;
        }

        $complianceQuery = "
            SELECT 
                u.id, 
                u.name, 
                u.role,
                u.branch,
                COUNT(l.id) as total_assigned,
                SUM(CASE WHEN l.status = 'completed' THEN 1 ELSE 0 END) as total_completed,
                SUM(CASE WHEN l.status = 'missed' THEN 1 ELSE 0 END) as total_missed
            FROM users u
            JOIN sop_logs l ON u.id = l.user_id
            $compWhere
            GROUP BY u.id
            ORDER BY (SUM(CASE WHEN l.status = 'completed' THEN 1 ELSE 0 END) / COUNT(l.id)) DESC
        ";
        $compStmt = $this->db->prepare($complianceQuery);
        $compStmt->execute($compParams);
        $compliance = $compStmt->fetchAll(PDO::FETCH_ASSOC);

        return [
            'stats' => $stats,
            'recent_logs' => $recent_logs,
            'compliance' => $compliance,
            'active_branch' => $branch
        ];
    }

    public function getMissedSops() {
        $branch = $_SESSION['active_branch'] ?? 'all';
        $where = "WHERE l.status = 'missed'";
        $params = [];
        if ($branch !== 'all') {
            $where .= " AND l.branch = ?";
            $params[] = $branch;
        }

        $query = "SELECT l.*, s.title, s.category, u.name as staff_name 
                  FROM sop_logs l 
                  JOIN sops s ON l.sop_id = s.id 
                  JOIN users u ON l.user_id = u.id 
                  $where 
                  ORDER BY l.date DESC, l.id DESC";
        $stmt = $this->db->prepare($query);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
