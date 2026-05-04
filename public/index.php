<?php
if (!is_dir(__DIR__ . '/../sessions')) {
    mkdir(__DIR__ . '/../sessions', 0777, true);
}
session_save_path(__DIR__ . '/../sessions');
session_start();

require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/../src/Controllers/AuthController.php';
require_once __DIR__ . '/../src/Controllers/SopController.php';
require_once __DIR__ . '/../src/Controllers/StaffController.php';
require_once __DIR__ . '/../src/Controllers/DashboardController.php';
require_once __DIR__ . '/../src/Controllers/UserController.php';
require_once __DIR__ . '/../src/Controllers/IncidentController.php';
require_once __DIR__ . '/../src/Models/PushSubscription.php';
require_once __DIR__ . '/../src/Controllers/AttendanceController.php';

$database = new Database();
$db = $database->getConnection();

if (!$db) {
    die("<div style='padding:2rem; font-family:sans-serif; text-align:center;'>
            <h2 style='color:#dc2626;'>Database Connection Failed</h2>
            <p>Please ensure <strong>MySQL</strong> is running in your XAMPP/Local environment.</p>
            <p style='color:#666; font-size:0.9rem;'>Error: SQLSTATE[HY000] [2002] Connection refused</p>
         </div>");
}

$action = $_GET['action'] ?? 'dashboard';

switch ($action) {
    case 'login':
        $auth = new AuthController($db);
        $error = $auth->handleLogin();
        $staffForPin = $auth->getStaffListForPin();
        include __DIR__ . '/../views/login.php';
        break;

    case 'pin_login':
        $auth = new AuthController($db);
        $error = $auth->handlePinLogin();
        if ($error) {
            $staffForPin = $auth->getStaffListForPin();
            $_GET['tab'] = 'pin';
            include __DIR__ . '/../views/login.php';
        }
        break;

    case 'set_pin':
        AuthController::checkAuth();
        $auth = new AuthController($db);
        $updateResult = $auth->handleSetPin();
        $profile = $auth->getProfileData();
        include __DIR__ . '/../views/profile.php';
        break;

    case 'save_push_subscription':
        AuthController::checkAuth();
        $data = json_decode(file_get_contents('php://input'), true);
        if ($data && isset($data['endpoint'])) {
            $pushModel = new PushSubscription($db);
            $pushModel->save(
                $_SESSION['user_id'], 
                $data['endpoint'], 
                $data['keys']['p256dh'] ?? '', 
                $data['keys']['auth'] ?? ''
            );
            echo json_encode(['success' => true]);
        }
        exit();

    case 'clock_in':
        $attendance = new AttendanceController($db);
        $attendance->showClockIn();
        break;

    case 'handle_clock_in':
        $attendance = new AttendanceController($db);
        $attendance->handleClockIn();
        break;

    case 'handle_clock_out':
        $attendance = new AttendanceController($db);
        $attendance->handleClockOut();
        break;

    case 'attendance_report':
        $attendance = new AttendanceController($db);
        $attendance->adminDashboard();
        break;

    case 'attendance_update_status':
        $attendance = new AttendanceController($db);
        $attendance->updateStatus();
        break;

    case 'logout':
        $auth = new AuthController($db);
        $auth->logout();
        break;

    case 'profile':
        $auth = new AuthController($db);
        $profile = $auth->getProfileData();
        $updateResult = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $updateResult = $auth->handleProfileUpdate();
        }
        include __DIR__ . '/../views/profile.php';
        break;

    case 'set_branch':
        AuthController::checkAdmin();
        $branch = $_GET['branch'] ?? 'all';
        $_SESSION['active_branch'] = $branch;
        header("Location: " . ($_SERVER['HTTP_REFERER'] ?? 'index.php?action=dashboard'));
        exit();

    case 'dashboard':
        AuthController::checkAuth();
        if ($_SESSION['user_role'] === 'admin') {
            $dash = new DashboardController($db);
            $data = $dash->adminDashboard();
            include __DIR__ . '/../views/admin/dashboard.php';
        } else {
            $staff = new StaffController($db);
            $tasks = $staff->dashboard();
            include __DIR__ . '/../views/staff/dashboard.php';
        }
        break;

    case 'missed_sops':
        AuthController::checkAdmin();
        $dash = new DashboardController($db);
        $missed_sops = $dash->getMissedSops();
        include __DIR__ . '/../views/admin/missed_sops.php';
        break;

    case 'sops':
        AuthController::checkAdmin();
        $sopCtrl = new SopController($db);
        $sops = $sopCtrl->listSops();
        include __DIR__ . '/../views/admin/sops.php';
        break;

    case 'sop_edit':
        AuthController::checkAdmin();
        $sopCtrl = new SopController($db);
        $sop = [];
        if (isset($_GET['id'])) {
            $sop = $sopCtrl->getSopDetails($_GET['id']);
        }
        include __DIR__ . '/../views/admin/sop_edit.php';
        break;

    case 'sop_save':
        AuthController::checkAdmin();
        $sopCtrl = new SopController($db);
        $sopCtrl->saveSop();
        break;

    case 'sop_delete':
        AuthController::checkAdmin();
        $sopCtrl = new SopController($db);
        $sopCtrl->deleteSop($_GET['id']);
        break;

    case 'staff':
        AuthController::checkAdmin();
        $userCtrl = new UserController($db);
        $staffList = $userCtrl->listStaff();
        include __DIR__ . '/../views/admin/staff.php';
        break;

    case 'staff_create':
        AuthController::checkAdmin();
        $userCtrl = new UserController($db);
        $error = $userCtrl->createStaff();
        include __DIR__ . '/../views/admin/staff_create.php';
        break;

    case 'assignments':
        AuthController::checkAdmin();
        $assign = new Assignment($db);
        $sopCtrl = new SopController($db);
        $userModel = new User($db);
        
        $assignments = $assign->getAll();
        $sops = $sopCtrl->listSops();
        
        // Simple user list for assignment
        $uStmt = $db->query("SELECT id, name, role FROM users WHERE role != 'admin'");
        $users = $uStmt->fetchAll(PDO::FETCH_ASSOC);
        
        include __DIR__ . '/../views/admin/assignments.php';
        break;

    case 'assignment_save':
        AuthController::checkAdmin();
        $assign = new Assignment($db);
        $assign->create(
            $_POST['sop_id'],
            $_POST['user_id'] ?: null,
            $_POST['role'] ?: null,
            $_POST['frequency'],
            $_POST['shift']
        );
        header("Location: index.php?action=assignments");
        break;

    case 'assignment_delete':
        AuthController::checkAdmin();
        $assign = new Assignment($db);
        $assign->delete($_GET['id']);
        header("Location: index.php?action=assignments");
        break;

    case 'checklist':
        AuthController::checkAuth();
        $staff = new StaffController($db);
        $data = $staff->checklist($_GET['sop_id'], $_GET['shift']);
        $sop = $data['sop'];
        $log = $data['log'];
        $steps = $data['steps'];
        include __DIR__ . '/../views/staff/checklist.php';
        break;

    case 'upload_proof':
        AuthController::checkAuth();
        $staff = new StaffController($db);
        $staff->uploadProof();
        break;

    case 'toggle_step':
        AuthController::checkAuth();
        $staff = new StaffController($db);
        $staff->toggleStep();
        break;

    // Feature 5: Traffic Control
    case 'traffic_control':
        AuthController::checkAdmin();
        include __DIR__ . '/../views/admin/traffic_control.php';
        break;

    case 'set_traffic':
        AuthController::checkAdmin();
        if (isset($_POST['level']) && in_array($_POST['level'], ['low', 'medium', 'high'])) {
            $db->prepare("UPDATE app_settings SET setting_value = ? WHERE setting_key = 'traffic_level'")->execute([$_POST['level']]);
        }
        header('Location: index.php?action=traffic_control');
        exit();

    // Feature 6: Incident Reporting
    case 'incident_report':
        AuthController::checkAuth();
        $incCtrl = new IncidentController($db);
        $formError = null;
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $formError = $incCtrl->createIncident();
        }
        include __DIR__ . '/../views/staff/incident_report.php';
        break;

    case 'incidents':
        AuthController::checkAdmin();
        $incCtrl = new IncidentController($db);
        $incidents = $incCtrl->listAll();
        include __DIR__ . '/../views/admin/incidents.php';
        break;

    // Feature 7: Sign-offs
    case 'signoffs':
        AuthController::checkAdmin();
        $query = "SELECT l.*, s.title, u.name as user_name FROM sop_logs l 
                  JOIN sops s ON l.sop_id = s.id AND s.requires_signoff = 1
                  JOIN users u ON l.user_id = u.id 
                  WHERE l.status = 'completed' AND l.signoff_status IS NULL
                  ORDER BY l.date DESC";
        $stmt = $db->prepare($query);
        $stmt->execute();
        $pending_signoffs = $stmt->fetchAll(PDO::FETCH_ASSOC);
        include __DIR__ . '/../views/admin/signoffs.php';
        break;

    case 'approve_sop':
        AuthController::checkAdmin();
        if (isset($_POST['log_id'])) {
            $stmt = $db->prepare("UPDATE sop_logs SET signoff_status='approved', signoff_by=?, signoff_at=NOW() WHERE id=?");
            $stmt->execute([$_SESSION['user_id'], $_POST['log_id']]);
        }
        header('Location: index.php?action=signoffs');
        exit();

    case 'reject_sop':
        AuthController::checkAdmin();
        if (isset($_POST['log_id'])) {
            $note = $_POST['note'] ?? null;
            $stmt = $db->prepare("UPDATE sop_logs SET signoff_status='rejected', signoff_by=?, signoff_note=?, signoff_at=NOW() WHERE id=?");
            $stmt->execute([$_SESSION['user_id'], $note, $_POST['log_id']]);
        }
        header('Location: index.php?action=signoffs');
        exit();

    // Feature 8: Inventory
    case 'inventory':
        AuthController::checkAdmin();
        include __DIR__ . '/../views/admin/inventory.php';
        break;

    case 'inventory_add':
        AuthController::checkAdmin();
        require_once __DIR__ . '/../src/Models/Inventory.php';
        $inv = new Inventory($db);
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $inv->addItem($_POST['name'], $_POST['unit'], $_POST['stock'], $_POST['threshold'], $_POST['category']);
            header('Location: index.php?action=inventory?msg=added');
            exit();
        }
        include __DIR__ . '/../views/admin/inventory_add.php';
        break;

    case 'inventory_update':
        AuthController::checkAdmin();
        require_once __DIR__ . '/../src/Models/Inventory.php';
        $inv = new Inventory($db);
        if (isset($_POST['id']) && isset($_POST['stock'])) {
            $inv->updateStock((int)$_POST['id'], (float)$_POST['stock']);
        }
        header('Location: index.php?action=inventory&msg=updated');
        exit();

    default:
        header("Location: index.php?action=dashboard");
        break;
}
