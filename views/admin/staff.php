<?php include __DIR__ . '/../layouts/header.php'; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2 class="fw-bold fs-3 m-0">Staff Management</h2>
    <a href="index.php?action=staff_create" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">
        <i class="fas fa-user-plus me-1"></i> New
    </a>
</div>

<?php if (isset($_GET['msg']) && $_GET['msg'] === 'created'): ?>
    <div class="alert alert-success py-2 rounded-3 text-center fw-500 mb-4" role="alert">
        <i class="fas fa-check-circle me-1"></i> Staff member successfully created!
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm mb-4">
    <div class="card-body p-0">
        <?php foreach ($staffList as $staff): ?>
        <div class="d-flex align-items-center p-3 border-bottom border-light">
            <div class="avatar-circle me-3 flex-shrink-0" style="width: 45px; height: 45px; font-size: 1.2rem;">
                <?php echo strtoupper(substr($staff['name'], 0, 1)); ?>
            </div>
            <div class="flex-grow-1">
                <h6 class="fw-bold mb-1 text-dark"><?php echo htmlspecialchars($staff['name']); ?></h6>
                <p class="mb-0 text-muted small">
                    <i class="fas fa-envelope me-1"></i> <?php echo htmlspecialchars($staff['email']); ?>
                </p>
            </div>
            <div class="text-end ms-2">
                <span class="badge bg-light text-brand border px-2 py-1 rounded-pill">
                    <?php echo str_replace('staff_', '', $staff['role']); ?>
                </span>
            </div>
        </div>
        <?php endforeach; ?>
        
        <?php if (empty($staffList)): ?>
        <div class="text-center py-5">
            <p class="text-muted mb-0 fw-500">No staff members found.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../layouts/footer.php'; ?>
