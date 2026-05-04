<?php include __DIR__ . '/layouts/header.php'; ?>

<div class="mb-4 text-center mt-3">
    <div class="avatar-circle mx-auto mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
        <?php echo strtoupper(substr($profile['name'], 0, 1)); ?>
    </div>
    <h2 class="fw-bold fs-3 mb-1"><?php echo htmlspecialchars($profile['name']); ?></h2>
    <p class="text-muted fw-500 mb-0"><?php echo htmlspecialchars($profile['email']); ?></p>
    <div class="mt-2">
        <span class="badge bg-light text-brand border px-3 py-2 rounded-pill text-uppercase" style="letter-spacing: 1px;">
            <?php echo str_replace('staff_', '', $profile['role']); ?>
        </span>
    </div>
</div>

<?php if (isset($updateResult)): ?>
    <div class="alert <?php echo $updateResult['success'] ? 'alert-success' : 'alert-danger'; ?> py-2 rounded-3 text-center fw-500 mb-4" role="alert">
        <i class="fas <?php echo $updateResult['success'] ? 'fa-check-circle' : 'fa-exclamation-circle'; ?> me-1"></i> 
        <?php echo htmlspecialchars($updateResult['message']); ?>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h4 class="fw-bold fs-5 mb-3">Change Password</h4>
        <form method="POST" action="index.php?action=profile">
            <div class="mb-3">
                <label class="form-label text-muted fw-600 small mb-2">Current Password</label>
                <input type="password" name="current_password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label text-muted fw-600 small mb-2">New Password</label>
                <input type="password" name="new_password" class="form-control" required minlength="6">
            </div>
            <div class="mb-4">
                <label class="form-label text-muted fw-600 small mb-2">Confirm New Password</label>
                <input type="password" name="confirm_password" class="form-control" required minlength="6">
            </div>
            <button type="submit" class="btn btn-primary w-100 py-3 fw-600">Update Password</button>
        </form>
    </div>
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-4">
        <h4 class="fw-bold fs-5 mb-1">Quick PIN Login</h4>
        <p class="text-muted small mb-3">Set a 4-digit PIN for faster sign-in on this device.</p>
        <form method="POST" action="index.php?action=set_pin">
            <div class="row g-3">
                <div class="col-6">
                    <label class="form-label text-muted fw-600 small mb-2">4-Digit PIN</label>
                    <input type="password" name="pin" class="form-control text-center fs-4" maxlength="4" pattern="\d{4}" inputmode="numeric" required placeholder="••••">
                </div>
                <div class="col-6">
                    <label class="form-label text-muted fw-600 small mb-2">Confirm PIN</label>
                    <input type="password" name="pin_confirm" class="form-control text-center fs-4" maxlength="4" pattern="\d{4}" inputmode="numeric" required placeholder="••••">
                </div>
            </div>
            <button type="submit" class="btn btn-outline-brand w-100 py-3 fw-600 mt-4">Save Quick PIN</button>
        </form>
    </div>
</div>

<div class="text-center mb-5">
    <a href="index.php?action=logout" class="btn btn-outline-danger py-3 px-5 rounded-pill fw-600 shadow-sm w-100" style="max-width: 300px;">
        <i class="fas fa-sign-out-alt me-2"></i> Log Out
    </a>
</div>

<?php include __DIR__ . '/layouts/footer.php'; ?>
