            </div> <!-- End col-12 -->
        </div> <!-- End row -->
    </div> <!-- End container -->

    <div class="text-center py-4 mb-5 pb-5">
        <p class="text-muted small mb-0">Built with ❤️ by <strong>George Asiedu Annan</strong></p>
    </div>

    <?php if(isset($_SESSION['user_id'])): ?>
    <!-- Mobile Bottom Navigation -->
    <div class="bottom-nav">
        <a href="index.php?action=dashboard" class="nav-item <?php echo (!isset($_GET['action']) || $_GET['action'] == 'dashboard') ? 'active' : ''; ?>">
            <i class="fas fa-home"></i>
            <span>Home</span>
        </a>
        <?php if ($_SESSION['user_role'] === 'admin'): ?>
            <a href="index.php?action=sops" class="nav-item <?php echo (isset($_GET['action']) && in_array($_GET['action'], ['sops', 'sop_edit'])) ? 'active' : ''; ?>">
                <i class="fas fa-tasks"></i>
                <span>SOPs</span>
            </a>
            <a href="index.php?action=staff" class="nav-item <?php echo (isset($_GET['action']) && in_array($_GET['action'], ['staff', 'staff_create'])) ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                <span>Staff</span>
            </a>
            <a href="index.php?action=incidents" class="nav-item <?php echo (isset($_GET['action']) && $_GET['action'] == 'incidents') ? 'active' : ''; ?>">
                <i class="fas fa-flag"></i>
                <span>Incidents</span>
            </a>
            <a href="index.php?action=signoffs" class="nav-item <?php echo (isset($_GET['action']) && $_GET['action'] == 'signoffs') ? 'active' : ''; ?>">
                <i class="fas fa-signature"></i>
                <span>Sign-offs</span>
            </a>
            <a href="index.php?action=attendance_report" class="nav-item <?php echo (isset($_GET['action']) && $_GET['action'] == 'attendance_report') ? 'active' : ''; ?>">
                <i class="fas fa-user-check"></i>
                <span>Attendance</span>
            </a>
        <?php else: ?>
            <a href="index.php?action=dashboard" class="nav-item <?php echo (!isset($_GET['action']) || $_GET['action'] == 'dashboard') ? 'active' : ''; ?>">
                <i class="fas fa-list-check"></i>
                <span>Tasks</span>
            </a>
            <a href="index.php?action=clock_in" class="nav-item <?php echo (isset($_GET['action']) && $_GET['action'] == 'clock_in') ? 'active' : ''; ?>">
                <i class="fas fa-clock"></i>
                <span>Clock-in</span>
            </a>
            <a href="index.php?action=incident_report" class="nav-item <?php echo (isset($_GET['action']) && $_GET['action'] == 'incident_report') ? 'active' : ''; ?>">
                <i class="fas fa-exclamation-triangle"></i>
                <span>Report</span>
            </a>
        <?php endif; ?>
        <a href="index.php?action=profile" class="nav-item <?php echo (isset($_GET['action']) && $_GET['action'] == 'profile') ? 'active' : ''; ?>">
            <i class="fas fa-user"></i>
            <span>Profile</span>
        </a>
    </div>
    <?php endif; ?>

    <!-- Global Success Overlay -->
    <div class="success-overlay" id="globalSuccessOverlay">
        <div class="success-icon-container">
            <i class="fas fa-check"></i>
        </div>
        <h2 class="fw-bold mb-2">SOP Completed</h2>
        <p class="text-white-50 mb-4 text-center px-4">Great job! Your progress has been logged and synced.</p>
        <button class="btn btn-light rounded-pill px-4 py-2 fw-600 text-brand" onclick="window.location.href='index.php?action=dashboard'">
            Back to Dashboard
        </button>
    </div>

    <!-- Toast Container -->
    <div class="toast-container position-fixed top-0 start-50 translate-middle-x p-3" style="z-index: 1100; margin-top: 60px;">
        <div id="generalToast" class="toast align-items-center text-white bg-dark border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body fw-500" id="generalToastBody">
                    Message
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="assets/js/app.js"></script>
    <script src="assets/js/push.js"></script>
    <script>
    // Service Worker Registration (PWA)
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then(reg => {
                    console.log('SW registered:', reg.scope);
                    // Show bell if push is supported and not yet granted
                    if ('PushManager' in window && Notification.permission !== 'granted') {
                        const bell = document.getElementById('notifBell');
                        if (bell) bell.classList.remove('d-none');
                    }
                })
                .catch(err => console.warn('SW registration failed:', err));
        });
    }
    </script>
</body>
</html>
