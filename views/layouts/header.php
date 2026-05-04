<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="theme-color" content="#1a6b3c">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Kizo SOP">
    <link rel="manifest" href="manifest.json">
    <link rel="apple-touch-icon" href="assets/icons/icon-192.png">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' — ' : ''; ?>Kizo SOP Manager</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>

    <!-- Mobile Top Bar -->
    <div class="top-bar">
        <a href="index.php?action=dashboard" class="brand">
            <i class="fas fa-clipboard-check me-2"></i> Kizo SOP
        </a>
        <div class="d-flex align-items-center gap-2">
            <?php if(isset($_SESSION['user_id'])): ?>
                <button id="notifBell" class="btn btn-sm btn-link text-white p-0 position-relative d-none" onclick="requestPushPermission()" title="Enable Notifications">
                    <i class="fas fa-bell fs-5"></i>
                    <span id="notifDot" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" style="font-size:0.5rem;">!</span>
                </button>
                <a href="index.php?action=profile" class="avatar-circle text-decoration-none">
                    <?php echo strtoupper(substr($_SESSION['user_name'], 0, 1)); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="container px-3 pb-3">
        <div class="row">
            <!-- Main Content Area -->
            <div class="col-12 col-md-8 mx-auto">
