<?php

require_once __DIR__ . '/../Models/Incident.php';

class IncidentController {
    private $db;
    private $incident;

    public function __construct($db) {
        $this->db = $db;
        $this->incident = new Incident($db);
    }

    public function listAll() {
        return $this->incident->getAll();
    }

    public function createIncident() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $type        = $_POST['type'] ?? '';
            $description = $_POST['description'] ?? '';
            $severity    = $_POST['severity'] ?? 'low';
            $reported_by = $_SESSION['user_id'];
            $image_path  = null;

            // Validate
            if (empty($type) || empty($description)) {
                return ['success' => false, 'message' => 'Type and description are required.'];
            }

            // Handle optional photo
            if (!empty($_FILES['photo']['name'])) {
                $photo = $_FILES['photo'];
                $uploadDir = __DIR__ . '/../../public/uploads/incidents/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

                $ext = strtolower(pathinfo($photo['name'], PATHINFO_EXTENSION));
                if (in_array($ext, ['jpg', 'jpeg', 'png'])) {
                    $fileName = 'incident_' . time() . '_' . $reported_by . '.' . $ext;
                    if (move_uploaded_file($photo['tmp_name'], $uploadDir . $fileName)) {
                        $image_path = 'uploads/incidents/' . $fileName;
                    }
                }
            }

            if ($this->incident->create($type, $description, $severity, $image_path, $reported_by)) {
                $incidentId = $this->incident->getLastInsertId();

                // Sync to Zoho for High/Critical incidents
                if (in_array($severity, ['high', 'critical'])) {
                    try {
                        require_once __DIR__ . '/../Integrations/ZohoCRM.php';
                        $zoho = new ZohoCRM();
                        $subject = "[" . strtoupper($severity) . "] Incident: " . ucwords(str_replace('_', ' ', $type));
                        $desc    = "Reporter: " . ($_SESSION['user_name'] ?? 'Staff') . "\nDescription: " . $description;
                        $zoho->createTask($subject, $desc);
                        $this->incident->markZohoSynced($incidentId);
                    } catch (Exception $e) {
                        // Non-fatal
                    }
                }

                header("Location: index.php?action=incident_report&msg=success");
                exit();
            }

            return ['success' => false, 'message' => 'Failed to save incident. Please try again.'];
        }
        return null;
    }
}
