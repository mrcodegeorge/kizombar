<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="mb-4">
    <a href="index.php?action=staff" class="text-brand text-decoration-none fw-600 mb-2 d-inline-block">
        <i class="fas fa-arrow-left me-2"></i> Back
    </a>
    <h2 class="fw-bold fs-3 mt-2 mb-0">Create Staff</h2>
</div>

<?php if (isset($error) && is_array($error) && !$error['success']): ?>
    <div class="alert alert-danger py-2 rounded-3 text-center fw-500 mb-4" role="alert">
        <i class="fas fa-exclamation-circle me-1"></i> <?php echo htmlspecialchars($error['message']); ?>
    </div>
<?php endif; ?>

<form action="index.php?action=staff_create" method="POST">
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body p-4">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-600 text-dark mb-2">Full Name</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. John Doe">
                </div>
                <div class="col-12">
                    <label class="form-label fw-600 text-dark mb-2">Email Address</label>
                    <input type="email" name="email" class="form-control" required placeholder="name@kizo.com">
                </div>
                <div class="col-12">
                    <label class="form-label fw-600 text-dark mb-2">Password</label>
                    <input type="password" name="password" class="form-control" required placeholder="••••••••" minlength="6">
                </div>
                <div class="col-12">
                    <label class="form-label fw-600 text-dark mb-2">Role</label>
                    <select name="role" class="form-select" required>
                        <option value="" disabled selected>Select a role...</option>
                        <option value="staff_bar">Bar Staff</option>
                        <option value="staff_kitchen">Kitchen Staff</option>
                        <option value="staff_cafe">Café Staff</option>
                        <option value="staff_cleaning">Cleaning Staff</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label fw-600 text-dark mb-2">Branch</label>
                    <select name="branch" class="form-select" required>
                        <option value="kizobar">Kizobar</option>
                        <option value="kizo_cafe">Kizo Café</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="sticky-bottom-action">
        <button type="submit" class="btn btn-primary w-100 py-3 fs-5 shadow">
            Create Account
        </button>
    </div>
</form>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
