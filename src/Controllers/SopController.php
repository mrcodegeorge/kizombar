<?php

require_once __DIR__ . '/../Models/Sop.php';

class SopController {
    private $db;
    private $sop;

    public function __construct($db) {
        $this->db = $db;
        $this->sop = new Sop($db);
    }

    public function listSops() {
        return $this->sop->getAll();
    }

    public function getSopDetails($id) {
        $data = $this->sop->getById($id);
        if ($data) {
            $data['steps'] = $this->sop->getSteps($id);
        }
        return $data;
    }

    public function saveSop() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $title = $_POST['title'] ?? '';
            $category = $_POST['category'] ?? '';
            $description = $_POST['description'] ?? '';
            $steps = $_POST['steps'] ?? [];
            $requires_signoff = isset($_POST['requires_signoff']) ? 1 : 0;
            $branch = $_POST['branch'] ?? 'all';
            $created_by = $_SESSION['user_id'];

            if ($id) {
                $this->sop->update($id, $title, $category, $description, $requires_signoff, $branch);
                $this->sop->deleteSteps($id);
                $sop_id = $id;
            } else {
                $sop_id = $this->sop->create($title, $category, $description, $created_by, $requires_signoff, $branch);
            }

            $requires_photo_arr = $_POST['requires_photo'] ?? [];

            foreach ($steps as $index => $step_text) {
                if (!empty(trim($step_text))) {
                    $req_photo = isset($requires_photo_arr[$index]) ? 1 : 0;
                    $this->sop->addStep($sop_id, $step_text, $index + 1, $req_photo);
                }
            }

            header("Location: index.php?action=sops");
            exit();
        }
    }

    public function deleteSop($id) {
        $this->sop->delete($id);
        header("Location: index.php?action=sops");
        exit();
    }
}
